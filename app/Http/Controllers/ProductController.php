<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Facades\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('product', compact('products'));
    }

    public function addToCart($id)
    {
        $quantity = Request::input('quantity');
        if ($quantity == 0) {
            return redirect()->back()->with('error', 'Quantity cannot be 0');
        }

        $product = Product::find($id);

        $cart = session()->get('cart');

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'id' => $id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $quantity
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function cart()
    {
        // delete session cart
        // session()->forget('cart');
        $carts = session()->get('cart');
        return view('cart', compact('carts'));
    }

    public function deleteCart($id)
    {
        $cart = session()->get('cart');
        unset($cart[$id]);
        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Product deleted from cart successfully!');
    }
}
