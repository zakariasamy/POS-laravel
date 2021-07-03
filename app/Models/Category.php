<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;

class Category extends Model
{
    use Translatable;
    protected $translatedAttributes = ['name'];
    protected $guarded = [];

    public function products(){

        return $this->hasMany(Product::class);

    }
}
