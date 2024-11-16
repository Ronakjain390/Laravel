<?php

namespace App\Http\Livewire\Seller\Screens;
use Livewire\Component;
use App\Models\PanelColumn;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use Illuminate\Support\Facades\Auth;

class InvoiceColumns extends Component
{
    public $invoiceDesignData = array(
        [
            'panel_id' => '1',
            'section_id' => '1',
            'feature_id' =>  '11',
            'default' => '0',
            'status' => '',
            'panel_column_default_name' => '',
            'panel_column_display_name' => '',
            'user_id' => '',
        ]
    );

    public $additionalInputs = 3;
    public $additionalInputsCount = 4;
    public $editMode = false;
    public $editModeIndex = null;
    public $statusCode, $successMessage, $errorMessage, $message, $validationErrorsJson;

    public function invoiceDesign()
    {
        // dd($this->invoiceDesignData);
    }


  
    public function createInvoiceDesign($id, $index)
    {
        $panelColumn = PanelColumn::find($id);
        $panelColumn->panel_column_display_name = $this->invoiceDesignData[$index]['panel_column_display_name'];
        $panelColumn->save();
        $this->successMessage = "Column updated successfully.";

        session()->flash('success', $this->successMessage);
        $this->editMode = false;
        $this->editModeIndex = null;
    }

    public function toggleEditMode($index)
    {
        $this->editMode = !$this->editMode;
        $this->editModeIndex = $index;
    }

    public function removeColumn($index)
    {
        if (empty($this->invoiceDesignData[$index]['panel_column_display_name'])) {
            unset($this->invoiceDesignData[$index]);
            $this->invoiceDesignData = array_values($this->invoiceDesignData);
            $this->reset(['errorMessage']);
        } else {
            $this->dispatchBrowserEvent('swal:confirm', [
                'type' => 'warning',
                'title' => 'Are you sure?',
                'text' => 'You want to delete this column?',
                'id' => $this->invoiceDesignData[$index]['id'],
                'index' => $index,
            ]);
        }
    }

    public function updateOrDeleteColumn($id, $index)
    {
        $panelColumn = PanelColumns::find($id);
        $panelColumn->panel_column_display_name = '';
        $panelColumn->save();

        // Update the local data
        $this->invoiceDesignData[$index]['panel_column_display_name'] = '';
    }

    public function mount() {
        $request = new Request;
    
        $requestData = [
            'panel_id' => '3',
            'section_id' => '2',
            'feature_id' => '12',
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];
    
        $newInvoiceDesign = new PanelColumnsController;
    
        $requestData['default'] = '1';
        $request->merge($requestData);
        $response = $newInvoiceDesign->index($request);
        $this->invoiceDesignData = $response->getData()->data;
    
        $requestData['default'] = '0';
        $request->merge($requestData);
        $response = $newInvoiceDesign->index($request);
    
        $additionalData = $response->getData()->data;
        // Filter out elements with an empty panel_column_display_name and store them in a separate array
        $this->hiddenColumns = array_filter($additionalData, function($item) {
            return empty($item->panel_column_display_name);
        });
    
        // Filter out elements with a non-empty panel_column_display_name
        $additionalData = array_filter($additionalData, function($item) {
            return !empty($item->panel_column_display_name);
        });
    
        $this->invoiceDesignData = array_merge($this->invoiceDesignData, $additionalData);
    }
    
    public function addColumn()
    {
        // Check if there are any hidden columns left
        if (!empty($this->hiddenColumns)) {
            // Get the last element from the invoiceDesignData array
            $lastElement = end($this->invoiceDesignData);
            // dd($lastElement);
            // Check if the last element has an empty panel_column_display_name
    
            if (($lastElement['panel_column_display_name'] === null || $lastElement['panel_column_display_name'] === '')) {
                $this->errorMessage = "Please fill in the last column before adding a new one.";
                session()->flash('error', $this->errorMessage);
                return;
            }

            // Get the first hidden column
            $firstHiddenColumn = array_shift($this->hiddenColumns);

            // Add the hidden column to the invoiceDesignData array
            $this->invoiceDesignData[] = $firstHiddenColumn;
        } else {
            $this->errorMessage = "No more columns to add.";
            session()->flash('error', $this->errorMessage);
        }
    }

    public function render()
    {
        return view('livewire.seller.screens.invoice-columns');
    }
}
