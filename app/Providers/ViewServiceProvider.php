<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer(['customer.*', 'cart.*'], function ($view) { // Load only where needed
            if (Auth::check()) {
                $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
                $total = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
            } else {
                $cartItems = collect();
                $total = 0;
            }

            $view->with(compact('cartItems', 'total'));
        });
    }

    public function register()
    {
        //
    }
}
