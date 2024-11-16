<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;
use App\Models\PanelColumn;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use Illuminate\Support\Facades\Auth;

class ChallanColumns extends Component
{
    public $challanDesignData = array(
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
    public $showRate;
    public $statusCode, $successMessage, $errorMessage, $message, $validationErrorsJson;

    public function challanDesign()
    {
        // dd($this->challanDesignData);
    }

    public function toggleRate()
    {
        $confirmationMessage = $this->showRate ? 'Rate column has been removed from your challan' : 'Rate column is added to your rate';

        $this->dispatchBrowserEvent('open-confirmation-modal', [
            'message' => $confirmationMessage,
            'callback' => 'updateShowRate',
        ]);
    }

    public function updateShowRate($confirmed)
    {
        if ($confirmed) {
            // Update the show_rate value in the database
            $user = User::find(Auth::id());
            $user->show_rate = !$user->show_rate;
            $user->save();

            // Update the $showRate property
            $this->showRate = $user->show_rate;
        }
    }


    public function createChallanDesign($id, $index)
    {
        $panelColumn = PanelColumn::find($id);

        // Trim any leading or trailing spaces from the panel_column_display_name
        $panelColumn->panel_column_display_name = trim($this->challanDesignData[$index]['panel_column_display_name']);

        // Debugging output (optional)
        // dd($panelColumn->panel_column_display_name);

        // Save the updated panel column
        $panelColumn->save();

        // Set success message
        $this->successMessage = "Column updated successfully.";

        // Flash success message to session
        session()->flash('success', $this->successMessage);

        // Reset edit mode
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
        if (empty($this->challanDesignData[$index]['panel_column_display_name'])) {
            unset($this->challanDesignData[$index]);
            $this->challanDesignData = array_values($this->challanDesignData);
            $this->reset(['errorMessage']);
        } else {
            $this->dispatchBrowserEvent('swal:confirm', [
                'type' => 'warning',
                'title' => 'Are you sure?',
                'text' => 'You want to delete this column?',
                'id' => $this->challanDesignData[$index]['id'],
                'index' => $index,
            ]);
            // unset($this->challanDesignData[$index]);
            // $this->challanDesignData = array_values($this->challanDesignData);
        }
    }

    public function updateOrDeleteColumn($id, $index)
    {
        // dd($id, $index);
        // Here, you can update the column's display name to null or delete the column entirely
        // Assuming you have a PanelColumns model and a relationship defined in your component

        $panelColumn = PanelColumn::find($id);
        $panelColumn->panel_column_display_name = '';
        $panelColumn->save();

        // Update the local data
        // $this->challanDesignData[$index]['panel_column_display_name'] = '';

        $this->successMessage = "Column deleted successfully.";
         unset($this->challanDesignData[$index]);
            $this->challanDesignData = array_values($this->challanDesignData);
        session()->flash('success', $this->successMessage);

    }

    // public function addColumn()
    // {
    //     $emptyIndices = [];
    //     $maxIndex = 6; // Set the maximum index to consider

    //     // Find the indices of empty fields from 3 to 6
    //     for ($i = 3; $i <= $maxIndex; $i++) {
    //         if (empty($this->challanDesignData[$i]['panel_column_display_name'])) {
    //             $emptyIndices[] = $i;
    //         }
    //     }

    //     // Check if there are any empty indices and the previous index has a value
    //     if (!empty($emptyIndices) && !empty($this->challanDesignData[$emptyIndices[0] - 1]['panel_column_display_name'])) {
    //         // Get the first empty index
    //         $firstEmptyIndex = $emptyIndices[0];

    //         // Add the new field at the first empty index
    //         $this->challanDesignData[$firstEmptyIndex] = [
    //             'panel_id' => '1',
    //             'section_id' => '1',
    //             'feature_id' => '11',
    //             'default' => '0',
    //             'status' => '',
    //             'panel_column_default_name' => 'column_' . $firstEmptyIndex,
    //             'panel_column_display_name' => '',
    //             'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
    //         ];
    //         // dd($this->challanDesignData);
    //     } else {
    //         $this->errorMessage = "Please fill in at least one column before adding a new one.";
    //         session()->flash('error', $this->errorMessage);
    //     }
    // }
    public function mount() {
        $request = new Request;

        $requestData = [
            'panel_id' => '1',
            'section_id' => '1',
            'feature_id' => '1',
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $newChallanDesign = new PanelColumnsController;

        $requestData['default'] = '1';
        $request->merge($requestData);
        $response = $newChallanDesign->index($request);
        $this->challanDesignData = $response->getData()->data;

        $requestData['default'] = '0';
        $request->merge($requestData);
        $response = $newChallanDesign->index($request);

        $additionalData = $response->getData()->data;
        // Filter out elements with an empty panel_column_display_name and store them in a separate array
        $this->hiddenColumns = array_filter($additionalData, function($item) {
            return empty($item->panel_column_display_name);
        });

        // Filter out elements with a non-empty panel_column_display_name
        $additionalData = array_filter($additionalData, function($item) {
            return !empty($item->panel_column_display_name);
        });

        $this->challanDesignData = array_merge($this->challanDesignData, $additionalData);

        $showColumns = User::where('id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->first()->pluck('show_rate');

        $this->showRate = $showColumns;
    }

    public function addColumn()
{
    // Check if there are any hidden columns left
    if (!empty($this->hiddenColumns)) {
        // Get the last element from the challanDesignData array
        $lastElement = end($this->challanDesignData);
        // Check if the last element has an empty panel_column_display_name
        if (($lastElement['panel_column_display_name'] === null || $lastElement['panel_column_display_name'] === '')) {
            $this->errorMessage = "Please fill in the last column before adding a new one.";
            session()->flash('error', $this->errorMessage);
            return;
        }

        // Get the first hidden column
        $firstHiddenColumn = array_shift($this->hiddenColumns);

        // Add the hidden column to the challanDesignData array
        $this->challanDesignData[] = $firstHiddenColumn;

        // Set editMode to true and editModeIndex to the index of the newly added column
        $this->editMode = true;
        $this->editModeIndex = array_key_last($this->challanDesignData); // PHP 7.3+ required
    } else {
        $this->errorMessage = "No more columns to add.";
        session()->flash('error', $this->errorMessage);
    }
}
    public function render()
    {
        return view('livewire.sender.screens.challan-columns');
    }

}
