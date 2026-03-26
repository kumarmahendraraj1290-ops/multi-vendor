<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Admin
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Customer
        User::factory()->create([
            'name' => 'John Customer',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        // Vendor 1
        $vendor1 = Vendor::create([
            'name' => 'TechGadgets',
            'email' => 'tech@vendor.com',
            'description' => 'Electronics and gadgets',
        ]);

        Product::insert([
            ['vendor_id' => $vendor1->id, 'name' => 'Wireless Mouse', 'price' => 29.99, 'stock' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor1->id, 'name' => 'Mechanical Keyboard', 'price' => 89.99, 'stock' => 30, 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor1->id, 'name' => 'USB-C Hub', 'price' => 45.00, 'stock' => 40, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Vendor 2
        $vendor2 = Vendor::create([
            'name' => 'BookHaven',
            'email' => 'books@vendor.com',
            'description' => 'Books and stationery',
        ]);

        Product::insert([
            ['vendor_id' => $vendor2->id, 'name' => 'Laravel Up & Running', 'price' => 39.99, 'stock' => 25, 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor2->id, 'name' => 'Clean Code', 'price' => 34.50, 'stock' => 20, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Vendor 3
        $vendor3 = Vendor::create([
            'name' => 'FitLife',
            'email' => 'fit@vendor.com',
            'description' => 'Fitness and wellness products',
        ]);

        Product::insert([
            ['vendor_id' => $vendor3->id, 'name' => 'Yoga Mat', 'price' => 24.99, 'stock' => 60, 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor3->id, 'name' => 'Resistance Bands Set', 'price' => 19.99, 'stock' => 45, 'created_at' => now(), 'updated_at' => now()],
            ['vendor_id' => $vendor3->id, 'name' => 'Water Bottle 1L', 'price' => 14.99, 'stock' => 100, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
