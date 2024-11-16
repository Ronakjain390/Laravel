<?php

namespace App\Http\Livewire\Button;

use Livewire\Component;

class Button extends Component
{
    public $buttonTitle = 'buttonTitle';

    public function render()
    {
        return view('livewire.button.button');
    }
}
