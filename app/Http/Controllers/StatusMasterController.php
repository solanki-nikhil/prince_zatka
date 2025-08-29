<?php

namespace App\Http\Controllers;

use App\Models\StatusMaster;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class StatusMasterController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(StatusMaster::orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('status-master.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('status_master.view_status_master');
        }
    }

    public function create()
    {
        return view('status_master.add_status_master');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status_name' => 'required|unique:status_masters,status_name',
        ], [
            'status_name.required' => 'Enter Status',
            'status_name.unique' => 'The Status Name has already been taken.'

        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->status_id)) {
                $statusMaster = StatusMaster::where('id', $request->status_id)->first();
                $response = ['data' => route('status-master.index'), 'status' => true, 'message' => ' Status Master Updated Successfully.'];
            } else {
                $statusMaster = new StatusMaster();
                $response = ['data' => route('status-master.index'), 'status' => true, 'message' => ' Status Master Added Successfully.'];
            }
            $statusMaster->status_name = $request->status_name;
            $result = $statusMaster->save();
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

    public function show(StatusMaster $statusMaster)
    {
        //
    }

    public function edit($id)
    {
        $statusMaster = StatusMaster::where('id', $id)->first();
        return view('status_master.add_status_master', compact('statusMaster'));
    }

    public function update(Request $request, StatusMaster $statusMaster)
    {
        //
    }

    public function destroy(StatusMaster $statusMaster)
    {
        try {
            $statusMaster = StatusMaster::where('id', $statusMaster->id)->first();
            $statusMaster->delete();
            return response()->json(array('success' => 1, "errorMessage" => 'Status Master Deleted'));
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
