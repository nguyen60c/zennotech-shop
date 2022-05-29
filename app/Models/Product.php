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

    public function user(){
        return $this->belongsTo(User::class,'creator_id','id');
    }

}
