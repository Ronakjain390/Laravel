<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use App\Models\Challan;
use Livewire\Component;
use App\Models\CompanyLogo;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\CompanyLogo\CompanyLogoController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

class CompanyLogoComponent extends Component
{
    public $challan_heading, $estimate_heading, $challan_logo_url, $estimate_logo_url, $po_logo_url, $invoice_logo_url, $companyLogoData, $invoice_heading, $successMessage, $errorMessage, $isChecked, $challan_alignment, $file, $requestData, $invoice_alignment, $challanTemporaryImageUrl, $estimateTemporaryImageUrl,$returnChallanTemporaryImageUrl, $poTemporaryImageUrl, $errors, $invoiceTemporaryImageUrl,  $challanData, $invoiceData , $receiptNoteData, $estimateData;
    public $challanStampChecked = false;
    public $invoiceStampChecked = false;
    public $activeTab;
    public $unit;
    public $shortName;

    use WithFileUploads;

    protected $listeners = ['unitAdded' => 'unitAdded'];

    public function unitAdded()
    {
        $this->unit = '';
        $this->shortName = '';
    }
    public $companyLogoDataset = array(
        'challan_heading' => '',
        'estimate_heading' => '',
        'invoice_heading' => '',
        'receipt_note_heading' => '',
        'challan_alignment' => '',
        'invoice_alignment' => '',
        'invoice_alignment' => '',
        'challan_stamp' => '',
        'invoice_stamp' => '',
        'invoice_logo_url' => '',
        'challan_logo_url' => '',
        'estimate_logo_url' => '',
        'barcode_accept' => '',
        'return_challan_logo_url' => '',
        'po_logo_url' => '',
        'return_challan_alignment' => '',
        'po_alignment' => '',
        'return_challan_heading' => '',
        'po_heading' => '',
        'return_challan_stamp' => '',
        'po_stamp' => '',
        'receipt_note_stamp' => '',

    );
    public $termsAndConditionsData = array(
        'content' => '',
        'panel_id' => '1',
        'section_id' => '1',
    );
    public $barcode;
    public $self_delivery;
    public $tags;
    public $payment_status;

    protected $queryString = ['activeTab'];


    public function updatedActiveTab($value)
{
    $tabEvents = [
        'tab1' => 'reloadTab1',
        'tab2' => 'reloadTab2',
        'tab3' => 'reloadTab3',
        'tab4' => 'reloadTab4',
        'tab5' => 'reloadTab5',
        // Add more tabs here if needed
    ];

    if (array_key_exists($value, $tabEvents)) {
        $this->emit($tabEvents[$value], $value); // Emit a custom event with the tab name
        $this->emit('redirectWithTab', $value); // Emit an event for client-side redirection
    }
}


