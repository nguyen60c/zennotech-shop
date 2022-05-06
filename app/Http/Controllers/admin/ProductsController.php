<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class ProductsController extends Controller
{
    /*Admin fields*/

    /*
     * Display a listing of products data
     */
    public function index()
    {

        abort_if(Gate::denies('admin.products.index'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');


        $products = Product::latest()->paginate(8);

        return view("admin.products.index", compact("products"));
    }

    /*
     * Show the form for creating a new resource
     */
    public function create()
    {
        abort_if(Gate::denies('admin.products.create'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.products.create');
    }

    /*
     * Handle creating product
     */
    public function store(Request $request)
    {

        abort_if(Gate::denies('admin.products.store'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validation = $request->validate([
            "name" => "required|min:3",
            "description" => "required|min:3",
            "details" => "required|min:3",
            "quantity" => "required|numeric|gt:0",
            "price" => "required|numeric|gt:0",
            "image" => "required|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);

        $input = $validation;

        $is_existed = Product::where("name", $input["name"])->get()->toArray()[0];

        if (count($is_existed) == 0) {
            if ($image = $request->file('image')) {
                $destinationPath = 'images/products/';
                $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
                $image->move($destinationPath, $profileImage);
                $input['image'] = "$profileImage";
            }

            $product = new Product();
            $product->name = $input["name"];
            $product->description = $input["description"];
            $product->details = $input["details"];
            $product->quantity = $input["quantity"];
            $product->price = $input["price"];
            $product->image = $input["image"];
            $product->save();

            return redirect()->route('admin.products.index')
                ->withSuccess(__('Product created successfully.'));
        }

        return redirect()->route('admin.products.index')
            ->withErrors(__('Product has been created before.'));
    }

    /*
     * Display the specified product
     */
    public function show(Product $product)
    {
        abort_if(Gate::denies('admin.products.show'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view("admin.products.show"
            , compact("product"));
    }

    /*
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        abort_if(Gate::denies('admin.products.edit'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view("admin.products.edit",
            compact("product"));
    }

    /*
     * Update the specified product
     */
    public function update(Request $request)
    {

        abort_if(Gate::denies('admin.products.update'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $validation = $request->validate([
            "name" => "required|min:3",
            "description" => "required|min:3",
            "details" => "required|min:3",
            "quantity" => "required|numeric|gt:0",
            "price" => "required|numeric|gt:0",
            'image' => ''
        ]);


        $input = $validation;

//        ddd($input);

        $product = Product::where("name", $input["name"])->first();

        if ($image = $request->file('image')) {

            ddd("hello");
            $destinationPath = 'images/products/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = "$profileImage";

            $product->name = $input["name"];
            $product->description = $input["description"];
            $product->details = $input["details"];
            $product->quantity = $input["quantity"];
            $product->price = $input["price"];
            $product->image = $input["image"];
            $product->save();
            return redirect()->route('admin.products.index')
                ->withSuccess(__('Product updated successfully.'));
        }
        ddd("hello2");

        $product->name = $input["name"];
        $product->description = $input["description"];
        $product->details = $input["details"];
        $product->quantity = $input["quantity"];
        $product->price = $input["price"];
        $product->save();


        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product updated successfully.'));
    }

    /*
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        abort_if(Gate::denies('admin.products.destroy'),
            Response::HTTP_FORBIDDEN, '403 Forbidden');

        $product->delete();

        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product deleted successfully.'));
    }
}
