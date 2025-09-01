<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Country; // Import your Country model

class RegisterController extends Controller
{
    
    // THIS WEB CODE
    public function index()
    {
        $country = Country::all(); // Fetch all countries
        return view('auth.register', compact('country')); // Pass the data to the register blade
    }

    public function register(Request $request)
{
    $mobile = 'required|numeric|digits:10|unique:users,mobile';
    $password = 'required';

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
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    DB::beginTransaction();
    try {
        $user = new User();
        $userProfile = new UserProfile();

        $user->name = $request->name;
        $user->email = ($request->mobile) . '@mail.com';
        $user->mobile = $request->mobile;
        $user->password = bcrypt($request->password);
        $user->status = '0';

        $result = $user->save();
        DB::commit();

        if ($result) {
            if (!$request->user_id) {
                $user->assignRole('customer');
            }

            $userProfile->user_id = $user->id;
            $userProfile->country_id = $request->country_id;
            $userProfile->company_name = $request->company_name ?? null;
            $userProfile->gst = $request->gst ?? null;
            $userProfile->address = $request->address ?? null;
            $userProfile->save();

            // Redirect directly to the login page with a success message
            return redirect()->route('login')
                ->with('success', 'User Registered Successfully. Please login.');
        } else {
            return redirect()->back()
                ->with('error', 'Something went wrong. Please try again.')
                ->withInput();
        }
    } catch (\Exception $e) {
        DB::rollback();
        return redirect()->back()
            ->with('error', 'Something went wrong. Please try again.')
            ->withInput();
    }
}

    
    // THIS IS API CODE

 public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'mobile'       => 'required|numeric|digits:10|unique:users,mobile',
            'country_id'   => 'required|integer',
            // 'state_id'     => 'required|integer',
            // 'city_id'      => 'required|integer',
            'address'      => 'required|string|max:500',
            'company_name' => 'required|string',
            'gst'          => 'required|string',
            'password'     => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Create User with profile using relationship
            $user = User::create([
                'name'     => $request->name,
                'mobile'   => $request->mobile,
                'email'    => $request->mobile . '@mail.com',
                'password' => bcrypt($request->password),
                'status'   => '0',
                'otp'      => rand(100000, 999999),
            ]);

            // Save profile in one line using relationship
            $user->profile()->create([
                'country_id' => $request->country_id,
                // 'state_id'   => $request->state_id,
                // 'city_id'    => $request->city_id,
                'address'    => $request->address,
                'company_name'    => $request->company_name,
                'gst'    => $request->gst,
                'address'    => $request->address,
            ]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'User Registered Successfully',
                'data'    => $user->load('profile') // include profile in response
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => 'Something went wrong. Please try again.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'mobile'   => 'required|numeric|digits:10',
        'password' => 'required|string|min:6',
    ], [
        'mobile.required'   => 'Enter Mobile Number',
        'password.required' => 'Enter Password'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors()
        ], 422);
    }

    // Check user
    $user = User::where('mobile', $request->mobile)->first();

    if (!$user) {
        return response()->json([
            'status'  => false,
            'message' => 'User not found'
        ], 404);
    }

    // Check password
    if (!\Hash::check($request->password, $user->password)) {
        return response()->json([
            'status'  => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    // If using Sanctum (recommended)
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
        'status'  => true,
        'message' => 'Login successful',
        'data'    => $user,
        'token'   => $token
        // 'data'    => [
        //     'user'  => $user,
        //     'token' => $token
        // ]
    ], 200);
}

