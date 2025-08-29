<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderTransection;
use App\Models\Product;
use App\Models\StockMaster;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{

    public function index(Request $request)
    {

        // do not let customer access this page
        if (Auth::user()->roles[0]->name == 'customer') {
            return redirect('/');
        }
        $where = "1 = 1";
        if ($request->category_id != 'All') {
            $where .= ' AND products.category_id = ' . $request->category_id;
        }

        if (request()->ajax()) {
            return DataTables::of(Product::with('category')->whereRaw($where)->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a href="' . route('product.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    $imagePath = htmlspecialchars($row->image, ENT_QUOTES, 'UTF-8');
                    $image2Path = $row->image2 ? asset('upload/product/' . $row->image2) : '';
                    $image3Path = $row->image3 ? asset('upload/product/' . $row->image3) : '';
                    $mainImage = $row->image ? asset('upload/product/' . $row->image) : '';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="#" class="product-image-modal-trigger" data-image="' . $mainImage . '" data-image2="' . $image2Path . '" data-image3="' . $image3Path . '">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . $mainImage . '" alt="' . $row->product_name . '" title="' . $row->product_name . '">';
                        $html .= '</a>';
                    } else {
                        $html .= ' ';
                    }
                    $html .= '</td>';
                    return $html;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        } else {
            $category = Category::get();
            return view('product.view_product', compact('category'));
        }
    }

    public function indexOld(Request $request)
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

        if (request()->ajax()) {
            return DataTables::of(Product::with('category', 'subCategory')->whereRaw($where)->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a href="' . route('product.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="' . asset('upload/product/' . $row->image) . '" data-fancybox="gallery_' . $row->id . '" data-caption="' . $row->product_name . '" class="gallary-item-overlay">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . asset('upload/product/' . $row->image) . '" alt="' . $row->product_name . '" title="' . $row->product_name . '">';
                        $html .= '</a>';
                    } else {
                        $html .= ' ';
                    }
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('sub_category_name', function ($row) {
                    return $row->subCategory->sub_category_name;
                })
                ->rawColumns(['action', 'image', 'sub_category_name'])
                ->make(true);
        } else {
            $category = Category::get();
            $subCategory = SubCategory::get();
            return view('product.view_product', compact('category', 'subCategory'));
        }
    }

    public function create()
    {
        $category = Category::get();
        return view('product.add_product', compact('category'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_code' => 'required',
            'box' => 'required',
            'price' => 'required',
            'category_id' => 'required',
            'product_name' => [
                'required',
                Rule::unique('products')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)->where('sub_category_id', $request->sub_category_id)->whereNull('deleted_at');
                })->ignore($request->product_id),
            ],
        ], [
            'product_name.required' => 'Enter Product',
            'product_name.unique' => 'The Product has already been taken',
            'category_id.required' => 'Select Category',
            'product_code.required' => 'Enter Product Code',
            'box.required' => 'Enter Box',
            'price.required' => 'Enter Price',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            // $stockMaster = '';
            if (!is_null($request->product_id)) {
                $product = Product::where('id', $request->product_id)->first();
                $response = ['data' => route('product.index'), 'status' => true, 'message' => ' Product Updated Successfully.'];
            } else {
                $product = new Product();
                // $stockMaster = new StockMaster();
                $response = ['data' => route('product.index'), 'status' => true, 'message' => ' Product Added Successfully.'];
            }
            $product->category_id = $request->category_id;
            // $product->sub_category_id = $request->sub_category_id;
            $product->product_name = ucwords($request->product_name);
            $product->product_code = $request->product_code;
            // $product->quantity = $request->quantity;
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
            // Handle image2 (optional)
            if ($request->hasfile('image2')) {
                if ($product->image2) {
                    $path = 'upload/product/' . $product->image2;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $PhotosDir = 'upload/product/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file2 = $request->file('image2');
                $filename2 = $request->product_name . '-2-' . time() . rand() . '.webp';
                $file2->move('upload/product/', $filename2);
                $product->image2 = $filename2;
            }
            // Handle image3 (optional)
            if ($request->hasfile('image3')) {
                if ($product->image3) {
                    $path = 'upload/product/' . $product->image3;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $PhotosDir = 'upload/product/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file3 = $request->file('image3');
                $filename3 = $request->product_name . '-3-' . time() . rand() . '.webp';
                $file3->move('upload/product/', $filename3);
                $product->image3 = $filename3;
            }
            $result = $product->save();
            DB::commit();
            if (!is_null($result)) {
                // if ($stockMaster) {
                //     $stockMaster->category_id = $request->category_id;
                //     $stockMaster->sub_category_id = $request->sub_category_id;
                //     $stockMaster->quantity = $request->quantity;
                //     $stockMaster->product_id = $product->id;
                //     $stockMaster->save();
                // }
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
        $subCategory = SubCategory::where('category_id', $id)->get();
        return response()->json($subCategory);
    }
    public function productFromCat($id)
    {
        // Get all products for the given category_id
        $products = Product::where('category_id', $id)->get();
    
        // If no products are found, return an empty array
        if ($products->isEmpty()) {
            return response()->json(['product' => []]);
        }
    
        // Return the products data as a response
        return response()->json(['product' => $products]);
    }
    
    
    public function edit($id)
    {
        $category = Category::get();
        $subCategory = SubCategory::get();
        $product = Product::where('id', $id)->first();
        return view('product.add_product', compact('category', 'subCategory', 'product'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('id', $request->user_id)->first();
        $subCategory = SubCategory::with('product')->where('id', $id)->first();
        if ($user->hasRole('distributor')) {
            $subCategory['role'] = 'distributor';
        } else if ($user->hasRole('dealer')) {
            $subCategory['role'] = 'dealer';
        } else {
            $subCategory['role'] = 'customer';
        }
        return response()->json($subCategory);
    }

    public function productChange(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        try {
            $product = Product::where('id', $product->id)->first();
            $orderTransection = OrderTransection::where('product_id', $product->id)->get();
            $order = 0;
            foreach ($orderTransection as $item) {
                $order += Order::where('id', $item->order_id)->count();
            }
            if ($order == 0) {
                if (!is_null($product)) {
                    $stockMaster = StockMaster::where('product_id', $product->id)->get();
                    foreach ($stockMaster as $item) {
                        $item->delete();
                    }
                }
                $path = 'upload/product/' . $product->image;
                if ($product->image) {
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $product->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'Product Deleted'));
            } else {
                return response()->json(array('warning' => 1, "errorMessage" => 'This Product Exists in Order'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
