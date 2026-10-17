<?php

return [
    'required' => ':attribute is required.',
    'email' => ':attribute must be a valid email address.',
    'string' => ':attribute must be text.',
    'integer' => ':attribute must be a whole number.',
    'numeric' => ':attribute must be a number.',
    'confirmed' => ':attribute confirmation does not match.',
    'exists' => 'The selected :attribute is invalid.',
    'unique' => ':attribute has already been taken.',
    'regex' => 'The :attribute format is invalid.',
    'in' => 'The selected :attribute is invalid.',
    'min' => [
        'numeric' => ':attribute must be at least :min.',
        'string' => ':attribute must be at least :min characters.',
    ],
    'max' => [
        'numeric' => ':attribute may not be greater than :max.',
        'string' => ':attribute may not be greater than :max characters.',
    ],
    'attributes' => [
        'product_id' => 'product',
        'variant_id' => 'variant',
        'quantity' => 'quantity',
        'destination_id' => 'destination',
        'email' => 'email',
        'keyword' => 'keyword',
    ],
];