<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\StockMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        if (!is_null($request->start) && !is_null($request->length) && !is_null($request->category_id) && !is_null($request->sub_category_id)) {
            $product = Product::with('category', 'subCategory')->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->skip($request->start)->take($request->length)->get();
        } elseif ((!is_null($request->category_id) || !is_null($request->sub_category_id))) {
            $product = Product::with('category', 'subCategory')->where('category_id', $request->category_id)->orwhere('sub_category_id', $request->sub_category_id)->get();
        } else {
            $product = Product::with('category', 'subCategory')->get();
        }
        if (count($product) > 0) {
            $path = asset('upload/product/');
            foreach ($product as $key => $value) {
                if (!is_null($value->image)) {
                    $value['image'] = $path . '/' . $value->image;
                } else {
                    $value['image'] = '';
                }
                $value['category_name'] = $value->category->category_name;
                $value['sub_category_name'] = $value->subCategory->sub_category_name;
                unset($value->category_id, $value->sub_category_id, $value->category, $value->subCategory, $value->deleted_at, $value->created_at, $value->updated_at);
            }
            $response = ['status' => true, 'message' => 'Product Listings.', 'product' => $product];
            return response($response, 200);
        } else {
            $response = ['status' => true, 'message' => 'Product Not Found.'];
            return response($response, 200);
        }
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'product_code' => 'required',
            'quantity' => 'required',
            'box' => 'required',
            'price' => 'required',
            'product_name' => [
                'required',
                // Rule::unique('products')->where(function ($query) use ($request) {
                //     return $query->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id);
                // }),
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->whereNull('deleted_at');
                })->ignore($request->product_id),
            ],
        ], [
            'product_name.required' => 'Enter Product Name.',
            'product_name.unique' => 'The Product has already been taken.',
            'category_id.required' => 'Select Category.',
            'sub_category_id.required' => 'Select Sub Category.',
            'product_code.required' => 'Enter Product Code.',
            'quantity.required' => 'Enter Quantity.',
            'box.required' => 'Enter Box.',
            'price.required' => 'Enter Price.',
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
            $stockMaster = '';
            if (!is_null($request->product_id)) {
                $product = Product::where('id', $request->product_id)->first();
                $response = ['status' => true, 'message' => 'Product Updated Successfully.'];
            } else {
                $product = new Product();
                $stockMaster = new StockMaster();
                $response = ['status' => true, 'message' => 'Product Added Successfully.'];
            }
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->product_name = $request->product_name;
            $product->product_code = $request->product_code;
            if (!$request->product_id) {
                $product->quantity = $request->quantity;
            }
            $product->box = $request->box;
            $product->price = $request->price;
            $product->description = $request->description;
            if ($request->hasfile('image')) {
                //image update than old image remove
                if ($product->image) {
                    $path = 'upload/product/' . $product->image;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }

                $PhotosDir = 'upload/product/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file = $request->file('image');
                $filename = $request->product_name . '-' . time() . rand() . '.webp';
                $file->move('upload/product/', $filename);
                $product->image = $filename;
            }
            $result = $product->save();
            DB::commit();
            if (!is_null($result)) {
                if ($stockMaster) {
                    $stockMaster->quantity = $request->quantity;
                    $stockMaster->category_id = $request->category_id;
                    $stockMaster->sub_category_id = $request->sub_category_id;
                    $stockMaster->product_id = $product->id;
                    $stockMaster->save();
                }
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

    public function destroy(Request $request)
    {
        if (is_null($request->id)) {
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
        $product = Product::find($request->id);
        if (!is_null($product)) {
            $orderTransection = OrderTransection::where('product_id', $product->id)->get();
            $order = 0;
            foreach ($orderTransection as $item) {
                $order += Order::where('id', $item->order_id)->count();
            }
            if ($order > 0) {
                $response = ['status' => false, 'message' => 'This Product Exists in Order'];
                return response($response, 200);
            } else {
                $stockMaster = StockMaster::where('product_id', $product->id)->get();
                foreach ($stockMaster as $item) {
                    $item->delete();
                }
                $path = 'upload/product/' . $product->image;
                if ($product->image) {
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $product->delete();
                $response = ['status' => true, 'message' => 'Product Deleted Successfully.'];
                return response($response, 200);
            }
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response($response, 201);
        }
    }
}
