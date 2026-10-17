<?php

return [
    'required' => ':attribute wajib diisi.',
    'email' => 'Format :attribute tidak valid.',
    'string' => ':attribute harus berupa teks.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'numeric' => ':attribute harus berupa angka.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'unique' => ':attribute sudah dipakai.',
    'regex' => 'Format :attribute tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
    ],
    'attributes' => [
        'product_id' => 'produk',
        'variant_id' => 'varian',
        'quantity' => 'jumlah',
        'destination_id' => 'tujuan',
        'email' => 'email',
        'keyword' => 'kata kunci',
    ],
];