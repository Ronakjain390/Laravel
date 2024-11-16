<?php

namespace App\Http\Livewire\Signature;

use Livewire\Component;

class SignatureComponent extends Component
{
    public $signatureId;
    public $show = false;

    protected $listeners = ['openSignatureModal' => 'openModal'];

    public function openModal($signatureId)
    {
        dd($signatureId); // This will print the signatureId (if you want to check if the value is being passed correctly or not
        $this->signatureId = $signatureId;
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
    }


    public function render()
    {
        return view('livewire.signature.signature-component');
    }
}
