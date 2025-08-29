<?php

namespace App\Http\Controllers;

use App\Models\PointRedeem;
use App\Models\UserProfile;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PointRedeemController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            return DataTables::of(UserProfile::with('user')->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a data-id="' . $row->id . '" role="button" href="javascript:void(0)" class="recive avatar bg-light-success p-50 m-0 text-success" data-bs-toggle="tooltip" data-placement="left" title="Transection History"><i class="fa fa-eye"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('name', function ($row) {
                    return $row->user->name;
                })
                ->rawColumns(['action', 'name'])
                ->make(true);
        } else {
            return view('redeem.view_redeem_point');
        }
    }

    public function create()
    {
        $userProfile = UserProfile::with('user')->get();
        return view('redeem.redeem_points', compact('userProfile'));
    }

    public function store(Request $request)
    {
        try {
            $userProfile = UserProfile::where('id', $request->user_profile_id)->where('redeem', '>=', $request->redeem_point)->first();
            if (!is_null($userProfile)) {
                $userProfile->redeem = $userProfile->redeem - $request->redeem_point;
                $userProfile->save();

                $pointRedeem = new PointRedeem();
                $pointRedeem->user_profile_id = $userProfile->id;
                $pointRedeem->redeem = $request->redeem_point;
                $pointRedeem->status = '1';
                $pointRedeem->save();
                $response = ['data' => route('redeem-point.index'), 'status' => true, 'message' => ' Redeem Successfully.'];
            } else {
                $response = ['status' => false, 'message' => ' Withdrawal Point can not be greater than Receivable Point.'];
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $response = ['status' => true, 'server_error' => 'Something went wrong. Please try again.'];
            return response()->json($response);
        }
    }

    public function show($id)
    {
        $pointRedeem = PointRedeem::where('user_profile_id', $id)->get();
        $data['html'] = view('redeem.transection_history_model', compact('pointRedeem'))->render();
        return response()->json($data);
    }

    public function edit()
    {
        //
    }

    public function update(Request $request, $id)
    {
        $userProfile = UserProfile::where('id', $id)->first();
        return response()->json($userProfile);
    }

    public function destroy(PointRedeem $pointRedeem)
    {
        //
    }
}
