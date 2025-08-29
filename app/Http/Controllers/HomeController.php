<?php

namespace App\Http\Controllers;

use App\Exports\ProductExport;
use App\Models\Order;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        // If user is a customer, show the customer dashboard
        if ($user->roles[0]->name == 'customer') {
            return redirect()->route('customer.dashboard');
        }

        $product = Product::count();
        $category = Category::count();
        $order = Order::count();
        $pending = Order::where('order_status', '1')->count();
        $active = Order::where('order_status', '2')->count();
        $reject = Order::where('order_status', '3')->count();
        $dispatch = Order::where('order_status', '4')->count();
        // $deliver = Order::where('order_status', '5')->count();
        $customer = User::where('status', '1')->whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
            }
        )->count();
        return view('home', compact('product', 'category', 'order', 'customer', 'pending', 'active', 'reject', 'dispatch'));
    }    

    public function orderExport(Request $request)
    {
        $status = $request->status;
        $name = $request->name;
        $where = "1 = 1";
        if ($status == 'All') {
            if (!is_null($name)) {
                $where .= ' AND (orders.name = "' . $name . '")';
            }
        } else {
            if (!is_null($name)) {
                $where .= ' AND (orders.order_status = ' . $status . ' AND orders.name = "' . $name . '")';
            } else {
                $where .= ' AND (orders.order_status = "' . $status . '")';
            }
        }

        if (!empty($request->s_date) && !empty($request->e_date)) {
            $s_date = date('Y-m-d', strtotime($request->s_date)) . ' 00:00:00';
            $e_date = date('Y-m-d', strtotime($request->e_date)) . ' 23:59:59';
            $where .= ' AND (orders.created_at >= "' . $s_date . '" AND orders.created_at <= "' . $e_date . '")';
        }
        return Excel::download(new UsersExport($where), 'Prince_Z_Orders.xlsx');
    }

    public function productExport(Request $request)
    {
        $where = "1 = 1";
        if ($request->category_id != 'All' && $request->sub_category_id != 'All') {
            $where .= ' AND (products.category_id = ' . $request->category_id . ' AND products.sub_category_id = "' . $request->sub_category_id . '")';
        } else if ($request->category_id == 'All' && $request->sub_category_id != 'All') {
            $where .= ' AND products.sub_category_id = "' . $request->sub_category_id . '"';
        } else if ($request->category_id != 'All' && $request->sub_category_id == 'All') {
            $where .= ' AND products.category_id = ' . $request->category_id;
        } else {
            $where = $where;
        }
        return Excel::download(new ProductExport($where), 'Prince_Z_Products.xlsx');
    }
}
