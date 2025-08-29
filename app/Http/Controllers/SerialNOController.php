<?php

namespace App\Http\Controllers;

use App\Models\SerialNo;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Models\WarrantyHistory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Psy\debug;

class SerialNOController extends Controller
{
    public function index(Request $request)
    {

        // do not let customer access this page
        if (Auth::user()->roles[0]->name == 'customer') {
            return redirect('/');
        }

        if ($request->ajax()) {

            // $data = SerialNo::with('product', 'user', 'category')->orderBy('id', 'DESC');
            $data = SerialNo::with(['product', 'product.category', 'user'])->orderBy('id', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    // $html .= '<a href="' . route('product.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= '<a href="javascript:void(0);" class="avatar bg-light-primary p-50 m-0 text-primary editSerial" data-id="' . $row->id . '" data-serial="' . $row->sn . '" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('serialno.view_serialno', ['title' => 'View Serial No']);
    }

    public function replaced(Request $request)
    {

        if ($request->ajax()) {

            // $data = SerialNo::with('product', 'user', 'category')->orderBy('id', 'DESC');
            $data = SerialNo::with(['product', 'product.category', 'user'])
                ->where('is_replace', true)
                ->orderBy('id', 'DESC');

            // if customer login filter out only his records
            if (Auth::user()->roles[0]->name == 'customer') {
                $data = SerialNo::with(['product', 'product.category', 'user'])
                    ->where('is_replace', true)
                    ->where('user_id', Auth::user()->id)
                    ->orderBy('id', 'DESC');
            }

            // not in use
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {

                    $html = '<div class="d-flex">';

                    // Status dropdown
                    $active6 = $active7 = '';
                    // Log::debug($row->status);

                    if ($row->status == 'In Stock') {
                        $btn = "btn-outline-info";
                        $title = "In Stock";
                        $active6 = "active bg-info";
                    } elseif ($row->status == 'Out of Stock') {
                        $btn = "btn-outline-dark";
                        $title = "Out of Stock";
                        $active7 = "active bg-dark";
                    } else {
                        $btn = "btn-outline-secondary";
                        $title = "Select";
                    }

                    $html .= '<div class="btn-group dropdown">
                        <button type="button" class="btn-sm btn ' . $btn . '">' . $title . '</button>
                        <button type="button" class="btn-sm btn ' . $btn . ' dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu p-0" data-bs-display="static"> 
                            <li><a class="dropdown-item change-status ' . $active6 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="In Stock">In Stock</a></li>
                            <li><a class="dropdown-item change-status ' . $active7 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="Out of Stock">Out of Stock</a></li>
                        </ul>
                    </div>';

                    // // Delete button
                    // $html .= '<a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete">
                    //     <i class="fa fa-trash"></i>
                    // </a>';

                    $html .= '</div>'; // Closing div for flex container

                    return $html;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('serialno.replaced_serialno', ['title' => 'View Replaced Items']);
    }
    public function rejected(Request $request)
    {

        if ($request->ajax()) {

            // $data = SerialNo::with('product', 'user', 'category')->orderBy('id', 'DESC');
            $data = SerialNo::with(['product', 'product.category', 'user'])
                ->where('is_reject', true)
                ->orderBy('id', 'DESC');

            // if customer login filter out only his records
            $is_customer = Auth::user()->roles[0]->name == 'customer';
            if ($is_customer == true) {
                $data = SerialNo::with(['product', 'product.category', 'user'])
                    ->where('is_reject', true)
                    ->where('user_id', Auth::user()->id)
                    ->orderBy('id', 'DESC');
            }



            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('location_status', function ($row) use ($is_customer) {

                    if($is_customer == true){
                        // $html = '<button type="button" class="btn btn-sm active btn-outline-info w-100" disabled>' . $row->location_status . '</button>';
                        // return $html;
                        return $row->location_status;
                    }

                    $html = '<div class="d-flex">';

                    // Status dropdown
                    $active6 = $active7 = '';
                    // Log::debug($row->status);

                    if ($row->location_status == 'In Store') {
                        $btn = "btn-outline-info";
                        $title = "In Store";
                        $active6 = "active bg-info";
                    } elseif ($row->location_status == 'Company Received') {
                        $btn = "btn-outline-dark";
                        $title = "Company Received";
                        $active7 = "active bg-dark";
                    } else {
                        $btn = "btn-outline-secondary";
                        $title = "Select";
                    }

                    $html .= '<div class="btn-group dropdown">
                        <button type="button" class="btn-sm btn ' . $btn . '">' . $title . '</button>
                        <button type="button" class="btn-sm btn ' . $btn . ' dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu p-0" data-bs-display="static"> 
                            <li><a class="dropdown-item change-status ' . $active6 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="In Store">In Store</a></li>
                            <li><a class="dropdown-item change-status ' . $active7 . '" href="javascript:void(0);" data-id="' . $row->id . '" data-value="Company Received">Company Received</a></li>
                        </ul>
                    </div>';

                    // // Delete button
                    // $html .= '<a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete">
                    //     <i class="fa fa-trash"></i>
                    // </a>';

                    $html .= '</div>'; // Closing div for flex container

                    return $html;
                })
                ->rawColumns(['location_status'])
                ->make(true);
        }

        return view('serialno.replaced_serialno', ['title' => 'View Rejected Items']);
    }

    public function indexOld(Request $request)
    {
        $where = "1 = 1";
        if ($request->category_id != 'All') {
            $where .= ' AND products.category_id = ' . $request->category_id;
        }

        if (request()->ajax()) {
            return DataTables::of(Product::with('category')->whereRaw($where)->orderBy('id', 'DESC'))
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $html = '<div class="d-flex">';
                    $html .= '<a href="' . route('product.edit', $row->id) . '" class="avatar bg-light-primary p-50 m-0 text-primary" data-bs-toggle="tooltip" data-placement="left" title="Edit"><i class="fa fa-edit"></i></a>';
                    $html .= ' <a data-id="' . $row->id . '" href="javascript:void(0);" id="confirm-text" class="avatar bg-light-danger p-50 m-0 text-danger delete mx-1" data-bs-toggle="tooltip" data-placement="left" title="Delete"><i class="fa fa-trash"></i></a>';
                    $html .= '</td>';
                    return $html;
                })
                ->editColumn('image', function ($row) {
                    $html = '<td>';
                    if ($row->image != '' && $row->image != null) {
                        $html .= '<a href="' . asset('upload/product/' . $row->image) . '" data-fancybox="gallery_' . $row->id . '" data-caption="' . $row->product_name . '" class="gallary-item-overlay">';
                        $html .= '<img class="img-fluid rounded" height="35" width="35" src="' . asset('upload/product/' . $row->image) . '" alt="' . $row->product_name . '" title="' . $row->product_name . '">';
                        $html .= '</a>';
                    } else {
                        $html .= ' ';
                    }
                    $html .= '</td>';
                    return $html;
                })
                ->rawColumns(['action', 'image'])
                ->make(true);
        } else {
            $category = Category::get();
            return view('serialno.view_serialno', compact('category'));
        }
    }



    public function create()
    {
        $category = Category::get();
        return view('product.add_product', compact('category'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'serial_no' => 'required|unique:serialno,sn|max:255', // Correct column name: 'sn'
        ]);

        SerialNo::create([
            'sn' => $request->serial_no // Store correctly in 'sn' column
        ]);

        return response()->json(['message' => 'Serial number added successfully']);
    }

    public function update(Request $request)
    {
        // $request->validate([
        //     'serial_no' => 'required|unique:serialno,sn,' . $request->serial_no_id
        // ]);

        $msg = "Serial Number updated successfully!";

        if ($request->serial_no) {
            SerialNo::where('id', $request->serial_no_id)->update([
                'sn' => $request->serial_no
            ]);
        }

        if ($request->status) {
            SerialNo::where('id', $request->serial_no_id)->update([
                'location_status' => $request->status
            ]);
            $msg = "Location updated successfully!";
        }

        return response()->json(['message' => $msg]);
    }

    public function show($id)
    {
        $subCategory = SubCategory::where('category_id', $id)->get();
        return response()->json($subCategory);
    }
    public function productFromCat($id)
    {
        // Get all products for the given category_id
        $products = Product::where('category_id', $id)->get();

        // If no products are found, return an empty array
        if ($products->isEmpty()) {
            return response()->json(['product' => []]);
        }

        // Return the products data as a response
        return response()->json(['product' => $products]);
    }


    public function edit($id)
    {
        $category = Category::get();
        $subCategory = SubCategory::get();
        $product = Product::where('id', $id)->first();
        return view('product.add_product', compact('category', 'subCategory', 'product'));
    }


    public function productChange(Request $request)
    {
        $product = Product::where('id', $request->product_id)->first();
        return response()->json($product);
    }

    public function destroy($id)
    {
        try {
            $serialNo = SerialNo::find($id);

            if (!$serialNo) {
                return response()->json([
                    'warning' => true,
                    'errorMessage' => 'Record not found!'
                ]);
            }

            $serialNo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Serial number deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'errorMessage' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function addWarranty()
    {
        $categories = Category::all();
        return view('serialno.add_warranty', compact('categories'));
    }



    public function getProductsByCategory(Request $request)
    {
        $products = Product::where('category_id', $request->category_id)->get();

        $options = '<option value="" selected disabled>Select Product</option>';
        foreach ($products as $product) {
            $options .= '<option value="' . $product->id . '">' . $product->product_name . '</option>';
        }

        return response()->json($options);
    }

    public function checkSerialNumber(Request $request)
    {
        $serialNumber = $request->serial_number;
        $warranty = SerialNo::with('product.category')->where('sn', $serialNumber)->first();

        if (!$warranty) {
            return response()->json(['status' => 'not_found', 'id' => '']);
        } elseif ($warranty->valid_to) {
            return response()->json(['status' => 'assigned', 'id' => $warranty->id, 'warranty' => $warranty]);
        } else {
            return response()->json(['status' => 'available', 'id' => $warranty->id, 'warranty' => $warranty]);
        }
    }

    public function checkAndSaveSerialNumber(Request $request)
    {
        $serialNumber = $request->new_serial_number;
        $serialNoId = $request->old_serial_id;

        $warranty = SerialNo::with('product.category')->where('sn', $serialNumber)->first();
        $oldSerial = SerialNo::find($serialNoId); // Get the old serial record

        if (!$warranty) {
            return response()->json(['status' => 'not_found', 'id' => '']);
        } elseif ($warranty->valid_to) {
            return response()->json(['status' => 'assigned', 'id' => $warranty->id, 'warranty' => $warranty]);
        } else {
            // Update SerialNo table
            $warranty->update([
                'product_id' => $oldSerial->product_id,
                'user_id' => Auth::id(),
                'cus_name' => $oldSerial->cus_name,
                'cus_village' => $oldSerial->cus_village,
                'cus_mobile' => $oldSerial->cus_mobile,
                'valid_from' => $oldSerial->valid_from,
                'valid_to' => $oldSerial->valid_to,
            ]);

            if ($warranty->remarks) {
                $warranty->remarks = $warranty->remarks . ', Replace from ' . $oldSerial->sn;
            } else {
                $warranty->remarks = 'Replace from ' . $oldSerial->sn;
            }

            if ($oldSerial->remarks) {
                $oldSerial->remarks = $oldSerial->remarks . ', Replace to ' . $warranty->sn;
            } else {
                $oldSerial->remarks = 'Replace to ' . $warranty->sn;
            }

            $oldSerial->is_reject = true;
            $oldSerial->location_status = "In Store"; // Company Received 
            $warranty->is_replace = true;
            $warranty->status = "In Stock"; // Out of Stock // this is order flag not in use

            $warranty->save();
            $oldSerial->save();

            WarrantyHistory::create([
                'serial_no_id' => $warranty->id, // $serialNumber,
                'category_id' => $warranty->category_id,
                'product_id' => $warranty->product_id,
                'user_id' => Auth::id(),
                'customer_name' => $warranty->cus_name,
                'customer_village' => $warranty->cus_village,
                'contact_number' => $warranty->cus_mobile,
                'warranty_date' => now(), //just today date - not related to warrrenty
            ]);

            if (Auth::user()->roles[0]->name == 'customer') {
                // make new instock order if request is from customer
                $orderController = App::make(OrderController::class);            
                $orderController->replaceOrder($oldSerial, 'In Stock', $warranty->sn);    
            }
            
            return response()->json(['status' => 'available', 'id' => $warranty->id, 'warranty' => $warranty]);
        }
    }


    public function uploadCSVSerialNo(Request $request)
    {
        // Validate file type
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        // Read CSV file
        if (!$request->hasFile('csv_file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded.'
            ], 400);
        }

        try {
            $file = $request->file('csv_file');
            $csvData = array_map('str_getcsv', file($file->getRealPath()));

            // Check if CSV has data
            if (count($csvData) <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'CSV file is empty or only contains headers.'
                ], 400);
            }

            $rowCount = count($csvData) - 1;
            $serialNumbersInCSV = [];
            $productCodes = [];
            $errors = [];

            // Collect product codes and serial numbers
            foreach (array_slice($csvData, 1) as $row) {
                $product_code = trim($row[0]);
                $serial_no = trim($row[1]);

                $productCodes[] = $product_code;
                $serialNumbersInCSV[] = $serial_no;
            }

            // Fetch all existing products & serials in one go
            $existingProducts = Product::whereIn('product_code', $productCodes)->pluck('id', 'product_code');
            $existingSerials = SerialNo::whereIn('sn', $serialNumbersInCSV)->pluck('sn')->toArray();

            // Validate rows
            $validatedData = [];
            foreach (array_slice($csvData, 1) as $row) {
                $product_code = trim($row[0]);
                $serial_no = trim($row[1]);

                // Check if product exists
                if (!isset($existingProducts[$product_code])) {
                    $errors[] = "Remove line with product code: $product_code. Product not found!";
                    continue;
                }

                // Check if serial number exists in DB
                if (in_array($serial_no, $existingSerials)) {
                    $errors[] = "Remove line with serial no: $serial_no. Serial number exists in the database!";
                    continue;
                }

                // Check if serial number is duplicated in CSV
                if (isset($serialNumbersInCSV[$serial_no])) {
                    $errors[] = "Remove duplicate serial no: $serial_no. Serial number is duplicated in the CSV!";
                    continue;
                }

                $serialNumbersInCSV[$serial_no] = true;

                // Store valid data for insertion
                $validatedData[] = [
                    'product_id' => $existingProducts[$product_code],
                    'sn' => $serial_no
                ];
            }

            // Return validation errors if found
            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors' => $errors
                ], 400);
            }

