<?php

namespace App\Http\Controllers\Dashboard\Client;

use App\Models\Order;
use App\Models\Client;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
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

        $request->validate([
            'products' => 'required|array',
        ]);

        $this->attach_order($request, $client);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.orders.index');
    }

    public function edit(Client $client, Order $order)
    {
        $categories = Category::with('products')->get();
        $orders = $client->orders()->with('products')->paginate(5);
        return view('dashboard.clients.orders.edit', compact('client', 'order', 'categories', 'orders'));
    } //end of edit

    public function update(Request $request, Client $client, Order $order)
    {
        //dd($request->products);
        $request->validate([
            'products' => 'required|array',
        ]);

        $this->detach_order($order);

        $this->attach_order($request, $client);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.orders.index');
    } //end of update



    private function attach_order($request, $client)
    {
        //dd($request->products);
        $total_price = 0;

        foreach ($request->products as $id => $quantity) {
            $request->products;
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

    private function detach_order($order)
    {
        foreach ($order->products as $product) {

            $product->update([
                'stock' => $product->stock + $product->pivot->quantity
            ]);
        }

        $order->delete();
    }
}
