<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Aksesoris Craft (Category 1)
            [
                'category_id' => 1,
                'name' => 'Manik-manik Warna-warni',
                'description' => 'Manik-manik dengan berbagai ukuran dan warna untuk keperluan craft Anda.',
                'price' => 25000,
                'stock' => 50,
                'image' => 'images/products/beads.jpg',
            ],
            [
                'category_id' => 1,
                'name' => 'Pita Satin Merah',
                'description' => 'Pita satin berkualitas tinggi dengan warna merah yang cerah, sempurna untuk dekorasi dan pembungkus kado.',
                'price' => 15000,
                'stock' => 100,
                'image' => 'images/products/red-ribbon.jpg',
            ],
            [
                'category_id' => 1,
                'name' => 'Glitter Craft Set',
                'description' => 'Set glitter dengan 12 warna berbeda untuk menambah kilauan pada proyek craft Anda.',
                'price' => 35000,
                'stock' => 30,
                'image' => 'images/products/glitter.jpg',
            ],
            [
                'category_id' => 1,
                'name' => 'Kancing Dekoratif',
                'description' => 'Kancing dengan berbagai bentuk dan warna untuk mempercantik hasil kerajinan Anda.',
                'price' => 20000,
                'stock' => 80,
                'image' => 'images/products/buttons.jpg',
            ],
            
            // Bahan Buket (Category 2)
            [
                'category_id' => 2,
                'name' => 'Bunga Flanel Premium',
                'description' => 'Bunga flanel berkualitas tinggi tersedia dalam berbagai warna, sempurna untuk buket dan dekorasi.',
                'price' => 45000,
                'stock' => 40,
                'image' => 'images/products/flannel-flowers.jpg',
            ],
            [
                'category_id' => 2,
                'name' => 'Kertas Krep Buket',
                'description' => 'Kertas krep khusus untuk membuat buket dengan tekstur yang lembut dan tahan lama.',
                'price' => 18000,
                'stock' => 60,
                'image' => 'images/products/crepe-paper.jpg',
            ],
            [
                'category_id' => 2,
                'name' => 'Kawat Buket Set',
                'description' => 'Set kawat dengan berbagai ukuran yang mudah dibentuk untuk kerangka buket Anda.',
                'price' => 30000,
                'stock' => 45,
                'image' => 'images/products/bouquet-wires.jpg',
            ],
            [
                'category_id' => 2,
                'name' => 'Bubble Wrap Buket',
                'description' => 'Bubble wrap khusus untuk pembungkus buket yang aman dan menarik.',
                'price' => 22000,
                'stock' => 70,
                'image' => 'images/products/bubble-wrap.jpg',
            ],
            
            // Kertas (Category 3)
            [
                'category_id' => 3,
                'name' => 'Kertas Scrapbook Vintage',
                'description' => 'Set kertas scrapbook dengan motif vintage, sempurna untuk proyek scrapbooking dan craft.',
                'price' => 55000,
                'stock' => 25,
                'image' => 'images/products/vintage-paper.jpg',
            ],
            [
                'category_id' => 3,
                'name' => 'Kertas Origami Premium',
                'description' => 'Kertas origami berkualitas tinggi dengan 100 lembar dan berbagai warna cerah.',
                'price' => 28000,
                'stock' => 60,
                'image' => 'images/products/origami-paper.jpg',
            ],
            [
                'category_id' => 3,
                'name' => 'Kertas Marmer',
                'description' => 'Kertas dengan motif marmer elegan untuk kartu ucapan dan scrapbooking.',
                'price' => 40000,
                'stock' => 35,
                'image' => 'images/products/marble-paper.jpg',
            ],
            [
                'category_id' => 3,
                'name' => 'Kertas Kraft Tebal',
                'description' => 'Kertas kraft berkualitas tinggi untuk kebutuhan packaging dan craft Anda.',
                'price' => 32000,
                'stock' => 50,
                'image' => 'images/products/kraft-paper.jpg',
            ],
            
            // Paper Bag (Category 4)
            [
                'category_id' => 4,
                'name' => 'Paper Bag Kraft Premium',
                'description' => 'Paper bag kraft berkualitas tinggi dengan pegangan tali yang kuat.',
                'price' => 8000,
                'stock' => 100,
                'image' => 'images/products/kraft-bag.jpg',
            ],
            [
                'category_id' => 4,
                'name' => 'Paper Bag Motif Bunga',
                'description' => 'Paper bag dengan motif bunga yang cantik untuk kado dan packaging.',
                'price' => 12000,
                'stock' => 80,
                'image' => 'images/products/flower-bag.jpg',
            ],
            [
                'category_id' => 4,
                'name' => 'Paper Bag Mini Set',
                'description' => 'Set paper bag mini untuk souvenir dan hadiah kecil dengan 10 macam warna.',
                'price' => 25000,
                'stock' => 60,
                'image' => 'images/products/mini-bags.jpg',
            ],
            [
                'category_id' => 4,
                'name' => 'Paper Bag Luxury',
                'description' => 'Paper bag premium dengan finishing glossy untuk hadiah spesial.',
                'price' => 18000,
                'stock' => 40,
                'image' => 'images/products/luxury-bag.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'category_id' => $product['category_id'],
                'name' => $product['name'],
                'slug' => Str::slug($product['name']),
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => $product['image'],
                'rating' => rand(30, 50) / 10,
            ]);
        }
    }
}