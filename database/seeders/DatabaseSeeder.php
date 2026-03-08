<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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

        // Products
        $products = [
            [
                'name' => 'Onigiri Tuna Mayo',
                'slug' => 'onigiri-tuna-mayo',
                'description' => 'Nasi kepal dengan isian tuna mayo yang lezat dan creamy. Dibungkus nori crispy yang renyah.',
                'price' => 15000,
                'stock' => 50,
                'category' => 'classic',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Salmon',
                'slug' => 'onigiri-salmon',
                'description' => 'Nasi kepal premium dengan isian salmon panggang yang kaya omega-3. Pilihan sehat dan lezat.',
                'price' => 18000,
                'stock' => 40,
                'category' => 'premium',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Chicken Teriyaki',
                'slug' => 'onigiri-chicken-teriyaki',
                'description' => 'Nasi kepal dengan ayam teriyaki manis dan gurih. Favorit anak-anak dan keluarga.',
                'price' => 16000,
                'stock' => 60,
                'category' => 'classic',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Ebi (Udang)',
                'slug' => 'onigiri-ebi',
                'description' => 'Nasi kepal dengan udang segar yang dimasak dengan bumbu spesial khas Jepang.',
                'price' => 17000,
                'stock' => 35,
                'category' => 'premium',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Umeboshi',
                'slug' => 'onigiri-umeboshi',
                'description' => 'Onigiri klasik Jepang dengan isian umeboshi (plum asin). Rasanya asam segar dan autentik.',
                'price' => 14000,
                'stock' => 45,
                'category' => 'classic',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Beef Yakiniku',
                'slug' => 'onigiri-beef-yakiniku',
                'description' => 'Nasi kepal dengan daging sapi yakiniku yang juicy dan penuh cita rasa BBQ.',
                'price' => 22000,
                'stock' => 25,
                'category' => 'premium',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Cheese',
                'slug' => 'onigiri-cheese',
                'description' => 'Nasi kepal dengan keju mozzarella yang meleleh di dalamnya. Perpaduan sempurna Jepang dan Western.',
                'price' => 16000,
                'stock' => 30,
                'category' => 'fusion',
                'is_available' => true,
            ],
            [
                'name' => 'Onigiri Natto',
                'slug' => 'onigiri-natto',
                'description' => 'Onigiri autentik dengan fermented soybeans khas Jepang. Kaya protein dan probiotik.',
                'price' => 15000,
                'stock' => 20,
                'category' => 'classic',
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
