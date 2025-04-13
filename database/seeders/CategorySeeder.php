<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Aksesoris Craft',
                'image' => 'images/category-accessories.jpg'
            ],
            [
                'name' => 'Bahan Buket',
                'image' => 'images/category-bouquet.jpg'
            ],
            [
                'name' => 'Kertas',
                'image' => 'images/category-paper.jpg'
            ],
            [
                'name' => 'Paper Bag',
                'image' => 'images/category-paperbag.jpg'
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'image' => $category['image']
            ]);
        }
    }
}