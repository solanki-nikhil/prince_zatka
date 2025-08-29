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

class CityController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(City::with('country', 'state'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('city.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('city.view_city');
        }
    }

    public function create()
    {
        $country = Country::get();
        return view('city.add_city', compact('country'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required',
            'state_id' => 'required',
            'city_name' => [
                'required',
                Rule::unique('cities')->where(function ($query) use ($request) {
                    return $query->where('country_id', $request->country_id)->where('state_id',$request->state_id);
                })->ignore($request->city_id),
            ],
        ], [
            'city_name.required' => 'Enter City',
            'city_name.unique' => 'The City has already been taken.'

        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->city_id)) {
                $city = City::where('id', $request->city_id)->first();
                $response = ['data' => route('city.index'), 'status' => true, 'message' => ' City Updated Successfully.'];
            } else {
                $city = new City();
                $response = ['data' => route('city.index'), 'status' => true, 'message' => ' City Added Successfully.'];
            }
            $city->country_id = $request->country_id;
            $city->state_id = $request->state_id;
            $city->city_name = $request->city_name;
            $result = $city->save();
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

    public function show($id)
    {
        $state = State::where('country_id', $id)->get();
        return response()->json($state);
    }

    public function edit($id)
    {
        $country = Country::get();
        $state = State::get();
        $city = City::where('id', $id)->first();
        return view('city.add_city', compact('city', 'country', 'state'));
    }

    public function update($id)
    {
        $city = City::where('state_id', $id)->get();
        return response()->json($city);
    }

    public function destroy(City $city)
    {
        try {
            $city = City::where('id', $city->id)->first();
            $userProfile = UserProfile::where('city_id', $city->id)->count();
            if ($userProfile == 0) {
                $city->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'City Deleted'));
            } else {
                return response()->json(array('warning' => 1, "errorMessage" => 'This City Exists in User'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
