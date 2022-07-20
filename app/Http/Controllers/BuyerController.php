<?php

namespace App\Http\Controllers;

use App\Models\Buyer;
use App\Http\Requests\StoreBuyerRequest;
use App\Http\Requests\UpdateBuyerRequest;

class BuyerController extends Controller
{
    public function index()
    {
        $carts = session()->get('cart');
        if (empty($carts)) {
            return redirect('/');
        }
        return view('buyer', compact('carts'));
    }
}