    public function mount()
    {
        $id = '';
        $request = request();
        $this->activeTab = request()->query('tab', 'tab1');

        $companyLogo = new CompanyLogoController;
        $data = $companyLogo->index($request, $id);
        $this->companyLogoData = json_decode($data->getContent(), true);
        // dd($this->companyLogoData);
        $this->isChecked = $this->companyLogoDataset['challan_stamp'] == 1;
        if (isset($this->companyLogoData['companyLogo']['challan_alignment'])) {
            $this->challan_alignment = $this->companyLogoData['companyLogo']['challan_alignment'];
        }

        if (isset($this->companyLogoData['companyLogo']['invoice_alignment'])) {
            $this->invoice_alignment = $this->companyLogoData['companyLogo']['invoice_alignment'];
        }

        $this->updateCompanyData();

        $query = new UserAuthController;
        $response = $query->userActivePlan($request);
        $response = $response->getData();

            $this->activePlan =  json_encode($response->user);
            // dd($this->activePlan);

        $this->invoiceTermsAndConditions($request);
        $this->challanTermsAndConditions($request);
        $this->returnChallanTermsAndConditions($request);
        $this->poTermsAndConditions($request);


        if (isset($this->companyLogoData['companyLogo']['signature_option_sender']) && $this->companyLogoData['companyLogo']['signature_option_sender'] == 'Signature') {
            $this->selectedOption = 'Signature';
        } elseif (isset($this->companyLogoData['companyLogo']['signature_option_sender']) && $this->companyLogoData['companyLogo']['signature_option_sender'] == 'FooterStamp') {
            $this->selectedOption = 'FooterStamp';
        } else {
            $this->selectedOption = 'None';
        }

        if (isset($this->companyLogoData['companyLogo']['signature_option_receiver']) && $this->companyLogoData['companyLogo']['signature_option_receiver'] == 'Signature') {
            $this->selectedOptionReceiver = 'Signature';
        } elseif (isset($this->companyLogoData['companyLogo']['signature_option_receiver']) && $this->companyLogoData['companyLogo']['signature_option_receiver'] == 'FooterStamp') {
            $this->selectedOptionReceiver = 'FooterStamp';
        } else {
            $this->selectedOptionReceiver = 'None';
        }

        if (isset($this->companyLogoData['companyLogo']['signature_option_seller']) && $this->companyLogoData['companyLogo']['signature_option_seller'] == 'Signature') {
            $this->selectedOptionSeller = 'Signature';
        } elseif (isset($this->companyLogoData['companyLogo']['signature_option_seller']) && $this->companyLogoData['companyLogo']['signature_option_seller'] == 'FooterStamp') {
            $this->selectedOptionSeller = 'FooterStamp';
        } else {
            $this->selectedOptionSeller = 'None';
        }
        if (isset($this->companyLogoData['companyLogo']['signature_option_buyer']) && $this->companyLogoData['companyLogo']['signature_option_buyer'] == 'Signature') {
            $this->selectedOptionBuyer = 'Signature';
        } elseif (isset($this->companyLogoData['companyLogo']['signature_option_buyer']) && $this->companyLogoData['companyLogo']['signature_option_buyer'] == 'FooterStamp') {
            $this->selectedOptionBuyer = 'FooterStamp';
        } else {
            $this->selectedOptionBuyer = 'None';
        }
        if (isset($this->companyLogoData['companyLogo']['signature_option_receipt_note']) && $this->companyLogoData['companyLogo']['signature_option_receipt_note'] == 'Signature') {
            $this->selectedOptionReceiptNote = 'Signature';
        } elseif (isset($this->companyLogoData['companyLogo']['signature_option_receipt_note']) && $this->companyLogoData['companyLogo']['signature_option_receipt_note'] == 'FooterStamp') {
            $this->selectedOptionReceiptNote = 'FooterStamp';
        } else {
            $this->selectedOptionReceiptNote = 'None';
        }

        // if (isset($this->companyLogoData['companyLogo']['signature_seller'])) {
        //     $this->selectedOptionSeller = 'Signature';
        // } else {
        //     $this->selectedOptionSeller = 'FooterStamp';
        // }
        // if (isset($this->companyLogoData['companyLogo']['signature_buyer'])) {
        //     $this->selectedOptionBuyer = 'Signature';
        // } else {
        //     $this->selectedOptionBuyer = 'FooterStamp';
        // }
        // if (isset($this->companyLogoData['companyLogo']['signature_receiver'])) {
        //     $this->selectedOptionReceiver = 'Signature';
        // } else {
        //     $this->selectedOptionReceiver = 'FooterStamp';
        // }

        if (isset($this->companyLogoData['companyLogo']['challan_templete'])) {
            $this->selectedChallanTemplate = $this->companyLogoData['companyLogo']['challan_templete'];
        }
        if (isset($this->companyLogoData['companyLogo']['grn_template'])) {
            $this->selectedGrnTemplate = $this->companyLogoData['companyLogo']['grn_template'];
        }

        $this->barcode = auth()->user()->barcode;
        $this->self_delivery = auth()->user()->self_delivery;
        $this->tags = auth()->user()->tags;
        $this->payment_status = auth()->user()->payment_status;
        // dd($this->companyLogoData['companyLogo']['challan_templete'], 'selectedChallanTemplate');

    }

    public $selectedOption, $senderSignature, $signatures, $signature_sender, $signature_receipt_note, $selectedChallanTemplate, $selectedGrnTemplate;

    public function selectedOption($option)
    {
        // dd($option);
        $this->selectedOption = $option;
    }

