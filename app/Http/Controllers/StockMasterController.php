<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\StockMaster;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class StockMasterController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(StockMaster::with('product')->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('stock-master.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            return view('stock_master.view_stock_master');
        }
    }

    public function create()
    {
        $category = Category::get();
        $product = Product::get();
        return view('stock_master.add_stock_master', compact('product','category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required',
            'product_id' => 'required',
        ], [
            'quantity.required' => 'Enter Quantity',
            'product_id.required' => 'Select Product',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->stock_id)) {
                $stockMaster = StockMaster::where('id', $request->stock_id)->first();
                $response = ['data' => route('stock-master.index'), 'status' => true, 'message' => ' Stock Master Updated Successfully.'];
            } else {
                $stockMaster = new StockMaster();
                $response = ['data' => route('stock-master.index'), 'status' => true, 'message' => ' Stock Master Added Successfully.'];
            }
            $stockMaster->product_id = $request->product_id;
            $stockMaster->category_id = $request->category_id;
            $stockMaster->sub_category_id = $request->sub_category_id;
            $stockMaster->quantity = $request->quantity;
            $result = $stockMaster->save();
            DB::commit();
            if (!is_null($result)) {
                $stockMaster = StockMaster::where('product_id', $request->product_id)->sum('quantity');
                $orderTransection = OrderTransection::where('product_id', $request->product_id)->sum('pices');
                $product = Product::where('id', $request->product_id)->first();
                $product->quantity = $stockMaster - $orderTransection;
                $product->save();
                return response()->json($response);
            } else {
                $response = ['status' => true, 'server_error' => 'Something went wrong. Please try again.'];
                return response()->json($response);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => true, 'server_error' => 'Something went wrong. Please try again.'];
            return response()->json($response);
        }
    }

    public function show($id)
    {
        $product = Product::where('sub_category_id',$id)->get();
        return response()->json($product);
    }

    public function edit($id)
    {
        $product = Product::get();
        $category = Category::get();
        $subCategory = SubCategory::get();
        $stockMaster = StockMaster::where('id', $id)->first();
        return view('stock_master.add_stock_master', compact('stockMaster', 'product','category','subCategory'));
    }

    public function update(Request $request, StockMaster $stockMaster)
    {
        //
    }

    public function destroy(StockMaster $stockMaster)
    {
        try {
            $stockMaster = StockMaster::where('id', $stockMaster->id)->first();
            $product = Product::where('id', $stockMaster->product_id)->first();
            $product->quantity = $product->quantity - $stockMaster->quantity;
            $product->save();
            $stockMaster->delete();
            return response()->json(array('success' => 1, "errorMessage" => 'Stock Master Deleted'));
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
