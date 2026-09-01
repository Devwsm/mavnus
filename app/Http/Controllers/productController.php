<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\product;
use App\Models\clothes;
use App\Models\accessoris;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class productController extends Controller
{
    // Halaman "Input Produk" (satu halaman buat clothes & accessories,
    // field yang muncul nyesuain kategori yang dipilih di dropdown).
    public function produk(Request $request)
    {
        $perPage = 24;

        // Kalau URL nunjuk ke produk tertentu lewat ?edit=ID (dari link
        // "Stok Perlu Perhatian" / "Rilis Terjadwal" / "Produk Terbaru" di
        // landing dashboard), otomatis lompat ke halaman yang beneran ada
        // produk itu - biar deep-link gak nyasar selalu ke halaman 1.
        if ($request->filled('edit') && !$request->filled('page')) {
            $targetProduct = product::find($request->input('edit'));
            if ($targetProduct) {
                $position = product::whereIn('category', ['clothes', 'accessories'])
                    ->where('created_at', '>', $targetProduct->created_at)
                    ->count();
                $request->query->set('page', intdiv($position, $perPage) + 1);
            }
        }

        $products = product::whereIn('category', ['clothes', 'accessories'])
            ->with(['images', 'clothes', 'accessories', 'variants'])
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        // Varian clothes dengan stok menipis (1-3 pcs) dari produk yang masih aktif
        $lowStockVariants = ProductVariant::with('product.images')
            ->whereHas('product', fn($query) => $query->where('is_active', true))
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->get();

        // Accessories dengan stok menipis (1-3 pcs) — stok tunggal, bukan variant
        $lowStockAccessories = product::accessoriesCategory()
            ->with('images', 'accessories')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where('stock', '<=', 3)
            ->orderBy('stock')
            ->get();

        return view('pages.dashboard.produk', compact('products', 'lowStockVariants', 'lowStockAccessories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'              => 'required|in:clothes,accessories',

            'name'                  => 'required|string|max:255',
            'price'                 => 'required|integer|min:0',
            'weight'                => 'required|integer|min:1',
            'description'           => 'nullable|string',

            'release_mode'          => 'required|in:now,scheduled',
            'published_at'          => [
                'required_if:release_mode,scheduled',
                'nullable',
                'date',
                'after:now',
                'before_or_equal:' . now()->addDays(7)->endOfDay(),
            ],

            // Clothes
            'color'                 => 'required_if:category,clothes|string|max:100',
            'material'              => 'required_if:category,clothes|string|max:100',
            'variants'              => 'required_if:category,clothes|array|min:1',
            'variants.*.size'       => 'required_if:category,clothes|in:S,M,L,XL',
            'variants.*.stock'      => 'required_if:category,clothes|integer|min:0',

            // Accessories
            'accessory_type'        => 'required_if:category,accessories|in:keychain,sticker,totebag',
            'stock'                 => 'required_if:category,accessories|integer|min:0',

            'images'                => 'nullable|array',
            'images.*'              => 'image|max:5120',
        ], [
            'category.required'     => 'Kategori wajib dipilih.',
            'category.in'           => 'Kategori tidak valid.',

            'name.required'         => 'Nama produk wajib diisi.',
            'name.max'              => 'Nama produk maksimal 255 karakter.',

            'price.required'        => 'Harga wajib diisi.',
            'price.integer'         => 'Harga harus berupa angka.',
            'price.min'             => 'Harga tidak boleh kurang dari 0.',

            'weight.required'       => 'Berat produk wajib diisi.',
            'weight.integer'        => 'Berat harus berupa angka.',
            'weight.min'            => 'Berat minimal 1 gram.',

            'color.required_if'     => 'Warna wajib diisi.',
            'color.max'             => 'Warna maksimal 100 karakter.',

            'material.required_if'  => 'Material wajib diisi.',
            'material.max'          => 'Material maksimal 100 karakter.',

            'release_mode.required'        => 'Pilih mau publish sekarang atau dijadwalkan.',
            'release_mode.in'              => 'Pilihan rilis tidak valid.',
            'published_at.required_if'     => 'Tanggal & jam rilis wajib diisi kalau memilih jadwalkan.',
            'published_at.date'            => 'Format tanggal & jam tidak valid.',
            'published_at.after'           => 'Waktu rilis harus di masa depan.',
            'published_at.before_or_equal' => 'Waktu rilis maksimal 7 hari dari sekarang.',

            'variants.required_if'         => 'Minimal isi satu ukuran & stok.',
            'variants.array'               => 'Format ukuran & stok tidak valid.',
            'variants.min'                 => 'Minimal isi satu ukuran & stok.',
            'variants.*.size.required_if'  => 'Ukuran wajib dipilih.',
            'variants.*.size.in'           => 'Ukuran harus salah satu dari S, M, L, atau XL.',
            'variants.*.stock.required_if' => 'Stok wajib diisi.',
            'variants.*.stock.integer'     => 'Stok harus berupa angka.',
            'variants.*.stock.min'         => 'Stok tidak boleh kurang dari 0.',

            'accessory_type.required_if'   => 'Tipe aksesoris wajib dipilih.',
            'accessory_type.in'            => 'Tipe aksesoris harus salah satu dari Keychain, Sticker, atau Totebag.',
            'stock.required_if'            => 'Stok wajib diisi.',
            'stock.integer'                => 'Stok harus berupa angka.',
            'stock.min'                    => 'Stok tidak boleh kurang dari 0.',

            'images.array'           => 'Format foto tidak valid.',
            'images.*.image'         => 'File yang diupload harus berupa gambar.',
            'images.*.max'           => 'Ukuran tiap foto maksimal 5MB.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Simpan data dasar produk ke tabel products
            $product = product::create([
                'category'     => $validated['category'],
                'name'         => $validated['name'],
                'slug'         => Str::slug($validated['name']) . '-' . uniqid(),
                'price'        => $validated['price'],
                'weight'       => $validated['weight'],
                'description'  => $validated['description'] ?? null,
                'is_active'    => true,
                // Accessories pakai stok tunggal, clothes tetap null (dihitung dari variants)
                'stock'        => $validated['category'] === 'accessories' ? $validated['stock'] : null,
                'published_at' => $validated['release_mode'] === 'scheduled'
                    ? $validated['published_at']
                    : now(),
            ]);

            // 2. Simpan detail sesuai kategori
            if ($validated['category'] === 'clothes') {
                clothes::create([
                    'product_id' => $product->id_product,
                    'color'      => $validated['color'],
                    'material'   => $validated['material'],
                ]);

                foreach ($validated['variants'] as $variant) {
                    ProductVariant::create([
                        'product_id' => $product->id_product,
                        'label'      => $variant['size'],
                        'stock'      => $variant['stock'],
                    ]);
                }
            } else {
                accessoris::create([
                    'product_id' => $product->id_product,
                    'type'       => $validated['accessory_type'],
                ]);
            }

            // 3. Konversi & simpan tiap foto yang diupload sebagai WebP
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $filename = Str::uuid() . '.webp';
                    $folder   = 'products/' . $validated['category'];
                    Storage::disk('public')->makeDirectory($folder);
                    $encoded = Image::decode($file)->encode(new WebpEncoder(quality: 80));
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $encoded);
                    ProductImage::create([
                        'product_id' => $product->id_product,
                        'image_path' => "{$folder}/{$filename}",
                        'sort_order' => $index,
                    ]);
                }
            }

            // 4. Hitung ulang status aktif/sold-out berdasarkan stok yang baru diinput
            $product->load('variants');
            $product->syncActiveStatus();
        });

        return redirect()
            ->route('dashboard.produk')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function destroy(Product $product)
    {
        $imagePaths = $product->images->pluck('image_path');
        DB::transaction(function () use ($product) {
            CartItem::where('product_id', $product->id_product)->delete();
            $product->delete();
        });
        // Baru hapus file fisik setelah transaksi database beneran sukses
        foreach ($imagePaths as $path) {
            Storage::disk('public')->delete($path);
        }

        return redirect()
            ->route('dashboard')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function update(Request $request, Product $product)
    {
        // Kategori gak bisa diganti lewat form edit — select-nya di-disable di
        // view, tapi tetep dikirim via hidden input (lihat field/category-select).
        // Kita paksa balik ke kategori asli produk buat jaga-jaga kalau hidden
        // input somehow gak kekirim/dimanipulasi.
        $category = $product->category;
        $request->merge(['category' => $category]);

        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'price'                 => 'required|integer|min:0',
            'weight'                => 'required|integer|min:1',
            'description'           => 'nullable|string',

            'release_mode'          => 'required|in:now,scheduled',
            'published_at'          => [
                'required_if:release_mode,scheduled',
                'nullable',
                'date',
                'before_or_equal:' . now()->addDays(7)->endOfDay(),
            ],

            // Clothes
            'color'                 => 'required_if:category,clothes|string|max:100',
            'material'              => 'required_if:category,clothes|string|max:100',
            'variants'              => 'required_if:category,clothes|array|min:1',
            'variants.*.size'       => 'required_if:category,clothes|in:S,M,L,XL',
            'variants.*.stock'      => 'required_if:category,clothes|integer|min:0',

            // Accessories
            'accessory_type'        => 'required_if:category,accessories|in:keychain,sticker,totebag',
            'stock'                 => 'required_if:category,accessories|integer|min:0',

            'images'                => 'nullable|array',
            'images.*'              => 'image|max:5120',

            'delete_images'         => 'nullable|array',
            'delete_images.*'       => 'integer|exists:product_images,id_product_image',
        ], [
            'name.required'         => 'Nama produk wajib diisi.',
            'price.required'        => 'Harga wajib diisi.',
            'price.integer'         => 'Harga harus berupa angka.',
            'weight.required'       => 'Berat produk wajib diisi.',
            'weight.integer'        => 'Berat harus berupa angka.',
            'weight.min'            => 'Berat minimal 1 gram.',

            'color.required_if'     => 'Warna wajib diisi.',
            'material.required_if'  => 'Material wajib diisi.',

            'release_mode.required'        => 'Pilih mau publish sekarang atau dijadwalkan.',
            'published_at.required_if'     => 'Tanggal & jam rilis wajib diisi kalau memilih jadwalkan.',
            'published_at.date'            => 'Format tanggal & jam tidak valid.',
            'published_at.before_or_equal' => 'Waktu rilis maksimal 7 hari dari sekarang.',

            'variants.required_if'         => 'Minimal isi satu ukuran & stok.',
            'variants.*.size.required_if'  => 'Ukuran wajib dipilih.',
            'variants.*.size.in'           => 'Ukuran harus salah satu dari S, M, L, atau XL.',
            'variants.*.stock.required_if' => 'Stok wajib diisi.',

            'accessory_type.required_if'   => 'Tipe aksesoris wajib dipilih.',
            'stock.required_if'            => 'Stok wajib diisi.',

            'images.*.image'        => 'File yang diupload harus berupa gambar.',
            'images.*.max'          => 'Ukuran tiap foto maksimal 5MB.',
        ]);

        DB::transaction(function () use ($validated, $request, $product, $category) {
            // 1. Update data dasar produk
            $product->update([
                'name'         => $validated['name'],
                'price'        => $validated['price'],
                'weight'       => $validated['weight'],
                'description'  => $validated['description'] ?? null,
                'stock'        => $category === 'accessories' ? $validated['stock'] : $product->stock,
                'published_at' => $validated['release_mode'] === 'scheduled'
                    ? $validated['published_at']
                    : now(),
            ]);

            // 2. Update detail sesuai kategori
            if ($category === 'clothes') {
                $product->clothes->update([
                    'color'    => $validated['color'],
                    'material' => $validated['material'],
                ]);

                $oldVariantIds = $product->variants()->pluck('id_variant');
                CartItem::whereIn('variant_id', $oldVariantIds)->delete();
                $product->variants()->delete();
                foreach ($validated['variants'] as $variant) {
                    ProductVariant::create([
                        'product_id' => $product->id_product,
                        'label'      => $variant['size'],
                        'stock'      => $variant['stock'],
                    ]);
                }
            } else {
                $product->accessories->update([
                    'type' => $validated['accessory_type'],
                ]);
            }

            // 3. Hapus foto yang ditandai untuk dihapus
            if (!empty($validated['delete_images'])) {
                $imagesToDelete = ProductImage::whereIn('id_product_image', $validated['delete_images'])
                    ->where('product_id', $product->id_product)
                    ->get();
                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // 4. Kalau ada foto baru diupload, tambahkan
            if ($request->hasFile('images')) {
                $startOrder = ($product->images()->max('sort_order') ?? -1) + 1;
                foreach ($request->file('images') as $index => $file) {
                    $filename = Str::uuid() . '.webp';
                    $folder   = 'products/' . $category;
                    Storage::disk('public')->makeDirectory($folder);
                    $encoded = Image::decode($file)->encode(new WebpEncoder(quality: 80));
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $encoded);
                    ProductImage::create([
                        'product_id' => $product->id_product,
                        'image_path' => "{$folder}/{$filename}",
                        'sort_order' => $startOrder + $index,
                    ]);
                }
            }

            // 5. Hitung ulang status berdasarkan stok terbaru
            $product->load('variants');
            $product->syncActiveStatus();
        });

        return redirect()
            ->route('dashboard.produk')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function show(string $slug)
    {
        $product = product::where('slug', $slug)
            ->with(['images', 'clothes', 'accessories', 'variants'])
            ->firstOrFail();

        // Produk yang masih dijadwalkan (belum waktunya tayang) gak boleh
        // diakses langsung lewat URL walau slug-nya udah ketebak
        if ($product->is_scheduled) {
            abort(404);
        }

        return view('pages.product_detail', compact('product'));
    }
}