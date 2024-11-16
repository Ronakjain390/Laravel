<?php

namespace App\Http\Controllers\V1\CompanyLogo;

use App\Http\Controllers\Controller;
use App\Models\CompanyLogo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class CompanyLogoController extends Controller
{
    public function index()
    {
        $id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Retrieve the company logo data
        $companyLogo = CompanyLogo::where('user_id', $id)->first();

        if ($companyLogo) {
            // Initialize variables for temporary URLs
            $challanTemporaryUrl = null;
            $invoiceTemporaryUrl = null;
            $returnChallanTemporaryUrl = null;
            $poTemporaryUrl = null;

            // Generate temporary URLs if the paths are valid
            if ($companyLogo->challan_logo_url) {
                $challanTemporaryUrl = Storage::disk('s3')->temporaryUrl($companyLogo->challan_logo_url, now()->addHours(1));
            }

            if ($companyLogo->invoice_logo_url) {
                $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($companyLogo->invoice_logo_url, now()->addHours(1));
            }

            if($companyLogo->return_challan_logo_url){
                $returnChallanTemporaryUrl = Storage::disk('s3')->temporaryUrl($companyLogo->return_challan_logo_url, now()->addHours(1));
            }

            if($companyLogo->po_logo_url){
                $poTemporaryUrl = Storage::disk('s3')->temporaryUrl($companyLogo->po_logo_url, now()->addHours(1));
            }

            // Add the temporary URLs to the companyLogo object
            $companyLogo->challanTemporaryImageUrl = $challanTemporaryUrl;
            $companyLogo->invoiceTemporaryImageUrl = $invoiceTemporaryUrl;
            $companyLogo->returnChallanTemporaryImageUrl = $returnChallanTemporaryUrl;
            $companyLogo->poTemporaryImageUrl = $poTemporaryUrl;

            return response()->json([
                'companyLogo' => $companyLogo,
                'message' => 'Success',
                'status_code' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Data not found'
            ], 404);
        }
    }



    // public function store(Request $request)
    // {
    //     $user = Auth::user();

    //     // Define the validation rules for the new fields
    //     $validator = Validator::make($request->all(), [
    //         'invoice_logo_url' => 'nullable|url',
    //         'challan_logo_url' => 'nullable|url',
    //         'challan_alignment' => 'nullable|in:left,right,center',
    //         'invoice_alignment' => 'nullable|in:left,right,center',
    //         'challan_heading' => 'nullable|string',
    //         'invoice_heading' => 'nullable|string',
    //         'challan_stamp' => 'nullable|in:0,1',
    //         'invoice_stamp' => 'nullable|in:0,1',
    //         'barcode_accept' => 'nullable|in:0,1',
    //     ]);

    //     // If validation fails, return an error response
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Validation error.',
    //             'errors' => $validator->errors(),
    //         ], 400);
    //     }
    //     if ($request->hasFile('invoice_logo')) {
    //         $image = $request->file('invoice_logo');
    //         $imagePath = 'logos/invoice/' . uniqid() . '.' . $image->getClientOriginalExtension();

    //         // Remove the older image if it exists
    //         if ($user->invoice_logo_url && Storage::disk('s3')->exists($user->invoice_logo_url)) {
    //             Storage::disk('s3')->delete($user->invoice_logo_url);
    //         }

    //         Storage::disk('s3')->put($imagePath, file_get_contents($image));
    //         $user->invoice_logo_url = $imagePath;
    //     }

    //     // Handle 'challan_logo_url' upload and storage
    //     if ($request->hasFile('challan_logo')) {
    //         $image = $request->file('challan_logo');
    //         $imagePath = 'logos/challan/' . uniqid() . '.' . $image->getClientOriginalExtension();

    //         // Remove the older image if it exists
    //         if ($user->challan_logo_url && Storage::disk('s3')->exists($user->challan_logo_url)) {
    //             Storage::disk('s3')->delete($user->challan_logo_url);
    //         }

    //         Storage::disk('s3')->put($imagePath, file_get_contents($image));
    //         $user->challan_logo_url = $imagePath;
    //     }
    //     // Create or update the record with the new field values
    //     $companyLogo = CompanyLogo::create([
    //         'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //         'section_id' => $request->input('section_id'),
    //         'invoice_logo_url' => $request->input('invoice_logo_url'),
    //         'challan_logo_url' => $request->input('challan_logo_url'),
    //         'challan_alignment' => $request->input('challan_alignment'),
    //         'invoice_alignment' => $request->input('invoice_alignment'),
    //         'challan_heading' => $request->input('challan_heading'),
    //         'invoice_heading' => $request->input('invoice_heading'),
    //         'challan_stamp' => $request->input('challan_stamp'),
    //         'invoice_stamp' => $request->input('invoice_stamp'),
    //         'barcode_accept' => $request->input('barcode_accept'),
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Settings updated successfully',
    //     ], 200);
    // }



    // Challan logo Upload
    public function logoChallanUpload(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'challan_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'challan_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ]);
        }

        // Get the uploaded file
        $file = $request->challan_logo_url;

        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->challan_logo_url) {
            Storage::disk('s3')->delete($customUserLogo->challan_logo_url);
        }

        // Store the image in the S3 bucket with the generated filename
        // $path = Storage::disk('s3')->put('challanlogos', $file);
        $path = Storage::disk('s3')->put('challanlogos', $file, $filename);
        // $fullUrl = Storage::disk('s3')->url($path);

        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'challan_logo_url' => $path, // Update the S3 URL to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'challan_logo_url' => $path, // Store the S3 URL to the image
            ]);
        }

        return response()->json([
            'message' => 'Custom logo uploaded successfully',
            'status_code' => 200,
        ]);
    }

    // Invoice Logo Upload
    public function logoInvoiceUpload(Request $request)
    {
        // Validate the request input

        $validator = Validator::make($request->all(), [
            'invoice_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'invoice_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->invoice_logo_url;
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->invoice_logo_url) {
            Storage::disk('s3')->delete($customUserLogo->invoice_logo_url);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('challanlogos', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'invoice_logo_url' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'invoice_logo_url' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Custom logo uploaded successfully',
            'status_code' => 200,
        ]);
    }
    // Return Challan Logo Upload
    public function logoReturnChallanUpload(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'return_challan_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'return_challan_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->return_challan_logo_url;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->return_challan_logo_url) {
            Storage::disk('s3')->delete($customUserLogo->return_challan_logo_url);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('returnchallanlogos', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'return_challan_logo_url' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'return_challan_logo_url' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Return Challan logo uploaded successfully',
            'status_code' => 200,
        ]);
    }

    // Purchase Order Logo Upload
    public function logoPOUpload(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'po_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'po_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);
        // dd($request->all());

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->po_logo_url;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->po_logo_url) {
            Storage::disk('s3')->delete($customUserLogo->po_logo_url);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('pologos', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'po_logo_url' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'po_logo_url' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'PO logo uploaded successfully',
            'status_code' => 200,
        ]);
    }
     // Challan logo Upload
     public function receiptNoteUpload(Request $request)
     {
         // Validate the request input
         $validator = Validator::make($request->all(), [
             'receipt_note_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
         ], [
             'receipt_note_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
         ]);


         if ($validator->fails()) {
             return response()->json([
                 'errors' => $validator->errors(),
                 'status_code' => 400,
             ],);
         }

         // Get the uploaded file
         $file = $request->receipt_note_logo_url;
         // dd($file);
         // Generate a unique filename for the image
         $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
         // dd($filename);
         // Get the current user's ID
         $user_id = auth()->user()->id;

         // Get the user's custom logo record from the database
         $customUserLogo = DB::table('company_logos')
             ->where('user_id', $user_id)
             ->first();

         // Delete the previous image if it exists
         if ($customUserLogo && $customUserLogo->receipt_note_logo_url) {
             Storage::disk('s3')->delete($customUserLogo->receipt_note_logo_url);
         }

         // Store the image in the S3 bucket with the generated filename
         $path = Storage::disk('s3')->put('challanlogos', $file, $filename);
         // dd($path);
         // Create or update the CustomUserLogo record in the database
         if ($customUserLogo) {
             DB::table('company_logos')
                 ->where('user_id', $user_id)
                 ->update([
                     'receipt_note_logo_url' => $path, // Update the S3 path to the image
                 ]);
         } else {
             DB::table('company_logos')->insert([
                 'user_id' => $user_id,
                 'receipt_note_logo_url' => $path, // Store the S3 path to the image
             ]);
         }

         return response()->json([
             'message' => 'Custom logo uploaded successfully',
             'status_code' => 200,
         ]);
     }

     // Challan logo Upload
    public function logoEstimateUpload(Request $request)
    {
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'estimate_logo_url' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'estimate_logo_url.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ]);
        }

        // Get the uploaded file
        $file = $request->estimate_logo_url;

        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();

        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->estimate_logo_url) {
            Storage::disk('s3')->delete($customUserLogo->estimate_logo_url);
        }

        // Store the image in the S3 bucket with the generated filename
        // $path = Storage::disk('s3')->put('challanlogos', $file);
        $path = Storage::disk('s3')->put('estimatelogos', $file, $filename);
        // $fullUrl = Storage::disk('s3')->url($path);

        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'estimate_logo_url' => $path, // Update the S3 URL to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'estimate_logo_url' => $path, // Store the S3 URL to the image
            ]);
        }

        return response()->json([
            'message' => 'Custom logo uploaded successfully',
            'status_code' => 200,
        ]);
    }

    // public function update(Request $request, $userId)
    // {
    //     // $companyLogo = CompanyLogo::where('user_id', $userId)->first();
    //     // dd($companyLogo);
    //      // Get the companyLogo for the given user ID
    // $companyLogo = CompanyLogo::where('user_id', $userId)->first();

    // // Check if the companyLogo exists
    // if (!$companyLogo) {
    //     // If it doesn't exist, create a new instance with dummy data
    //     $companyLogo = new CompanyLogo([
    //         'user_id' => $userId,
    //     ]);
    //     $companyLogo->save(); // Save the dummy data

    //     // Fetch the newly created entry from the database
    //     $companyLogo = CompanyLogo::where('user_id', $userId)->first();
    // }
    //     // if (!$companyLogo) {
    //     //     return response()->json([
    //     //         'success' => false,
    //     //         'message' => 'CompanyLogo not found.',
    //     //         'status_code' => 404,
    //     //     ]);
    //     // }
    //     // dd($request->all());
    //     $validator = Validator::make($request->all(), [
    //         'challan_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
    //         'invoice_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
    //         'challan_alignment' => 'nullable|in:left,right,center',
    //         'invoice_alignment' => 'nullable|in:left,right,center',
    //         'challan_heading' => 'nullable|string',
    //         'invoice_heading' => 'nullable|string',
    //         'challan_stamp' => 'nullable|in:0,1',
    //         'invoice_stamp' => 'nullable|in:0,1',
    //         'barcode_accept' => 'nullable|in:0,1',
    //     ]);

    //     // dd($request->input('challan_alignment'));
    //     // Update the existing CompanyLogo record with the new field values
    //     $companyLogo->update([
    //         'section_id' => $request->input('section_id'),
    //         // 'invoice_logo_url' => $request->input('invoice_logo_url'),
    //         // 'challan_logo_url' => $request->input('challan_logo_url'),
    //         'invoice_alignment' => $request->input('invoice_alignment'),
    //         'challan_heading' => $request->input('challan_heading'),
    //         'invoice_heading' => $request->input('invoice_heading'),
    //         'challan_stamp' => $request->input('challan_stamp'),
    //         'invoice_stamp' => $request->input('invoice_stamp'),
    //         'barcode_accept' => $request->input('barcode_accept'),
    //         'challan_alignment' => $request->input('challan_alignment'),
    //     ]);

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Settings updated successfully',
    //         'status_code' => 200,
    //     ]);
    // }

    public function update(Request $request, $userId)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'challan_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'invoice_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'estimate_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'challan_alignment' => 'nullable|in:left,right,center',
            'invoice_alignment' => 'nullable|in:left,right,center',
            'challan_heading' => 'nullable|string',
            'invoice_heading' => 'nullable|string',
            'estimate_heading' => 'nullable|string',
            'return_challan_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'po_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
            'return_challan_heading' => 'nullable|string',
            'po_heading' => 'nullable|string',
            'challan_stamp' => 'nullable|boolean',
            'invoice_stamp' => 'nullable|boolean',
            'barcode_accept' => 'nullable|boolean',
            'po_stamp' => 'nullable|boolean',
            'return_challan_stamp' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
                'status_code' => 422,
            ]);
        }

        $companyLogo = CompanyLogo::where('user_id', $userId)->first();

        if (!$companyLogo) {
            $companyLogo = new CompanyLogo(['user_id' => $userId]);
            $companyLogo->save(); // Save the dummy data
        }
        $companyLogo = CompanyLogo::where('user_id', $userId)->first();

        // Define default values for each field
        $defaults = [
            'section_id' => $companyLogo->section_id,
            'challan_alignment' => $companyLogo->challan_alignment ?? 'center',
            'invoice_alignment' => $companyLogo->invoice_alignment ?? 'center',
            'challan_heading' => $companyLogo->challan_heading ?? null,
            'invoice_heading' => $companyLogo->invoice_heading ?? null,
            'return_challan_heading' => $companyLogo->return_challan_heading ?? null,
            'po_heading' => $companyLogo->po_heading ?? null,
            'challan_stamp' => $companyLogo->challan_stamp ?? true,
            'invoice_stamp' => $companyLogo->invoice_stamp ?? true,
            'return_challan_stamp' => $companyLogo->return_challan_stamp ?? true,
            'po_stamp' => $companyLogo->po_stamp ?? true,
            'barcode_accept' => $companyLogo->barcode_accept ?? null,
            'po_alignment' => $companyLogo->po_alignment ?? 'center',
            'return_challan_alignment' => $companyLogo->return_challan_alignment ?? 'center',
        ];

        // Merge the request data with the default values
        $data = array_merge($defaults, $request->all());

        // Update the existing CompanyLogo record with the new field values
        $companyLogo->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully',
            'status_code' => 200,
        ]);
    }

    public function removePreviewImage(Request $request)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $user = CompanyLogo::where('user_id', $userId)->first();
        // dd($user);
        $imageType = $request->input('type');
        $imagePath = '';

        switch ($imageType) {
            case 'challan':
                $imagePath = $user->challan_logo_url;
                break;
            case 'invoice':
                $imagePath = $user->invoice_logo_url;
                break;
            case 'return_challan':
                $imagePath = $user->return_challan_logo_url;
                break;
            case 'po':
                $imagePath = $user->po_logo_url;
                break;
        }

        // if ($imagePath && Storage::disk('s3')->exists($imagePath)) {
        //     Storage::disk('s3')->delete($imagePath);
        // }

        switch ($imageType) {
            case 'challan':
                $user->challan_logo_url = null;
                break;
            case 'invoice':
                $user->invoice_logo_url = null;
                break;
            case 'return_challan':
                $user->return_challan_logo_url = null;
                break;
            case 'po':
                $user->po_logo_url = null;
                break;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Image removed successfully',
            'status_code' => 200,
        ]);
    }


