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

    /**
     * @return int|null
     */
    public function getUserId()
    {
        $userId = isset(auth()->user()->id) ? auth()->user()->id : 0;
        return $userId;
    }

    /**
     * @return Cart[]|\Illuminate\Database\Eloquent\Collection
     */
    public function totalItems()
    {
        return Cart::all();
    }

    /**
     * @return int
     */
    public function totalDistinctItems()
    {
        if ($this->getUserId()){
            return Cart::select("product_id")
                ->where('user_id', $this->getUserId())->distinct()->get();
        }
        return 0;
    }
}

