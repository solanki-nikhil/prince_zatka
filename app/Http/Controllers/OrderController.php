<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Cart;
use App\Models\SerialNo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // do not let customer access this page
        if (Auth::user()->roles[0]->name == 'customer') {
            return redirect('/');
        }

        $status = $request->status;
        $name = $request->name;
        $where = "1 = 1";
        if ($status == 'All') {
            if (!is_null($name)) {
                $where .= ' AND (orders.name = "' . $name . '")';
            } else {
                $where = "1 = 1";
            }
        } else {
            if (!is_null($name)) {
                $where .= ' AND (orders.order_status = ' . $status . ' AND orders.name = "' . $name . '")';
            } else {
                $where .= ' AND (orders.order_status = "' . $status . '")';
            }
        }

        if ($request->has('is_outstock') && $request->is_outstock === 'true') {
            $where .= ' AND (orders.is_outstock = 1)';
        } else {
            $where .= ' AND (orders.is_outstock = 0)'; // hide this if you want to show all orders including out of stock
        }

        if (!empty($request->s_date) && !empty($request->e_date)) {
            $s_date = date('Y-m-d', strtotime($request->s_date)) . ' 00:00:00';
            $e_date = date('Y-m-d', strtotime($request->e_date)) . ' 23:59:59';
            $where .= ' AND (orders.created_at >= "' . $s_date . '" AND orders.created_at <= "' . $e_date . '")';
        }
        if (request()->ajax()) {
            return DataTables::of(Order::with('user')->select('orders.*')->whereRaw($where)->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    // $html .= '<a data-id="' . $row->id . '" role="button" href="' . route('generate-pdf', $row->id) . '" class="pdf avatar bg-light p-50 m-0 text-dark" data-bs-toggle="tooltip" data-placement="left" title="Download PDF"><i class="fa fa-download"></i></a>';
                    $html .= '<a data-id="' . $row->id . '" role="button" href="javascript:void(0)" class="mx-1 view_product avatar bg-light-success p-50 m-0 text-success" data-bs-toggle="tooltip" data-placement="left" title="View"><i class="fa fa-eye"></i></a>';
                    $html .= ' <a href="' . route('order.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    if ($row->order_status != 3) {
                        $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="mx-1 delete avatar bg-light-danger p-50 m-0 text-danger" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    }
                    if ($row->order_status == 4) {
                        $html .= '<a data-id="' . $row->id . '" 
                                        data-order-id="' . $row->order_id . '" 
                                        data-status="' . $row->status . '" 
                                        data-lr-number="' . $row->lr_number . '" 
                                        data-lr-date="' . $row->lr_date . '" 
                                        data-cases="' . $row->cases . '" 
                                        data-lr-photo="' . asset('upload/order/' . $row->lr_photo) . '" 
                                        href="javascript:void(0);" 
                                        class="dispatch avatar bg-light-primary p-50 m-0 text-primary" 
                                        data-bs-toggle="tooltip" 
                                        data-placement="left" 
                                        title="Dispatch Info">
                                        <i class="fa fa-truck"></i>
                                    </a>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('order_by', function ($row) {
                    if ($row->order_by == '0') {
                        return 'Self';
                    } else {
                        return 'Admin';
                    }
                })
                ->editColumn('user_type', function ($row) {
                    if ($row->user_type == '0') {
                        return 'Self';
                    } else {
                        return 'Other';
                    }
                })
                ->editColumn('quantity', function ($row) {
                    if ($row->total_box != 0 && $row->total_box != '') {
                        return $row->total_box;
                    } else {
                        return '0';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i', strtotime($row->created_at));
                })
                ->editColumn('total_amount', function ($row) {
                    return number_format($row->total_amount, 2);
                })
                ->editColumn('address', function ($row) {
                    return strlen($row->address) > 25 ? substr($row->address, 0, 25) . '..' : $row->address;
                })
                ->addColumn('order_status', function ($row) {
                    if ($row->order_status == 5) {
                        return '<button type="button" class="btn btn-outline-primary w-100">Deliver</button>';
                    } else {
                        $html = '<div class="">';
                        $active1 =  $active2  = $active3  = $active4  = $active5  = '';
                        if ($row->order_status == 1) {
                            $btn = "btn-outline-warning";
                            $title = "Pending";
                            $active1 = "active bg-warning";
                        }
                        if ($row->order_status == 2) {
                            $btn = "btn-outline-success";
                            $title = "Active";
                            $active2 = "active bg-success";
                        }
                        if ($row->order_status == 3) {
                            $btn = "btn-outline-secondary";
                            $title = "Reject";
                            $active3 = "active bg-secondary";
                        }
                        if ($row->order_status == 4) {
                            $btn = "btn-outline-danger";
                            $title = "Dispatch";
                            $active4 = "active bg-danger";
                        }
                        // if ($row->order_status == 5) {
                        //     $btn = "btn-outline-primary";
                        //     $title = "Deliver";
                        //     $active5 = "active bg-primary";
                        // }
                        $html .= '<div class="btn-group w-100">
                                    <button type="button" class="btn-sm btn ' . $btn . '">' . $title . '</button>
                                    <button type="button" class="btn-sm btn ' . $btn . ' dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu p-0">
                                    <li><a class="dropdown-item change-status ' . $active1 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-bill="' . $row->bill_number . '" data-value="1">Pending</a></li>
                                    <li><a class="dropdown-item change-status ' . $active2 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-bill="' . $row->bill_number . '" data-value="2">Active</a></li>
                                    <li><a class="dropdown-item change-status ' . $active3 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-bill="' . $row->bill_number . '" data-value="3">Reject</a></li>
                                    <li><a class="dropdown-item change-status ' . $active4 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-bill="' . $row->bill_number . '" data-value="4">Dispatch</a></li>
                                    </ul>
                                </div>';
                        // // <li><a class="dropdown-item change-status ' . $active5 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-bill="' . $row->bill_number . '" data-value="5">Deliver</a></li>
                        return $html;
                    }
                })
                ->rawColumns(['action', 'order_status', 'user_type', 'order_by', 'created_at', 'total_amount', 'address', 'quantity'])
                ->make(true);
        } else {
            return view('order.view_order');
        }
    }

    public function customerview(Request $request)
    {
        $userId = Auth::id();
        $status = $request->status;
        $name = $request->name;
        $where = "1 = 1";
        if ($status == 'All') {
            if (!is_null($name)) {
                $where .= ' AND (orders.name = "' . $name . '")';
            } else {
                $where = "1 = 1";
            }
        } else {
            if (!is_null($name)) {
                $where .= ' AND (orders.order_status = ' . $status . ' AND orders.name = "' . $name . '")';
            } else {
                $where .= ' AND (orders.order_status = "' . $status . '")';
            }
        }

        if ($request->has('is_outstock') && $request->is_outstock === 'true') {
            $where .= ' AND (orders.is_outstock = 1)';
        } else {
            $where .= ' AND (orders.is_outstock = 0)'; // hide this if you want to show all orders including out of stock
        }

        if (!empty($request->s_date) && !empty($request->e_date)) {
            $s_date = date('Y-m-d', strtotime($request->s_date)) . ' 00:00:00';
            $e_date = date('Y-m-d', strtotime($request->e_date)) . ' 23:59:59';
            $where .= ' AND (orders.created_at >= "' . $s_date . '" AND orders.created_at <= "' . $e_date . '")';
        }


        if (request()->ajax()) {

            return DataTables::of(Order::with('user')->select('orders.*')->where('user_id', $userId)->whereRaw($where)->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    // $html .= '<a data-id="' . $row->id . '" role="button" href="' . route('generate-pdf', $row->id) . '" class="pdf avatar bg-light p-50 m-0 text-dark" data-bs-toggle="tooltip" data-placement="left" title="Download PDF"><i class="fa fa-download"></i></a>';
                    $html .= '<a data-id="' . $row->id . '" role="button" href="javascript:void(0)" class="mx-1 view_product avatar bg-light-success p-50 m-0 text-success" data-bs-toggle="tooltip" data-placement="left" title="View"><i class="fa fa-eye"></i></a>';
                    $html .= ' <a href="' . route('order.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    if ($row->order_status == 1) {
                        $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="mx-1 reject avatar bg-light-danger p-50 m-0 text-danger" data-bs-toggle="tooltip" data-placement="left" title="Cancel"><i class="fa fa-cancel"></i></a>';
                    } else if ($row->order_status == 4) {
                        $html .= '<a data-id="' . $row->id . '" 
                                        data-order-id="' . $row->order_id . '" 
                                        data-status="' . $row->status . '" 
                                        data-lr-number="' . $row->lr_number . '" 
                                        data-lr-date="' . $row->lr_date . '" 
                                        data-cases="' . $row->cases . '" 
                                        data-lr-photo="' . asset('upload/order/' . $row->lr_photo) . '" 
                                        href="javascript:void(0);" 
                                        class="dispatch avatar bg-light-primary p-50 mx-1 text-primary" 
                                        data-bs-toggle="tooltip" 
                                        data-placement="left" 
                                        title="Dispatch Info">
                                        <i class="fa fa-truck"></i>
                                    </a>';
                    }
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('order_by', function ($row) {
                    if ($row->order_by == '0') {
                        return 'Self';
                    } else {
                        return 'Company';
                    }
                })
                ->editColumn('quantity', function ($row) {
                    if ($row->total_box != 0 && $row->total_box != '') {
                        return $row->total_box;
                    } else {
                        return '0';
                    }
                })
                ->editColumn('created_at', function ($row) {
                    return date('d/m/Y H:i', strtotime($row->created_at));
                })
                ->editColumn('total_amount', function ($row) {
                    return number_format($row->total_amount, 2);
                })
                ->addColumn('order_status', function ($row) {
                    $statusMap = [
                        1 => ['class' => 'btn-outline-warning', 'text' => 'Pending'],
                        2 => ['class' => 'btn-outline-success', 'text' => 'Active'],
                        3 => ['class' => 'btn-outline-secondary', 'text' => 'Reject'],
                        4 => ['class' => 'btn-outline-danger', 'text' => 'Dispatch'],
                        // 5 => ['class' => 'btn-outline-primary', 'text' => 'Deliver'],
                    ];

                    if (isset($statusMap[$row->order_status])) {
                        $html = '<button type="button" class="btn btn-sm ' . $statusMap[$row->order_status]['class'] . ' w-100" disabled>' . $statusMap[$row->order_status]['text'] . '</button>';
                        return $html;
                    } else {
                        return '<span class="text-muted">Unknown</span>'; // Fallback for unknown status
                    }
                })
                ->rawColumns(['action', 'order_status', 'order_by', 'created_at', 'total_amount', 'quantity'])
                ->make(true);
        } else {
            return view('customer.customer_order');
        }
    }

    public function customOrderUpdate($id, Request $request)
    {
        $order = Order::where('id', $id)->first();
        $order->order_status = $request->status;
        if ($request->has('remarks')) {
            $order->remarks = $request->remarks;
        }
        $order->save();

        // Send push notification for order status update
        $statusText = $this->getStatusText($request->status);
        if ($statusText) {
            app(\App\Services\PushNotificationService::class)->sendOrderStatusNotification($order, $statusText);
        }

        // ✅ Reformat the row data as per DataTables structure
        $formattedOrder = [
            'id' => $order->id,
            'order_status' => $this->getOrderStatusHtml($order->order_status), // ✅ Use function to format status
            'order_by' => ($order->order_by == 0) ? 'Self' : 'Company',
            'order_id' => $order->order_id,
            'created_at' => date('d/m/Y H:i', strtotime($order->created_at)),
            'quantity' => ($order->total_box != 0 && $order->total_box != '') ? $order->total_box : '0',
            'total_amount' => number_format($order->total_amount, 2),
            'action' => $this->getActionButtons($order) // ✅ Function for action buttons
        ];

        $response = ['status' => true, 'message' => 'Order Status Updated Successfully.', 'data' => $formattedOrder];
        return response()->json($response);
    }

    // ✅ Function to format order status HTML
    private function getOrderStatusHtml($status)
    {
        $statusMap = [
            1 => ['class' => 'btn-outline-warning', 'text' => 'Pending'],
            2 => ['class' => 'btn-outline-success', 'text' => 'Active'],
            3 => ['class' => 'btn-outline-secondary', 'text' => 'Reject'],
            4 => ['class' => 'btn-outline-danger', 'text' => 'Dispatch'],
            // 5 => ['class' => 'btn-outline-primary', 'text' => 'Deliver'],
        ];

        if (isset($statusMap[$status])) {
            return '<button type="button" class="btn btn-sm ' . $statusMap[$status]['class'] . ' w-100" disabled>' . $statusMap[$status]['text'] . '</button>';
        }

        return '<span class="text-muted">Unknown</span>'; // Fallback
    }

    // ✅ Function to return action buttons
    private function getActionButtons($order)
    {
        $html = '<div class="d-flex">';
        // $html .= '<a data-id="' . $order->id . '" role="button" href="' . route('generate-pdf', $order->id) . '" class="pdf avatar bg-light p-50 m-0 text-dark" data-bs-toggle="tooltip" data-placement="left" title="Download PDF"><i class="fa fa-download"></i></a>';
        $html .= '<a data-id="' . $order->id . '" role="button" href="javascript:void(0)" class="mx-1 view_product avatar bg-light-success p-50 m-0 text-success" data-bs-toggle="tooltip" data-placement="left" title="View"><i class="fa fa-eye"></i></a>';
        $html .= ' <a href="' . route('order.edit', $order->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';

        if ($order->order_status != 3) {
            $html .= ' <a data-id="' . $order->id . '" href="javascript:void(0);" id="confirm-text" class="mx-1 reject avatar bg-light-danger p-50 m-0 text-danger" data-bs-toggle="tooltip" data-placement="left" title="Cancel"><i class="fa fa-cancel"></i></a>';
        }

        $html .= '</div>';
        return $html;
    }

    // ✅ Function to get status text for notifications
    private function getStatusText($status)
    {
        $statusMap = [
            1 => 'Pending',
            2 => 'Active',
            3 => 'Rejected',
            4 => 'Dispatched',
            5 => 'Delivered',
        ];

        return $statusMap[$status] ?? null;
    }


    public function generatePDF($id)
    {
        $order = Order::with('orderTransection', 'orderTransection.product')->where('id', $id)->first();

        $name = $order->order_id;
        $base64 = 'data:image/png;base64,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';
        $data = [
            'title' => 'Your Order',
            'order' => $order,
            'base64' => $base64,
        ];
        $pdf = PDF::loadView('order.generate_pdf', $data);
        return $pdf->download($name . '.pdf');
    }

    public function create()
    {
        $category = Category::get();
        $user = User::where('status', '1')->whereHas(
            'roles',
            function ($q) {
                $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
            }
        )->get();
        return view('order.add_order', compact('category', 'user'));
    }

    public function store(Request $request)
    {

        $mobile = 'nullable|numeric|digits:10';
        $address = 'nullable';

        $validator = Validator::make($request->all(), [
            'mobile' => $mobile,
            'address' => $address,
        ], [
            'mobile.required' => 'Enter Mobile.',
            'address.required' => 'Enter Address.',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            $id = $request->user_id;
            $user = User::where('id', $id)->first();
            $userProfile = UserProfile::where('user_id', $user->id)->first();
            if (!is_null($request->order_id)) {
                $order = Order::where('id', $request->order_id)->first();
                $order->user_id = $user->id;
                $order->order_by = '1';     //1 for order from admin, 0 fro customer

                $order->name = $user->name;
                $order->mobile = $user->mobile;
                $order->address = $userProfile->address;

                $box = 0;
                $price_amount = 0;
                $final_amount = 0;
                $grand_total = 0;
                if (count($request->invoice) > 0) {
                    foreach ($request->invoice as $key => $value) {
                        if (!is_null($value['quantity']) && !is_null($value['product_id'])) {
                            if (!is_null($value['order_transection_id'])) {
                                $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
                                $product = Product::where('id', $value['product_id'])->first();

                                if (!is_null($product)) {
                                    $box += $value['quantity'];
                                    $price_amount += $value['price'];

                                    $final_amount += ($value['quantity'] * $value['price']);

                                    $product->quantity = ($product->quantity - $orderTransection->pices) + $value['quantity'];
                                    $product->save();
                                } else {
                                    $response = ['status' => false, 'message' => 'Product not exist.'];
                                    return response()->json($response);
                                }
                            } else {
                                $product = Product::where('id', $value['product_id'])->first();
                                if (!is_null($product)) {
                                    $box += $value['quantity'];
                                    $price_amount += $value['price'];

                                    $final_amount += ($value['quantity'] * $value['price']);

                                    $product->quantity = $product->quantity + $value['quantity'];
                                    $product->save();
                                } else {
                                    $response = ['status' => false, 'message' => 'Product not valid.'];
                                    return response()->json($response);
                                }
                            }
                        } else {
                            $response = ['status' => false, 'message' => 'Please input proper data.'];
                            return response()->json($response);
                        }
                    }
                }
                $order->total_box = $box;
                $order->total_pices = 0;

                $order->total_amount = $final_amount;
                $result = $order->save();
                DB::commit();

                if (!is_null($result)) {
                    foreach ($request->invoice as $key => $value) {
                        $product = Product::where('id', $value['product_id'])->first();

                        $orderTransection = new OrderTransection();
                        if (!is_null($value['order_transection_id'])) {
                            $orderTransection = OrderTransection::where('id', $value['order_transection_id'])->first();
                        } else {
                            $orderTransection = new OrderTransection();
                        }
                        //extra
                        $orderTransection->order_id = $order->id;
                        $orderTransection->category_id = $value['category_id'];
                        $orderTransection->product_id = $value['product_id'];

                        $orderTransection->box = $value['quantity'];
                        $orderTransection->amount = $product->price;

                        $orderTransection->per_box_pices = $product->box;
                        $orderTransection->pices = 0;

                        $orderTransection->total_amount = ($product->price) * ($value['quantity']);
                        $orderTransection->save();
                    }
                    $response = ['data' => route('order.index'), 'status' => true, 'message' => ' Order Updated Successfully.'];
                    return response()->json($response);
                } else {
                    $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                    return response()->json($response);
                }
            } else {
                $order = new Order();
                $order->user_id = $user->id;
                $order->order_by = '1';      //1 for order from admin, 0 fro customer

                $order->name = $user->name;
                $order->mobile = $user->mobile;
                $order->address = $userProfile->address;

                $order->order_id = $this->orderIdGenerate();
                $order->order_status = 1;

                $box = 0;
                $price_amount = 0;
                $final_amount = 0;
                $grand_total = 0;

                if (count($request->invoice) > 0) {
                    foreach ($request->invoice as $key => $value) {
                        if (!is_null($value['quantity']) && !is_null($value['product_id']) && !is_null($value['price'])) {
                            $product = Product::where('id', $value['product_id'])->first();
                            if (!is_null($product)) {
                                $box += $value['quantity'];
                                $price_amount += $value['price'];

                                $final_amount += ($value['quantity'] * $value['price']);

                                $product->quantity = $product->quantity + $value['quantity'];
                                $product->save();
                            } else {
                                $response = ['status' => false, 'message' => 'Product not valid..'];
                                return response()->json($response);
                            }
                        } else {
                            $response = ['status' => false, 'message' => 'Please input proper data!'];
                            return response()->json($response);
                        }
                    }
                }
                $order->total_box = $box;
                $order->total_pices = 0;
                $order->total_amount = $final_amount;
                $result = $order->save();
                DB::commit();

                if (!is_null($result)) {
                    foreach ($request->invoice as $key => $value) {
                        $product = Product::where('id', $value['product_id'])->first();
                        $orderTransection = new OrderTransection();
                        $orderTransection->order_id = $order->id;
                        $orderTransection->category_id = $value['category_id'];
                        $orderTransection->product_id = $value['product_id'];

                        $orderTransection->box = $value['quantity'];
                        $orderTransection->amount = $product->price;

                        $orderTransection->per_box_pices = $product->box;
                        $orderTransection->pices = 0;

                        $orderTransection->total_amount = ($product->price) * ($value['quantity']);
                        $orderTransection->save();
                    }
                    $response = ['data' => route('order.index'), 'status' => true, 'message' => 'Order Added Successfully.'];
                    return response()->json($response);
                } else {
                    $response = ['status' => false, 'server_error' => 'Something went wrong. Please try again..'];
                    return response()->json($response);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'server_error' => 'Something went wrong. Please try again...'];
            // return response()->json($response);
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    public function cartsubmit(Request $request)
    {

        DB::beginTransaction();
        try {
            $id = Auth::id();
            $user = User::where('id', $id)->first();
            $userProfile = UserProfile::where('user_id', $user->id)->first();


            $order = new Order();
            $order->user_id = $user->id;
            $order->order_by = '0';      //1 for order from admin, 0 fro customer

            $order->name = $user->name;
            $order->mobile = $user->mobile;
            $order->address = $userProfile->address;

            $order->order_id = $this->orderIdGenerate();
            $order->order_status = 1;

            $box = 0;
            $price_amount = 0;
            $final_amount = 0;
            $grand_total = 0;

            $cartItems = Cart::where('user_id', $user->id)->get();

            if ($cartItems->count() > 0) {
                foreach ($cartItems as $cartItem) {
                    $product = Product::where('id', $cartItem->product_id)->first();

                    if (!is_null($product)) {
                        $box += $cartItem->quantity;
                        $price_amount += $product->price;

                        $final_amount += ($cartItem->quantity * $product->price);

                        $product->quantity = $product->quantity + $cartItem->quantity;
                        $product->save();
                    } else {
                        $response = ['status' => false, 'message' => 'Product not valid..'];
                        return response()->json($response);
                    }
                }
            } else {
                $response = ['status' => false, 'message' => 'Cart is Empty!'];
                return response()->json($response);
            }
            $order->total_box = $box;
            $order->total_pices = 0;
            $order->total_amount = $final_amount;
            $result = $order->save();
            DB::commit();

            if (!is_null($result)) {
                foreach ($cartItems as $cartItem) {
                    $product = Product::where('id', $cartItem->product_id)->first();

                    $orderTransection = new OrderTransection();
                    $orderTransection->order_id = $order->id;
                    $orderTransection->category_id = $product->category->id;
                    $orderTransection->product_id = $cartItem->product_id;
                    $orderTransection->box = $cartItem->quantity;

                    $orderTransection->amount = $product->price;
                    $orderTransection->per_box_pices = $product->box;
                    $orderTransection->pices = 0;

                    $orderTransection->total_amount = ($product->price) * ($cartItem->quantity);
                    $orderTransection->save();
                }
                Cart::where('user_id', $user->id)->delete();

                $response = ['data' => route('order.index'), 'status' => true, 'message' => 'Order Added Successfully.'];
                return response()->json($response);
            } else {
                $response = ['status' => false, 'message' => 'Something went wrong. Please try again..'];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again...'];
            // return response()->json($response);
            return response()->json([
                'status' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }

    // for replace item
    public function directOrderFromSerial(Request $request)
    {
        // DB::beginTransaction();
        try {
            $id = Auth::id();
            $user = User::find($id);
            $userProfile = UserProfile::where('user_id', $user->id)->first();

            if (!$user || !$userProfile) {
                return response()->json(['status' => false, 'message' => 'User not found.']);
            }

            // Validate request
            $validatedData = $request->validate([
                'serial_no_id' => 'required|exists:serialno,id', // Ensure serial_id exists in serialno table
            ]);

            // Get Serial Number details
            $serialNo = SerialNo::find($validatedData['serial_no_id']);

            if (!$serialNo || !$serialNo->product) {
                return response()->json(['status' => false, 'message' => 'Invalid Serial Number.']);
            }

            $order = $this->replaceOrder($serialNo);

            return response()->json([
                'status' => true,
                'message' => 'Order Created Successfully',
                'order_id' => $order->order_id
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again...',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        }
    }


    public function replaceOrder($serialNo, $status = null, $newSerialNo = null)
    {
        DB::beginTransaction();
        try {
            $id = Auth::id();
            $user = User::where('id', $id)->first();
            $userProfile = UserProfile::where('user_id', $user->id)->first();
            
            $product = $serialNo->product; // Get associated product

            // Create new order
            $order = new Order();
            $order->user_id = $user->id;
            $order->order_by = '0'; // 0 = Customer order
            $order->name = $user->name;
            $order->mobile = $user->mobile;
            $order->address = $userProfile->address;
            $order->order_id = $this->orderIdGenerate();
            $order->order_status = 1;
            $order->total_box = 1;
            $order->total_pices = 0;
            $order->total_amount = $product->price;
            if ($status && $newSerialNo) {
                // In Stock; Old sn: 1234; New sn 1234
                $order->remarks = $status . '; Old sn: ' . $serialNo->sn . '; New sn : ' . $newSerialNo;
            } else {
                $order->remarks = 'Out of Stock; Old sn: ' . $serialNo->sn;
            }
            $order->is_outstock = true;
            $order->save();

            // Insert into Order Transactions
            $orderTransection = new OrderTransection();
            $orderTransection->order_id = $order->id;
            $orderTransection->category_id = $product->category->id ?? null;
            $orderTransection->product_id = $product->id;
            $orderTransection->box = 1; // Quantity is always 1
            $orderTransection->amount = $product->price;
            $orderTransection->per_box_pices = $product->box ?? 0;
            $orderTransection->pices = 0;
            $orderTransection->total_amount = $product->price;
            $orderTransection->save();

            // update serial number with reject status
            $serialNo->is_reject = true;
            $serialNo->location_status = "In Store"; // Company Received 
            $serialNo->save();

            DB::commit();

            return $order;

        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
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

    public function show($id)
    {
        $order = Order::where('id', $id)->first();
        $orderTransection = OrderTransection::with('product')->where('order_id', $order->id)->get();
        $data['html'] = view('order.order_product', compact('orderTransection', 'order'))->render();
        return response()->json($data);
    }

    public function edit($id)
    {
        $order = Order::with('orderTransection')->where('id', $id)->first();
        $category = Category::get();
        foreach ($order->orderTransection as $key => $item) {
            // $item->subCategory = SubCategory::where('category_id', $item->category_id)->get();
            // $item->product = Product::where('sub_category_id', $item->sub_category_id)->get();

            $item->product = Product::where('category_id', $item->category_id)->get();
        }
        $role = Auth::user()->roles[0]->name;

        $user = User::where('status', '1')
            ->when($role === 'customer', fn($q) => $q->where('id', Auth::id()))
            ->when($role !== 'customer', fn($q) => $q->whereHas(
                'roles',
                fn($q) =>
                $q->whereIn('name', ['distributor', 'dealer', 'customer'])
            ))
            ->get();

        // below is retrieve 
        // $user = User::where('status', '1')->whereHas(
        //     'roles',
        //     function ($q) {
        //         $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
        //     }
        // )->get();

        return view('order.add_order', compact('category', 'user', 'order'));
    }

    public function update($id, Request $request)
    {
        $order = Order::where('id', $id)->first();

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $order->order_status = $request->status;
        $order->bill_number = $request->no;
        if ($request->has('remarks')) {
            $order->remarks = $request->remarks;
        }
        // If status is 4 (Dispatched), save additional details
        if ($request->status == 4) {

            if ($request->hasFile('lr_photo')) {
                // Delete old image
                if ($order->lr_photo) {
                    $path = 'upload/order/' . $order->lr_photo;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }

                // Upload new image
                $file = $request->file('lr_photo');
                $filename = $order->order_id . '-' . time() . rand() . '.webp';
                $file->move('upload/order/', $filename);

                $order->lr_photo = $filename;
            }

            $order->lr_number = $request->lr_number;
            $order->lr_date = $request->lr_date;
            $order->cases = $request->cases;
        }

        $order->save();

        // Send push notification for order status update
        $statusText = $this->getStatusText($request->status);
        if ($statusText) {
            app(\App\Services\PushNotificationService::class)->sendOrderStatusNotification($order, $statusText);
        }

        return response()->json(['status' => true, 'message' => 'Order Status Updated Successfully.']);
    }


    public function destroy($id)
    {
        $order = Order::find($id);
        if (!is_null($order)) {
            $orderTransection = OrderTransection::where('order_id', $order->id)->get();
            foreach ($orderTransection as $item) {
                $product = Product::where('id', $item->product_id)->first();
                $product->quantity = $product->quantity + $item->pices;
                $product->save();
                $item->delete();
            }
            $path = 'upload/product/' . $order->lt_photo;
            if ($order->lt_photo) {
                if (File::exists($path)) {
                    unlink($path);
                }
            }
            $order->delete();
            $response = ['status' => true, 'message' => 'Order Deleted Successfully.'];
            return response()->json($response);
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response()->json($response);
        }
    }

    public function removeSingleOrder(Request $request)
    {
        $orderTransection = OrderTransection::find($request->id);
        $order = Order::where('id', $orderTransection->order_id)->count();
        if ($order > 2) {
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
                $response = ['status' => true, 'message' => 'Product Remove Successfully.'];
                return response($response, 200);
            } else {
                $response = ['status' => false, 'message' => 'This Record does not exist.'];
                return response($response, 201);
            }
        } else {
            $response = ['status' => false, 'message' => 'Order item greater than or equal to 2.'];
            return response($response, 201);
        }
    }

    public function removeSingleItem(Request $request)
    {
        $orderTransection = OrderTransection::find($request->id);
        $order = Order::where('id', $orderTransection->order_id)->count();
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
            $response = ['status' => true, 'message' => 'Product Remove Successfully.'];
            return response()->json($response);
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response()->json($response);
        }
    }
}
