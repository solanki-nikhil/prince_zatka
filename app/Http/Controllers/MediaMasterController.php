<?php

namespace App\Http\Controllers;

use App\Models\MediaMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\File;

class MediaMasterController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(MediaMaster::orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('media-master.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })                
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="' . asset('upload/media/' . $row->image) . '" data-fancybox="gallery_' . $row->id . '" data-caption="'. $row->title .'" class="gallary-item-overlay">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . asset('upload/media/' . $row->image) . '" alt="' . $row->title . '" title="' . $row->title . '">';
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
            return view('media_master.view_media_master');
        }
    }

    public function customermedia()
    {
        if (request()->ajax()) {
            return DataTables::of(MediaMaster::orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->editColumn('image', function ($row) {
                    // open video on dialog
                    $imagePath = !empty($row->image) ? asset('upload/media/' . $row->image) : asset('image/default.png');

                    if (!empty($row->link)) {
                        // Check if it's a YouTube link
                        preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|.*?[&?]v=))([\w-]{11})/', $row->link, $matches);

                        if (!empty($matches[1])) {
                            // YouTube: use Fancybox iframe
                            $embedUrl = "https://www.youtube.com/embed/" . $matches[1];
                            return '<a href="' . $embedUrl . '" data-fancybox data-type="iframe" data-caption="' . $row->title . '" class="gallery-item-overlay">
                                        <img class="img rounded" height="300" width="300" src="' . $imagePath . '" alt="' . $row->title . '" title="' . $row->title . '">
                                    </a>';
                        } else {
                            // Other web link: open in new tab
                            return '<a href="' . $row->link . '" target="_blank" rel="noopener" class="gallery-item-overlay">
                                        <img class="img rounded" height="300" width="300" src="' . $imagePath . '" alt="' . $row->title . '" title="' . $row->title . '">
                                    </a>';
                        }
                    } else {
                        // No link: just the image
                        return '<img class="img rounded" height="300" width="300" src="' . $imagePath . '" alt="' . $row->title . '" title="' . $row->title . '">';
                    }
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('customer.customer_media_master');
        }
    }
    

    public function create()
    {
        return view('media_master.add_media_master');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ], [
            'title.required' => 'Enter Title',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->media_id)) {
                $mediaMaster = MediaMaster::where('id', $request->media_id)->first();
                $response = ['data' => route('media-master.index'), 'status' => true, 'message' => ' Media Master Updated Successfully.'];
            } else {
                $mediaMaster = new mediaMaster();
                $response = ['data' => route('media-master.index'), 'status' => true, 'message' => ' Media Master Added Successfully.'];
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

    public function show(MediaMaster $mediaMaster)
    {
        //
    }

    public function edit($id)
    {
        $mediaMaster = MediaMaster::where('id', $id)->first();
        return view('media_master.add_media_master', compact('mediaMaster'));
    }

    public function update(Request $request, MediaMaster $mediaMaster)
    {
        //
    }

    public function destroy(MediaMaster $mediaMaster)
    {
        try {
            $mediaMaster = MediaMaster::where('id', $mediaMaster->id)->first();
            $path = 'upload/media/' . $mediaMaster->image;
            if ($mediaMaster->image) {
                if (File::exists($path)) {
                    unlink($path);
                }
            }
            $mediaMaster->delete();
            return response()->json(array('success' => 1, "errorMessage" => 'Media Master Deleted'));
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
