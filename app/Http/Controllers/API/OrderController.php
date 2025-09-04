<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Cart;

class OrderController extends Controller
{
    // Generate next order id like PZ_000001
    private function generateOrderId()
    {
        $lastOrder = Order::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        return 'PZ_' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    // Create order from cart
    public function store(Request $request)
{
    $user = Auth::user();
    $carts = Cart::with('product')->where('user_id', $user->id)->get();

    if ($carts->isEmpty()) {
        return response()->json(['status' => false, 'message' => 'Cart is empty'], 400);
    }

    DB::beginTransaction();
    try {
        // If address provided, update user profile also
        $address = $request->address ?? $user->profile->address;
        if ($request->filled('address') && $user->profile) {
            $user->profile->update(['address' => $request->address]);
        }

        $order = Order::create([
            'user_id'      => $user->id,
            'user_type'    => 0,
            'order_by'     => 0,
            'order_id'     => $this->generateOrderId(),
            'name'         => $user->name,
            'mobile'       => $user->mobile,
            'address'      => $address,
            'total_pices'  => 0,
            'total_box'    => 0,
            'total_amount' => 0,
            'order_status' => 1 // pending
        ]);

        $totalPieces = 0;
        $totalBox = 0;
        $totalAmount = 0;

        foreach ($carts as $cart) {
            $perBox = $cart->product->per_box_pices ?? 1;
            $pieces = $cart->quantity * $perBox;
            $amount = $cart->product->price ?? 0;
            $lineTotal = $pieces * $amount;

            OrderTransection::create([
                'order_id'        => $order->id,
                'category_id'     => $cart->product->category_id ?? null,
                'sub_category_id' => $cart->product->sub_category_id ?? 0,
                'product_id'      => $cart->product_id,
                'box'             => $cart->quantity,
                'amount'          => $amount,
                'pices'           => $pieces,
                'per_box_pices'   => $perBox,
                'total_amount'    => $lineTotal,
            ]);

            $totalPieces += $pieces;
            $totalBox += $cart->quantity;
            $totalAmount += $lineTotal;
        }

        $order->update([
            'total_pices'  => $totalPieces,
            'total_box'    => $totalBox,
            'total_amount' => $totalAmount
        ]);

        // clear cart
        Cart::where('user_id', $user->id)->delete();

        DB::commit();
        return response()->json(['status' => true, 'message' => 'Order created', 'data' => $order->load('orderTransection')], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['status' => false, 'message' => 'Error creating order', 'error' => $e->getMessage()], 500);
    }
}

    // List all orders of user
    public function index()
    {
        $orders = Order::with('orderTransection.product')
            ->where('user_id', Auth::id())
            ->orderBy('id','desc')
            ->get();

        return response()->json(['status' => true, 'message' => $orders->isEmpty() ? 'No orders found' : 'Orders fetched successfully', 'data' => $orders]);
    }

    // Update order only if pending
   public function update(Request $request, $id)
{
    $order = Order::where('id',$id)->where('user_id',Auth::id())->first();
    if (!$order) return response()->json(['status'=>false,'message'=>'Order not found'],404);

    if ($order->order_status != 1) {
        return response()->json(['status'=>false,'message'=>'Only pending orders can be updated'],403);
    }

    // If new address given, update in profile also
    if ($request->filled('address')) {
        $order->update(['address' => $request->address]);
        if (Auth::user()->profile) {
            Auth::user()->profile->update(['address' => $request->address]);
        }
    }

    // remarks removed (not saved)
    
    return response()->json(['status'=>true,'message'=>'Order updated','data'=>$order]);
}

    // Cancel order with remark
public function cancel(Request $request, $id)
{
    $order = Order::where('id', $id)
        ->where('user_id', Auth::id())
        ->first();

    if (!$order) {
        return response()->json(['status' => false, 'message' => 'Order not found'], 404);
    }

    if ($order->order_status != 1) {
        return response()->json(['status' => false, 'message' => 'Only pending orders can be cancelled'], 403);
    }

    // $remark = $request->remark ?? 'Cancelled by user'; // default remark

    $order->update([
        'order_status' => 3, // 3 = reject/cancelled
        'remarks'      => $request->remarks ?? null, // save remarks in remarks column
    ]);

    return response()->json([
        'status'  => true,
        'message' => 'Order cancelled',
        'data'    => $order
    ]);
}


    // Delete order (soft delete)
    public function destroy($id)
    {
        $order = Order::where('id',$id)->where('user_id',Auth::id())->first();
        if (!$order) return response()->json(['status'=>false,'message'=>'Order not found'],404);

        $order->delete();
        return response()->json(['status'=>true,'message'=>'Order deleted']);
    }

    // View single order
    public function show($id)
    {
        $order = Order::with('orderTransection.product')
            ->where('id',$id)
            ->where('user_id',Auth::id())
            ->first();

        if (!$order) return response()->json(['status'=>false,'message'=>'Order not found'],404);

        return response()->json(['status'=>true, 'message' => 'Order details fetched successfully','data'=>$order]);
    }

    // public function index(Request $request)
    // {
    //     $id = Auth::id();
    //     $where = "1 = 1";
    //     if (Auth::user()->roles[0]->name == 'admin') {
    //         if (!is_null($request->id)) {
    //             $where .= ' AND orders.user_id =' . $request->id; //particular order get in admin
    //         } else {
    //             $where = $where; //all order display in admin
    //         }
    //     } else {
    //         $where .= ' AND orders.user_id =' . $id; //all order for customer login 
    //     }
    //     if (!is_null($request->start) && !is_null($request->length)) {
    //         $order = Order::whereRaw($where)->with('user', 'orderTransection', 'orderTransection.product')->skip($request->start)->take($request->length)->orderBy('id', 'DESC')->get();
    //     } else {
    //         $order = Order::whereRaw($where)->with('user', 'orderTransection', 'orderTransection.product', 'orderTransection.category', 'orderTransection.subCategory')->orderBy('id', 'DESC')->get();
    //     }
    //     if (count($order) > 0) {
    //         foreach ($order as $key => $value) {
    //             if ($value->order_by == '0') {
    //                 $value['order_by'] = 'Self';
    //             } else {
    //                 $value['order_by'] = 'Admin';
    //             }
    //             if ($value->user_type == '0') {
    //                 $value['user_type'] = 'Self';
    //             } else {
    //                 $value['user_type'] = 'Other';
    //             }
    //             $value['customer_name'] = $value->user->name;
    //             $value['customer_mobile'] = $value->user->mobile;
    //             $value['total_quantity'] = $value->total_pices;
    //             $value['date'] = $value->created_at->format('d-m-Y H:i:s A');
    //             unset($value->user, $value->total_pices, $value->total_box, $value->updated_at, $value->deleted_at, $value->created_at);
    //             foreach ($value->orderTransection as $item) {
    //                 $item['category_name'] = $item->category->category_name;
    //                 $item['sub_category_name'] = $item->subCategory->sub_category_name;
    //                 $item['product_name'] = $item->product->product_name;
    //                 $item['product_code'] = $item->product->product_code;
    //                 $item['quantity'] = $item->pices;
    //                 unset($item->category, $item->subCategory, $item->product, $item->per_box_pices, $item->pices, $item->deleted_at, $item->created_at, $item->updated_at);
    //             }
    //         }
    //         $response = ['status' => true, 'message' => 'Order Listings.', 'order' => $order];
    //         return response($response, 200);
    //     } else {
    //         $response = ['status' => true, 'message' => 'Order Not Found.'];
    //         return response($response, 200);
    //     }
    // }

    // public function store(Request $request)
    // {
    //     if ($request->user_type == 0) {
    //         $name = 'nullable';
    //         $mobile = 'nullable|numeric|digits:10';
    //         $address = 'nullable';
    //     } else {
    //         $name = 'nullable';
    //         $mobile = 'nullable|numeric|digits:10';
    //         $address = 'nullable';
    //     }
    //     $validator = Validator::make($request->all(), [
    //         'user_type' => 'required',
    //         'name' => $name,
    //         'mobile' => $mobile,
    //         'address' => $address,
    //     ], [
    //         'user_type.required' => 'Select User Type.',
    //         'name.required' => 'Enter Name.',
    //         'mobile.required' => 'Enter Mobile.',
    //         'address.required' => 'Enter Address.',
    //     ]);
    //     if ($validator->fails()) {
    //         $error = '';
    //         foreach ($validator->messages()->all() as $item) {
    //             $error .= $item;
    //         }
    //         $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $error];
    //         return response()->json($response);
    //     }
    //     DB::beginTransaction();
    //     try {
    //         if (Auth::user()->roles[0]->name == 'admin' || Auth::user()->roles[0]->name == 'stock-admin' || Auth::user()->roles[0]->name == 'coupon-admin') {
    //             $order_by = '1';
    //             $id = $request->user_id;
    //         } else {
    //             $order_by = '0';
    //             $id = Auth::id();
    //         }
    //         $user = User::where('id', $id)->first();
    //         $userProfile = UserProfile::where('user_id', $user->id)->first();
    //         if (!is_null($request->order_id)) {
    //             $order = Order::where('id', $request->order_id)->where('order_status', '1')->first();
    //             if (!is_null($order)) {
    //                 $order->user_id = $user->id;
    //                 $order->user_type = $request->user_type;
    //                 $order->order_by = $order_by;
    //                 if ($request->user_type == 0) {
    //                     $order->name = $user->name;
    //                     $order->mobile = $user->mobile;
    //                     $order->address = $userProfile->address;
    //                 } else {
    //                     $order->name = $request->name;
    //                     $order->mobile = $request->mobile;
    //                     $order->address = $request->address;
    //                 }
    //                 $box = 0;
    //                 $pices = 0;
    //                 $final_amount = 0;
    //                 $total = 0;
    //                 foreach ($request->orderTransection as $key => $value) {
    //                     if (!is_null($value['order_transection_id'])) {
    //                         $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
    //                         $product = Product::where('id', $value['product_id'])->first();
    //                         $editQty = $orderTransection->pices + $product->quantity;
    //                         if ($editQty >= $value['quantity']) {
    //                             $box += $value['quantity'] / $product->box;
    //                             $pices += $value['quantity'];
    //                             // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
    //                             $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
    //                             $total += $final_amount * $value['quantity'];
    //                          //   $total += $final_amount * $product->box * $value['quantity'];
    //                             $product->quantity = ($product->quantity + $orderTransection->pices) - $value['quantity'];
    //                             $product->save();
    //                         } else {
    //                             $response = ['status' => false, 'message' => 'Quantity out of stock.'];
    //                             return response()->json($response);
    //                         }
    //                     } else {
    //                         $product = Product::where('id', $value['product_id'])->where('quantity', '>=', $value['quantity'])->first();
    //                         if (!is_null($product)) {
    //                             $box += $value['quantity'] / $product->box;
    //                             $pices += $value['quantity'];
    //                             // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
    //                             $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
    //                             $total += $final_amount * $value['quantity'];
    //                           //  $total += $final_amount * $product->box * $value['quantity'];
    //                             $product->quantity = $product->quantity - $value['quantity'];
    //                             $product->save();
    //                         } else {
    //                             $response = ['status' => false, 'message' => 'Quantity out of stock.'];
    //                             return response($response, 201);
    //                         }
    //                     }
    //                 }
    //                 $order->total_box = $box;
    //                 $order->total_pices = $pices;
    //                 $order->total_amount = $total;
    //                 $result = $order->save();
    //                 DB::commit();

    //                 if (!is_null($result)) {
    //                     foreach ($request->orderTransection as $key => $value) {
    //                         $product = Product::where('id', $value['product_id'])->first();
    //                         if (!is_null($value['order_transection_id'])) {
    //                             $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
    //                             $orderTransection->order_id = $order->id;
    //                             $orderTransection->category_id = $value['category_id'];
    //                             $orderTransection->sub_category_id = $value['sub_category_id'];
    //                             $orderTransection->product_id = $value['product_id'];
    //                             $orderTransection->box = $value['quantity'] / $product->box;
    //                             $orderTransection->amount = $product->price;
    //                             $orderTransection->per_box_pices = $product->box;
    //                             $orderTransection->discount = $value['discount'];
    //                             $orderTransection->pices = $value['quantity'];
    //                             // $origanlPrice = $value['quantity'] * $product->price;
    //                             $origanlPrice =  $product->price;

    //                             // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                             $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                             $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
    //                             $orderTransection->save();
    //                         } else {
    //                             $orderTransection = OrderTransection::where('order_id', $order->id)->where('product_id', '=', $value['product_id'])->first();
    //                             if (is_null($orderTransection)) {
    //                                 $orderTransection = new OrderTransection();
    //                                 $orderTransection->order_id = $order->id;
    //                                 $orderTransection->category_id = $value['category_id'];
    //                                 $orderTransection->sub_category_id = $value['sub_category_id'];
    //                                 $orderTransection->product_id = $value['product_id'];
    //                                 $orderTransection->box = $value['quantity'] / $product->box;
    //                                 $orderTransection->amount = $product->price;
    //                                 $orderTransection->per_box_pices = $product->box;
    //                                 $orderTransection->discount = $value['discount'];
    //                                 $orderTransection->pices = $value['quantity'];
    //                                 // $origanlPrice = $value['quantity'] * $product->price;
    //                                 $origanlPrice =  $product->price;
    //                                 // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                                 $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                                 $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
    //                                 $orderTransection->save();
    //                             }
    //                         }
    //                     }
    //                     $response = ['status' => true, 'message' => 'Order Updated Successfully.'];
    //                     return response($response, 200);
    //                 } else {
    //                     $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //                     return response($response, 500);
    //                 }
    //             } else {
    //                 $response = ['status' => false, 'message' => 'Can not Updated.'];
    //                 return response($response, 201);
    //             }
    //         } else {
    //             $order = new Order();
    //             $order->user_id = $user->id;
    //             $order->user_type = $request->user_type;
    //             $order->order_by = $order_by;
    //             if ($request->user_type == 0) {
    //                 $order->name = $user->name;
    //                 $order->mobile = $user->mobile;
    //                 $order->address = $userProfile->address;
    //             } else {
    //                 $order->name = $request->name;
    //                 $order->mobile = $request->mobile;
    //                 $order->address = $request->address;
    //             }
    //             $order->order_id = $this->orderIdGenerate();
    //             $order->order_status = 1;
    //             $box = 0;
    //             $pices = 0;
    //             $final_amount = 0;
    //             $total = 0;
    //             foreach ($request->orderTransection as $key => $value) {
    //                 $product = Product::where('id', $value['product_id'])->where('quantity', '>=', $value['quantity'])->first();
    //                 if (!is_null($product)) {
    //                     $box += $value['quantity'] / $product->box;
    //                     $pices += $value['quantity'];
    //                     // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
    //                     $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
    //                     $product->quantity = $product->quantity - $value['quantity'];
    //                     $total += $final_amount * $value['quantity'];
    //                    // $total += $final_amount * $product->box * $value['quantity'];
    //                     $product->save();
    //                 } else {
    //                     $response = ['status' => false, 'message' => 'Quantity out of stock.'];
    //                     return response($response, 201);
    //                 }
    //             }
    //             $order->total_box = $box;
    //             $order->total_pices = $pices;
    //             $order->total_amount = $total;
    //             $result = $order->save();
    //             DB::commit();

    //             if (!is_null($result)) {
    //                 foreach ($request->orderTransection as $key => $value) {
    //                     $product = Product::where('id', $value['product_id'])->first();
    //                     $orderTransection = new OrderTransection();
    //                     $orderTransection->order_id = $order->id;
    //                     $orderTransection->category_id = $value['category_id'];
    //                     $orderTransection->sub_category_id = $value['sub_category_id'];
    //                     $orderTransection->product_id = $value['product_id'];
    //                     $orderTransection->box = $value['quantity'] / $product->box;
    //                     $orderTransection->amount = $product->price;
    //                     $orderTransection->per_box_pices = $product->box;
    //                     $orderTransection->pices = $value['quantity'];
    //                     $orderTransection->discount = $value['discount'];
    //                     // $origanlPrice = $value['quantity'] * $product->price;
    //                     $origanlPrice =  $product->price;

    //                     // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                     $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
    //                     $orderTransection->total_amount = $item_total * $value['quantity'];
    //                    // $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
    //                     $orderTransection->save();
    //                 }
    //                 $response = ['status' => true, 'message' => 'Order Add Successfully.'];
    //                 return response($response, 200);
    //             } else {
    //                 $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //                 return response($response, 500);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // //link(unique number generate)
    // public function orderIdGenerate()
    // {
    //     $orderStart = '000001';
    //     $order = Order::select('order_id')->orderBy('id', 'desc')->first();
    //     if (!is_null($order)) {
    //         $odr = substr($order->order_id, 3);
    //         $orderStart = str_pad($odr + 1, 6, '0', STR_PAD_LEFT);
    //     }
    //     return 'PZ_' . $orderStart;
    // }

    // //order status update
    // public function update(Request $request, Order $order)
    // {
    //     try {
    //         $order = Order::where('id', $request->id)->first();
    //         $order->order_status = $request->order_status;
    //         if ($request->order_status == 4 || $request->order_status == 5) {
    //             $order->bill_number = $request->bill_number;
    //         } else {
    //             $order->bill_number = null;
    //         }
    //         $order->save();
    //         $response = ['status' => true, 'message' => 'Order Status Updated Successfully.'];
    //         return response($response, 200);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // public function destroy(Request $request)
    // {
    //     if (is_null($request->id)) {
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    //     $order = Order::where('id', $request->id)->where('order_status', '1')->first();
    //     if (!is_null($order)) {
    //         $orderTransection = OrderTransection::where('order_id', $order->id)->get();
    //         foreach ($orderTransection as $item) {
    //             $product = Product::where('id', $item->product_id)->first();
    //             $product->quantity = $product->quantity + $item->pices;
    //             $product->save();
    //             $item->delete();
    //         }
    //         $order->delete();
    //         $response = ['status' => true, 'message' => 'Order Deleted Successfully.'];
    //         return response($response, 200);
    //     } else {
    //         $response = ['status' => false, 'message' => 'This Record does not exist.'];
    //         return response($response, 201);
    //     }
    // }

    // public function removeOrder(Request $request)
    // {
    //     if (is_null($request->id)) {
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    //     $orderTransection = OrderTransection::find($request->id);
    //     if (!is_null($orderTransection)) {
    //         $product = Product::where('id', $orderTransection->product_id)->first();
    //         $product->quantity = $product->quantity + $orderTransection->pices;
    //         $product->save();
    //         $order = Order::where('id', $orderTransection->order_id)->first();
    //         $order->total_pices = $order->total_pices - $orderTransection->pices;
    //         $order->total_box = $order->total_box - $orderTransection->box;
    //         $order->total_amount = $order->total_amount - $orderTransection->total_amount;
    //         $order->save();
    //         $orderTransection->delete();
    //         $response = ['status' => true, 'message' => 'Order Transection Deleted Successfully.'];
    //         return response($response, 200);
    //     } else {
    //         $response = ['status' => false, 'message' => 'This Record does not exist.'];
    //         return response($response, 201);
    //     }
    // }
}
