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
            ['email' => 'admin@ub.edu.ph'],
            [
                'name' => 'SAO Administrator',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'student_id_number' => 'ADM-2026-001',
                'phone' => '09171234567',
                'status' => 'active'
            ]
        );

        $student = User::firstOrCreate(
            ['email' => 'student@ub.edu.ph'],
            [
                'name' => 'Decsten Matibag',
                'password' => Hash::make('password123'),
                'role' => 'student',
                'student_id_number' => 'UB-2024-8812',
                'phone' => '09189876543',
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

        $elec = Category::where('slug', 'electronics')->first();
        $ids = Category::where('slug', 'ids-cards')->first();
        $bags = Category::where('slug', 'bags-wallets')->first();

        // 3. Create Sample Found Items
        $found1 = FoundItem::firstOrCreate(
            ['title' => 'Black Sony Noise Canceling Headphones'],
            [
                'user_id' => $admin->id,
                'category_id' => $elec->id,
                'description' => 'Black wireless over-ear headphones found on library desk 3rd floor near window.',
                'date_found' => now()->subDays(1),
                'location' => 'Main Library 3rd Floor',
                'storage_location' => 'SAO Office Cabinet B1',
                'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'status' => 'claim_pending'
            ]
        );

        $found2 = FoundItem::firstOrCreate(
            ['title' => 'Brown Leather Wallet'],
            [
                'user_id' => $admin->id,
                'category_id' => $bags->id,
                'description' => 'Contains driver license and cash. Found near Student Center cafeteria.',
                'date_found' => now()->subDays(2),
                'location' => 'Student Center Cafeteria',
                'storage_location' => 'SAO Office Safe #2',
                'image_path' => 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&q=80',
                'status' => 'available'
            ]
        );

        $found3 = FoundItem::firstOrCreate(
            ['title' => 'UB Student ID Card (Maria Santos)'],
            [
                'user_id' => $admin->id,
                'category_id' => $ids->id,
                'description' => 'BS Computer Science student ID found near Gymnasium entrance.',
                'date_found' => now()->subHours(5),
                'location' => 'UB Gymnasium Entrance',
                'storage_location' => 'SAO Front Desk Drawer A',
                'image_path' => 'https://images.unsplash.com/photo-1589829545856-d10d557cf95f?w=500&q=80',
                'status' => 'available'
            ]
        );

        // 4. Create Sample Lost Items
        $lost1 = LostItem::firstOrCreate(
            ['title' => 'Black Sony Over-Ear Headphones'],
            [
                'user_id' => $student->id,
                'category_id' => $elec->id,
                'description' => 'Black wireless headphones left in library desk 3rd floor. Has a small scratch on the right ear cup.',
                'date_lost' => now()->subDays(2),
                'location' => 'Main Library',
                'image_path' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'status' => 'claim_pending'
            ]
        );

        $lost2 = LostItem::firstOrCreate(
            ['title' => 'Scientific Calculator Casio fx-991EX'],
            [
                'user_id' => $student->id,
                'category_id' => $elec->id,
                'description' => 'Black and silver Casio calculator with name sticker "Decsten M." on back.',
                'date_lost' => now()->subDays(3),
                'location' => 'Science Bldg Room 302',
                'image_path' => null,
                'status' => 'open'
            ]
        );

        // 5. Create Sample Claim Request
        Claim::firstOrCreate(
            [
                'lost_item_id' => $lost1->id,
                'found_item_id' => $found1->id,
                'user_id' => $student->id
            ],
            [
                'proof_description' => 'I have the original Bluetooth pairing receipt and my name Decsten is registered in Sony Headphones App. The right cup has a tiny scratch.',
                'proof_image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
                'status' => 'pending',
                'admin_notes' => null,
                'verified_by' => null
            ]
        );

        if ($this->command) {
            $this->command->info('  ✔ Created default users (admin@ub.edu.ph & student@ub.edu.ph)');
            $this->command->info('  ✔ Seeded 6 categories, sample found items, lost reports & claim requests!');
        }
    }
}
