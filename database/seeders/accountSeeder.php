<?php

namespace Database\Seeders;

use App\Models\account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class accountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        account::create([
            'username' => 'owner.mavnus',
            'name' => 'Owner',
            'password' => Hash::make('manage@mavnus'),
            'role' => 'owner',
            'is_active' => true,
        ]);
        account::create([
            'username' => 'admin.mavnus',
            'name' => 'Admin',
            'password' => Hash::make('manage@mavnus'),
            'role' => 'admin_produk',
            'is_active' => true,
        ]);
        account::create([
            'username' => 'staff.mavnus',
            'name' => 'Staff',
            'password' => Hash::make('manage@mavnus'),
            'role' => 'staff_pesanan',
            'is_active' => true,
        ]);
    }
}