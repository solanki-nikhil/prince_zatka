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

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $id = Auth::id();
        $where = "1 = 1";
        if (Auth::user()->roles[0]->name == 'admin') {
            if (!is_null($request->id)) {
                $where .= ' AND orders.user_id =' . $request->id; //particular order get in admin
            } else {
                $where = $where; //all order display in admin
            }
        } else {
            $where .= ' AND orders.user_id =' . $id; //all order for customer login 
        }
        if (!is_null($request->start) && !is_null($request->length)) {
            $order = Order::whereRaw($where)->with('user', 'orderTransection', 'orderTransection.product')->skip($request->start)->take($request->length)->orderBy('id', 'DESC')->get();
        } else {
            $order = Order::whereRaw($where)->with('user', 'orderTransection', 'orderTransection.product', 'orderTransection.category', 'orderTransection.subCategory')->orderBy('id', 'DESC')->get();
        }
        if (count($order) > 0) {
            foreach ($order as $key => $value) {
                if ($value->order_by == '0') {
                    $value['order_by'] = 'Self';
                } else {
                    $value['order_by'] = 'Admin';
                }
                if ($value->user_type == '0') {
                    $value['user_type'] = 'Self';
                } else {
                    $value['user_type'] = 'Other';
                }
                $value['customer_name'] = $value->user->name;
                $value['customer_mobile'] = $value->user->mobile;
                $value['total_quantity'] = $value->total_pices;
                $value['date'] = $value->created_at->format('d-m-Y H:i:s A');
                unset($value->user, $value->total_pices, $value->total_box, $value->updated_at, $value->deleted_at, $value->created_at);
                foreach ($value->orderTransection as $item) {
                    $item['category_name'] = $item->category->category_name;
                    $item['sub_category_name'] = $item->subCategory->sub_category_name;
                    $item['product_name'] = $item->product->product_name;
                    $item['product_code'] = $item->product->product_code;
                    $item['quantity'] = $item->pices;
                    unset($item->category, $item->subCategory, $item->product, $item->per_box_pices, $item->pices, $item->deleted_at, $item->created_at, $item->updated_at);
                }
            }
            $response = ['status' => true, 'message' => 'Order Listings.', 'order' => $order];
            return response($response, 200);
        } else {
            $response = ['status' => true, 'message' => 'Order Not Found.'];
            return response($response, 200);
        }
    }

    public function store(Request $request)
    {
        if ($request->user_type == 0) {
            $name = 'nullable';
            $mobile = 'nullable|numeric|digits:10';
            $address = 'nullable';
        } else {
            $name = 'nullable';
            $mobile = 'nullable|numeric|digits:10';
            $address = 'nullable';
        }
        $validator = Validator::make($request->all(), [
            'user_type' => 'required',
            'name' => $name,
            'mobile' => $mobile,
            'address' => $address,
        ], [
            'user_type.required' => 'Select User Type.',
            'name.required' => 'Enter Name.',
            'mobile.required' => 'Enter Mobile.',
            'address.required' => 'Enter Address.',
        ]);
        if ($validator->fails()) {
            $error = '';
            foreach ($validator->messages()->all() as $item) {
                $error .= $item;
            }
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $error];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (Auth::user()->roles[0]->name == 'admin' || Auth::user()->roles[0]->name == 'stock-admin' || Auth::user()->roles[0]->name == 'coupon-admin') {
                $order_by = '1';
                $id = $request->user_id;
            } else {
                $order_by = '0';
                $id = Auth::id();
            }
            $user = User::where('id', $id)->first();
            $userProfile = UserProfile::where('user_id', $user->id)->first();
            if (!is_null($request->order_id)) {
                $order = Order::where('id', $request->order_id)->where('order_status', '1')->first();
                if (!is_null($order)) {
                    $order->user_id = $user->id;
                    $order->user_type = $request->user_type;
                    $order->order_by = $order_by;
                    if ($request->user_type == 0) {
                        $order->name = $user->name;
                        $order->mobile = $user->mobile;
                        $order->address = $userProfile->address;
                    } else {
                        $order->name = $request->name;
                        $order->mobile = $request->mobile;
                        $order->address = $request->address;
                    }
                    $box = 0;
                    $pices = 0;
                    $final_amount = 0;
                    $total = 0;
                    foreach ($request->orderTransection as $key => $value) {
                        if (!is_null($value['order_transection_id'])) {
                            $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
                            $product = Product::where('id', $value['product_id'])->first();
                            $editQty = $orderTransection->pices + $product->quantity;
                            if ($editQty >= $value['quantity']) {
                                $box += $value['quantity'] / $product->box;
                                $pices += $value['quantity'];
                                // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
                                $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
                                $total += $final_amount * $value['quantity'];
                             //   $total += $final_amount * $product->box * $value['quantity'];
                                $product->quantity = ($product->quantity + $orderTransection->pices) - $value['quantity'];
                                $product->save();
                            } else {
                                $response = ['status' => false, 'message' => 'Quantity out of stock.'];
                                return response()->json($response);
                            }
                        } else {
                            $product = Product::where('id', $value['product_id'])->where('quantity', '>=', $value['quantity'])->first();
                            if (!is_null($product)) {
                                $box += $value['quantity'] / $product->box;
                                $pices += $value['quantity'];
                                // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
                                $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
                                $total += $final_amount * $value['quantity'];
                              //  $total += $final_amount * $product->box * $value['quantity'];
                                $product->quantity = $product->quantity - $value['quantity'];
                                $product->save();
                            } else {
                                $response = ['status' => false, 'message' => 'Quantity out of stock.'];
                                return response($response, 201);
                            }
                        }
                    }
                    $order->total_box = $box;
                    $order->total_pices = $pices;
                    $order->total_amount = $total;
                    $result = $order->save();
                    DB::commit();

                    if (!is_null($result)) {
                        foreach ($request->orderTransection as $key => $value) {
                            $product = Product::where('id', $value['product_id'])->first();
                            if (!is_null($value['order_transection_id'])) {
                                $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
                                $orderTransection->order_id = $order->id;
                                $orderTransection->category_id = $value['category_id'];
                                $orderTransection->sub_category_id = $value['sub_category_id'];
                                $orderTransection->product_id = $value['product_id'];
                                $orderTransection->box = $value['quantity'] / $product->box;
                                $orderTransection->amount = $product->price;
                                $orderTransection->per_box_pices = $product->box;
                                $orderTransection->discount = $value['discount'];
                                $orderTransection->pices = $value['quantity'];
                                // $origanlPrice = $value['quantity'] * $product->price;
                                $origanlPrice =  $product->price;

                                // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                                $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                                $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
                                $orderTransection->save();
                            } else {
                                $orderTransection = OrderTransection::where('order_id', $order->id)->where('product_id', '=', $value['product_id'])->first();
                                if (is_null($orderTransection)) {
                                    $orderTransection = new OrderTransection();
                                    $orderTransection->order_id = $order->id;
                                    $orderTransection->category_id = $value['category_id'];
                                    $orderTransection->sub_category_id = $value['sub_category_id'];
                                    $orderTransection->product_id = $value['product_id'];
                                    $orderTransection->box = $value['quantity'] / $product->box;
                                    $orderTransection->amount = $product->price;
                                    $orderTransection->per_box_pices = $product->box;
                                    $orderTransection->discount = $value['discount'];
                                    $orderTransection->pices = $value['quantity'];
                                    // $origanlPrice = $value['quantity'] * $product->price;
                                    $origanlPrice =  $product->price;
                                    // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                                    $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                                    $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
                                    $orderTransection->save();
                                }
                            }
                        }
                        $response = ['status' => true, 'message' => 'Order Updated Successfully.'];
                        return response($response, 200);
                    } else {
                        $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                        return response($response, 500);
                    }
                } else {
                    $response = ['status' => false, 'message' => 'Can not Updated.'];
                    return response($response, 201);
                }
            } else {
                $order = new Order();
                $order->user_id = $user->id;
                $order->user_type = $request->user_type;
                $order->order_by = $order_by;
                if ($request->user_type == 0) {
                    $order->name = $user->name;
                    $order->mobile = $user->mobile;
                    $order->address = $userProfile->address;
                } else {
                    $order->name = $request->name;
                    $order->mobile = $request->mobile;
                    $order->address = $request->address;
                }
                $order->order_id = $this->orderIdGenerate();
                $order->order_status = 1;
                $box = 0;
                $pices = 0;
                $final_amount = 0;
                $total = 0;
                foreach ($request->orderTransection as $key => $value) {
                    $product = Product::where('id', $value['product_id'])->where('quantity', '>=', $value['quantity'])->first();
                    if (!is_null($product)) {
                        $box += $value['quantity'] / $product->box;
                        $pices += $value['quantity'];
                        // $final_amount = ($value['quantity'] * $product->price) - (($value['quantity'] * $product->price) * $value['discount'] / 100);
                        $final_amount = ($product->price) - (($product->price) * $value['discount'] / 100);
                        $product->quantity = $product->quantity - $value['quantity'];
                        $total += $final_amount * $value['quantity'];
                       // $total += $final_amount * $product->box * $value['quantity'];
                        $product->save();
                    } else {
                        $response = ['status' => false, 'message' => 'Quantity out of stock.'];
                        return response($response, 201);
                    }
                }
                $order->total_box = $box;
                $order->total_pices = $pices;
                $order->total_amount = $total;
                $result = $order->save();
                DB::commit();

                if (!is_null($result)) {
                    foreach ($request->orderTransection as $key => $value) {
                        $product = Product::where('id', $value['product_id'])->first();
                        $orderTransection = new OrderTransection();
                        $orderTransection->order_id = $order->id;
                        $orderTransection->category_id = $value['category_id'];
                        $orderTransection->sub_category_id = $value['sub_category_id'];
                        $orderTransection->product_id = $value['product_id'];
                        $orderTransection->box = $value['quantity'] / $product->box;
                        $orderTransection->amount = $product->price;
                        $orderTransection->per_box_pices = $product->box;
                        $orderTransection->pices = $value['quantity'];
                        $orderTransection->discount = $value['discount'];
                        // $origanlPrice = $value['quantity'] * $product->price;
                        $origanlPrice =  $product->price;

                        // $orderTransection->total_amount = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                        $item_total = $origanlPrice - ($origanlPrice * $value['discount'] / 100);
                        $orderTransection->total_amount = $item_total * $value['quantity'];
                       // $orderTransection->total_amount = $item_total * $value['quantity'] * $product->box;
                        $orderTransection->save();
                    }
                    $response = ['status' => true, 'message' => 'Order Add Successfully.'];
                    return response($response, 200);
                } else {
                    $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                    return response($response, 500);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
    }

    //link(unique number generate)
    public function orderIdGenerate()
    {
        $orderStart = '000001';
        $order = Order::select('order_id')->orderBy('id', 'desc')->first();
        if (!is_null($order)) {
            $odr = substr($order->order_id, 3);
            $orderStart = str_pad($odr + 1, 6, '0', STR_PAD_LEFT);
        }
        return 'PZ_' . $orderStart;
    }

    //order status update
    public function update(Request $request, Order $order)
    {
        try {
            $order = Order::where('id', $request->id)->first();
            $order->order_status = $request->order_status;
            if ($request->order_status == 4 || $request->order_status == 5) {
                $order->bill_number = $request->bill_number;
            } else {
                $order->bill_number = null;
            }
            $order->save();
            $response = ['status' => true, 'message' => 'Order Status Updated Successfully.'];
            return response($response, 200);
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
    }

    public function destroy(Request $request)
    {
        if (is_null($request->id)) {
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
        $order = Order::where('id', $request->id)->where('order_status', '1')->first();
        if (!is_null($order)) {
            $orderTransection = OrderTransection::where('order_id', $order->id)->get();
            foreach ($orderTransection as $item) {
                $product = Product::where('id', $item->product_id)->first();
                $product->quantity = $product->quantity + $item->pices;
                $product->save();
                $item->delete();
            }
            $order->delete();
            $response = ['status' => true, 'message' => 'Order Deleted Successfully.'];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response($response, 201);
        }
    }

    public function removeOrder(Request $request)
    {
        if (is_null($request->id)) {
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
        $orderTransection = OrderTransection::find($request->id);
        if (!is_null($orderTransection)) {
            $product = Product::where('id', $orderTransection->product_id)->first();
            $product->quantity = $product->quantity + $orderTransection->pices;
            $product->save();
            $order = Order::where('id', $orderTransection->order_id)->first();
            $order->total_pices = $order->total_pices - $orderTransection->pices;
            $order->total_box = $order->total_box - $orderTransection->box;
            $order->total_amount = $order->total_amount - $orderTransection->total_amount;
            $order->save();
            $orderTransection->delete();
            $response = ['status' => true, 'message' => 'Order Transection Deleted Successfully.'];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response($response, 201);
        }
    }
}