            // Insert valid data into DB
            SerialNo::insert($validatedData);

            return response()->json([
                'success' => true,
                'message' => "$rowCount serial numbers added successfully!"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while processing the file.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function storeWarranty(Request $request)
    {
        $request->validate([
            'serial_no_id' => 'required',
            'category_id' => 'required',
            'product_id' => 'required',
            'customer_name' => 'required',
            'customer_village' => 'required',
            'contact_number' => 'required',
        ]);

        $serialNo = SerialNo::where('id', $request->serial_no_id)->first();

        if ($serialNo->valid_to) {
            return response()->json(['error' => 'Product is assigned to this serial number!'], 400);
        }

        // Update SerialNo table
        $serialNo->update([
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'cus_name' => $request->customer_name,
            'cus_village' => $request->customer_village,
            'cus_mobile' => $request->contact_number,
            'valid_from' => now(),
            'valid_to' => now()->addYears($request->warranty_duration),

        ]);

        WarrantyHistory::create([
            'serial_no_id' => $request->serial_no_id,
            'category_id' => $request->category_id,
            'product_id' => $request->product_id,
            'user_id' => Auth::id(),
            'customer_name' => $request->customer_name,
            'customer_village' => $request->customer_village,
            'contact_number' => $request->contact_number,
            'warranty_date' => now(), //just today date - not related to warrrenty
        ]);

        return response()->json(['success' => true, 'message' => 'Warranty added successfully!']);
    }
}
