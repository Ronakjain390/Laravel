<?php
// PDFGenerator.php
namespace App\Services\PDFServices;


use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Aws\S3\S3Client;
use App\Models\Challan;
use App\Models\Estimates;
use App\Mail\ChallanPDFMail;
use App\Mail\SfpReturnChallanPDFMail;
use App\Mail\SfpChallanPDFMail;
use App\Mail\SfpInvoicePDFMail;
use App\Mail\AcceptChallanPDFMail;
use App\Mail\AcceptReturnChallanPDFMail;
use App\Mail\RejectChallanPDFMail;
use App\Mail\InvoicePDFMail;
use App\Mail\EstimatePDFMail;
use League\Flysystem\Config;
use App\Models\ReturnChallan;
use League\Flysystem\Filesystem;
use App\Mail\ReturnChallanPDFMail;
use App\Mail\PurchaseOrderPDFMail;
use App\Models\Invoice;
use App\Models\CompanyLogo;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Log;
use App\Mail\ReturnInvoicePDFMail;
use App\Models\ReturnInvoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;
use App\Mail\AddCommentSentChallanMail;
use App\Mail\AddCommentReturnChallanMail;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class PDFEmailService
{
    public function userRegistrationEmail($userId)
    {
        // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData);
        $subject = 'New User Registration';
        $message = 'Hello, Admin New user has been register. Kindly login to your https://theparchi.com/admin-login account to accept or reject it. Still not using TheParchi? Get your free account today.';

        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("sourabhverma.793@gmail.com")->send(new ChallanPDFMail($subject, $message, $pdfUrl));
            Mail::to("jainronak390@gmail.com");
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    public function sendChallanByEmail(Challan $challan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData);
        $subject = 'New ' . (empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading) . ' ' . $challan->challan_series . '-' . $challan->series_num . ' from ' . ucfirst($challan->senderUser->company_name ?? $challan->senderUser->name);
        $message = 'Hello, ' . $challan->receiver . ' you have received a new Challan from ' . ucfirst($challan->senderUser->company_name ?? $challan->senderUser->name) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("sourabhverma.793@gmail.com")->send(new ChallanPDFMail($subject, $message, $pdfUrl));
            Mail::to($recipientEmail)->send(new ChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }
    public function sendReturnChallanByEmail(ReturnChallan $returnChallan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($recipientEmail);
        $subject = 'New ' . ($pdfData->challan_heading ?? 'ReturnChallan ') .' '. $returnChallan->challan_series . '-' . $returnChallan->series_num. ' from '. ucfirst($returnChallan->sender);
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));

        $message = 'Hello, ' . $returnChallan->receiver . ' you have received a new ReturnChallan from ' . $returnChallan->sender . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new ReturnChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $returnChallan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending ReturnChallan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending ReturnChallan PDF email: ' . $e->getMessage());
            // dd('not-sent');
            return false;
        }
    }

    // Send Invoice
    public function sendInvoiceByEmail(Invoice $invoice, $pdfUrl, $recipientEmail)
    {
        // $subject = 'Invoice PDF - ' . $invoice->invoice_series . '-' . $invoice->series_num;
        // $message = 'Hello, ' . $invoice->buyer . ' you have received a new Invoice from ' . $invoice->sender . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData);
        $subject = 'New ' . (empty($pdfData->invoiceheading) ? 'Invoice' : $pdfData->invoiceheading) . ' ' . $invoice->invoice_series . '-' . $invoice->series_num . ' from ' . ucfirst($invoice->seller);
        $message = 'Hello, ' . $invoice->buyer . ' you have received a new Invoice from ' . ucfirst($invoice->seller) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new InvoicePDFMail($subject, $message, $pdfUrl,$invoice));

            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Invoice PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Invoice PDF email: ' . $e->getMessage());
            return false;
        }
    }

    // Send Return Invoice
    public function sendPurchaseOrderByEmail(PurchaseOrder $purchaseOrder, $pdfUrl, $recipientEmail)
    {
        $subject = 'PurchaseOrder PDF - ' . $purchaseOrder->purchase_order_series . '-' . $purchaseOrder->series_num;
        $message = 'Hello, ' . $purchaseOrder->buyer . ' you have received a new PurchaseOrder from ' . $purchaseOrder->seller . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';

        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new PurchaseOrderPDFMail($subject, $message, $pdfUrl, $purchaseOrder));
            // Mail::to($recipientEmail)->send(new ChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan));
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending PurchaseOrder PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending PurchaseOrder PDF email: ' . $e->getMessage());
            return false;
        }
    }

    // Send Receipt Note Email
    public function sendReceiptNoteByEmail(GoodsReceipt $goodsReceipt, $pdfUrl, $recipientEmail)
    {
        // dd($goodsReceipt);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData);
        $subject = 'New ' . (empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading) . ' ' . $goodsReceipt->challan_series . '-' . $goodsReceipt->series_num . ' from ' . ucfirst($goodsReceipt->senderUser->company_name ?? $goodsReceipt->senderUser->name);
        $message = 'Hello, ' . $goodsReceipt->receiver . ' you have received a new Challan from ' . ucfirst($goodsReceipt->senderUser->company_name ?? $goodsReceipt->senderUser->name) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("sourabhverma.793@gmail.com")->send(new ChallanPDFMail($subject, $message, $pdfUrl));
            Mail::to($recipientEmail)->send(new ChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $goodsReceipt));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    // Send Estimate
    public function sendEstimateByEmail(Estimates $estimate, $pdfUrl, $recipientEmail)
    {
        // $subject = 'Estimate PDF - ' . $estimate->invoice_series . '-' . $estimate->series_num;
        // $message = 'Hello, ' . $estimate->buyer . ' you have received a new Estimate from ' . $estimate->sender . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($pdfData);
        $subject = 'New ' . (empty($pdfData->invoiceheading) ? 'Quotation' : $pdfData->invoiceheading) . ' ' . $estimate->estimate_series . '-' . $estimate->series_num . ' from ' . ucfirst($estimate->seller);
        $message = 'Hello, ' . $estimate->buyer . ' you have received a new Quotation from ' . ucfirst($estimate->seller) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new EstimatePDFMail($subject, $message, $pdfUrl,$estimate));

            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Estimate PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Estimate PDF email: ' . $e->getMessage());
            return false;
        }
    }

    public function acceptChallanByEmail(Challan $challan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($recipientEmail);
        $subject = ($pdfData->challan_heading ?? 'Challan ') .' '. $challan->challan_series . '-' . $challan->series_num. ' Accepted by '. ucfirst($challan->receiver);
        $message = 'Hello, ' . ucfirst($challan->sender) . ' you have received a new Challan from ' . ucfirst($challan->receiver) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("sourabhverma.793@gmail.com")->send(new ChallanPDFMail($subject, $message, $pdfUrl));
            Mail::to($recipientEmail)->send(new AcceptChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }
    public function rejectChallanByEmail(Challan $challan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($recipientEmail);
        $subject = ($pdfData->challan_heading ?? 'Challan ') .' '. $challan->challan_series . '-' . $challan->series_num. ' Rejected by '. ucfirst($challan->receiver);
        $message = 'Hello, ' . $challan->receiver . ' you have received a new Challan from ' . ucfirst($challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("sourabhverma.793@gmail.com")->send(new ChallanPDFMail($subject, $message, $pdfUrl));
            Mail::to($recipientEmail)->send(new RejectChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    public function acceptReturnChallanByEmail(ReturnChallan $returnChallan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($recipientEmail);
        $subject = ($pdfData->challan_heading ?? 'ReturnChallan ') .' '. $returnChallan->challan_series . '-' . $returnChallan->series_num. ' Accepted by '. ucfirst($returnChallan->receiver);
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));

        $message = 'Hello, ' . $returnChallan->sender . ' you have received a new ReturnChallan from ' . $returnChallan->receiver . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new AcceptReturnChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $returnChallan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending ReturnChallan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending ReturnChallan PDF email: ' . $e->getMessage());
            // dd('not-sent');
            return false;
        }
    }

    public function rejectReturnChallanByEmail(ReturnChallan $returnChallan, $pdfUrl, $recipientEmail)
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        // dd($recipientEmail);
        $subject = ($pdfData->challan_heading ?? 'ReturnChallan ') .' '. $returnChallan->challan_series . '-' . $returnChallan->series_num. ' Accepted by '. ucfirst($returnChallan->receiver);
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));

        $message = 'Hello, ' . $returnChallan->sender . ' you have received a new ReturnChallan from ' . $returnChallan->receiver . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        try {
            // Send the email with the PDF attachment using the mailable
            Mail::to($recipientEmail)->send(new AcceptReturnChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $returnChallan));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending ReturnChallan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending ReturnChallan PDF email: ' . $e->getMessage());
            // dd('not-sent');
            return false;
        }
    }

    // Send SFP Challan Email
    public function sendChallanSfpByEmail(Challan $challan, $recipientEmail, $userName)
    {
        // dd($recipientEmail);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $challan->pdf_url;
        $subject = 'New ' . (empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading) . ' ' . $challan->challan_series . '-' . $challan->series_num .' from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender);

        $message = 'Hello, ' . $challan->receiver . ' you have received a new SFP Challan Request from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpChallanPDFMail($subject, $message, $pdfUrl, $challan, $userName));
            Mail::to($recipientEmail)->send(new SfpChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan, $userName));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    // Send SFP Return Challan Email
    public function returnChallanSfpByEmail(ReturnChallan $challan, $recipientEmail, $userName)
    {
        // dd($challan->statuses);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $challan->pdf_url;
        $subject = 'New ' . (empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading) . ' ' . $challan->challan_series . '-' . $challan->series_num .' from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender);

        $message = 'Hello, ' . $challan->receiver . ' you have received a new SFP Challan'.(empty($pdfData->challan_heading) ? 'Challan' : $pdfData->challan_heading) . ' from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpReturnChallan($subject, $message, $pdfUrl, $challan,$userName));
            Mail::to($recipientEmail)->send(new SfpReturnChallanPDFMail($subject, $message, $invoiceTemporaryUrl, $challan, $userName));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    public function invoiceSfpByEmail(Invoice $invoice, $recipientEmail, $userName)
    {

        // dd($invoice->statuses);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $invoice->pdf_url;
        $subject = 'New ' . (empty($pdfData->invoice_heading) ? 'Invoice' : $pdfData->invoice_heading) . ' ' . $invoice->invoice_series . '-' . $invoice->series_num .' from ' . ucfirst(isset($invoice->statuses[0]->team_user_name) ? $invoice->statuses[0]->team_user_name : $invoice->sender);

        $message = 'Hello, ' . $invoice->receiver . ' you have received a new SFP Invoice'.(empty($pdfData->invoice_heading) ? 'Invoice' : $pdfData->invoice_heading) . ' from ' . ucfirst(isset($invoice->statuses[0]->team_user_name) ? $invoice->statuses[0]->team_user_name : $invoice->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it. Still not using TheParchi? Get your free account today.';
        $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpInvoicePDFMail($subject, $message, $pdfUrl, $invoice, $userName));
            Mail::to($recipientEmail)->send(new SfpInvoicePDFMail($subject, $message, $invoiceTemporaryUrl, $challan, $userName));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Invoice PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Invoice PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    // Mail on add Comment
    public function addCommentSentChallanMail(Challan $challan, $recipientEmail, $status_comment)
    {
        $request = request();
        // dd($request->all(), $request->has('receiver'));0
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $challan->pdf_url;

        if($request->has('receiver'))
        {
            $heading = $pdfData->return_challan_heading  ?? 'Challan';
        }
        elseif($request->has('sender'))
        {
            $heading = $pdfData->challan_heading ?? 'Return Challan';
        }

        $subject = 'New ' . $heading . ' ' . $challan->challan_series . '-' . $challan->series_num .' from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender);

        // $message = 'Hello, ' . $challan->receiver . ' you have received a new SFP Challan Request from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it.';
        // $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpChallanPDFMail($subject, $message, $pdfUrl, $challan, $userName));
            Mail::to($recipientEmail)->send(new AddCommentSentChallanMail($subject, $challan, $status_comment, $heading));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }

    // Mail on add Comment
    public function addCommentReturnChallanMail(ReturnChallan $returnChallan, $recipientEmail, $status_comment)
    {
        $request = request();
        // dd($request->all(), $request->has('receiver'));0
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $returnChallan->pdf_url;

        if($request->has('receiver'))
        {
            $heading = $pdfData->return_challan_heading  ?? 'Challan';
        }
        elseif($request->has('sender'))
        {
            $heading = $pdfData->challan_heading ?? 'Return Challan';
        }

        $subject = 'New ' . $heading . ' ' . $returnChallan->challan_series . '-' . $returnChallan->series_num .' from ' . ucfirst(isset($returnChallan->statuses[0]->team_user_name) ? $returnChallan->statuses[0]->team_user_name : $returnChallan->sender);

        // $message = 'Hello, ' . $challan->receiver . ' you have received a new SFP Challan Request from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it.';
        // $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpChallanPDFMail($subject, $message, $pdfUrl, $challan, $userName));
            Mail::to($recipientEmail)->send(new AddCommentReturnChallanMail($subject, $returnChallan, $status_comment, $heading));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }
    // Mail on add Comment
    public function addCommentInvoiceMail(Invoice $invoice, $recipientEmail, $status_comment)
    {
        $request = request();
        // dd($request->all(), $request->has('receiver'));0
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $pdfData = CompanyLogo::where('user_id', $userId)->first();
        $pdfUrl = $challan->pdf_url;

        if($request->has('receiver'))
        {
            $heading = $pdfData->return_challan_heading  ?? 'Challan';
        }
        elseif($request->has('sender'))
        {
            $heading = $pdfData->challan_heading ?? 'Return Challan';
        }

        $subject = 'New ' . $heading . ' ' . $challan->challan_series . '-' . $challan->series_num .' from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender);

        // $message = 'Hello, ' . $challan->receiver . ' you have received a new SFP Challan Request from ' . ucfirst(isset($challan->statuses[0]->team_user_name) ? $challan->statuses[0]->team_user_name : $challan->sender) . '. Kindly login to your https://theparchi.com account to accept or reject it.';
        // $invoiceTemporaryUrl = Storage::disk('s3')->temporaryUrl($pdfUrl, now()->addHours(1));
        // dd($invoiceTemporaryUrl);
        try {
            // Send the email with the PDF attachment using the mailable
            // Mail::to("jainronak390@gmail.com")->send(new SfpChallanPDFMail($subject, $message, $pdfUrl, $challan, $userName));
            Mail::to($recipientEmail)->send(new AddCommentSentChallanMail($subject, $challan, $status_comment, $pdfData->challan_heading));
            // dd('sent');
            // Return true to indicate that the email was sent successfully
            return true;
        } catch (\Exception $e) {
            Log::channel('pdfemailerrorlog')->error("Error sending Challan PDF email: " . $e->getMessage());
            // Handle the exception, log the error, or return false to indicate that the email sending failed.
            Log::error('Error sending Challan PDF email: ' . $e->getMessage());
            // dd('not sent');
            return false;
        }
    }
}
