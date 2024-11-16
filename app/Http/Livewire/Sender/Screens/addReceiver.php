<?php

namespace App\Http\Livewire\Sender\Screens;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Receiver; // Adjust the namespace based on your Receiver model
use App\Models\ReceiverDetails; // Adjust the namespace based on your ReceiverDetails model
use App\Models\User;
use App\Models\Feature;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\Receivers\ReceiversController;

class addReceiver 
{

    public function addManualReceiverComponent(Request $request)
    {
        
        // $input = $request->all();

        // Emit a Livewire event with the result
        // $this->emit('manualReceiverAdded', $result);
        // dd($validatedData );
    }
    

    
}
