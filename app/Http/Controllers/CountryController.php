<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(Country::orderBy('id', 'desc'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<td>';
                    $html .= '<a href="' . route('country.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->escapeColumns([])
                ->make(true);
        } else {
            return view('country.view_country');
        }
    }

    public function create()
    {
        return view('country.add_country');
    }

    public function store(Request $request)
    {
        if (!is_null($request->country_id)) {
            $country = 'required|unique:countries,country_name,'.$request->country_id;
        }else{
            $country = 'required|unique:countries,country_name';
        }

        $validator = Validator::make($request->all(), [
            'country_name' => $country,
        ], [
            'country_name.required' => 'Enter Country',
            'country_name.unique' => 'The Country Name has already been taken.'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->country_id)) {
                $country = Country::where('id', $request->country_id)->first();
                $response = ['data' => route('country.index'), 'status' => true, 'message' => ' Country Updated Successfully.'];
            } else {
                $country = new Country();
                $response = ['data' => route('country.index'), 'status' => true, 'message' => ' Country Added Successfully.'];
            }
            $country->country_name = $request->country_name;
            $result = $country->save();
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

    public function show(Country $country)
    {
        //
    }

    public function edit($id)
    {
        $country = Country::where('id', $id)->first();
        return view('country.add_country', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        //
    }

    public function destroy(Country $country)
    {
        try {
            $country = Country::where('id', $country->id)->first();
            $userProfile = UserProfile::where('country_id', $country->id)->count();
            if ($userProfile == 0) {
                $city = City::where('country_id', $country->id)->get();
                foreach ($city as $item) {
                    $item->delete();
                }
                $state = State::where('country_id', $country->id)->get();
                foreach ($state as $item) {
                    $item->delete();
                }
                $country->delete();
                return response()->json(array('success' => 1, "errorMessage" => 'Country Deleted'));
            }else{
                return response()->json(array('warning' => 1, "errorMessage" => 'This Country Exists in User'));
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Error'));
        } catch (\Exception $e) {
            return response()->json(array('success' => 0, "errorMessage" => 'Server Error'));
        }
    }
}
