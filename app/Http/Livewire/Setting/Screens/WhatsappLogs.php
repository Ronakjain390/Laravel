<?php

namespace App\Http\Livewire\Setting\Screens;
use App\Models\WalletLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Livewire\Component;

class WhatsappLogs extends Component
{
    public function render()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $whatsappLogs = WalletLog::where('user_id', $userId)->get();
        
        return view('livewire.setting.screens.whatsapp-logs', [
            'whatsappLogs' => $whatsappLogs,
        ]);
    }
}
