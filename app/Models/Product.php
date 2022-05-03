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
        "quantity",
        "price",
        "image"
    ];

    public function cart()
    {
        return $this->belongsToMany(Cart::class);
    }

    public function order_details(){
        return $this->belongsToMany(Order_details::class);
    }
}
