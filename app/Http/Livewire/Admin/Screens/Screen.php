<?php

namespace App\Http\Livewire\Admin\Screens;

use Livewire\Component;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

use App\Http\Controllers\V1\Seller\SellerController;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Invoice\InvoiceController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\PurchaseOrder\PurchaseOrderController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class Screen extends Component
{

    public $persistedTemplate, $persistedActiveFeature, $features = [], $activeFeature, $message, $errors, $validationErrorsJson, $successMessage, $errorMessage, $challanFiltersData, $state, $selectedUserDetails, $totalAmount, $invoiceData, $template, $inputsDisabled, $rate, $data, $quantity, $index, $totalSales = 0, $discountEntered = false, $calculateTax = false;
    public $sellerDatas, $statusCode, $ColumnDisplayNames, $invoiceFiltersData, $panelColumnDisplayNames, $billTo, $responseData, $sellerList;



    public function mount()
    {
        if (session()->has('persistedTemplate')) {
            $this->persistedTemplate = view()->exists('components.panel.buyer.' . session('persistedTemplate')) ? session('persistedTemplate') : "index";
            $this->persistedActiveFeature = view()->exists('components.panel.buyer.' . session('persistedTemplate')) ? session('persistedActiveFeature') : null;
            $request = request();
            $id = '';
            switch ($this->persistedTemplate) {
               case 'all_users':
                dd('Admin');
                $this->allUsers();
                break;

                default:
                case 'others':
                    break;
            }
        } else {
            $this->persistedTemplate = 'index';
            $this->persistedActiveFeature = null;
        }
    }

    // Method to save the $persistedTemplate value to the session
    public function savePersistedTemplate($template, $activeFeature = null)
    {
        session(['persistedTemplate' => $template]);
        session(['persistedActiveFeature' => $activeFeature]);
    }
    public function handleFeatureRoute($template, $activeFeature)
    {

        $this->persistedTemplate = view()->exists('components.panel.buyer.' . $template) ? $template : 'index';
        $this->persistedActiveFeature = view()->exists('components.panel.buyer.' . $template) ? $activeFeature : null;
        $this->savePersistedTemplate($template, $activeFeature);

        $this->mount();
    }


    protected $listeners = [
        'featureRoute' => 'handleFeatureRoute',
    ];


    public function innerFeatureRedirect($template, $activeFeature)
    {

        $this->handleFeatureRoute($template, $activeFeature);
        $this->template = '';
        $this->activeFeature = '';
    }


    public function render()
    {

        return view('livewire.admin.screens.screen');
    }
}