public function logout(Request $request)
{
    try {
        // Revoke the current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Logout successful'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status'  => false,
            'message' => 'Something went wrong during logout',
            'error'   => $e->getMessage()
        ], 500);
    }
}




    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'email' => 'required|unique:users,email',
    //         'mobile' => 'required|numeric|digits:10|unique:users,mobile',
    //         'country_id' => 'required',
    //         'state_id' => 'required',
    //         'city_id' => 'required',
    //         'address' => 'required',

    //     ], [
    //         'name.required' => 'Enter Name.',
    //         'email.required' => 'Enter Email.',
    //         'mobile.required' => 'Enter Mobile.',
    //         'country_id.required' => 'Select Country.',
    //         'state_id.required' => 'Select State.',
    //         'city_id.required' => 'Select Country.',
    //         'address.required' => 'Enter Address.',
    //     ]);
    //     if ($validator->fails()) {
    //         $error = '';
    //         foreach ($validator->messages()->all() as $item) {
    //             $error .= $item;
    //         }
    //         $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $error];
    //         return response()->json($response);
    //     }
    //     DB::beginTransaction();
    //     try {
    //         $user = new User();
    //         $user->name = $request->name;
    //         $user->email = $request->email;
    //         $user->mobile = $request->mobile;
    //         $user->password = bcrypt($request->password);
    //         $user->status = '0';
    //         $user->otp = '123456'; //Str::random(6);
    //         $result = $user->save();
    //         DB::commit();
    //         if (!is_null($result)) {
    //             $user->assignRole('customer');
    //             $userProfile = new UserProfile();
    //             $userProfile->user_id = $user->id;
    //             $userProfile->country_id = $request->country_id;
    //             $userProfile->state_id = $request->state_id;
    //             $userProfile->city_id = $request->city_id;
    //             $userProfile->address = $request->address;
    //             $userProfile->save();

    //             $response = ['status' => true, 'message' => 'OTP Send Successfully.'];
    //             return response($response, 200);
    //         } else {
    //             $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //             return response($response, 500);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // //register after verify
    // public function show(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'otp' => 'required',
    //     ], [
    //         'otp.required' => 'Enter OTP',
    //     ]);
    //     if ($validator->fails()) {
    //         $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
    //         return response()->json($response);
    //     }
    //     try {
    //         $user = User::where('mobile', $request->mobile)->where('otp', $request->otp)->first();
    //         if (!is_null($user)) {
    //             // $user->status = '1';
    //             $user->otp = "";
    //             $user->save();
    //             $response = ['status' => true, 'message' => 'OTP Verify Successfully.'];
    //             return response($response, 200);
    //         } else {
    //             $response = ['status' => false, 'message' => 'Opps! OTP do not match.'];
    //             return response($response, 201);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // //forget password
    // public function edit(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'mobile' => 'required',
    //     ], [
    //         'mobile.required' => 'Enter Mobile/Email',
    //     ]);
    //     if ($validator->fails()) {
    //         $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
    //         return response()->json($response);
    //     }
    //     try {
    //         $user = User::where('mobile', $request->mobile)->orwhere('email', $request->mobile)->first();
    //         if (!is_null($user)) {
    //             $user->otp = '123456';
    //             Str::random(6);
    //             $user->save();
    //             $response = ['status' => true, 'message' => 'OTP Send Successfully'];
    //             return response($response, 200);
    //         } else {
    //             $response = ['status' => false, 'message' => 'Opps! These credentials do not match our records.'];
    //             return response($response, 201);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // //reset password
    // public function update(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
    //         'password_confirmation' => ['min:8'],
    //     ]);
    //     if ($validator->fails()) {
    //         $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
    //         return response()->json($response);
    //     }
    //     try {
    //         $user = User::where('mobile', $request->mobile)->first();
    //         if (!is_null($user)) {
    //             $user->password = bcrypt($request->password);
    //             $user->save();
    //             $response = ['status' => true, 'message' => 'Password Updated Successfully.'];
    //             return response($response, 200);
    //         } else {
    //             $response = ['status' => false, 'message' => 'Opps! Something went wrong. Please try again.'];
    //             return response($response, 201);
    //         }
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
    //         return response($response, 500);
    //     }
    // }

    // public function destroy($id)
    // {
    //     //
    // }
}
