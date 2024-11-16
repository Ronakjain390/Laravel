<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Validation\ValidationException;
use App\Http\Livewire\Sender\Screens\sentInvoice;
use App\Http\Livewire\Sender\Screens\createInvoice;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;

class Screen extends Component
{

    public $persistedTemplate;
    public $persistedActiveFeature;
    public $features = [];
    public $activeFeature;
    public $template;
    public $rate;
    public $data;
    public $quantity;
    public $totalAmount;
    public $discount;
    public $rows = [];
    public $createInvoice;
    public $validationErrorsJson = [];
    public $buyerUserData;
    public $isTaxIncluded = false;
    public $createInvoiceInstance;
    public $name;
    public $savedInvoiceId; // New property to store the saved ID
    public $isEditing = false;
    public $isSendEnabled = false;
    public $sentInvoice, $challanFiltersData, $challanData, $selectedColumnName, $tableTdData;
    public $autofillData = [];
    public $ColumnDisplayNames, $invoiceFiltersData, $receivedColumnDisplayNames, $sentColumnDisplayNames, $assigned_to_name, $discountValue, $totalAmountWithoutTax;
    protected $addBuyer, $storeInvoiceSeries;
    private $addBuyerCode, $selectedUserDetailsData;
    public $response, $buyerData, $seriesNoData, $newInvoiceDesign, $modifySentInvoiceData, $modifySentInvoice;
    public $newInvoiceSeriesNoController, $addInvoiceSeries, $buyerDatas, $buyerDetails, $gst, $showData;
    public $buyer_name, $company_name, $email, $address, $pincode, $phone, $state, $city, $tan, $errorMessage, $successMessage, $showManualBuyerTab, $buyer_special_id, $errors, $statusCode, $message, $PanelColumnData, $termsIndexData;

    //sent invoice
    public $invoiceData;

    // create invoice screen
    public $inputsDisabled = true;
    public $selectedUser;
    public $selectedUserDetails = [];
    public $totalSales = 0;
    public $discountEntered = false;
    public $discountPercentage = 0;
    public $calculateTax = true;
    public $sameAsBilling = true;
    public $billTo;
    public $column1;
    public $column2;
    public $column3;
    public $column4;

   
    public function render()
    {
        
        return view('livewire.setting.screens.screen', [
            

        ]);
    }


}
