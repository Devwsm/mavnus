<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Support\Like;

class Order extends Model
{
    public const STATUSES = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

    /**
     * Label (bahasa dashboard), warna, dan ikon tiap status. Satu-satunya tempat yang mengatur
     * tampilan status di dashboard (kartu, badge, daftar), jadi mengubah warna cukup di sini.
     */
    public const STATUS_META = [
        'pending'    => ['label' => 'Pending',    'color' => '#B77B1C', 'icon' => 'bi-hourglass-split'],
        'processing' => ['label' => 'Diproses',   'color' => '#1C1CB7', 'icon' => 'bi-gear-fill'],
        'shipped'    => ['label' => 'Dikirim',    'color' => '#5E1C5E', 'icon' => 'bi-truck'],
        'completed'  => ['label' => 'Selesai',    'color' => '#1C7B1C', 'icon' => 'bi-check-circle-fill'],
        'cancelled'  => ['label' => 'Dibatalkan', 'color' => '#e05656', 'icon' => 'bi-x-circle-fill'],
    ];

    protected $primaryKey = 'id_order';
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'total_weight',
        'subtotal',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id_order');
    }

    // Relasi ke akun customer - nullable, order guest gak punya user_id
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Status tujuan yang boleh dipilih dari status sekarang (termasuk status itu sendiri).
     * 'cancelled' tidak bisa dibuka lagi (stok sudah dikembalikan), dan tidak ada jalan
     * balik ke 'pending' karena pesanan pending lama langsung kena pembatalan otomatis.
     */
    public static function nextStatuses(string $current): array
    {
        return match ($current) {
            'pending'    => ['pending', 'processing', 'cancelled'],
            'processing' => ['processing', 'shipped', 'cancelled'],
            'shipped'    => ['shipped', 'processing', 'completed'],
            'completed'  => ['completed', 'shipped'],
            default      => [$current], // cancelled & status tak dikenal: terkunci
        };
    }

    /**
     * Pesanan yang dibatalkan otomatis karena tidak dibayar tepat waktu
     * (status 'cancelled' + payment_status 'expired'), beda dengan batal manual oleh staff.
     */
    public function isExpired(): bool
    {
        return $this->status === 'cancelled' && $this->payment_status === 'expired';
    }

    /**
     * Jumlah pesanan per status (semua status selalu ada, minimal 0), dalam satu query.
     * Isi $userId untuk menghitung pesanan milik satu akun pembeli saja.
     *
     * @return array<string, int>
     */
    public static function countByStatus(?int $userId = null): array
    {
        $counts = static::query()
            ->when($userId !== null, fn(Builder $query) => $query->where('user_id', $userId))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return collect(self::STATUSES)
            ->mapWithKeys(fn(string $status) => [$status => (int) ($counts[$status] ?? 0)])
            ->all();
    }

    /** Label status untuk badge di tampilan. */
    public function statusLabel(): string
    {
        return $this->isExpired() ? 'Kedaluwarsa' : ucfirst($this->status);
    }

    /** Label status sesuai bahasa yang dipilih pengunjung (dipakai di halaman customer & email). */
    public function localizedStatus(): string
    {
        return __('order.status.' . ($this->isExpired() ? 'expired' : $this->status));
    }

    /**
     * Rapikan filter dari query string: nilai yang bukan string atau tidak valid dibuang
     * (jadi string kosong), tanggal dinormalkan ke Y-m-d, dan from/to ditukar kalau terbalik.
     *
     * @return array{q: string, status: string, from: string, to: string}
     */
    public static function normalizeFilters(array $raw): array
    {
        $q = is_string($raw['q'] ?? null) ? trim(mb_substr($raw['q'], 0, 100)) : '';

        $status = $raw['status'] ?? null;
        $status = is_string($status) && in_array($status, self::STATUSES, true) ? $status : '';

        $from = self::parseDate($raw['from'] ?? null);
        $to = self::parseDate($raw['to'] ?? null);

        if ($from && $to && $from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        return [
            'q'      => $q,
            'status' => $status,
            'from'   => $from?->format('Y-m-d') ?? '',
            'to'     => $to?->format('Y-m-d') ?? '',
        ];
    }

    /**
     * Filter pesanan: q (nomor pesanan / nama / HP / email), status, dan rentang tanggal
     * (from/to, inklusif). Dipakai daftar pesanan staff dan semua export.
     */
    public function scopeFilter(Builder $query, array $raw): Builder
    {
        $f = self::normalizeFilters($raw);

        if ($f['status'] !== '') {
            $query->where('status', $f['status']);
        }

        if ($f['q'] !== '') {
            $like = Like::contains($f['q']);

            $query->where(function (Builder $w) use ($like) {
                foreach (['order_number', 'customer_name', 'customer_phone', 'customer_email'] as $column) {
                    $w->orWhereRaw(Like::sql($column), [$like]);
                }
            });
        }

        if ($f['from'] !== '') {
            $query->where('created_at', '>=', Carbon::parse($f['from'])->startOfDay());
        }

        if ($f['to'] !== '') {
            $query->where('created_at', '<=', Carbon::parse($f['to'])->endOfDay());
        }

        return $query;
    }

    private static function parseDate(mixed $value): ?Carbon
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            $date = Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }

        // Tolak tanggal yang "meluap" (mis. 2026-02-31 jadi 3 Maret)
        return $date && $date->format('Y-m-d') === $value ? $date : null;
    }

    public static function generateOrderNumber(): string
    {
        return 'MVN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}