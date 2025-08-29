<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class SubCategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(SubCategory::with('category')->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('sub-category.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="' . asset('upload/subcategory/' . $row->image) . '" data-fancybox="gallery_' . $row->id . '" data-caption="' . $row->sub_category_name . '" class="gallary-item-overlay">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . asset('upload/subcategory/' . $row->image) . '" alt="' . $row->sub_category_name . '" title="' . $row->sub_category_name . '">';
                        $html .= '</a>';
                    } else {
                        $html .= ' ';
                    }
                    $html .= '</td>';
                    return $html;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('sub_category.view_sub_category');
        }
    }

    public function create()
    {
        $category = Category::get();
        return view('sub_category.add_sub_category', compact('category'));
    }

    public function store(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'distributor_discount' => 'required',
            'dealer_discount' => 'required',
            'category_id' => 'required',            
            'sub_category_name' => [
                'required',
                Rule::unique('sub_categories')->where(function ($query) use ($request) {
                    return $query->where('category_id', $request->category_id)->whereNull('deleted_at');
                })->ignore($request->sub_category_id),
            ],
        ], [
            'sub_category_name.required' => 'Enter Sub Category',
            'distributor_discount.required' => 'Enter Distributor Discount',
            'dealer_discount.required' => 'Enter Dealer Discount',
            'category_id.required' => 'Select Category',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->sub_category_id)) {
                $subCategory = subCategory::where('id', $request->sub_category_id)->first();
                $response = ['data' => route('sub-category.index'), 'status' => true, 'message' => ' Sub Category Updated Successfully.'];
            } else {
                $subCategory = new subCategory();
                $response = ['data' => route('sub-category.index'), 'status' => true, 'message' => ' Sub Category Added Successfully.'];
            }
            $subCategory->category_id = $request->category_id;
            $subCategory->sub_category_name = $request->sub_category_name;
            $subCategory->distributor_discount = $request->distributor_discount;
            $subCategory->dealer_discount = $request->dealer_discount;
            if ($request->hasfile('image')) {
                //image update than old image remove
                if ($subCategory->image) {
                    $path = 'upload/subcategory/' . $subCategory->image;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }

                $PhotosDir = 'upload/subcategory/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file = $request->file('image');
                $filename = $request->sub_category_name . '-' . time() . rand() . '.webp';
                $file->move('upload/subcategory/', $filename);
                $subCategory->image = $filename;
            }
            $result = $subCategory->save();
            DB::commit();
            if (!is_null($result)) {
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

    public function show(SubCategory $subCategory)
    {
        //
    }

    public function edit($id)
    {
        $category = Category::get();
        $subCategory = SubCategory::where('id', $id)->first();
        return view('sub_category.add_sub_category', compact('subCategory', 'category'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        //
    }

    public function destroy(SubCategory $subCategory)
    {
        try {
            $subCategory = SubCategory::where('id', $subCategory->id)->first();
            $product = Product::where('sub_category_id', $subCategory->id)->count();
            if ($product > 0) {
                return response()->json(array('warning' => 1, "errorMessage" => 'Already Exists this Category'));
            } else {
                $path = 'upload/subcategory/' . $subCategory->image;
                if ($subCategory->image) {
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $subCategory->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'Sub Category Deleted'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