    public function updatedSelectedOptionReceiver($value)
    {
        $this->selectedOptionReceiver = $value;
        $this->saveSelectedOption();
    }

    public function updatedSelectedOptionSeller($value)
    {
        $this->selectedOptionSeller = $value;
        $this->saveSelectedOption();
    }

    public function updatedSelectedOptionBuyer($value)
    {
        $this->selectedOptionBuyer = $value;
        $this->saveSelectedOption();
    }

    public function updatedSelectedOptionReceiptNote()
    {
        $this->saveSelectedOption();
    }

    public function updatedSelectedOption($value)
    {
        // dd($value);
        $this->saveSelectedOption();
    }

    public function saveSelectedOption()
    {
            $id = $this->companyLogoData['companyLogo']['id'];
            // Retrieve the CompanyLogo model instance
            $companyLogo = CompanyLogo::find($id);

            $companyLogo->signature_option_sender = $this->selectedOption;
            $companyLogo->signature_option_receiver = $this->selectedOptionReceiver;
            $companyLogo->signature_option_seller = $this->selectedOptionSeller;
            $companyLogo->signature_option_buyer = $this->selectedOptionBuyer;
            $companyLogo->signature_option_receipt_note = $this->selectedOptionReceiptNote;
            // Save the changes to the database
            $companyLogo->save();

        // if($this->selectedOption)
        // {
        //     // Update the signature_option_sender field
        //     $companyLogo->signature_option_sender = $this->selectedOption;
        //     // Save the changes to the database
        //     $companyLogo->save();
        //     // dd($companyLogo);
        // }elseif($this->selectedOptionReceiver)
        //     {
        //         dd($this->selectedOptionReceiver);
        //         // Update the signature_option_sender field
        //         $companyLogo->signature_option_receiver = $this->selectedOptionReceiver;
        //         // Save the changes to the database
        //         $companyLogo->save();
        //     }
        //     elseif($this->selectedOptionSeller)
        //     {
        //         // Update the signature_option_sender field
        //         $companyLogo->signature_option_seller = $this->selectedOptionSeller;
        //         // Save the changes to the database
        //         $companyLogo->save();
        //     }
        //     elseif($this->selectedOptionBuyer)
        //     {
        //         // Update the signature_option_sender field
        //         $companyLogo->signature_option_buyer = $this->selectedOptionBuyer;
        //         // Save the changes to the database
        //         $companyLogo->save();
        //     }
        //     elseif($this->selectedOptionReceiptNote)
        //     {
        //         // Update the signature_option_sender field
        //         $companyLogo->signature_option_receipt_note = $this->selectedOptionReceiptNote;
        //         // Save the changes to the database
        //         $companyLogo->save();
        //     }


    }

    public function viewTemplate($template)
    {
        $filePath = storage_path('app/pdf_templates/' . $template);

        if (file_exists($filePath)) {
            return response()->file($filePath, ['Content-Type' => 'application/pdf']);
        }

        abort(404);
    }


    public function updatedSelectedChallanTemplate()
    {
        DB::table('company_logos')
            ->where('user_id', auth()->id())
            ->update(['challan_templete' => $this->selectedChallanTemplate]);

            $this->successMessage = 'Challan template Changed Successfully';
    }
    public function updatedSelectedGrnTemplate()
    {
        DB::table('company_logos')
            ->where('user_id', auth()->id())
            ->update(['grn_templete' => $this->selectedGrnTemplate]);

            $this->successMessage = 'Receipt Note template Changed Successfully';
    }




