<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Models\clothes;
use App\Models\ClothesVariant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;


class clothesController extends Controller
{
    //
    public function clothes()
    {
        return view('pages.dashboard.clothes');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'price'                => 'required|integer|min:0',
            'description'          => 'nullable|string',
            'color'                => 'required|string|max:100',
            'material'             => 'required|string|max:100',

            'variants'             => 'required|array|min:1',
            'variants.*.size'      => 'required|in:S,M,L,XL',
            'variants.*.stock'     => 'required|integer|min:0',

            'images'               => 'nullable|array',
            'images.*'             => 'image|max:5120',
        ], [
            'name.required'         => 'Nama produk wajib diisi.',
            'name.max'              => 'Nama produk maksimal 255 karakter.',

            'price.required'        => 'Harga wajib diisi.',
            'price.integer'         => 'Harga harus berupa angka.',
            'price.min'             => 'Harga tidak boleh kurang dari 0.',

            'color.required'        => 'Warna wajib diisi.',
            'color.max'             => 'Warna maksimal 100 karakter.',

            'material.required'     => 'Material wajib diisi.',
            'material.max'          => 'Material maksimal 100 karakter.',

            'variants.required'     => 'Minimal isi satu ukuran & stok.',
            'variants.array'        => 'Format ukuran & stok tidak valid.',
            'variants.min'          => 'Minimal isi satu ukuran & stok.',

            'variants.*.size.required' => 'Ukuran wajib dipilih.',
            'variants.*.size.in'       => 'Ukuran harus salah satu dari S, M, L, atau XL.',

            'variants.*.stock.required' => 'Stok wajib diisi.',
            'variants.*.stock.integer'  => 'Stok harus berupa angka.',
            'variants.*.stock.min'      => 'Stok tidak boleh kurang dari 0.',

            'images.array'          => 'Format foto tidak valid.',
            'images.*.image'        => 'File yang diupload harus berupa gambar.',
            'images.*.max'          => 'Ukuran tiap foto maksimal 5MB.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            // 1. Simpan data dasar produk ke tabel products
            $product = product::create([
                'category'    => 'clothes',
                'name'        => $validated['name'],
                'slug'        => Str::slug($validated['name']) . '-' . uniqid(), // slug unik biar gak bentrok antar produk
                'price'       => $validated['price'],
                'description' => $validated['description'] ?? null,
                'is_active'   => true, // sementara, akan disesuaikan di bawah
            ]);

            // 2. Simpan detail warna & material, terhubung ke product di atas
            $clothes = clothes::create([
                'product_id' => $product->id_product,
                'color'      => $validated['color'],
                'material'   => $validated['material'],
            ]);

            // 3. Simpan tiap baris ukuran & stok yang diinput staff
            foreach ($validated['variants'] as $variant) {
                ClothesVariant::create([
                    'clothes_id' => $clothes->id_clothes,
                    'size'       => $variant['size'],
                    'stock'      => $variant['stock'],
                ]);
            }

            // 4. Konversi & simpan tiap foto yang diupload sebagai WebP
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $filename = Str::uuid() . '.webp';
                    $folder   = 'products/clothes';
                    Storage::disk('public')->makeDirectory($folder); // pastikan folder tujuan ada
                    $encoded = Image::decode($file)->encode(new WebpEncoder(quality: 80));
                    Storage::disk('public')->put("{$folder}/{$filename}", (string) $encoded);
                    ProductImage::create([
                        'product_id' => $product->id_product,
                        'image_path' => "{$folder}/{$filename}",
                        'sort_order' => $index, // urutan sesuai file yang dipilih staff
                    ]);
                }
            }

            // 5. Hitung ulang status aktif/sold-out berdasarkan total stok yang baru diinput
            $product->load('clothes.variants');
            $product->syncActiveStatus();
        });

        return redirect()
            ->route('dashboard.clothes')
            ->with('success', 'Produk clothes berhasil ditambahkan.');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            // hapus file fisik tiap foto dari storage
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }
            // hapus produk — clothes, variants, dan product_images
            // ikut terhapus otomatis lewat cascadeOnDelete() di migration
            $product->delete();
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Produk berhasil dihapus.');
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'price'                => 'required|integer|min:0',
            'description'          => 'nullable|string',
            'color'                => 'required|string|max:100',
            'material'             => 'required|string|max:100',

            'variants'             => 'required|array|min:1',
            'variants.*.size'      => 'required|in:S,M,L,XL',
            'variants.*.stock'     => 'required|integer|min:0',

            'images'               => 'nullable|array',
            'images.*'             => 'image|max:5120',
        ], [
            'name.required'         => 'Nama produk wajib diisi.',
            'price.required'        => 'Harga wajib diisi.',
            'price.integer'         => 'Harga harus berupa angka.',
            'color.required'        => 'Warna wajib diisi.',
            'material.required'     => 'Material wajib diisi.',
            'variants.required'     => 'Minimal isi satu ukuran & stok.',
            'variants.*.size.required' => 'Ukuran wajib dipilih.',
            'variants.*.size.in'       => 'Ukuran harus salah satu dari S, M, L, atau XL.',
            'variants.*.stock.required' => 'Stok wajib diisi.',
            'images.*.image'        => 'File yang diupload harus berupa gambar.',
            'images.*.max'          => 'Ukuran tiap foto maksimal 5MB.',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {

            // 1. Update data dasar produk
            $product->update([
                'name'        => $validated['name'],
                'price'       => $validated['price'],
                'description' => $validated['description'] ?? null,
            ]);

            // 2. Update detail warna & material
            $product->clothes->update([
                'color'    => $validated['color'],
                'material' => $validated['material'],
            ]);

            // 3. Ganti semua variant lama dengan yang baru
            //    (lebih aman daripada update satu-satu, karena staff bisa saja
            //    menghapus/menambah baris size di form)
            $product->clothes->variants()->delete();

            foreach ($validated['variants'] as $variant) {
                ClothesVariant::create([
                    'clothes_id' => $product->clothes->id_clothes,
                    'size'       => $variant['size'],
                    'stock'      => $variant['stock'],
                ]);
            }

            // 4. Kalau ada foto baru diupload, tambahkan (foto lama tetap ada)
            if ($request->hasFile('images')) {
                $startOrder = $product->images()->max('sort_order') + 1;

                foreach ($request->file('images') as $index => $file) {
                    $filename = Str::uuid() . '.webp';
                    $folder   = 'products/clothes';

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
            $product->load('clothes.variants');
            $product->syncActiveStatus();
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function show(string $slug)
    {
        // Assume your table column for the slug is `slug`
        $product = Product::where('slug', $slug)
            ->with(['images', 'clothes.variants'])
            ->firstOrFail();

        return view('pages.product_detail', compact('product'));
    }
}