<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Onigiri',
            'email' => 'admin@onigiri.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'phone' => '08123456789',
        ]);

        // Customer
        User::create([
            'name' => 'Pelanggan Setia',
            'email' => 'customer@onigiri.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'phone' => '08987654321',
            'address' => 'Jl. Contoh No. 123, Jakarta',
        ]);

        // Kurir
        User::create([
            'name' => 'Kurir Express',
            'email' => 'kurir@onigiri.com',
            'password' => bcrypt('password'),
            'role' => 'courier',
            'phone' => '08123456789',
            'photo' => 'couriers/wy.png',
        ]);

        // Products
        $products = [
            [
                'name' => 'Onigiri Tuna Mayo',
                'slug' => 'onigiri-tuna-mayo',
                'description' => 'Nasi kepal dengan isian tuna mayo yang lezat dan creamy. Dibungkus nori crispy yang renyah.',
                'price' => 12000,
                'stock' => 50,
                'category' => 'classic',
                'image' => 'products/tuna_mayo.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Hot Tuna',
                'slug' => 'onigiri-hot-tuna',
                'description' => 'Nasi kepal premium dengan isian salmon panggang yang kaya omega-3. Pilihan sehat dan lezat.',
                'price' => 12000,
                'stock' => 50,
                'category' => 'classic',
                'image' => 'products/hot_tuna.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Beef Rendang',
                'slug' => 'onigiri-beef-rendang',
                'description' => 'Nasi kepal dengan ayam teriyaki manis dan gurih. favorit anak-anak dan keluarga.',
                'price' => 12000,
                'stock' => 50,
                'category' => 'classic',
                'image' => 'products/hot_tuna.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Chicken Mayo',
                'slug' => 'onigiri-chicken-mayo',
                'description' => 'Nasi kepal dengan udang segar yang dimasak dengan bumbu spesial khas Jepang.',
                'price' => 12000,
                'stock' => 50,
                'category' => 'classic',
                'image' => 'products/chicken_mayo.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Hot Chicken',
                'slug' => 'onigiri-hot-chicken',
                'description' => 'Onigiri klasik Jepang dengan isian umeboshi (plum asin). Rasanya asam segar dan autentik.',
                'price' => 12000,
                'stock' => 50,
                'category' => 'classic',
                'image' => 'products/hot_chicken.jpg',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Shirasu Teri',
                'slug' => 'onigiri-shirasu-teri',
                'description' => 'Nasi kepal dengan daging sapi yakiniku yang juicy dan penuh cita rasa BBQ.',
                'price' => 15000,
                'stock' => 50,
                'category' => 'premium',
                'image' => 'products/shirasu_teri.jpg',
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
