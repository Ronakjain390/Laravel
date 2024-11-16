<?php

namespace App\Http\Livewire\Setting\Screens;

use App\Models\Buyer;
use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
// use App\Http\Controllers\V1\Orders\OrdersController;

class Address extends Component
{
    public $panelName;
    public $companyLogoData, $planData, $featureData, $showModal = false, $orderData,  $errorMessage, $index, $companyLogo, $successMessage;
    private $UserDetails;
    public $inputsResponseDisabled = true;
    public $openSearchModal = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $searchModalAction;
    protected $listeners = ['cityAndStateByPincode'];
    public $addAddress =  array(
                "user_id" => "",
                "address" => "",
                "pincode" => "",
                "phone" => "",
                "gst_number" => "",
                "state" => "",
                "city" => "",
                "bank_name" => "",
                "branch_name" => "",
                "bank_account_no" => "",
                "ifsc_code" => "",
                "tan" => "",
                "location_name" => "",
                "organisation_type" => "",
    );

    public function mount(Request $request)
    {


    }





    public function editAddress($user)
    {

        // Set the initial values for the modal input fields
        $this->addAddress = [
            'id' => $user['id'],
            'location_name' => $user['location_name'],
            'phone' => $user['phone'],
            'address' => $user['address'],
            'pincode' => $user['pincode'],
            'state' => $user['state'],
            'city' => $user['city'],
            // Add more fields as needed
        ];

        // Open the modal
        $this->dispatchBrowserEvent('openEditModal');
    }


    public function updateAddress(Request $request){
        $request->merge($this->addAddress);
        // dd($request);
          // Create instances of necessary classes
        //   dd($request->id);
          $newUserController = new UserAuthController;

          $response = $newUserController->updateUserDetail($request, $request->id);
          $result = $response->getData();
        //   dd($result);
        if (isset($result->status) && $result->status === 200) {
            $this->successMessage = $result->message;
            // dd($this->successMessage);
            // Additional logic if needed for a successful response
        } else {
            $this->errorMessage = isset($result->errors) ? json_encode($result->errors) : 'An error occurred.';
            // Additional logic if needed for an unsuccessful response
        }
        $this->reset(['addAddress', ]);
    }

    public function deleteAddress($id)
    {
        $controller = new UserAuthController;
        $controller->delete($id);
        // $this->emit('triggerDelete', $id);
        // $this->mount();
        // dd('delete');
        $this->reset(['addAddress']);
    }

    public function closeModal()
    {
        $this->dispatchBrowserEvent('closeModal');
    }


    public function openModal()
    {
            $this->openSearchModal = true;
            $this->searchModalHeading = 'Add Address';
            $this->searchModalButtonText = 'Save Changes';
            $this->searchModalAction = 'createAddress';

        // $this->closeModal();
    }
    public function closeTagModal()
    {
        // dd('close');
        $this->openSearchModal = false;


    }

    public function createAddress() {
        // Validate the request inputs
        $request = request();
        // Merge validated data with the request
        $request->merge($this->addAddress);
        // dd($request);
        $validatedData = $request->validate([
            'location_name' => 'required|string|max:255',
            'phone' => 'required|string|min:10|max:10',
            'address' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
        ]);

        // Add user ID to the data
        $this->addAddress['user_id'] = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Merge validated data with the request
        $request->merge($this->addAddress);

        $newUserController = new UserAuthController;
        $detailResponse = $newUserController->storeUserDetail($request);
        $result = $detailResponse->getData();

        if (isset($result->status) && $result->status === 200) {
             // After successful save
             $this->dispatchBrowserEvent('closeModal');
             $this->reset('addAddress');
             $this->emit('addressAdded');
            $this->successMessage = $result->message;
            // Additional logic if needed for a successful response
        } else {
            $this->errorMessage = isset($result->errors) ? json_encode($result->errors) : 'An error occurred.';
            // Additional logic if needed for an unsuccessful response
        }
        $this->openSearchModal = false;
        $this->reset(['addAddress']);
    }

    private function areRequiredFieldsFilled()
{
    $requiredFields = [
        'location_name', 'phone', 'address', 'pincode', 'state', 'city',
    ];

    return collect($requiredFields)->every(function ($field) {
        return !empty($this->addAddress[$field]);
        $this->inputsResponseDisabled = false;
    });
}
    public function cityAndStateByPincode()
    {
        // dd('cityAndStateByPincode');
        $pincode = $this->addAddress['pincode'];

        $receiverController = new BuyersController;
        $response = $receiverController->fetchCityAndStateByPincode($pincode);
        $result = $response->getData();
        if (isset($result->city) && isset($result->state)) {
            $this->addAddress['city'] = $result->city;
            $this->addAddress['state'] = $result->state;
            $this->inputsResponseDisabled = false;
        }
    }

    public function render(Request $request)
    {
        $user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Merge user_id into the request as an associative array
        $request->merge(['user_id' => $user_id]);


        $newUserController = new UserAuthController;
        $detailResponse = $newUserController->index($request);
        // $response = $detailResponse->getData()->data;
        // $columnsData = json_decode($detailResponse->content(), true);
        $this->userAddress = $detailResponse->getData()->data;
        // dd($this->userAddress);
        return view('livewire.setting.screens.userAddress');
    }
}