//     public function update(Request $request, $userId)
// {
//     // Validate the request data
//     $validator = Validator::make($request->all(), [
//         'challan_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
//         'invoice_logo_url' => 'nullable|file|mimes:jpg,jpeg,png,gif',
//         'challan_alignment' => 'nullable|in:left,right,center',
//         'invoice_alignment' => 'nullable|in:left,right,center',
//         'challan_heading' => 'nullable|string',
//         'invoice_heading' => 'nullable|string',
//         'challan_stamp' => 'nullable|in:0,1',
//         'invoice_stamp' => 'nullable|in:0,1',
//         'barcode_accept' => 'nullable|in:0,1',
//     ]);

//     // Check if the validation fails
//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => 'Validation failed',
//             'errors' => $validator->errors(),
//             'status_code' => 422,
//         ]);
//     }

//     // Get the companyLogo for the given user ID
//     $companyLogo = CompanyLogo::where('user_id', $userId)->first();

//     // Check if the companyLogo exists
//     if (!$companyLogo) {
//         // If it doesn't exist, create a new instance with dummy data
//         $companyLogo = new CompanyLogo([
//             'user_id' => $userId,
//         ]);
//         $companyLogo->save(); // Save the dummy data

//         // Fetch the newly created entry from the database
//         $companyLogo = CompanyLogo::where('user_id', $userId)->first();
//     }
//     // dd($companyLogo, $request->input('challan_alignment'));

