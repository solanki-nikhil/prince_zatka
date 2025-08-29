<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class StateController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(State::with('country'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('state.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('state.view_state');
        }
    }

    public function create()
    {
        $country = Country::get();
        return view('state.add_state', compact('country'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [            
            'country_id' => 'required',
            'state_name' => [
                'required',
                Rule::unique('states')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id)->whereNull('deleted_at');
                })->ignore($request->state_id),
            ],
        ], [
            'state_name.required' => 'Enter State',
            'country_id.required' => 'Select Country',
            'state_name.unique' => 'The State has already been taken.'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->state_id)) {
                $state = State::where('id', $request->state_id)->first();
                $response = ['data' => route('state.index'), 'status' => true, 'message' => ' State Updated Successfully.'];
            } else {
                $state = new State();
                $response = ['data' => route('state.index'), 'status' => true, 'message' => ' State Added Successfully.'];
            }
            $state->country_id = $request->country_id;
            $state->state_name = $request->state_name;
            $result = $state->save();
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

    public function show(State $state)
    {
        //
    }

    public function edit($id)
    {
        $country = Country::get();
        $state = State::where('id', $id)->first();
        return view('state.add_state', compact('state', 'country'));
    }

    public function update(Request $request, State $state)
    {
        //
    }

    public function destroy(State $state)
    {
        try {
            $state = State::where('id', $state->id)->first();
            $userProfile = UserProfile::where('state_id', $state->id)->count();
            if ($userProfile == 0) {
                $city = City::where('state_id', $state->id)->get();
                foreach ($city as $item) {
                    $item->delete();
                }
                $state->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'State Deleted'));
            } else {
                return response()->json(array('warning' => 1, "errorMessage" => 'This State Exists in User'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
