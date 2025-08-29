<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'password' => 'required',

        ], [
            'mobile.required' => 'Enter Mobile.',
            'password.required' => 'Enter Password.',
        ]);
        if ($validator->fails()) {
            $error = '';
            foreach ($validator->messages()->all() as $item) {
                $error .= $item;
            }
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $error];
            return response()->json($response);
        }

        $user = User::where('mobile', $request->mobile)->orwhere('email', $request->mobile)->first();
        if (!$user || !Hash::check($request->password, $user->password) || $user->status != '1') {
            return response(['status' => false, 'message' => 'do not match our record.'], 200);
        }
        $token = $user->createToken('token')->plainTextToken;

        if ($user->role = $user->roles[0]->name != 'admin') {
            $userProfile = UserProfile::with('user', 'country', 'state', 'city')->where('user_id', $user->id)->first();
            $userProfile['name'] = $userProfile->user->name;
            $userProfile['mobile'] = $userProfile->user->mobile;
            $userProfile['email'] = $userProfile->user->email;
            $userProfile['country_name'] = $userProfile->country->country_name;
            $userProfile['state_name'] = $userProfile->state->state_name;
            $userProfile['city_name'] = $userProfile->city->city_name;
            $userProfile['country_id'] = $userProfile->country_id;
            $userProfile['state_id'] = $userProfile->state_id;
            $userProfile['city_id'] = $userProfile->city_id;
            $userProfile['role'] = $userProfile->user->roles[0]->name;
            unset($userProfile->user, $userProfile->country, $userProfile->state, $userProfile->city, $userProfile->deleted_at, $userProfile->created_at, $userProfile->updated_at);
        } else {
        $user->role = $user->roles[0]->name;
        unset($user->roles, $user->email_verified_at, $user->two_factor_secret, $user->two_factor_recovery_codes, $user->two_factor_confirmed_at, $user->otp,$user->created_at, $user->updated_at);
            $userProfile = $user;
        }
        $response = [
            'status' => true,
            'message' => 'Login Successfully!!',
            'user' => $userProfile,
            'token' => $token
        ];
        return response($response, 200);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
