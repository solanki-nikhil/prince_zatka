<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Order;
use App\Models\State;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class UserProfileController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(UserProfile::with('user', 'country')->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a href="' . route('user.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="mx-1 avatar bg-light-danger p-50 m-0 text-danger delete" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->addColumn('status', function ($row) {
                    $html = '<div class="">';
                    $active1 =  $active2  = '';
                    if ($row->user->status == '0') {
                        $btn = "btn-outline-warning";
                        $title = "Pending";
                        $active1 = "active bg-warning";
                    }
                    if ($row->user->status == '1') {
                        $btn = "btn-outline-success";
                        $title = "Active";
                        $active2 = "active bg-success";
                    }
                    $html .= '<div class="btn-group w-100">
                                    <button type="button" class="btn-sm btn ' . $btn . '">' . $title . '</button>
                                    <button type="button" class="btn-sm btn ' . $btn . ' dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="visually-hidden">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu p-0">
                                    <li><a class="dropdown-item status-change ' . $active1 . '" href="javascript:void(0);" data-id="' . $row->user->id . '" data-value="0">Pending</a></li>
                                    <li><a class="dropdown-item status-change ' . $active2 . '" href="javascript:void(0);" data-id="' . $row->user->id . '" data-value="1">Active</a></li>
                                    </ul>
                                </div>';
                    return $html;
                })
                ->addColumn('company', function ($row) {
                    // Ensure 'company' field is accessed correctly
                    return ($row->company_name ?? '') . '<br>' . ($row->gst ?? '0');
                })

                ->addColumn('date', function ($row) {
                    // Ensure 'company' field is accessed correctly
                    return ($row->created_at ?? '');
                })
                ->rawColumns(['action', 'status', 'company'])
                ->make(true);
        } else {
            return view('user.user_list');
        }
    }

    public function create()
    {
        $country = Country::get();
        return view('user.user_add', compact('country'));
    }

    public function store(Request $request)
    {
        $user = User::where('id', $request->user_id)->first();
        if (!is_null($request->user_id)) {
            // $email = 'required|unique:users,email,' . $request->user_id;
            $mobile = 'required|numeric|digits:10|unique:users,mobile,' . $request->user_id;
            $password = 'nullable';
        } else {
            // $email = 'required|unique:users,email';
            $mobile = 'required|numeric|digits:10|unique:users,mobile';
            $password = 'required';
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => $mobile,
            'country_id' => 'required',
            'password' => $password,
        ], [
            'name.required' => 'Enter Name',
            'country_id.required' => 'Select Country',
            'mobile.required' => 'Enter Mobile No.',
            'password.required' => 'Enter Password'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            $userProfile = '';
            if (!is_null($request->user_id)) {
                $user = User::where('id', $request->user_id)->first();
                $userProfile = UserProfile::where('user_id', $user->id)->first();
                $response = ['data' => route('user.index'), 'status' => true, 'message' => ' User Updated Successfully.'];
            } else {
                $user = new User();
                $userProfile = new UserProfile();
                $response = ['data' => route('user.index'), 'status' => true, 'message' => ' User Added Successfully.'];
            }
            $user->name = $request->name;
            $user->email = ($request->mobile) . '@mail.com';
            $user->mobile = $request->mobile;
            if (!is_null($request->password)) {
                $user->password = bcrypt($request->password);
            }
            $user->status = '0';
            $result = $user->save();
            DB::commit();
            if (!is_null($result)) {
                if (!$request->user_id) {
                    $user->assignRole('customer');
                }
                if ($userProfile) {
                    $userProfile->user_id = $user->id;
                    $userProfile->country_id = $request->country_id;
                    $userProfile->company_name = $request->company_name;
                    $userProfile->gst = $request->gst;
                    $userProfile->address = $request->address;
                    $userProfile->save();
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

    public function edit($id)
    {
        $userProfile = UserProfile::with('user')->where('id', $id)->first();
        $country = Country::get();
        $state = State::where('country_id', $userProfile->country_id)->get();
        $city = City::where('state_id', $userProfile->state_id)->get();
        return view('user.user_add', compact('userProfile', 'country', 'state', 'city'));
    }

    public function show($id, Request $request)
    {
        $user = User::where('id', $id)->first();
        if (!is_null($user)) {
            $user->status = $request->status;
            $user->save();
            $response = ['status' => true, 'message' => ' User Status Updated Successfully.'];
        } else {
            $response = ['status' => false, 'message' => ' Something went wrong. Please try again.'];
        }
        return response()->json($response);
    }

    public function update($id, Request $request)
    {
        $user = User::where('id', $id)->first();
        $user->save();
        if ($request->role == 2) {
            $user->syncRoles([]);
            $user->roles()->attach(Role::where('name', 'customer')->first());
        } elseif ($request->role == 3) {
            $user->syncRoles([]);
            $user->roles()->attach(Role::where('name', 'distributor')->first());
        } else {
            $user->syncRoles([]);
            $user->roles()->attach(Role::where('name', 'dealer')->first());
        }
        $response = ['status' => true, 'message' => ' User Role Updated Successfully.'];
        return response()->json($response);
    }

    public function destroy($id)
    {
        $userProfile = UserProfile::find($id);
        $user = User::where('id', $userProfile->user_id)->first();
        if (!is_null($userProfile)) {
            $order =  Order::where('user_id', $user->id)->count();
            if ($order > 0) {
                $response = ['status' => false, 'message' => 'Customer Order Already Exsits.'];
                return response()->json($response);
            } else {
                $user->delete();
                $userProfile->delete();
                $response = ['status' => true, 'message' => 'User Deleted Successfully.'];
                return response()->json($response);
            }
        } else {
            $response = ['status' => false, 'message' => 'This Record does not exist.'];
            return response()->json($response);
        }
    }

    public function userChange(Request $request)
    {
        $userProfile = UserProfile::with('user')->where('user_id', $request->user_id)->first();
        return response()->json($userProfile);
    }

    public function verifyPassword(Request $request)
    {
        $user = User::where('id',  $request->user_id)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $response  = ['status' => true];
        } else {
            $response  = ['status' => false];
        }
        return response()->json($response);
    }

    public function editProfile()
    {
        return view('user.user_profile');
    }

    
    public function updateProfile(Request $request)
    {

        $request->validate([
            'password' => ['nullable', 'min:8', 'confirmed'],
        ], [
            'password.confirmed' => 'Password and Confirm Password do not match.',
        ]);
        

        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|min:6|confirmed'
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}
