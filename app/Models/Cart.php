<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = "carts";

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function cart(){
        return $this->hasMany(Product::class,"product_id","id");
    }

    public function totalItems(){
        return Cart::all();
    }

    public function totalDistinctItems(){
        return Cart::select("product_id")->distinct()->get();
    }

}

