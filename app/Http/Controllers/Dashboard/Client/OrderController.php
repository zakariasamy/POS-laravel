<?php

namespace App\Http\Controllers\Dashboard\Client;

use Exception;
use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function create(Client $client)
    {

        $categories = Category::with('products')->get(); // so the user can choose cat & show the prods in that cat
        $orders = $client->orders()->with('products')->paginate(5);
        return view('dashboard.clients.orders.create', compact('client', 'categories', 'orders'));

    }

    public function store(Request $request, Client $client)
    {
        try{
        $request->validate([
            'products' => 'required|array',
        ]);
        DB::beginTransaction();
        $this->attach_order($request, $client);
        DB::commit();
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
        }

        catch(Exception $ex){
            DB::rollback();
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.orders.index');
        }
    }

    public function edit(Client $client, Order $order)
    {
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(5);
        return view('dashboard.clients.orders.edit', compact('client', 'order', 'categories', 'orders'));
    } //end of edit

    public function update(Request $request, Client $client, Order $order)
    {

        try{
        $request->validate([
            'products' => 'required|array',
        ]);
        DB::beginTransaction();

        $this->update_order($request, $client, $order);

        DB::commit();
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.orders.index');

        catch(Exception $ex){
            DB::rollback();
            session()->flash('fail', __('site.fail'));
            return redirect()->route('dashboard.orders.index');
        }
    }

    } //end of update



    private function attach_order($request, $client)
    {

        $total_price = 0;

        foreach ($request->products as $id => $quantity) {

            $product = Product::FindOrFail($id);
            $total_price += $product->sale_price * $quantity['quantity'];

            $product->update([
                'stock' => $product->stock - $quantity['quantity']
            ]);

        } //end of foreach

        // create order for the client
        $order = $client->orders()->create(['total_price' => $total_price]);

        // attach products for that order created for client
        $order->products()->attach($request->products);
    }

    private function update_order($request, $client, $order)
    {
        // get order_products to get quantity of each product
        $products = $order->products;

        $total_price = 0;
        foreach ($request->products as $id => $quantity) {

            $product = Product::FindOrFail($id);
            $products->filter(function($prod) use($id){
                return $prod->id == $id;
           });


            $old_quantity = $products[0]->pivot->quantity;
            $curr_quantity = $quantity['quantity'];
            if($curr_quantity > $old_quantity){
                $plus = $curr_quantity - $old_quantity;
                $product->update([
                    'stock' => $product->stock - $plus
                ]);
            }
            else if($curr_quantity < $old_quantity){
                $minus = $old_quantity - $curr_quantity;
                $product->update([
                    'stock' => $product->stock + $minus
                ]);
            }

            $total_price += $product->sale_price * $quantity['quantity'];

        } //end of foreach


        $order->products()->sync($request->products);
        $order->update([
            'total_price' => $total_price
        ]);
    }

}
