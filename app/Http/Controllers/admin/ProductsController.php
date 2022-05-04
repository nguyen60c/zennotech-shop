<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /*Admin fields*/

    /*
     * Display a listing of products data
     */
    public function index(){
        $products = Product::latest()->paginate(15);

        return view("admin.products.index",compact("products"));
    }

    /*
     * Show the form for creating a new resource
     */
    public function create(){
        return view('admin.products.create');
    }

    /*
     * Handle creating product
     */
    public function store(Request $request){

        Product::create(array_merge($request->only("name", "description", "details",
            "price", "quantity", "image"),[
            'user_id' => auth()->id()
        ]));

        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product created successfully.'));
    }

    /*
     * Display the specified product
     */
    public function show(Product $product){
        return view("admin.products.show"
            ,compact("product"));
    }

    /*
     * Show the form for editing the specified product
     */
    public function edit(Product $product){
        return view("admin.products.edit",
            compact("product"));
    }

    /*
     * Update the specified product
     */
    public function update(Request $request, Product $product){
        $product->update($request->only("name", "description", "details",
            "price", "quantity", "image"));

        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product updated successfully.'));
    }

    /*
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product deleted successfully.'));
    }


    /*User fields*/
    public function show_list_products(){
        $products = Product::latest()->paginate(15);

        return view("",compact("products"));
    }
}
