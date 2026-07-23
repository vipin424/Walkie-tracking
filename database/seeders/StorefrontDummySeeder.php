<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Str;

class StorefrontDummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Broad Categories (For large cards - Image 1 style)
        $broadCategories = [
            [
                'name' => 'Photography',
                'description' => 'Rent GoPro, Insta360, DJI, DSLR Cameras & more',
                'badge_color' => '#ff5722', // Orange
                'image_path' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'name' => 'Gaming',
                'description' => 'Rent PS5, Xbox, Racing Wheel & more',
                'badge_color' => '#9c27b0', // Purple
                'image_path' => 'https://images.unsplash.com/photo-1606144042851-40ea9039ef8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'name' => 'Outdoor',
                'description' => 'Rent Trekking Jacket, Riding Boots, Camping Tents & more',
                'badge_color' => '#4caf50', // Green
                'image_path' => 'https://images.unsplash.com/photo-1504280387586-538be29d10e5?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
            [
                'name' => 'Entertainment',
                'description' => 'Rent Projectors, Speakers, VR, Mics & more',
                'badge_color' => '#e91e63', // Pink
                'image_path' => 'https://images.unsplash.com/photo-1544427920-c49ccf17e3f8?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80',
                'is_featured' => true,
            ],
        ];

        foreach ($broadCategories as $cat) {
            Category::firstOrCreate(['slug' => Str::slug($cat['name'])], $cat);
        }

        // 2. Specific Sub-Categories (For top carousel - Image 2 style)
        $subCategories = [
            ['name' => 'Walkie Talkies', 'image_path' => 'https://images.unsplash.com/photo-1533168285918-a61de3a8ee26?w=200&h=200&fit=crop'],
            ['name' => 'Action Cameras', 'image_path' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=200&h=200&fit=crop'],
            ['name' => 'Gaming Console', 'image_path' => 'https://images.unsplash.com/photo-1486401899868-0e435ed85128?w=200&h=200&fit=crop'],
            ['name' => 'Audio Visual', 'image_path' => 'https://images.unsplash.com/photo-1520625368383-207d5718df88?w=200&h=200&fit=crop'],
            ['name' => 'Camping Gear', 'image_path' => 'https://images.unsplash.com/photo-1523987355523-c7b5b0dd90a7?w=200&h=200&fit=crop'],
            ['name' => 'Projectors', 'image_path' => 'https://images.unsplash.com/photo-1574347713437-05c317ff2195?w=200&h=200&fit=crop'],
        ];

        foreach ($subCategories as $cat) {
            Category::firstOrCreate(['slug' => Str::slug($cat['name'])], [
                'name' => $cat['name'],
                'slug' => Str::slug($cat['name']),
                'image_path' => $cat['image_path'],
                'is_featured' => false
            ]);
        }

        // 3. Create Dummy Products mapped to Categories
        $walkieCat = Category::where('slug', 'walkie-talkies')->first();
        if ($walkieCat) {
            Item::firstOrCreate(['name' => 'Motorola Walkie Talkie Pro'], [
                'category_id' => $walkieCat->id,
                'slug' => 'motorola-walkie-talkie-pro',
                'description' => 'Long range 10km walkie talkie suitable for events.',
                'unit_price' => 300.00,
                'security_deposit' => 0.00,
                'total_stock' => 100,
                'image_path' => 'https://images.unsplash.com/photo-1533168285918-a61de3a8ee26?w=600&h=600&fit=crop',
                'is_active' => true
            ]);
        }

        $cameraCat = Category::where('slug', 'action-cameras')->first();
        if ($cameraCat) {
            Item::firstOrCreate(['name' => 'GoPro Hero 11 Black'], [
                'category_id' => $cameraCat->id,
                'slug' => 'gopro-hero-11',
                'description' => 'Capture your adventures in 5.3K.',
                'unit_price' => 500.00,
                'security_deposit' => 2000.00,
                'total_stock' => 10,
                'image_path' => 'https://images.unsplash.com/photo-1526170375885-4d8ecf77b99f?w=600&h=600&fit=crop',
                'is_active' => true
            ]);
        }

        $gamingCat = Category::where('slug', 'gaming-console')->first();
        if ($gamingCat) {
            Item::firstOrCreate(['name' => 'Sony PlayStation 5'], [
                'category_id' => $gamingCat->id,
                'slug' => 'sony-playstation-5',
                'description' => 'Next-gen gaming experience.',
                'unit_price' => 1200.00,
                'security_deposit' => 5000.00,
                'total_stock' => 5,
                'image_path' => 'https://images.unsplash.com/photo-1606144042851-40ea9039ef8b?w=600&h=600&fit=crop',
                'is_active' => true
            ]);
        }

        $audioCat = Category::where('slug', 'audio-visual')->first();
        if ($audioCat) {
            Item::firstOrCreate(['name' => 'JBL PartyBox 310'], [
                'category_id' => $audioCat->id,
                'slug' => 'jbl-partybox-310',
                'description' => 'Powerful portable speaker with light show.',
                'unit_price' => 1500.00,
                'security_deposit' => 3000.00,
                'total_stock' => 20,
                'image_path' => 'https://images.unsplash.com/photo-1520625368383-207d5718df88?w=600&h=600&fit=crop',
                'is_active' => true
            ]);
        }
    }
}
