<?php

namespace App\Http\Livewire\Grn\Screen;


use App\Models\Challan;
use Livewire\Component;
use App\Models\User;
use App\Models\Receiver;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Models\ChallanStatus;
use App\Models\UserDetails;
use App\Models\ChallanSfp;
use App\Models\CompanyLogo;
use App\Models\ReceiverDetails;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanOrderDetail;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Livewire\Sender\Screens\addReceiver;
use App\Http\Livewire\Sender\Screens\sentChallan;
use App\Http\Livewire\Sender\Screens\createChallan;
use App\Http\Controllers\V1\Challan\ChallanController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\ReceiverGoodsReceipt\ReceiverGoodsReceiptsController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Http\Controllers\V1\ReturnChallan\ReturnChallanController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;
use App\Http\Controllers\V1\TermsAndConditions\TermsAndConditionsController;
class GoodsPrefix extends Component
{
    public $addChallanSeriesData = array(
        'series_number' => '',
        'valid_from' => '',
        'valid_till' => '',
        'receiver_user_id' => '',
        'panel_id' => '5',
        'section_id' => '1',
        'assigned_to_rg_id' => '',
        'assigned_to_name' => '',
        'default' => '0',
        'status' => 'active',
    );

    public $updateChallanSeriesData = [];
    public $receiverDatas;
    public $successMessage;
    public function goodsPrefix(Request $request)
    {
        $request->merge($this->addChallanSeriesData);
        // dd($request->assigned_to_rg_id);
        if($request->assigned_to_rg_id == 'default'){
            $request->merge(['assigned_to_rg_id' => '', 'default' => '1']);
        }
        $newChallanSeriesNoController = new PanelSeriesNumberController;
        $response = $newChallanSeriesNoController->store($request);
        // $this->reset(['addChallanSeriesData']);
        $result = $response->getData();
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            $this->reset(['addChallanSeriesData',  'message', 'errorMessage']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '5']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiverGoodsReceiptsController;

            $request->replace([]);
            $response = $newReceiversController->index($request);
            $receiverData = $response->getData();
            $this->receiverDatas = $receiverData->data;
        }
    }
    public function challanSeries(Request $request)
    {
        // if($request->ass)
        $request->merge($this->addChallanSeriesData);
        // dd($request->assigned_to_r_id);
        if($request->assigned_to_r_id == 'default'){
            $request->merge(['assigned_to_rg_id' => '', 'default' => '1']);
        }
        // dd($request);
        $newChallanSeriesNoController = new PanelSeriesNumberController;
    $response = $newChallanSeriesNoController->store($request);
        // $this->reset(['addChallanSeriesData']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        switch($result->status_code){
            case 200:
                $this->dispatchBrowserEvent('show-success-message', [$result->message]);
                $this->successMessage = $result->message;
                $this->reset(['addChallanSeriesData']);
                $request->replace([]);
                $newChallanSeriesIndex = new PanelSeriesNumberController;
                $request->merge(['panel_id' => '5']);
                $data = $newChallanSeriesIndex->index($request);

                $this->seriesNoData = $data->getData()->data;
                $newReceiversController = new ReceiverGoodsReceiptsController;

                $request->replace([]);
                $response = $newReceiversController->index($request);
                $receiverData = $response->getData();
                $this->receiverDatas = $receiverData->data;
                break;
            case 400:
                $errorMessages = $result->message;
                    if (isset($result->errors)) {
                        foreach ($result->errors as $field => $messages) {
                            foreach ($messages as $message) {
                                $errorMessages .= "\n" . $message;
                            }
                        }
                    }
                    $this->dispatchBrowserEvent('show-error-message', [$errorMessages]);
                    $this->errorMessage = $errorMessages;
                    break;

        }
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            $this->reset(['addChallanSeriesData']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '5']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiverGoodsReceiptsController;

            $request->replace([]);
            $response = $newReceiversController->index($request);
            $receiverData = $response->getData();
            $this->receiverDatas = $receiverData->data;
        }
    }
    public function selectChallanSeries($seriesData)
    {
        $seriesData = json_decode($seriesData);
        $this->reset(['updateChallanSeriesData']);
        $this->updateChallanSeriesData = (array)$seriesData;
        $this->updateChallanSeriesData['assigned_to_r_id'] = '';

    }

    public function resetChallanSeries()
    {
        $this->reset(['updateChallanSeriesData']);
    }


    public function deleteChallanSeries($id)
    {

        $controller = new PanelSeriesNumberController;
        $controller->destroy($id);
        $this->dispatchBrowserEvent('show-success-message', ['Challan Series deleted successfully']);
        // $request = new request;
        // $this->successMessage = $result->message;
        // return redirect()->route('sender', ['template' => 'sent_challan'])->with('message', $this->successMessage ?? $this->errorMessage);

        // $this->emit('triggerDelete', $id);
    }

    public function updatePanelSeries()
    {
        // dd($this->itemId);
        $request =  request();
        $request->merge($this->updateChallanSeriesData);
        if($request->assigned_to_rg_id == 'default'){
            $request->merge(['assigned_to_rg_id' => '', 'default' => '1']);
        }
        // Create instances of necessary classes
        $PanelSeriesNumberController = new PanelSeriesNumberController;
        // dd($request);

        $response = $PanelSeriesNumberController->update($request, $this->updateChallanSeriesData['id']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->dispatchBrowserEvent('show-success-message', [$result->message]);
            $this->dispatchBrowserEvent('close-edit-modal'); // Add this line

            $this->reset(['updateChallanSeriesData']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '1']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;

        } else {
            $this->errorMessage = json_encode($result->errors);
        }
    }


    public function render()
    {
        $request = new Request();
        $newChallanSeriesIndex = new PanelSeriesNumberController;
                $request->merge(['panel_id' => '5', 'section_id' => '1']);
                $data = $newChallanSeriesIndex->index($request);

                $this->seriesNoData = $data->getData()->data;
                // dd($this->seriesNoData);
                $newReceiversController = new ReceiverGoodsReceiptsController;
                $response = $newReceiversController->index($request);
                $receiverData = $response->getData();
                $this->receiverDatas = $receiverData->data;
        return view('livewire.grn.screen.goods-prefix');
    }
}
