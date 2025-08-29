<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class CategoryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Category::orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('category.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="' . asset('upload/category/' . $row->image) . '" data-fancybox="gallery_' . $row->id . '" data-caption="' . $row->category_name . '" class="gallary-item-overlay">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . asset('upload/category/' . $row->image) . '" alt="' . $row->category_name . '" title="' . $row->category_name . '">';
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
            return view('category.view_category');
        }
    }

    public function create()
    {
        return view('category.add_category');
    }

    public function store(Request $request)
    {
        if (!is_null($request->category_id)) {
            $category = 'required|unique:categories,category_name,'.$request->category_id;
        }else{
            $category = 'required|unique:categories,category_name';
        }
        $validator = Validator::make($request->all(), [
            'category_name' => $category,
        ], [
            'category_name.required' => 'Enter Category',
            'category_name.unique' => 'The Category has already been taken.'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->category_id)) {
                $category = Category::where('id', $request->category_id)->first();
                $response = ['data' => route('category.index'), 'status' => true, 'message' => ' Category Updated Successfully.'];
            } else {
                $category = new Category();
                $response = ['data' => route('category.index'), 'status' => true, 'message' => ' Category Added Successfully.'];
            }
            $category->category_name = $request->category_name;
            if ($request->hasfile('image')) {
                //image update than old image remove
                if ($category->image) {
                    $path = 'upload/category/' . $category->image;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }

                $PhotosDir = 'upload/category/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file = $request->file('image');
                $filename = $request->category_name . '-' . time() . rand() . '.webp';
                $file->move('upload/category/', $filename);
                $category->image = $filename;
            }

            $result = $category->save();
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

    public function show(Category $category)
    {
        //
    }

    public function edit($id)
    {
        $category = Category::where('id', $id)->first();
        return view('category.add_category', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        //
    }

    public function destroy(Category $category)
    {
        try {
            $category = Category::where('id', $category->id)->first();
            $subCatgory = SubCategory::where('category_id', $category->id)->count();
            if ($subCatgory > 0) {
                return response()->json(array('warning' => 1, "errorMessage" => 'Already Exists this Category'));
            } else {
                $path = 'upload/category/' . $category->image;
                if ($category->image) {
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }
                $category->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'Category Deleted'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
