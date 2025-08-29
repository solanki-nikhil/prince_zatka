<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MediaMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class MediaMasterApiController extends Controller
{
    public function index(Request $request)
    {
        if (!is_null($request->start) && !is_null($request->length)) {
            $mediaMaster = MediaMaster::skip($request->start)->take($request->length)->get();
        } else {
            $mediaMaster = MediaMaster::get();
        }
        if (count($mediaMaster) > 0) {
            $path = asset('upload/media/');
            foreach ($mediaMaster as $key => $value) {
                if (!is_null($value->image)) {
                    $value['image'] = $path . '/' . $value->image;
                } else {
                    $value['image'] = '';
                }
                unset($value->deleted_at, $value->created_at, $value->updated_at);
            }
            $response = ['status' => true, 'message' => 'Media Listings.', 'media' => $mediaMaster];
            return response($response, 200);
        } else {
            $response = ['status' => true, 'message' => 'Media Not Found.'];
            return response($response, 200);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ], [
            'title.required' => 'Enter Title',
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
            if (!is_null($request->media_id)) {
                $mediaMaster = MediaMaster::where('id', $request->media_id)->first();
                $response = ['status' => true, 'message' => 'Media Master Updated Successfully.'];
            } else {
                $mediaMaster = new mediaMaster();
                $response = ['status' => true, 'message' => 'Media Master Added Successfully.'];
            }
            $mediaMaster->title = $request->title;
            $mediaMaster->description = $request->description;
            $mediaMaster->link = $request->link;
            if ($request->hasfile('image')) {
                //image update than old image remove
                if ($mediaMaster->image) {
                    $path = 'upload/media/' . $mediaMaster->image;
                    if (File::exists($path)) {
                        unlink($path);
                    }
                }

                $PhotosDir = 'upload/media/';
                if (!file_exists($PhotosDir)) {
                    mkdir($PhotosDir, 0777, true);
                }
                $file = $request->file('image');
                $filename = $request->title . '-' . time() . rand() . '.webp';
                $file->move('upload/media/', $filename);
                $mediaMaster->image = $filename;
            }
            $result = $mediaMaster->save();
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


    public function destroy(Request $request)
    {
        if (is_null($request->id)) {
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
        $mediaMaster = MediaMaster::find($request->id);
        if (!is_null($mediaMaster)) {            
            $path = 'upload/media/' . $mediaMaster->image;
            if ($mediaMaster->image) {
                if (File::exists($path)) {
                    unlink($path);
                }
            }
            $mediaMaster->delete();
            $response = ['status' => true, 'message' => 'Media Master Deleted Successfully.'];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response($response, 201);
        }
    }
}
