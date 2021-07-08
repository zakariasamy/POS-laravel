<?php

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ar_titles = ['منتج 1', 'منتج 2'];

        $en_titles = ['prod 1', 'prod 2'];


        foreach($en_titles as $index => $en_title){
            Product::create([
                'category_id' => 1,
                'purchase_price' => 500,
                'sale_price' => 600,
                'stock' => 5,
                'en' => [
                    'name' => $en_title,
                    'description' => 'desc of ' . $en_title
                ],
                'ar' => [
                    'name' => $ar_titles[$index],
                    'description' => 'وصف ' . $ar_titles[$index]
                ]
                ]);

        } // end of foreach
    }
}
