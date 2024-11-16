<?php

namespace App\Http\Livewire\Admin\Dashboard;
use App\Http\Controllers\V1\Plans\PlansController;
use App\Http\Controllers\V1\Orders\OrdersController;
use App\Http\Controllers\V1\Pricing\PricingController;
use stdClass;
use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\V1\Admin\Auth\AdminAuthController;

class Allusers extends Component
{
    public $allUsers,$validationErrorsJson, $statusCode, $updatedProfileData, $message, $planId, $data, $isChecked,$plans;
    public $selectedCheckboxes = [];
    public $toggleDataset = array(
        'sender' => '',
        'receiver' => '',
        'seller' => '',
        'buyer' => '',
    );
    public $showAnnualPlans = false;
    public $monthlyValidity = true; 
    public $activeTab = 'panel-1'; // Default active tab
    public $searchTerm = '';

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }
    public $tableTdData, $currentPage = 1, $paginateLinks;
    public $showAlert = false; 
  
    // }

    public function allUsers($page, $searchTerm = null){
        $request = new Request(['page' => $page]);
     
        $challanController = new AdminAuthController();
        $tableTdData = $challanController->allUsers($request);
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        $this->paginateLinks = $tableTdData->getData()->data->links;
        // dd($this->paginateLinks,$this->currentPage,$this->tableTdData  );
        // $this->challanFiltersData = json_encode($tableTdData->getData()->filters);
    }
 
  

    // public function allUsers($page, $searchTerm = null) {
    //     $perPage = 100;
    //     $request = new Request(['page' => $page]);
    //     $challanController = new AdminAuthController();
        
    //     // Call the allUsers method with the search term if provided
    //     $tableTdData = $searchTerm ? $challanController->allUsers($request, $searchTerm) : $challanController->allUsers($request);
    
    //     // Access the returned data
    //     $data = $tableTdData->getData()->data;
    
    //     // Check the type of $data
    //     if (is_int($data)) {
    //         // If $data is an integer, there might be an error in the allUsers method
    //         dd("Error occurred in retrieving users. Return value is an integer: " . $data);
    //     }
    
    //     // Process the retrieved users as needed
    //     // foreach ($data as $user) {
    //     //     dd($user);
    //     //     // Process each user, for example:
    //     //     // echo "User ID: " . $user->id . ", Email: " . $user->email . "<br>";
    //     // }
    
    //     // You can still assign other data you need
    //     $this->tableTdData = $data->data;
    //     $this->currentPage = $data->current_page;
    //     $this->paginateLinks = $data->links;
    // }
    
    
    

    public function selectPlan($id)
    {
        session(['selected_plan_id' => $id]);
        // $request = request();
        // $challanController = new AdminAuthController();
        // $tableTdData = $challanController->allUsers($request);
        // $this->tableTdData = $tableTdData->getData()->data->data;
        // $this->currentPage = $tableTdData->getData()->data->current_page;
        // $this->paginateLinks = $tableTdData->getData()->data->links;
    }

    // public function addToCart($planId)
    // {
    //     // Retrieve user ID from the session if set
    //     $userId = session('selected_plan_id');
    //     $planIds[] = $planId;
    //     // dd($planIds);
    //     $request = request();
    //     $planData = new OrdersController;
    //     $request->merge(['plan_ids' => $planIds, 'user_id' => $userId]);
    //     // dd($request);
    //     $data = $planData->storeAdmin($request);
    //     // dd($data);
    //     $this->plans = $data->getData()->data;
    //     $this->message = 'Item added to cart successfully';
    //     // $this->emit('cartUpdated');
    // }
    public function addToCart($planId,  $planType)
    {
        // dd($planId, $planType);
        $userId = session('selected_plan_id');
        $planIds[] = $planId;
        
        $request = request();
        $planData = new OrdersController;
        $request->merge(['plan_ids' => $planIds, 'user_id' => $userId, 'plan_type' => $planType]);

        $data = $planData->storeAdmin($request);

        $this->plans = $data->getData()->data;
    // dd($this->plans);
        $this->message = 'Package Applied Successfully';

        $this->reset();

        return redirect()->to(route('all-users'))->with('success', 'Package Applied Successfully');
    }

 

    
    public function updateSender($userId)
    {
        // dd($userId);
    $request = request();
        $request->merge($this->toggleDataset);
        // dd($request);
        // $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $toggleUpdate = new AdminAuthController;
        $response = $toggleUpdate->update($request, $userId);
        $result = $response->getData();
        // dd($result);
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
        } else {
            $this->errorMessage = json_encode($result->errors);
        }
         

    }
    protected $listeners = ['triggerDelete' => 'removeUser'];

    
    public function removeUser($userId)
    {
        $user = User::find($userId);
        // dd($user);
        if (!$user) {
            // Handle the case where the user is not found
        }
        $user->update([
            'test_users' => true,
        ]);

        // show the alert 
        session()->flash('success', 'User removed successfully.');
    }
        public $current_page;
     
    public function render()
    {
        $request = request();
        $searchTerm = $this->searchTerm;
        $request->merge(['search' => $searchTerm]);
    
        $currentRoute = request()->route()->getName();
        if ($currentRoute == 'admin.test-users') {
            $request->merge(['test_users' => true]);
        }
    
        $challanController = new AdminAuthController();
        $tableTdData = $challanController->allUsers($request);
        
        // dd($tableTdData->getData());
        $this->tableTdData = $tableTdData->getData()->data->data;
        $this->currentPage = $tableTdData->getData()->data->current_page;
        // dd($this->currentPage);
        $this->paginateLinks = $tableTdData->getData()->data->links;
       
    
        $User = User::all();
        $this->allUsers = $User;
    
        $planData = new PlansController;
        $data = $planData->index($request);
        $response = json_decode($data->getContent(), true);
        $this->plans = $response['data'];
    
        if ($currentRoute == 'admin.test-users') {
            return view('livewire.admin.dashboard.allUsers.testUsers');
        } else {
            return view('livewire.admin.dashboard.allUsers.allUsers');
        }
    }
    
}
// /var/www/TheParchi2.0/resources/views/livewire/admin/dashboard/allUsers/allUsers.blade.php