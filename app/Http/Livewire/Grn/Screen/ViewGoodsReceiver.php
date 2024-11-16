<?php

namespace App\Http\Livewire\Grn\Screen;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\ReceiverGoodsReceipt\ReceiverGoodsReceiptsController;

class ViewGoodsReceiver extends Component
{
    public function deleteReceiver($id)
    {
        $receiver = new ReceiverGoodsReceiptsController;
        $receiver->delete($id);
        $this->dispatchBrowserEvent('show-success-message', ['Receiver deleted successfully']);

    }

    public function render()
    {
        $request = request();
        $newReceiversController = new ReceiverGoodsReceiptsController;
        $response = $newReceiversController->index($request);
        $receiverData = $response->getData();
        $this->receiverDatas = $receiverData->data;


        return view('livewire.grn.screen.view-goods-receiver', [
            'receiverDatas' => $this->receiverDatas,
        ]);
    }
}
