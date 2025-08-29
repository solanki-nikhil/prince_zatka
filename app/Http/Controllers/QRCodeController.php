<?php

namespace App\Http\Controllers;

use App\Models\QRCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class QRCodeController extends Controller
{
    public function index()
    {
        DB::statement("SET SQL_MODE=''");
        $qRCode = QRCode::select('amount','created_at')
            ->selectRaw('batch_number,COUNT(*) as total')
            ->where('is_used', '0')
            ->groupBy('batch_number')
            ->orderBy('id', 'DESC')
            ->get();
        if (request()->ajax()) {
            return DataTables::of($qRCode)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a role="button" href="' . route('generate-qr', $row->batch_number) . '" class="pdf avatar bg-light p-50 m-0 text-dark" data-bs-toggle="tooltip" data-placement="left" title="Download PDF"><i class="fa fa-download"></i></a>';
                    $html .= '<a data-id="' . $row->batch_number . '" role="button" href="javascript:void(0)" class="mx-1 view_qrcode avatar bg-light-success p-50 m-0 text-success" data-bs-toggle="tooltip" data-placement="left" title="View"><i class="fa fa-eye"></i></a>';
                    $html .= '<a data-id="' . $row->batch_number . '" href="javascript:void(0);" id="confirm-text" class="delete avatar bg-light-danger p-50 m-0 text-danger" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('amount', function ($row) {
                    return number_format($row->amount, 2);
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('d-m-Y');
                })
                ->rawColumns(['action', 'amount','created_at'])
                ->make(true);
        } else {
            return view('qr_code.view_qr_code');
        }
    }

    public function generateQR($id)
    {
        $qRCode = QRCode::where('batch_number', $id)->where('is_used', '0')->get();
        $data = [
            'title' => 'Coupon Code',
            'qRCode' => $qRCode,
        ];
        $name = "Prince_Z";
        $pdf = PDF::loadView('qr_code.generate_qr', $data);
        return $pdf->download($name . '.pdf');
    }

    public function create()
    {
        return view('qr_code.add_qr_code');
    }

    public function store(Request $request)
    {
        if (!is_null($request->qr_code_id)) {
            $temp = 'nullable';
        } else {
            $temp = 'required';
        }
        $validator = Validator::make($request->all(), [
            'number' => $temp,
        ], [
            'number.required' => 'Enter Number of QRCode',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'message' => 'Please Input Proper Data !!', 'errors' => $validator->errors()];
            return response()->json($response);
        }
        DB::beginTransaction();
        try {
            if (!is_null($request->qr_code_id)) {
                $qRCode = QRCode::where('id', $request->qr_code_id)->first();
                $qRCode->amount = $request->amount;
                $result = $qRCode->save();
                $response = ['data' => route('qr-code.index'), 'status' => true, 'message' => ' QRCode Updated Successfully.'];
            } else {
                $batch = Str::random(10);
                for ($i = 1; $i <= $request->number; $i++) {
                    $qRCode = new QRCode();
                    $qRCode->user_id = Auth::id();
                    $qRCode->amount = $request->amount;
                    $random = str_shuffle('1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ');
                    $firstCode = substr($random, 0, 20);
                    $qRCode->code = $firstCode;
                    $qRCode->batch_number = $batch;
                    $result = $qRCode->save();
                    $response = ['data' => route('qr-code.index'), 'status' => true, 'message' => ' QRCode Added Successfully.'];
                }
            }
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
        $qRCode = QRCode::where('batch_number', $id)->where('is_used', '0')->get();
        $data['html'] = view('qr_code.qr_code_model', compact('qRCode'))->render();
        return response()->json($data);
    }

    public function edit($id)
    {
        $qRCode = QRCode::where('id', $id)->first();
        return view('qr_code.add_qr_code', compact('qRCode'));
    }

    public function update(Request $request, $id)
    {
        //delete single item for qrcode
        $qRCode = QRCode::find($request->id);
        $qRCode->delete();
        return response()->json($qRCode);
    }

    public function destroy($id)
    {
        //delete all item for batch qrcode
        $qRCode = QRCode::where('batch_number', $id)->get();
        foreach ($qRCode as $item) {
            $item->delete();
        }
        return response()->json($qRCode);
    }
}
