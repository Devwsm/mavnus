<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Support\Like;

#[Fillable(['name', 'email', 'phone', 'address', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Pesanan yang dibuat saat akun ini login (pesanan guest tidak punya user_id). */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /** Cari anggota lewat nama, email, atau nomor HP. Kata kunci kosong = tidak memfilter. */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if ($term === '') {
            return $query;
        }

        $like = Like::contains($term);

        return $query->where(function (Builder $w) use ($like) {
            foreach (['name', 'email', 'phone'] as $column) {
                $w->orWhereRaw(Like::sql($column), [$like]);
            }
        });
    }
}