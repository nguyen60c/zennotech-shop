<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class CreateProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Product::create([
            'name' => 'MacBook Pro',
            'details' => '15 inch, 1TB HDD, 32GB RAM',
            'quantity' => '120',
            'price' => 2499.99,
            'description' => 'MackBook Pro',
            'image' => 'macbook-pro.png',
            "creator_id" => 3
        ]);
        Product::create([
            'name' => 'Dell Vostro 3557',
            'details' => '15 inch, 1TB HDD, 8GB RAM',
            'quantity' => '200',
            'price' => 1499.99,
            'description' => 'Dell Vostro 3557',
            'image' => 'dell-v3557.png',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'iPhone 11 Pro',
            'details' => '6.1 inch, 64GB 4GB RAM',
            'quantity' => '320',
            'price' => 649.99,
            'description' => 'iPhone 11 Pro',
            'image' => 'iphone-11-pro.png',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Remax 610D Headset',
            'details' => '6.1 inch, 64GB 4GB RAM',
            'quantity' => '110',
            'price' => 8.99,
            'description' => 'Remax 610D Headset',
            'image' => 'remax-610d.jpg',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Samsung LED TV',
            'details' => '24 inch, LED Display, Resolution 1366 x 768',
            'quantity' => '21',
            'price' => 41.99,
            'description' => 'Samsung LED TV',
            'image' => 'samsung-led-24.png',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Samsung Digital Camera',
            'details' => '16.1MP, 5x Optical Zoom',
            'quantity' => '32',
            'price' => 144.99,
            'description' => 'Samsung Digital Camera',
            'image' => 'samsung-mv800.jpg',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Huawei GR 5 2017',
            'details' => '5.5 inch, 32GB 4GB RAM',
            'quantity' => '12',
            'price' => 148.99,
            'description' => 'Huawei GR 5 2017',
            'image' => 'gr5-2017.jpg',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Iphone 5s',
            'details' => '5.5 inch, 32GB 2GB RAM',
            'quantity' => '111',
            'price' => 142.99,
            'description' => 'Iphone 5s',
            'image' => 'iphone-5.jpg',
            "creator_id" => 3
        ]);

        Product::create([
            'name' => 'Iphone 6s',
            'details' => '5.5 inch, 32GB 4GB RAM',
            'quantity' => '112',
            'price' => 200.99,
            'description' => 'Iphone 6s',
            'image' => 'iphone-6.jpg',
            "creator_id" => 3
        ]);
    }
}