    public function updateCompanyData()
    {
        $request =  request();
        $request->replace([]);
        if (isset($this->companyLogoData['companyLogo'])) {
            // dd($this->companyLogoData['companyLogo']);
            $this->companyLogoDataset = [
                'challan_heading' => $this->companyLogoData['companyLogo']['challan_heading'],
                'estimate_heading' => $this->companyLogoData['companyLogo']['estimate_heading'],
                'invoice_heading' => $this->companyLogoData['companyLogo']['invoice_heading'],
                'receipt_note_heading' => $this->companyLogoData['companyLogo']['receipt_note_heading'],
                'challan_stamp' => $this->companyLogoData['companyLogo']['challan_stamp'] ?? true,
                'invoice_stamp' => $this->companyLogoData['companyLogo']['invoice_stamp'] ?? true,
                'barcode_accept' => $this->companyLogoData['companyLogo']['barcode_accept'],
                'challan_alignment' => $this->companyLogoData['companyLogo']['challan_alignment'],
                'invoice_alignment' => $this->companyLogoData['companyLogo']['invoice_alignment'],
                'challanTemporaryImageUrl' => $this->companyLogoData['companyLogo']['challanTemporaryImageUrl'],
                // 'estimateTemporaryImageUrl' => $this->companyLogoData['companyLogo']['estimateTemporaryImageUrl'],
                'invoiceTemporaryImageUrl' => $this->companyLogoData['companyLogo']['invoiceTemporaryImageUrl'],
                'returnChallanTemporaryImageUrl' => $this->companyLogoData['companyLogo']['returnChallanTemporaryImageUrl'],
                'poTemporaryImageUrl' => $this->companyLogoData['companyLogo']['poTemporaryImageUrl'],
                'return_challan_alignment' => $this->companyLogoData['companyLogo']['return_challan_alignment'],
                'po_alignment' => $this->companyLogoData['companyLogo']['po_alignment'],
                'return_challan_heading' => $this->companyLogoData['companyLogo']['return_challan_heading'],
                'po_heading' => $this->companyLogoData['companyLogo']['po_heading'],
                'return_challan_stamp' => $this->companyLogoData['companyLogo']['return_challan_stamp'] ?? true,
                'po_stamp' => $this->companyLogoData['companyLogo']['po_stamp'] ?? true,
                'signature_sender' => $this->companyLogoData['companyLogo']['signature_sender'],
                'signature_receiver' =>  $this->companyLogoData['companyLogo']['signature_receiver'],
                'signature_seller' =>  $this->companyLogoData['companyLogo']['signature_seller'],
                'signature_receipt_note' =>  $this->companyLogoData['companyLogo']['signature_receipt_note'],
                'signature_buyer' =>  $this->companyLogoData['companyLogo']['signature_buyer'],
                'challan_templete' =>  $this->companyLogoData['companyLogo']['challan_templete'],
            ];
        }


    }

    // Upload Invoice Logo
    public function companyInvoiceLogo()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        // dd($request);
        $logoController = new CompanyLogoController;
        $response = $logoController->logoInvoiceUpload($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    // Upload Challan Logo
    public function companyChallanLogo()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        $logoController = new CompanyLogoController;
        $response = $logoController->logoChallanUpload($request, $userId);
        $result = $response->getData();
        // dd($result);
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }

     // Upload Estimate Logo
     public function companyEstimateLogo()
     {
         $request = request();
         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

         $request->merge($this->companyLogoDataset);
         $logoController = new CompanyLogoController;
         $response = $logoController->logoEstimateUpload($request, $userId);
         $result = $response->getData();
         // dd($result);
         if ($result->status_code === 200) {
             $this->successMessage = $result->message;
         } else {
             $this->errorMessage = json_encode($result->errors);
         }

     }

     // Upload Challan Logo
     public function receiptNoteLogo()
     {
         $request = request();
         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

         $request->merge($this->companyLogoDataset);
         $logoController = new CompanyLogoController;
         $response = $logoController->receiptNoteUpload($request, $userId);
         $result = $response->getData();

         if ($result->status_code === 200) {
             $this->successMessage = $result->message;
         } else {
             $this->errorMessage = json_encode($result->errors);
         }

     }

