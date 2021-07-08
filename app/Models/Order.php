<?php

namespace App\Models;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];


    public function products(){

        return $this->belongsToMany(Product::class, 'product_order');

    }

    public function client(){

        return $this->belongsTo(Client::class);

    }
}
