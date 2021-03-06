<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Dashboard\ProductCreateRequest;
use App\Http\Requests\Dashboard\ProductUpdateRequest;

class ProductController extends Controller
{
    public function __construct()
    {
        // laratrust package
        $this->middleware(['permission:products-create'])->only('create');
        $this->middleware(['permission:products-read'])->only('index');
        $this->middleware(['permission:products-update'])->only('edit');
        $this->middleware(['permission:products-delete'])->only('destroy');
    }
    public function index(Request $request)
    {

        $categories = Category::all();


        $products = Product::when($request->category_id, function ($q) use ($request) {

            return $q->where('category_id', $request->category_id);
        })->when($request->search, function ($q) use ($request) {

            return $q->whereTranslationLike('name', '%' . $request->search . '%');
        })->latest()->paginate(5);

        return view('dashboard.products.index', compact('categories', 'products'));
    }


    public function create()
    {
        $categories = Category::all();
        return view('dashboard.products.create', compact('categories'));
    }


    public function store(ProductCreateRequest $request)
    {
        try {
            $request_data = $request->all();

            if ($request->image) {

                Image::make($request->image)
                    ->resize(300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                    ->save(public_path('assets/product_images/' . $request->image->hashName()));

                $request_data['image'] = $request->image->hashName();
            }

            Product::create($request_data);
            session()->flash('success', __('site.added_successfully'));
            return redirect()->route('dashboard.products.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.products.index');
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('dashboard.products.edit', compact('categories', 'product'));
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {

        $request_data = $request->all();

        if ($request->image) {

            if ($product->image != 'default.png') {

                Storage::disk('product_images')->delete('/' . $product->image);
            }

            Image::make($request->image)
                ->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                })
                ->save(public_path('assets/product_images/' . $request->image->hashName()));

            $request_data['image'] = $request->image->hashName();
        }

        $product->update($request_data);
        try {
            session()->flash('success', __('site.updated_successfully'));
            return redirect()->route('dashboard.products.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.products.index');
        }
    }

    public function destroy(Product $product)
    {
        try {
            if ($product->image != 'default.png') {

                Storage::disk('product_images')->delete('/' . $product->image);
            }

            $product->delete();
            session()->flash('success', __('site.deleted_successfully'));
            return redirect()->route('dashboard.products.index');
        } catch (Exception $ex) {
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.products.index');
        }
    }
}
