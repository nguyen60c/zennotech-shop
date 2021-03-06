<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "products";

    protected $fillable = [
        "name",
        "description",
        "details",
        "quantity",
        "price",
        "image"
    ];

    public function cart()
    {
        return $this->belongsTo(Cart::class,"cart_product",
            "cart_id","product_id");
    }

//    public function user(){
//        return $this->belongsToMany(User::class);
//    }
}
