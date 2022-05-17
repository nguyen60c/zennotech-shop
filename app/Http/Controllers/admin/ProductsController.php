<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
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

        if(auth()->user()->getRoleNames()[0] == "admin"){

            $products = Product::latest()->paginate(8);

            foreach ($products as $key => $item) {

                $user = User::where("id", $item["creator_id"])->get()->toArray()[0];

                $products[$key]->creator_name = $user["name"];

            }

        }else{
            $products = Product::where("creator_id", auth()->user()->id)->latest()->paginate(8);

            foreach ($products as $key => $item) {

                $user = User::where("id", $item["creator_id"])->get()->toArray()[0];

                $products[$key]->creator_name = $user["name"];
            }
        }

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
            "image" => "image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);

        $data_input = $validation;

        $is_existed = Product::where("name", $data_input["name"])->get()->toArray();

        if (count($is_existed) == 0) {
            $this->isExistProductInDB($request, $is_existed, $data_input);

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

        if($product["creator_id"] !== auth()->user()->id &&
            auth()->user()->getRoleNames()[0] != "admin"){
            abort(403, 'Unauthorized action.');
        }

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

        if($product["creator_id"] !== auth()->user()->id &&
            auth()->user()->getRoleNames()[0] != "admin"){
            abort(403, 'Unauthorized action.');
        }

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
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $input = $validation;

        $product = Product::where("name", $input["name"])->first();

        if ($request->hasFile('image')) {

            $image = $request->file("image");
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

        if($product["creator_id"] !== auth()->user()->id &&
            auth()->user()->getRoleNames()[0] != "admin"){
            abort(403, 'Unauthorized action.');
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->withSuccess(__('Product deleted successfully.'));
    }


    /*
     * Check is product existed or not
     * */
    public function isExistProductInDB($request, $is_existed, $input)
    {

            if ($image = $request->file('image')) {

                $input_image = $this->isExistImageRequest($image);

            }

            $input = array_merge($input, ["image" => $input_image]);

            $this->createNewProduct($input);

    }

    /*
     * Check coming request image
     *
     * @return name of image
     */
    public function isExistImageRequest($image)
    {

            $destinationPath = 'images/products/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $input['image'] = $profileImage;
            return $input['image'];
    }

    /*
     * Create a new product in db
     * @param $input
     */
    public function createNewProduct($input)
    {
        $product = new Product();
        $product->name = $input["name"];
        $product->description = $input["description"];
        $product->details = $input["details"];
        $product->quantity = $input["quantity"];
        $product->price = $input["price"];
        $product->image = $input["image"];
        $product->creator_id = auth()->user()->id;
        $product->save();
    }
}
