<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /*Show 15 products each time in DB*/
    public function index()
    {
        return Product::all();
    }

    /*Create new product*/
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "slug" => "required",
            "details" => "required",
            "price" => "required",
            "quantity" => "required",
            "description" => "required",
            "image_path" => "required"
        ]);

        return Product::create($request->all());
    }

    /*show specified product by id*/
    public function show($id)
    {
        return Product::find($id);
    }

    /*Update specified product by id*/
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return $product;
    }

    /*Delete specified product by id*/
    public function destroy($id)
    {
        $product = Product::find($id);
        return $product->delete();
    }

    /*Search name products*/
    public function search($name)
    {
        return Product::where("name", "like", "%" . $name . "%")->get();
    }
}
