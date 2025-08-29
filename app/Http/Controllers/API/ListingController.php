<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\PointRedeem;
use App\Models\QRCode;
use App\Models\Slider;
use App\Models\State;
use App\Models\StatusMaster;
use App\Models\SubCategory;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ListingController extends Controller
{
    public function sliderList()
    {
        $slider = Slider::select('id','image','title')->get();
        if (count($slider) > 0) {
            $path = asset('upload/slider/');
            foreach ($slider as $key => $value) {
                if (!is_null($value->image)) {
                    $value['image'] = $path . '/' . $value->image;
                } else {
                    $value['image'] = '';
                }
                $value['user_id'] = Auth::id();
            }
            $response = ['status' => true, 'message' => 'Slider Listings.','role'=> Auth::user()->roles[0]->name, 'slider' => $slider];
            return response($response, 200);
        } else {
            $response = ['status' => true, 'message' => 'Slider Not Found.'];
            return response($response, 200);
        }
    }

    public function countryList()
    {
        $country = Country::get();
        foreach ($country as $key => $value) {
            unset($value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'Country Listings.', 'country' => $country];
        return response($response, 200);
    }

    public function stateList()
    {
        $state = State::with('country')->get();
        foreach ($state as $key => $value) {
            $value['country_name'] = $value->country->country_name;
            unset($value->country_id, $value->country, $value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'State Listings.', 'state' => $state];
        return response($response, 200);
    }

    public function cityList()
    {
        $city = City::with('country', 'state')->get();
        foreach ($city as $key => $value) {
            $value['country_name'] = $value->country->country_name;
            $value['state_name'] = $value->state->state_name;
            unset($value->country_id, $value->state_id, $value->country, $value->state, $value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'City Listings.', 'city' => $city];
        return response($response, 200);
    }

    public function categoryList(Request $request)
    {
        $category = Category::orderBy('id', 'DESC')->get();
        $path = asset('upload/category/');
        foreach ($category as $key => $value) {
            if (!is_null($value->image)) {
                $value['image'] = $path . '/' . $value->image;
            } else {
                $value['image'] = '';
            }
            unset($value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'Category Listings.', 'category' => $category];
        return response($response, 200);
    }

    public function subCategoryList(Request $request)
    {
        $path = asset('upload/subcategory/');
        if (!is_null($request->category_id)) {
            $subCategory = SubCategory::with('category')->where('category_id', $request->category_id)->orderBy('id', 'DESC')->get();
        } else {
            $subCategory = SubCategory::with('category')->orderBy('id', 'DESC')->get();
        }
        foreach ($subCategory as $key => $value) {
            if (!is_null($value->image)) {
                $value['image'] = $path . '/' . $value->image;
            } else {
                $value['image'] = '';
            }
            $value['category_name'] = $value->category->category_name;
            unset($value->category, $value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'Sub Category Listings.', 'subCategory' => $subCategory];
        return response($response, 200);
    }

    public function statusList()
    {
        $statusMaster = StatusMaster::get();
        foreach ($statusMaster as $key => $value) {
            unset($value->deleted_at, $value->created_at, $value->updated_at);
        }
        $response = ['status' => true, 'message' => 'Status Listings.', 'statusMaster' => $statusMaster];
        return response($response, 200);
    }

    public function userList(Request $request)
    {
        $user = Auth::user();
        if ($user->role = $user->roles[0]->name == 'admin' || $user->role = $user->roles[0]->name == 'stock-admin' || $user->role = $user->roles[0]->name == 'coupon-admin') {
            if (!is_null($request->id)) {
                $user = User::with('userDetails', 'userDetails.country', 'userDetails.state', 'userDetails.city')->where('id', $request->id)->where('status', '1')->whereHas(
                    'roles',
                    function ($q) {
                        $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
                    }
                )->orderBy('id', 'DESC')->get();
            } else {
                if (!is_null($request->start) && !is_null($request->length)) {
                    $user = User::with('userDetails', 'userDetails.country', 'userDetails.state', 'userDetails.city')->where('status', '1')->whereHas(
                        'roles',
                        function ($q) {
                            $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
                        }
                    )->skip($request->start)->take($request->length)->orderBy('id', 'DESC')->get();
                } else {
                    $user = User::with('userDetails', 'userDetails.country', 'userDetails.state', 'userDetails.city')->where('status', '1')->whereHas(
                        'roles',
                        function ($q) {
                            $q->where('name', 'distributor')->orwhere('name', 'dealer')->orwhere('name', 'customer');
                        }
                    )->orderBy('id', 'DESC')->get();
                }
            }
            foreach ($user as $value) {
                $value['name'] = $value->name;
                $value['mobile'] = $value->mobile;
                $value['email'] = $value->email;
                $value['user_profile_id'] = $value->userDetails->id;
                $value['points'] = $value->userDetails->redeem;
                $value['address'] = $value->userDetails->address;
                $value['country_name'] = $value->userDetails->country->country_name;
                $value['state_name'] = $value->userDetails->state->state_name;
                $value['city_name'] = $value->userDetails->city->city_name;
                $value['role'] = $value->roles[0]->name;
                unset($value->userDetails, $value->roles, $value->email_verified_at, $value->two_factor_recovery_codes, $value->two_factor_secret, $user->two_factor_recovery_codes, $value->two_factor_confirmed_at, $value->otp, $value->created_at, $value->updated_at);
            }
            $response = ['status' => true, 'message' => 'User Listings.', 'userProfile' => $user];
        } else {
            $user = User::with('userDetails', 'userDetails.country', 'userDetails.state', 'userDetails.city')->where('status', '1')->where('id', $user->id)->first();
            $user['name'] = $user->name;
            $user['mobile'] = $user->mobile;
            $user['email'] = $user->email;
            $user['address'] = $user->userDetails->address;
            $user['points'] = $user->userDetails->redeem;
            $user['user_profile_id'] = $user->userDetails->id;
            $user['country_name'] = $user->userDetails->country->country_name;
            $user['state_name'] = $user->userDetails->state->state_name;
            $user['city_name'] = $user->userDetails->city->city_name;
            $user['role'] = $user->roles[0]->name;
            unset($user->userDetails, $user->roles, $user->email_verified_at, $user->two_factor_recovery_codes, $user->two_factor_secret, $user->two_factor_recovery_codes, $user->two_factor_confirmed_at, $user->otp, $user->created_at, $user->updated_at);
            $response = ['status' => true, 'message' => 'User Listings.', 'userProfile' => $user];
        }
        return response($response, 200);
    }

    public function userProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'country_id' => 'required',
            'state_id' => 'required',
            'city_id' => 'required',
            'address' => 'required',
        ], [
            'name.required' => 'Enter Name.',
            'email.required' => 'Enter Email.',
            'mobile.required' => 'Enter Mobile.',
            'country_id.required' => 'Select Country.',
            'state_id.required' => 'Select State.',
            'city_id.required' => 'Select Country.',
            'address.required' => 'Enter Address.',
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
            $user = User::where('id', $request->user_id)->first();
            $user->name = $request->name;
            $result = $user->save();
            DB::commit();
            if (!is_null($result)) {
                $userProfile = UserProfile::where('user_id', $request->user_id)->first();
                $userProfile->user_id = $request->user_id;
                $userProfile->country_id = $request->country_id;
                $userProfile->state_id = $request->state_id;
                $userProfile->city_id = $request->city_id;
                $userProfile->address = $request->address;
                $userProfile->save();

                $response = ['status' => true, 'message' => 'User Profile Updated Successfully.'];
                return response($response, 200);
            } else {
                $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                return response($response, 500);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
    }

    public function redeem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ], [
            'code.required' => 'Scan Proper QRCode.',
        ]);
        if ($validator->fails()) {
            $error = '';
            foreach ($validator->messages()->all() as $item) {
                $error .= $item;
            }
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $error];
            return response($response, 201);
        }
        DB::beginTransaction();
        try {
            $qRCodeCheck = QRCode::where('code', $request->code)->first();
            if (!is_null($qRCodeCheck)) {
                $qRCode = QRCode::where('is_used', '0')->where('code', $request->code)->first();
                $user = User::with('userDetails', 'userDetails.country', 'userDetails.state', 'userDetails.city')->where('id', Auth::id())->first();
                $user['name'] = $user->name;
                $user['mobile'] = $user->mobile;
                $user['email'] = $user->email;
                $user['address'] = $user->userDetails->address;
                $user['credited_points'] = $user->userDetails->redeem;
                $user['user_profile_id'] = $user->userDetails->id;
                $user['country_name'] = $user->userDetails->country->country_name;
                $user['state_name'] = $user->userDetails->state->state_name;
                $user['city_name'] = $user->userDetails->city->city_name;
                $user['role'] = $user->roles[0]->name;
                unset($user->userDetails, $user->roles, $user->email_verified_at, $user->two_factor_recovery_codes, $user->two_factor_secret, $user->two_factor_recovery_codes, $user->two_factor_confirmed_at, $user->otp, $user->created_at, $user->updated_at);
                if (!is_null($qRCode)) {
                    $userProfile = UserProfile::where('user_id', Auth::id())->first();
                    $qRCode->is_used = '1';
                    $qRCode->used_by = $userProfile->id;
                    $qRCode->used_date = date('Y-m-d');
                    $result = $qRCode->save();
                    DB::commit();
                    if (!is_null($result)) {
                        $pointRedeem = new PointRedeem();
                        $pointRedeem->user_profile_id = $userProfile->id;
                        $pointRedeem->redeem = $qRCode->amount;
                        $pointRedeem->status = '0';
                        $pointRedeem->save();
                        $userProfile->redeem = $userProfile->redeem + $qRCode->amount;
                        $userProfile->save();
                        $user['point'] = $qRCode->amount;
                        $user['credited_points'] = $userProfile->redeem;

                        $response = ['status' => true, 'message' => 'Verify Successfully. Please Redeem.', 'Info' => $user];
                        return response($response, 200);
                    } else {
                        $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
                        return response($response, 500);
                    }
                } else {
                    $response = ['status' => false, 'message' => 'This coupon code already used.', 'Info' => $user];
                    return response($response, 201);
                }
            } else {
                $response = ['status' => false, 'message' => 'invalid coupon code.'];
                return response($response, 201);
            }
        } catch (\Exception $e) {
            DB::rollback();
            $response = ['status' => false, 'message' => 'Something went wrong. Please try again.'];
            return response($response, 500);
        }
    }

    public function transection(Request $request)
    {
        $user = Auth::user();
        if ($user->role = $user->roles[0]->name == 'admin' || $user->role = $user->roles[0]->name == 'stock-admin' || $user->role = $user->roles[0]->name == 'coupon-admin') {
            if (!is_null($request->user_profile_id)) {
                $id = $request->user_profile_id;
            } else {
                $response = ['status' => false, 'message' => 'User Profile ID is required.'];
                return response($response, 201);
            }
        } else {
            $userProfile = UserProfile::where('user_id', Auth::id())->first();
            $id = $userProfile->id;
        }
        $pointRedeem = PointRedeem::with('userDetails', 'userDetails.user')->where('user_profile_id', $id)->get();
        if (count($pointRedeem) > 0) {
            foreach ($pointRedeem as $item) {
                if ($item->status == '0') {
                    $item['status'] = 'credit';
                } else {
                    $item['status'] = 'debit';
                }
                $item['points'] = $item->redeem;
                $item['date'] = $item->created_at->format('d-m-Y');
                $item['name'] = $item->userDetails->user->name;
                unset($item->userDetails, $item->redeem, $item->user_profile_id, $item->created_at, $item->updated_at, $item->deleted_at);
            }
            $response = ['status' => true, 'message' => 'Transection History.', 'Transection' => $pointRedeem];
            return response($response, 200);
        } else {
            $response = ['status' => false, 'message' => 'Transection History not found.'];
            return response($response, 201);
        }
    }
}