    // Upload Return Challan Logo
    public function companyReturnChallanLogo()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        $logoController = new CompanyLogoController;
        $response = $logoController->logoReturnChallanUpload($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    // Upload PO Logo
    public function companyPOLogo()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        // dd($request);
        $logoController = new CompanyLogoController;
        $response = $logoController->logoPOUpload($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    //Update Challan Alignment
    public function updateChallanAlignment($alignment)
    {
        $this->companyLogoDataset['challan_alignment'] = $alignment;
        $request = request();
        $request->merge($this->companyLogoDataset);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $logoController = new CompanyLogoController;
        $response = $logoController->update($request, $userId);
        // $this->challanHeading();
        $result = $response->getData();

        if (property_exists($result, 'errors')) {
            $this->errorMessage = json_encode($result->errors);
        } else {
            $this->successMessage = $result->message;
        }


        // Refresh the Livewire component to reflect the updated data
        $this->mount();
    }

    //Update Invoice Alignment
    public function updateInvoiceAlignment($alignment)
    {
        $this->companyLogoDataset['invoice_alignment'] = $alignment;
        $request = request();
        $request->merge($this->companyLogoDataset);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $logoController = new CompanyLogoController;
        $response = $logoController->update($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

        $this->mount();
    }

    // Remove Image

    public function removePreviewImage($type)
    {
        $request = request();
        $request->merge($this->companyLogoDataset);
        $request->merge(['type' => $type]);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $logoController = new CompanyLogoController;
        $response = $logoController->removePreviewImage($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }

    }


    // Update Challan Headings
    public function challanHeading()
    {
        $request =  request();
        // $request->merge($this->companyLogoDataset);
        $request->merge([
            'challan_heading' => $this->companyLogoDataset['challan_heading'],
            'invoice_heading' => $this->companyLogoDataset['invoice_heading'],
            'return_challan_heading' => $this->companyLogoDataset['return_challan_heading'],
            'po_heading' => $this->companyLogoDataset['po_heading'],
            'receipt_note_heading' => $this->companyLogoDataset['receipt_note_heading'],
            'estimate_heading' => $this->companyLogoDataset['estimate_heading'],
        ]);
        // dd($request);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $logoController = new CompanyLogoController;
        $response = $logoController->update($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->reset(['successMessage', 'errorMessage']);
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

    public function challanToggle()
    {
        $request = request();
        $request->merge($this->companyLogoDataset);

        // Set the value of challan_stamp based on whether the toggle is checked or not
        $challanStampValue = $request->has('companyLogoDataset.challan_stamp') ? $request->input('companyLogoDataset.challan_stamp') : true;
        $request->merge(['companyLogoDataset' => ['challan_stamp' => $challanStampValue]]);

        // Your existing logic for updating the company logo
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $logoController = new CompanyLogoController;
        $response = $logoController->update($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
        $this->mount();
    }

    // CHALLAN TERMS AND CONDITIONS
    public function challanTermsAndConditions(Request $request)
    {
        // $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 1]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->challanData = (array) $data->getData()->data;
        // dd($this->termsIndexData);
    }

    // Receipt Note TERMS AND CONDITIONS
    public function receiptNoteTermsAndConditions(Request $request)
    {
        // dd($request->all());
        // $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 5]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->receiptNoteData = (array) $data->getData()->data;
        // dd($this->termsIndexData);
    }

     // Extimate TERMS AND CONDITIONS
     public function estimateTermsAndConditions(Request $request)
     {
        // dd($request->all());
        // $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 6]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->estimateData = (array) $data->getData()->data;
        // dd($this->termsIndexData);
     }

    public function addTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 1]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);

    }

    public function addReceiptNoteTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 5]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);

    }

