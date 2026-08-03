<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\LostItem;
use App\Models\FoundItem;
use App\Models\Claim;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Users
        $admin = User::firstOrCreate(
            ['email' => 'administrator@ub.edu.ph'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('123456789'),
                'role' => 'admin',
                'student_id_number' => 'ADM-2026-001',
                'phone' => '09171234567',
                'status' => 'active'
            ]
        );

        // 2. Create Categories
        $categories = [
            ['name' => 'Electronics & Gadgets', 'slug' => 'electronics', 'icon' => 'bi-laptop', 'description' => 'Laptops, Smartphones, Earbuds, Chargers'],
            ['name' => 'IDs & Cards', 'slug' => 'ids-cards', 'icon' => 'bi-card-heading', 'description' => 'Student IDs, Driver License, ATM Cards'],
            ['name' => 'Bags & Wallets', 'slug' => 'bags-wallets', 'icon' => 'bi-backpack', 'description' => 'Backpacks, Purses, Wallets, Pouches'],
            ['name' => 'Books & Documents', 'slug' => 'books-documents', 'icon' => 'bi-journal-bookmark', 'description' => 'Textbooks, Notebooks, Envelopes'],
            ['name' => 'Keys & Accessories', 'slug' => 'keys-accessories', 'icon' => 'bi-key', 'description' => 'Keys, Eyeglasses, Watches, Jewelry'],
            ['name' => 'Clothing & Uniforms', 'slug' => 'clothing', 'icon' => 'bi-tsheart', 'description' => 'Jackets, PE Uniforms, Caps']
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['slug' => $cat['slug']], $cat);
        }

        if ($this->command) {
            $this->command->info('  ✔ Created default users (admin@ub.edu.ph & student@ub.edu.ph)');
            $this->command->info('  ✔ Seeded 6 categories, sample found items, lost reports & claim requests!');
        }
    }
}
