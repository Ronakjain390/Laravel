<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;

class ExpotModal extends Component
{
    public $filtersApplied = false; // Track whether filters are applied or not
    public $exportOption = 'current_page'; // Default export option
    public $exportType = 'csv_for_excel'; // Default export type

    protected $listeners = ['openExportModal'];

    public function openExportModal($filtersApplied)
    {
        $this->filtersApplied = $filtersApplied;
        $this->exportOption = $filtersApplied ? 'filtered_data' : 'current_page';
        $this->dispatchBrowserEvent('open-modal'); // Trigger the modal open event
    }

    public function export()
    {
        // Your export logic using the Export class
        // You can pass the selected options to your export method

        $this->emit('exportProducts', $this->exportOption, $this->exportType);
    }

    
    public function render()
    {
        return view('livewire.sender.screens.expot-modal');
    }
}