    public function addEstimateTerms(Request $request)
    {
        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 6], ['section_id' => 2]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);
    }

    public function deleteChallanTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);
        // $this->mount();
        // dd('delete');
    }

    public $selectedContent, $itemId;

    public function selectChallanTerms($data)
    {
        $item = json_decode($data, true);
        // dd($item);
        $this->selectedContent = $item['content'];
        $this->itemId = $item['id'];
        // dd($this->id);
    }

    // RETURN CHALLAN TERMS AND CONDITIONS
    public function addReturnChallanTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 2]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);

    }


    public function returnChallanTermsAndConditions(Request $request)
    {
        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 2]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->returnChallanData = (array) $data->getData()->data;
        // dd($this->returnChallanData);
    }

    public function deleteReturnChallanTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);

    }

    public function selectReturnChallanTerms($data)
    {
        $item = json_decode($data, true);
        $this->selectedContent = $item['content'];
    }
    // RETURN CHALLAN TERMS AND CONDITIONS
    public function addInvoiceTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 3]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);

    }

    // ADD PO TERMS AND CONDITIONS
    public function addPoTerms(Request $request)
    {

        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 4]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);

    }

    // PO TERMS AND CONDITIONS
    public function poTermsAndConditions(Request $request)
    {
        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 4]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->poData = (array) $data->getData()->data;
        // dd($this->poData);
    }

    public function deletePOTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);

    }

    public function selectPOTerms($data)
    {
        $item = json_decode($data, true);
        $this->selectedContent = $item['content'];
    }

    public $openSearchModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    public $panelId;
    public $sectionId;

    public function closeTagModal()
    {
        // dd('close');
        $this->openSearchModal = false;

        $this->reset(['selectedContent', 'itemId']);
    }


    public function tagModal($data, $action)
    {
        // dd($data, $action);
        if($action == 'addTags'){
            // $item = json_decode($data, true);
            $this->selectedContent = $data['content'];
            $this->itemId = $data['id'];
            $this->panelId = $data['panel_id'];
            $this->sectionId = $data['section_id'];

            // $this->itemId = $itemId;
            $this->openSearchModal = true;
            $this->searchModalHeading = 'Edit Terms';
            $this->searchModalButtonText = 'Update';
            $this->searchModalAction = 'Edit';
        }
        // $this->closeModal();
    }

    // Edit Challan Terms and Conditions
    public function editChallan()
    {
        $request = request();
        // Wrap $this->selectedContent in an array with a key
        $request->merge(['content' => $this->selectedContent]); // Replace 'selectedContentKey' with the actual key you want to use
        // dd($request->all());
        $request->merge(['panel_id' => $this->panelId, 'section_id' => $this->sectionId]);
        $request->merge(['id' => $this->itemId]);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->update($request, $this->itemId);
        $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData', 'errorMessage']);
        $this->openSearchModal = false;
    }

    // INVOICE TERMS AND CONDITIONS
    public function invoiceTermsAndConditions(Request $request)
    {
        $request->merge($this->termsAndConditionsData);
        $request->merge(['panel_id' => 3]);
        $termsIndex = new TermsAndConditionsController;
        $data = $termsIndex->index($request);
        $this->invoiceData = (array) $data->getData()->data;
        // dd($this->invoiceData);
        $request->merge($this->termsAndConditionsData);
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->store($request);
        // $this->successMessage = $response->getData()->message;
        $this->reset(['termsAndConditionsData']);
    }


    public function deleteInvoiceTerms($id)
    {
        $controller = new TermsAndConditionsController;
        $controller->destroy($id);
        // $this->emit('triggerDelete', $id);
        $this->mount();
    }


    public function selectInvoiceTerms($data)
    {
        $item = json_decode($data, true);
        $this->selectedContent = $item['content'];
    }

    public function updatePanelSeries($panelId)
{
    // Update the panel_id in the termsAndConditionsData array
    // dd($panelId);
    $this->termsAndConditionsData['panel_id'] = $panelId;
    // Check if there is any selected content to update
    if (!empty($this->selectedContent)) {
        // Assuming the selectedContent has an 'id' that can be used to identify the record to update
        $id = $this->selectedContent['id'];

        // Prepare the data for updating
        // This might include more fields depending on your requirements
        $updateData = [
            'panel_id' => $panelId,
            'content' => $this->selectedContent['content'],
            // Add other fields here as needed
        ];

        // Convert update data to a request object or any format your update method requires
        $request = new Request($updateData);

        // Assuming you have a method to update the terms based on ID
        // You might need to implement this update method in your controller
        $termsAndConditionsController = new TermsAndConditionsController;
        $response = $termsAndConditionsController->update($id, $request);

        // Optionally, handle the response
        // For example, updating the success message, re-fetching updated data, etc.
        // $this->successMessage = $response->getData()->message;
    }
}




    // Upload Challan Logo
    public function signatureSender()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        $logoController = new CompanyLogoController;
        $response = $logoController->signatureUploadSender($request, $userId);
        $result = $response->getData();
        // dd($result);
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }

     // Upload Challan Logo
     public function signatureReceiver()
     {
         $request = request();
         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

         $request->merge($this->companyLogoDataset);
         $logoController = new CompanyLogoController;
         $response = $logoController->signatureUploadReceiver($request, $userId);
         $result = $response->getData();

         if ($result->status_code === 200) {
             $this->successMessage = $result->message;
         } else {
             $this->errorMessage = json_encode($result->errors);
         }
     }

       // Upload Challan Logo
       public function signatureSeller()
       {
           $request = request();
           $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

           $request->merge($this->companyLogoDataset);
           $logoController = new CompanyLogoController;
           $response = $logoController->signatureUploadSeller($request, $userId);
           $result = $response->getData();

           if ($result->status_code === 200) {
               $this->successMessage = $result->message;
           } else {
               $this->errorMessage = json_encode($result->errors);
           }
       }

         // Upload Challan Logo
     public function signatureBuyer()
     {
         $request = request();
         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

         $request->merge($this->companyLogoDataset);
         $logoController = new CompanyLogoController;
         $response = $logoController->signatureUploadBuyer($request, $userId);
         $result = $response->getData();

         if ($result->status_code === 200) {
             $this->successMessage = $result->message;
         } else {
             $this->errorMessage = json_encode($result->errors);
         }
     }

      // Upload Challan Logo
    public function signatureReceiptNote()
    {
        $request = request();
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request->merge($this->companyLogoDataset);
        $logoController = new CompanyLogoController;
        $response = $logoController->signatureUploadReceiptNote($request, $userId);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }


     public function getPdfFiles()
    {
        $pdfFiles = [
            ['id' => 1, 'path' => asset('pdf/template1.pdf')],
            ['id' => 2, 'path' => asset('pdf/template2.pdf')],
            ['id' => 3, 'path' => asset('pdf/template3.pdf')],
            ['id' => 4, 'path' => asset('pdf/template4.pdf')],
            ['id' => 5, 'path' => asset('pdf/template5.pdf')],
            ['id' => 6, 'path' => asset('pdf/template6.pdf')],
        ];

        return $pdfFiles;
    }

    public function grnPdfFiles()
    {
        $grnPdfFiles = [
            ['id' => 1, 'path' => asset('pdf/grn_template1.pdf')],

        ];
        return $grnPdfFiles;
    }

    public function updatedBarcode()
    {
        auth()->user()->update(['barcode' => $this->barcode]);
        session()->flash('message', 'Barcode updated successfully.');
    }

    public function updatedSelfDelivery()
    {
        auth()->user()->update(['self_delivery' => $this->self_delivery]);
        session()->flash('message', 'Self delivery updated successfully.');
    }

    public function updatedTags()
    {
        auth()->user()->update(['tags' => $this->tags]);
        session()->flash('message', 'Tags updated successfully.');
    }

    public function updatedPaymentStatus()
    {
        auth()->user()->update(['payment_status' => $this->payment_status]);
        session()->flash('message', 'Payment status updated successfully.');
    }

    public $showUploadButton = false;
    public function render(Request $request)
    {
         // Example condition to hide the upload button
        // Check if 'challan_logo_url' in $companyLogoDataset has a specific condition
    if (!empty($this->companyLogoDataset['signature_sender'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }
    if (!empty($this->companyLogoDataset['invoice_logo_url'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }
    if (!empty($this->companyLogoDataset['return_challan_logo_url'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }
    if (!empty($this->companyLogoDataset['po_logo_url'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }
    if (!empty($this->companyLogoDataset['signature_sender'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }

    if (!empty($this->companyLogoDataset['estimate_logo_url'])) {
        $this->showUploadButton = true; // Hide the button if condition is met
    }

        $pdfFiles = $this->getPdfFiles();
        // dd($pdfFiles);
        $grnPdfFiles = $this->grnPdfFiles();
        $this->invoiceTermsAndConditions($request);
        $this->challanTermsAndConditions($request);
        $this->returnChallanTermsAndConditions($request);
        $this->poTermsAndConditions($request);
        $this->receiptNoteTermsAndConditions($request);
        $this->estimateTermsAndConditions($request);

        return view('livewire.setting.screens.CompanyLogoComponent', compact('pdfFiles', 'grnPdfFiles'));
    }
}
