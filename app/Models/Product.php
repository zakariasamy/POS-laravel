<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Product extends Model
{
    use Translatable;
    protected $translatedAttributes = ['name', 'description'];
    protected $guarded = [];
    protected $appends = ['images_path', 'profit_percent'];

    public function category(){

        return $this->belongsTo(Category::class);

    }

    public function getImagePathAttribute(){

        return asset('/assets/product_images/' . $this->image);

    }

    public function getProfitPercentAttribute($purchase_price){

        $profit =  $this->purchase_price - $this->sale_price;
        $percent = ( $profit / $this->purchase_price ) * 100;
        return  number_format($percent, 2); // if profit percent 2.22254 - it will be shown 2.22

    }


}
