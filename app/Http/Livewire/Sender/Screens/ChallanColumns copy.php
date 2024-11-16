<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;  
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
    public function challanDesign()
{
   
    // dd($this->challanDesignData);
}
public $index;

    public function createChallanDesign($index){
        $request = new Request;
        for ($i = 0; $i <= $this->additionalInputs; $i++) {
                    $inputKey = "$i"; // Assuming the input names are column3, column4, etc.
                    // dd($inputKey);
                    // dd($this->challanDesignData[$i]['panel_column_default_name']);
                    if (isset($this->challanDesignData[$i]['panel_column_display_name'])) {
                        $panelColumnDisplay = $this->challanDesignData[$i]['panel_column_display_name'];
                        $panelColumnDefault = "column_$i";
        
                        // Define the data array for the new record
        
                        if (isset($this->challanDesignData[$i]['id'])) {
                            $data = [
                                'id' => $this->challanDesignData[$i]['id'],
                                'panel_id' => '1',
                                'section_id' => '1',
                                'feature_id' => '1',
                                'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                                'panel_column_display_name' => $panelColumnDisplay,
                                'panel_column_default_name' => $panelColumnDefault,
                                'status' => 'active',
                            ];
        
                            $request->merge($data);
                            // dump($request);
                            $newChallanDesign = new PanelColumnsController;
                            $response = $newChallanDesign->update($request, $this->challanDesignData[$i]['id']);
                            $result = $response->getData();
                            $this->emit('createChallanDesign', $index);

                            // dd($result);
                            // Set the status code and message received from the result
                            $this->statusCode = $result->status_code;
                    
                            if ($result->status_code === 200) {
                                $this->successMessage = $result->message;

                                session()->flash('success', $this->successMessage);

                                $this->reset(['statusCode', 'message', 'errorMessage']);
                            } else {
                                $this->errorMessage = json_encode($result->errors);

                                session()->flash('error', $this->errorMessage);
                            }
                        }
                    }
                }
        // dd($request);
    }

    public $additionalInputs = 3, $editMode, $editModeIndex, $statusCode, $message, $errorMessage;

    public function challanSeries(Request $request)
    {
        // if($request->ass)
        $request->merge($this->addChallanSeriesData);
        // dd($request->assigned_to_r_id);
        if($request->assigned_to_r_id == 'default'){
            $request->merge(['assigned_to_r_id' => '', 'default' => '1']);
        }
        $newChallanSeriesNoController = new PanelSeriesNumberController;
        $response = $newChallanSeriesNoController->store($request);
        // $this->reset(['addChallanSeriesData']);
        $result = $response->getData();
        // dd($result);
        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200 || $result->status_code === 201) {
            $this->successMessage = $result->message;
            $this->reset(['addChallanSeriesData', 'statusCode', 'message', 'errorMessage']);
            $request->replace([]);
            $newChallanSeriesIndex = new PanelSeriesNumberController;
            $request->merge(['panel_id' => '1']);
            $data = $newChallanSeriesIndex->index($request);

            $this->seriesNoData = $data->getData()->data;
            $newReceiversController = new ReceiversController;

            $request->replace([]);
            $response = $newReceiversController->index($request);
            $receiverData = $response->getData();
            $this->receiverDatas = $receiverData->data;
            // dump("3");
            // dump(json_encode($this->receiverDatas));
        
        }
    }

    public function render()
    {
        $request = new Request;

        $requestData = [
            'panel_id' => '1',
            'section_id' => '1',
            'feature_id' => '1',
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];
    
        $newChallanDesign = new PanelColumnsController;
        
        // Fetch data with default value 0
        $requestData['default'] = '1';
        $request->merge($requestData);
        $response = $newChallanDesign->index($request);
        $this->challanDesignData = $response->getData()->data;
    
        // Fetch data with default value 1
        $requestData['default'] = '0';
        $request->merge($requestData);
        $response = $newChallanDesign->index($request);
        $this->challanDesignData = array_merge($this->challanDesignData, $response->getData()->data);
    
        $this->additionalInputs = count($this->challanDesignData);
        return view('livewire.sender.screens.challan-columns');
    }
}
