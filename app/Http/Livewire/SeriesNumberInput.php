<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Challan;
use App\Models\ReturnChallan;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Invoice;
use App\Models\Estimates;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SeriesNumberInput extends Component
{
    public $seriesNumber;
    public $challanSeries;
    public $userId;
    public $method;
    public $initialSeriesNumber;
    public $inputsDisabled = true;

    protected $rules = [
        'seriesNumber' => 'required|numeric',
    ];

    public function mount($challanSeries, $seriesNumber, $method)
    {
        // dd($challanSeries);
        $this->challanSeries = $challanSeries;
        $this->seriesNumber = $seriesNumber;
        $this->method = $method;
        $this->initialSeriesNumber = $seriesNumber; // Store the initial series number
        $this->userId = Auth::getDefaultDriver() == 'team-user'
            ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id
            : Auth::guard(Auth::getDefaultDriver())->user()->id;
    }

    // public function updatedSeriesNumber($value)
    // {
    //     // dd($value);
    //     $this->validateOnly('seriesNumber');
    //     // dd($this->method);
    //     if ($this->method === 'challan') {
    //         $latestSeriesNum = Challan::where('challan_series', $this->challanSeries)
    //             ->where('sender_id', $this->userId)
    //             ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //         // Allow the series number if it is equal to the initial series number
    //         // if ($this->seriesNumber > $latestSeriesNum && $this->seriesNumber != $this->initialSeriesNumber) {
    //         //     session()->flash('error', 'You cannot use this series number.');
    //         //     $this->emit('disabledInputs', true);
    //         //     return;
    //         // }

    //         $existingChallan = Challan::where('challan_series', $this->challanSeries)
    //             ->where('sender_id', $this->userId)
    //             ->where('series_num', $this->seriesNumber)
    //             ->withTrashed()
    //             ->first();

    //         if ($existingChallan) {
    //             if ($existingChallan->trashed()) {
    //                 // The series number is soft-deleted, so it can be reused
    //                 $this->emit('seriesNumberUpdated', $this->seriesNumber);

    //             } else {
    //                 session()->flash('error', 'This series number already exists. Please use a different series number.');
    //                 $this->emit('disabledInputs', true);
    //             }
    //         } else {
    //             // Proceed with the default series number
    //             $this->emit('seriesNumberUpdated', $this->seriesNumber);
    //         }
    //     } elseif ($this->method === 'invoice') {
    //         $latestSeriesNum = Invoice::where('invoice_series', $this->challanSeries)
    //             ->where('seller_id', $this->userId)
    //             ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

    //         // Allow the series number if it is equal to the initial series number
    //         // if ($this->seriesNumber > $latestSeriesNum && $this->seriesNumber != $this->initialSeriesNumber) {
    //         //     session()->flash('error', 'You cannot use this series number.');
    //         //     $this->emit('disabledInputs', true);
    //         //     return;
    //         // }

    //         $existingInvoice = Invoice::where('invoice_series', $this->challanSeries)
    //             ->where('seller_id', $this->userId)
    //             ->where('series_num', $this->seriesNumber)
    //             ->withTrashed()
    //             ->first();

    //             // dd($existingInvoice);
    //         // if ($existingInvoice) {
    //         //     if ($existingInvoice->trashed()) {
    //         //         // The series number is soft-deleted, so it can be reused
    //         //         $this->emit('seriesNumberUpdated', $this->seriesNumber);
    //         //     } else {
    //         //         session()->flash('error', 'This series number already exists. Please use a different series number.');
    //         //         $this->emit('disabledInputs', true);
    //         //     }
    //         // } else {
    //         //     // Proceed with the default series number
    //         //     $this->emit('seriesNumberUpdated', $this->seriesNumber);
    //         // }

    //         // dd($this->seriesNumber);
    //         if ($existingInvoice) {
    //             if ($existingInvoice->trashed()) {
    //                 // The series number is soft-deleted, so it can be reused
    //                 $this->emit('seriesNumberUpdated', $this->seriesNumber);

    //             } else {
    //                 session()->flash('error', 'This series number already exists. Please use a different series number.');
    //                 $this->emit('disabledInputs', true);
    //             }
    //         } else {
    //             // Proceed with the default series number
    //             $this->emit('seriesNumberUpdated', $this->seriesNumber);
    //         }
    //     }
    // }

    public function updatedSeriesNumber($value)
    {
        $this->validateOnly('seriesNumber');

        if ($this->method === 'challan') {
            $latestSeriesNum = Challan::where('challan_series', $this->challanSeries)
                ->where('sender_id', $this->userId)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

            $existingChallan = Challan::where('challan_series', $this->challanSeries)
                ->where('sender_id', $this->userId)
                ->where('series_num', $this->seriesNumber)
                ->withTrashed()
                ->first();

            if ($existingChallan) {
                if ($existingChallan->trashed()) {
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        } elseif ($this->method === 'invoice') {
            $latestSeriesNum = Invoice::where('invoice_series', $this->challanSeries)
                ->where('seller_id', $this->userId)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

            $existingInvoice = Invoice::where('invoice_series', $this->challanSeries)
                ->where('seller_id', $this->userId)
                ->where('series_num', $this->seriesNumber)
                ->withTrashed()
                ->first();

            if ($existingInvoice) {
                if ($existingInvoice->trashed()) {
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        } elseif ($this->method === 'estimate') {
            $latestSeriesNum = Estimates::where('estimate_series', $this->challanSeries)
                ->where('seller_id', $this->userId)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

            $existingEstimate = Estimate::where('estimate_series', $this->challanSeries)
                ->where('seller_id', $this->userId)
                ->where('series_num', $this->seriesNumber)
                ->withTrashed()
                ->first();

            if ($existingEstimate) {
                if ($existingEstimate->trashed()) {
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        }
        elseif ($this->method === 'return_challan') {
            $latestSeriesNum = ReturnChallan::where('challan_series', $this->challanSeries)
                ->where('sender_id', $this->userId)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

            $existingChallan = ReturnChallan::where('challan_series', $this->challanSeries)
                ->where('sender_id', $this->userId)
                ->where('series_num', $this->seriesNumber)
                ->withTrashed()
                ->first();

            if ($existingChallan) {
                if ($existingChallan->trashed()) {
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        }
        elseif ($this->method === 'purchase_order') {
            $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $this->challanSeries)
                ->where('buyer_id', $this->userId)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

            $existingOrder = PurchaseOrder::where('purchase_order_series', $this->challanSeries)
                ->where('buyer_id', $this->userId)
                ->where('series_num', $this->seriesNumber)
                ->withTrashed()
                ->first();

            if ($existingOrder) {
                if ($existingOrder->trashed()) {
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        }
        // Gooods Receipt
        elseif($this->method === 'receipt_note' ){
            $latestSeriesNum = PurchaseOrder::where('purchase_order_series', $this->challanSeries)
            ->where('receiver_goods_receipts_id', $this->userId)
            ->max(DB::raw('CAST(series_num AS UNSIGNED)'));

        $existingOrder = PurchaseOrder::where('purchase_order_series', $this->challanSeries)
            ->where('receiver_goods_receipts_id', $this->userId)
            ->where('series_num', $this->seriesNumber)
            ->withTrashed()
            ->first();

            if($existingOrder){
                if($existingOrder->trashed()){
                    $this->emit('seriesNumberUpdated', $this->seriesNumber);
                } else {
                    session()->flash('error', 'This series number already exists. Please use a different series number.');
                    $this->emit('disabledInputs', true);
                }
            } else {
                $this->emit('seriesNumberUpdated', $this->seriesNumber);
            }
        }
    }


    public function render()
    {
        return view('livewire.series-number-input');
    }
}
