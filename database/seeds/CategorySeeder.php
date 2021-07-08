<?php

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ar_cats = ['قسم1', 'قسم2'];
        $en_cats = ['cat1', 'cat2'];

        foreach($en_cats as $index => $cat){
            Category::create([
                'en' => [
                    'name' => $cat
                ],
                'ar' => [
                    'name' => $ar_cats[$index]
                ]
                ]);

        } // end of foreach

    }
}
