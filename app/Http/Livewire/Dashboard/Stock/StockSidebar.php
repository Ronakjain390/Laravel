<?php

namespace App\Http\Livewire\Dashboard\Stock;

use Livewire\Component;

class StockSidebar extends Component
{
    public function render()
    {
         
            // dd(session()->all());
            // // $currentRoutePrefix = explode('/', parse_url($currentUrl, PHP_URL_PATH))[1];
            // // Store the previous URL in the session
            //     if (!session()->has('previous_url')) {
            //         session(['previous_url' => url()->previous()]);
            //     }
            //     // dd(session()->has('previous_url'), session('previous_url'));
            //     // Retrieve the previous URL from the session
            //     $previousUrl = session('previous_url');
            //     $currentRoutePrefix = explode('/', parse_url($previousUrl, PHP_URL_PATH))[1] ?? 'default';
            // // endphp
        
            // if($currentRoutePrefix === 'sender')
            //     return view ('livewire.sender.sidebar.sidebar');
            // elseif($currentRoutePrefix === 'seller')
               
            
        return view('livewire.dashboard.stock.stock-sidebar');
    }
}
