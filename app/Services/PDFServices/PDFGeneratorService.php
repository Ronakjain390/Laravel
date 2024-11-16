<?php
// PDFGenerator.php
namespace App\Services\PDFServices;


use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Aws\S3\S3Client;
use App\Models\Challan;
use App\Models\GoodsReceipt;
use App\Models\CompanyLogo;
use App\Models\Invoice;
use App\Models\PurchaseOrder;
use League\Flysystem\Config;
use App\Models\ReturnChallan;
use App\Models\ReturnInvoice;
use App\Models\TermsAndConditions;
use League\Flysystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class PDFGeneratorService
{
    private $s3Adapter;
    private $bucket;

    public function __construct()
    {
        $this->s3Adapter = new S3Client([
            'version' => 'latest',
            'region' => config('services.ses.region'),
            'credentials' => [
                'key' => config('services.ses.key'),
                'secret' => config('services.ses.secret'),
            ],
        ]);
        $this->bucket = config('services.s3.bucket');
    }

    public function generateChallanPDF($challan)
    {
        // try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 1)
            ->get();
                // dd($pdfData);
            // Use the Laravel View class to render the HTML template with data
            // $html = view('pdf.sender.challan_pdf', ['challan' => $challan])->render();
            if ($pdfData->challan_templete == 3) {
                $html = view('pdf.sender.challan_pdf_2', ['challan' => $challan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions, 'imagePath' => public_path('image/'.$challan->signature)])->render();
            }elseif ($pdfData->challan_templete == 5) {
                // dd('sdf');
                $html = view('pdf.sender.challan_pdf_5', ['challan' => $challan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions, 'imagePath' => public_path('image/'.$challan->signature)])->render();
            }
            elseif ($pdfData->challan_templete == 6) {
                $html = view('pdf.sender.challan_pdf_6', ['challan' => $challan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions, 'imagePath' => public_path('image/'.$challan->signature)])->render();
            }
             else {
                // dd('else');
                $html = view('pdf.sender.challan_pdf', ['challan' => $challan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions, 'imagePath' => public_path('image/'.$challan->signature)])->render();
            }

            // Create a new Dompdf instance
            $dompdf = new Dompdf();
            // dd($dompdf);
            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);


            if ($pdfData->challan_templete == 5) {
            $dompdf->setPaper([0, 0, 226.77, 566.93], 'portrait');
            }elseif($pdfData->challan_templete == 6) {
                $dompdf->setPaper([0, 0, 265, 566.93], 'portrait');
            }
            else{
                $dompdf->setPaper('A4', 'portrait');
            }

            // dd($dompdf);

            // Render the PDF content
            $dompdf->render();
            // dd($dompdf->output());
            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $challan->senderUser;
            $senderName = strtolower(str_replace(' ', '-', $user->name));
            $senderId = $user->id;
            $panelName = 'sender'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $challanSeries = strtolower($challan->challan_series);
            $seriesNum = $challan->series_num;
            $fileName = "Challan_{$challanSeries}_{$seriesNum}.pdf";

            $filePath = "/{$senderName}-{$senderId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";
            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);
            // dd($result,Storage::disk('s3')->url($filePath));

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     // 'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;
            // dd(Storage::disk('s3')->url($filePath));

            return response()->json([
                'message' => 'PDF created successfully.',
                'challan_id' => $challan->id,
                'pdf_url' => $pdfUrl,
                'pdfData' => $pdfData,
                'status_code' => 200
            ], 200);
        // } catch (\Exception $e) {
        //     // Handle any errors that occur during PDF generation and storage
        //     // You can log the error or throw an exception if needed
        //     // Log the error in the pdferrorlog file
        //     Log::channel('pdferrorlog')->error("Error generating and storing Challan PDF: " . $e->getMessage());

        //     // Throw the exception to be handled further up the call stack
        //     throw new \Exception("Error generating and storing Challan PDF: " . $e->getMessage());
        // }
    }

    public function generateReturnChallanPDF(ReturnChallan $returnChallan)
    {
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();

            // Use the Laravel View class to render the HTML template with data
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 2)
            ->get();

            $html = View::make('pdf.receiver.return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $returnChallan->senderUser;
            $senderName = strtolower(str_replace(' ', '-', $user->name));
            $senderId = $user->id;
            $panelName = 'receiver'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $returnChallanSeries = strtolower($returnChallan->challan_series);
            $seriesNum = $returnChallan->series_num;
            $fileName = "ReturnChallan_{$returnChallanSeries}_{$seriesNum}.pdf";

            $filePath = "/{$senderName}-{$senderId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);


            $pdfUrl = $filePath;

            return response()->json([
                'message' => 'PDF created successfully.',
                'return_challan_id' => $returnChallan->id,
                'pdf_url' => $pdfUrl,
                'pdfData' => $pdfData,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing ReturnChallan PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing ReturnChallan PDF: " . $e->getMessage());
        }
    }

    public function generateSelfReturnChallanPDF(ReturnChallan $returnChallan)
    {
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();

            // Use the Laravel View class to render the HTML template with data
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 2)
            ->get();

            $html = View::make('pdf.receiver.self_return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $returnChallan->senderUser;
            $senderName = strtolower(str_replace(' ', '-', $user->name));
            $senderId = $user->id;
            $panelName = 'receiver'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $returnChallanSeries = strtolower($returnChallan->challan_series);
            $seriesNum = $returnChallan->series_num;
            $fileName = "ReturnChallan_{$returnChallanSeries}_{$seriesNum}.pdf";

            $filePath = "/{$senderName}-{$senderId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;

            return response()->json([
                'message' => 'PDF created successfully.',
                'return_challan_id' => $returnChallan->id,
                'pdf_url' => $pdfUrl,
                'pdfData' => $pdfData,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing ReturnChallan PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing ReturnChallan PDF: " . $e->getMessage());
        }
    }

    public function generateInvoicePDF($invoice)
    {
        // dd($invoice);
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 3)
            ->get();
            // dd($termsAndConditions);

            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.seller.invoice_pdf', ['invoice' => $invoice,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // $html = View::make('pdf.receiver.return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // dd($html);
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $invoice->sellerUser;
            $sellerName = strtolower(str_replace(' ', '-', $user->name));
            $sellerId = $user->id;
            $panelName = 'seller'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $invoiceSeries = strtolower($invoice->invoice_series);
            $seriesNum = $invoice->series_num;
            $fileName = "Invoice_{$invoiceSeries}_{$seriesNum}.pdf";

            $filePath = "/{$sellerName}-{$sellerId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;


            return response()->json([
                'message' => 'PDF created successfully.',
                'invoice_id' => $invoice->id,
                'pdf_url' => $pdfUrl,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing Invoice PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing Invoice PDF: " . $e->getMessage());
        }
    }

    // Create Estimate PDF
    public function generateEstimatePDF($estimate)
    {
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 6)
            ->get();
            // dd($termsAndConditions);

            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.seller.estimate_pdf', ['estimate' => $estimate,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // $html = View::make('pdf.receiver.return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // dd($html);
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $estimate->sellerUser;
            $sellerName = strtolower(str_replace(' ', '-', $user->name));
            $sellerId = $user->id;
            $panelName ='seller'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $estimateSeries = strtolower($estimate->estimate_series);
            $seriesNum = $estimate->series_num;

            $fileName = "Estimate_{$estimateSeries}_{$seriesNum}.pdf";
            $filePath = "/{$sellerName}-{$sellerId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";
            // Check if the PDF file already exists

            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;


            return response()->json([
               'message' => 'PDF created successfully.',
                'estimate_id' => $estimate->id,
                'pdf_url' => $pdfUrl,
               'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing Invoice PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing Invoice PDF: " . $e->getMessage());

            return response()->json([
               'message' => 'Error generating PDF.',
               'status_code' => 500
            ], 500);

        }
    }

    // Create Eway Bill PDF
    public function generateInvoiceEwayBillPDF($invoice)
    {
        // dd($invoice);
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 3)
            ->get();
            // dd($termsAndConditions);

            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.seller.invoice_e_way_bill', ['invoice' => $invoice,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // $html = View::make('pdf.receiver.return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // dd($html);
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $invoice->sellerUser;
            $sellerName = strtolower(str_replace(' ', '-', $user->name));
            $sellerId = $user->id;
            $panelName = 'seller'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $invoiceSeries = strtolower($invoice->invoice_series);
            $seriesNum = $invoice->series_num;
            $fileName = "Invoice_{$invoiceSeries}_{$seriesNum}.pdf";

            $filePath = "/{$sellerName}-{$sellerId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;


            return response()->json([
                'message' => 'PDF created successfully.',
                'invoice_id' => $invoice->id,
                'pdf_url' => $pdfUrl,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing Invoice PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing Invoice PDF: " . $e->getMessage());
        }
    }

    public function generatePurchaseOrder($purchaseOrder)
    {
        // try {
            // dd($purchaseOrder);
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->sender) ? DB::table('users')->where('special_id', $prefix->sender->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 4)
            ->get();
            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.buyer.purchase_order_pdf', ['purchaseOrder' => $purchaseOrder,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $purchaseOrder->sellerUser;
            $sellerName = strtolower(str_replace(' ', '-', $user->name));
            $sellerId = $user->id;
            $panelName = 'receiver'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $returnInvoiceSeries = strtolower($purchaseOrder->purchase_order_series);
            $seriesNum = $purchaseOrder->series_num;
            $fileName = "ReturnInvoice_{$returnInvoiceSeries}_{$seriesNum}.pdf";

            $filePath = "/{$sellerName}-{$sellerId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;

            return response()->json([
                'message' => 'PDF created successfully.',
                'purchase_order_id' => $purchaseOrder->id,
                'pdf_url' => $pdfUrl,
                'status_code' => 200
            ], 200);
        // } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
        //     Log::channel('pdferrorlog')->error("Error generating and storing ReturnInvoice PDF: " . $e->getMessage());

        //     // Throw the exception to be handled further up the call stack
        //     throw new \Exception("Error generating and storing ReturnInvoice PDF: " . $e->getMessage());
        // }
    }
    public function planInvoicePDF($orders)
    {
        // dd($orders);
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 4)
            ->get();
            // dd($termsAndConditions);

            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.orderInvoice.order_invoice_pdf', ['orders' => $orders,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();
            // $html = View::make('pdf.receiver.return_challan_pdf', ['challan' => $returnChallan, 'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // dd($html);
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $orders->user;
            $buyerName = strtolower(str_replace(' ', '-', $user->name));
            $buyerId = $user->id;
            $panelName = 'buyer'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $fileName = "Invoice.pdf";

            $filePath = "/{$buyerName}-{$buyerId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;
                // dd($pdfUrl);

            return response()->json([
                'message' => 'PDF created successfully.',
                'orders_id' => $orders->id,
                'pdf_url' => $pdfUrl,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing Invoice PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing Invoice PDF: " . $e->getMessage());
        }
    }

    public function generateGoodsReceiptPDF($goodsReceipt)
    {
        // dd($goodsReceipt);
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
            // $userId = isset($prefix->seller) ? DB::table('users')->where('special_id', $prefix->seller->special_id)->pluck('id')->first() : null;
            $pdfData = CompanyLogo::where('user_id', $userId)->first();
            // dd($pdfData);
            $termsAndConditions = TermsAndConditions::where('user_id', $userId)
            ->where('panel_id', 5)
            ->get();
            // dd($termsAndConditions);

            // Use the Laravel View class to render the HTML template with data
            $html = View::make('pdf.goodsReceipt.receipt_note_pdf', ['goodsReceipt' => $goodsReceipt,'pdfData' => $pdfData, 'termsAndConditions' => $termsAndConditions])->render();

            // dd($html);
            // Create a new Dompdf instance
            $dompdf = new Dompdf();

            // Set Dompdf options if needed
            $options = new Options();
            $options->set('isRemoteEnabled', true); // Enable loading images or CSS from external URLs
            $dompdf->setOptions($options);

            // Load the HTML content into Dompdf
            $dompdf->loadHtml($html);

            // (Optional) Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');

            // Render the PDF content
            $dompdf->render();

            // Get the PDF content as a string
            $pdfContent = $dompdf->output();

            // Create the file path based on the user-specific criteria
            $user = $goodsReceipt->senderUser;
            $senderName = strtolower(str_replace(' ', '-', $user->name));
            $senderId = $user->id;
            $panelName = 'sender'; // Replace with the actual panel name
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $date = Carbon::now()->day;
            $invoiceSeries = strtolower($goodsReceipt->invoice_series);
            $seriesNum = $goodsReceipt->series_num;
            $fileName = "Invoice_{$invoiceSeries}_{$seriesNum}.pdf";

            $filePath = "/{$senderName}-{$senderId}/{$panelName}/{$year}/{$month}/{$date}/{$fileName}";

            // Check if the PDF file already exists
            if (Storage::disk('s3')->exists($filePath)) {
                // Delete the older file
                Storage::disk('s3')->delete($filePath);
            }

            // Store the new PDF in the S3 bucket
            $result = Storage::disk('s3')->put($filePath, $pdfContent);

            // $result = $this->s3Adapter->putObject([
            //     'Bucket' => $this->bucket,
            //     'Key' => $filePath,
            //     'Body' => $pdfContent,
            //     'ACL' => 'public-read', // Make the PDF publicly accessible
            // ]);

            // Get the URL of the stored PDF
            $pdfUrl = $filePath;


            return response()->json([
                'message' => 'PDF created successfully.',
                'invoice_id' => $goodsReceipt->id,
                'pdf_url' => $pdfUrl,
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            // Handle any errors that occur during PDF generation and storage
            // You can log the error or throw an exception if needed
            // Log the error in the pdferrorlog file
            Log::channel('pdferrorlog')->error("Error generating and storing Invoice PDF: " . $e->getMessage());

            // Throw the exception to be handled further up the call stack
            throw new \Exception("Error generating and storing Invoice PDF: " . $e->getMessage());
        }
    }
}
