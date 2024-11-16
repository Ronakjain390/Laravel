<?php

namespace App\Http\Livewire\Setting\Screens;
use App\Models\CompanyLogo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Livewire\Component;

class Template extends Component
{
    public $template;
    public $panel_type; 

    public function mount($panel_type){
        $this->panel_type = $panel_type; 
        $data = CompanyLogo::where('user_id', Auth::user()->id)->first();
        $this->template = $data->receipt_note_template;
    }

    public function updatedTemplate($value)
    {
        // dd($value);
        $data = CompanyLogo::where('user_id', Auth::user()->id)->first();
        $data->receipt_note_template = $value;
        $data->save();
        $this->dispatchBrowserEvent('show-success-message-template', ['messageTemplate' => 'Template updated successfully']);
        // session()->flash('message', 'Units updated successfully');
    }
  
    
    public function render()
    {
        return view('livewire.setting.screens.template');
    }
}
