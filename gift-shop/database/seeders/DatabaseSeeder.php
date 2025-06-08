<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create categories
        $categories = [
            [
                'name' => 'Mugs',
                'description' => 'Personalized mugs for every occasion',
                'is_active' => true,
            ],
            [
                'name' => 'T-Shirts',
                'description' => 'Custom printed t-shirts',
                'is_active' => true,
            ],
            [
                'name' => 'Puzzles',
                'description' => 'Custom photo puzzles',
                'is_active' => true,
            ],
            [
                'name' => 'Keychains',
                'description' => 'Personalized keychains',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create sample products
        $products = [
            [
                'category_id' => 1, // Mugs
                'name' => 'Classic White Mug',
                'description' => 'Personalize this classic white mug with your favorite photos and text.',
                'base_price' => 14.99,
                'stock' => 100,
                'is_active' => true,
            ],
            [
                'category_id' => 1,
                'name' => 'Magic Color Changing Mug',
                'description' => 'This mug reveals your custom design when filled with hot liquid.',
                'base_price' => 19.99,
                'stock' => 50,
                'is_active' => true,
            ],
            [
                'category_id' => 2, // T-Shirts
                'name' => 'Cotton T-Shirt',
                'description' => 'High-quality cotton t-shirt perfect for custom designs.',
                'base_price' => 24.99,
                'stock' => 200,
                'is_active' => true,
            ],
            [
                'category_id' => 3, // Puzzles
                'name' => 'Custom Photo Puzzle (500 pieces)',
                'description' => 'Turn your favorite photo into a 500-piece puzzle.',
                'base_price' => 29.99,
                'stock' => 75,
                'is_active' => true,
            ],
            [
                'category_id' => 4, // Keychains
                'name' => 'Acrylic Photo Keychain',
                'description' => 'Crystal clear acrylic keychain with your photo.',
                'base_price' => 9.99,
                'stock' => 150,
                'is_active' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
