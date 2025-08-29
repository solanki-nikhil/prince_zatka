<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $request->session()->put('custom_message', '');
            $user = User::where('email', $request->email)->orwhere('mobile', $request->email)->first();
            // if ($user && Hash::check($request->password, $user->password) && ($user->hasRole('admin') || $user->hasRole('stock-admin') || $user->hasRole('coupon-admin')))
            if ($user && Hash::check($request->password, $user->password) )
            {  
                if ($user && $user->status == 0) { 
                    $request->session()->put('custom_message', 'Your Application is in review, Please wait.');
                    return null;
                } else {
                    return $user;
                } 
                
            } else {
                Auth::logout();
                // $request->session()->flash('class', 'alert alert-warning');
                // $request->session()->flash('status', 'Error Has Occuered.');
                return null;
            }
        });
    }
}