//     // Update the existing or newly created CompanyLogo record with the new field values
//     $companyLogo->update([
//         'section_id' => $request->input('section_id'),
//         'challan_alignment' => $request->input('challan_alignment', $companyLogo->challan_alignment ?? 'center'),
//         'invoice_alignment' => $request->input('invoice_alignment', $companyLogo->invoice_alignment ?? 'center'),
//         'invoice_heading' => $request->input('invoice_heading', $companyLogo->invoice_heading),
//         'challan_stamp' => $request->input('challan_stamp', $companyLogo->challan_stamp),
//         'invoice_stamp' => $request->input('invoice_stamp', $companyLogo->invoice_stamp),
//         'barcode_accept' => $request->input('barcode_accept', $companyLogo->barcode_accept),
//     ]);


//     return response()->json([
//         'success' => true,
//         'message' => 'Settings updated successfully',
//         'status_code' => 200,
//     ]);
// }
    // Challan logo Upload
    public function signatureUploadSender(Request $request)
    {
        // dd( $request->all() );
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_sender' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_sender.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_sender;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_sender) {
            Storage::disk('s3')->delete($customUserLogo->signature_sender);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureSender', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_sender' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_sender' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }
    public function signatureUploadSeller(Request $request)
    {
        // dd( $request->all() );
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_seller' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_seller.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_seller;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_seller) {
            Storage::disk('s3')->delete($customUserLogo->signature_seller);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureSender', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_seller' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_seller' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }

    public function signatureUploadReceiver(Request $request)
    {

        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_receiver' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_receiver.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_receiver;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_receiver) {
            Storage::disk('s3')->delete($customUserLogo->signature_receiver);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureSender', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_receiver' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_receiver' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }


    public function signatureUploadBuyer(Request $request)
    {

        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_seller' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_seller.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_seller;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_seller) {
            Storage::disk('s3')->delete($customUserLogo->signature_seller);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureSender', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_seller' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_seller' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }

    public function signatureUploadBuyerReceiver(Request $request)
    {

        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_buyer' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_buyer.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_buyer;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_buyer) {
            Storage::disk('s3')->delete($customUserLogo->signature_buyer);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureSender', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_buyer' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_buyer' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }

    public function signatureUploadReceiptNote(Request $request)
    {
        // dd( $request->all() );
        // Validate the request input
        $validator = Validator::make($request->all(), [
            'signature_receipt_note' => 'required|file|mimes:jpg,jpeg,png,gif|dimensions:max_width=700,max_height=100',
        ], [
            'signature_receipt_note.dimensions' => 'Maximum Logo dimensions allowed is 700x100 pxl.',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'status_code' => 400,
            ],);
        }

        // Get the uploaded file
        $file = $request->signature_receipt_note;
        // dd($file);
        // Generate a unique filename for the image
        $filename = md5(uniqid()) . '.' . $file->getClientOriginalExtension();
        // dd($filename);
        // Get the current user's ID
        $user_id = auth()->user()->id;

        // Get the user's custom logo record from the database
        $customUserLogo = DB::table('company_logos')
            ->where('user_id', $user_id)
            ->first();

        // Delete the previous image if it exists
        if ($customUserLogo && $customUserLogo->signature_receipt_note) {
            Storage::disk('s3')->delete($customUserLogo->signature_receipt_note);
        }

        // Store the image in the S3 bucket with the generated filename
        $path = Storage::disk('s3')->put('signatureReceiptNote', $file, $filename);
        // dd($path);
        // Create or update the CustomUserLogo record in the database
        if ($customUserLogo) {
            DB::table('company_logos')
                ->where('user_id', $user_id)
                ->update([
                    'signature_receipt_note' => $path, // Update the S3 path to the image
                ]);
        } else {
            DB::table('company_logos')->insert([
                'user_id' => $user_id,
                'signature_receipt_note' => $path, // Store the S3 path to the image
            ]);
        }

        return response()->json([
            'message' => 'Signature uploaded successfully',
            'status_code' => 200,
        ]);
    }
}
