<?php

namespace App\Http\Livewire\Setting;

use Livewire\Component;
use App\Models\TeamUser;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\Teams\TeamsController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\TeamUserPermission\TeamUserPermissionController;

class TeamMember extends Component
{
    public $successMessage, $errorMessage;

    public $teams, $teamMembers, $teamMember, $teamMemberPermissions, $panelsData, $password_confirmation, $password, $member_id;
    public $team_name,$get_team_name;
    public $team;
    public $team_id;
    public $team_owner_user_id;
    public $status = 'active';
    public $showModal = false;
    public $isOpen = false;
    public $team_member = [
        'team_user_name' => '',
        'team_name' => '',
        'email' => '',
        'password' => '',
        'team_user_address' => '',
        'team_user_pincode' => '',
        'phone' => '',
        'team_user_state' => '',
        'team_user_city' => '',
        'team_id' => '',
        'team_owner_user_id' => '',
        'status' => 'active',
    ];

    // protected $rules = [
    //     'team_member.team_user_name' => 'required|string',
    //     'team_member.email' => 'email|unique:team_users,email|nullable',
    //     // 'team_member.team_name' => 'required',
    //     'team_member.password' => 'min:8',
    //     'team_member.phone' => 'required',
    //     // 'password' => 'min:8|same:password_confirmation',
    //     // 'password_consfirmation' => 'required',
    // ];
    // public $passwordValidated = false;
    // public function updated($propertyName)
    // {
    //     $this->validateOnly($propertyName);
    //     if ($propertyName === 'password' || $propertyName === 'password_confirmation') {
    //         $this->passwordValidated = $this->validate([
    //             'password' => 'min:8|same:password_confirmation',
    //             'password_confirmation' => 'required',
    //         ]);
    //     }
    // }
    // protected $messages = [
    //     'team_member.team_user_name.required' => 'The team user name is required.',
    //     // 'team_member.team_name' => 'Please select the team',
    //     'team_member.password' => 'Please enter the password',
    //     'team_member.password.min' => 'The password must be at least :min characters',
    //     'team_member.email.unique' => 'This email is already in use.',
    //     'team_member.phone.required' => 'The phone number is required.',
    // ];

    public function mount()
    {
        $query = new TeamsController;
        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teams = $query->data;
            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }


        $request = request();
        $query = new PanelController;

        $query = $query->index($request);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->panelsData = $query->data;
            // dd($this->panelsData);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }
    public function dehydrate()
    {
        // Perform any necessary cleanup or state management before the component is serialized
        $this->team_member= null;
        $query = new TeamsController;
        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teams = $query->data;
            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
        $query = new TeamUserController;

        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teamMembers = $query->data;
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }

        $request = request();
        $query = new PanelController;

        $query = $query->index($request);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->panelsData = $query->data;
            // dd($this->panelsData);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
        $query = new TeamsController;
        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teams = $query->data;
            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
        $this->selectTeam($this->team);
    }

    public function selectTeam($team)
    {
        if ($team !== null) {
            $this->team_name = $team['team_name'] ?? '';
            $this->team_id = $team['id'] ?? '';
            $this->team_member['team_name'] = $team['team_name'] ?? '';
            $this->team_member['team_id'] = $team['id'] ?? '';

            // Trigger an event for frontend updates without resetting the whole component
            $this->emit('teamSelected', $team);
        }
    }

    public $team_user_name;
    public $email;
    public $phone;
    public $modalHeading;
    public $modalButtonText;
    public $modalAction;
    public $team_member_id;
    public $teamUserId;

    public function editTeamMember($member)
    {
        $this->isOpen = true;
        $this->modalHeading = 'Edit Team Member';
        $this->modalButtonText = 'Save';
        $this->modalAction = 'updateTeamMember';

        // Set the properties with the member's data
        $this->teamUserId = $member['id']; // Set the id property
        $this->team_user_name = $member['team_user_name'];
        $this->email = $member['email'];
        $this->phone = $member['phone'];
    }

    public function updateTeamMember()
    {
        // Validate the input data
        $validatedData = $this->validate([
            'team_user_name' => 'required',
            'email' => 'required|email',
            // 'phone' => 'required',
        ]);

        // Find the team member by their id
        $teamMember = TeamUser::find($this->teamUserId);

        // Update the team member's data
        $teamMember->team_user_name = $this->team_user_name;
        $teamMember->email = $this->email;
        $teamMember->phone = $this->phone;

        // Save the changes to the database
        $teamMember->save();

        // Reset the form fields and close the modal
        $this->successMessage = 'Team member updated successfully';
        $this->isOpen = false;
    }
        // protected $rules = [
        //     'team_member.team_user_name' => 'required',
        //     'team_member.email' => 'required|email|unique:team_users,email',
        //     'team_member.phone' => 'required|max:10',
        //     'team_member.password' => 'required|min:8|same:team_member.password_confirmation',
        // ];

        // protected $messages = [
        //     'team_member.team_user_name.required' => 'The team user name is required.',
        //     'team_member.password.required' => 'Please enter the password',
        //     'team_member.password.min' => 'The password must be at least :min characters',
        //     'team_member.email.unique' => 'This email is already in use.',
        //     'team_member.phone.required' => 'The phone number is required.',
        // ];

        // public function updated($propertyName)
        // {
        //     $this->validateOnly($propertyName);
        //     if ($propertyName === 'team_member.password' || $propertyName === 'team_member.password_confirmation') {
        //         $this->passwordValidated = $this->validate([
        //             'team_member.password' => 'min:8|same:team_member.password_confirmation',
        //             'team_member.password_confirmation' => 'required',
        //         ]);
        //     }
        // }
        public $passwordValidated = false;

        public function createTeamMember()
        {
            $request = request();

            // Prepare the team_member array
            $this->team_member['password'] = Hash::make($this->team_member['password']);
            $this->team_member['team_owner_user_id'] = Auth::user()->id;
            $this->team_member['team_name'] = $this->team_name;
            $this->team_member['team_id'] = $this->team_id;

            // Extract updates from the request and merge them
            $updates = $request->input('updates', []); // Default to an empty array if 'updates' is not present
            foreach ($updates as $update) {
                if (isset($update['payload']['name']) && isset($update['payload']['value'])) {
                    $name = $update['payload']['name'];
                    $value = $update['payload']['value'];
                    $request->merge([$name => $value]); // Merge each name-value pair
                }
            }

            // Merge the team_member data into the request
            $request->merge(['team_member' => $this->team_member]);

            // Flatten the request data (move team_member fields to top level)
            $teamMemberData = $request->input('team_member', []); // Default to an empty array if 'team_member' is not present
            if (is_array($teamMemberData)) {
                $request->merge($teamMemberData); // Only merge if $teamMemberData is an array
            }

            // Remove the original team_member array from the request
            $request->request->remove('team_member');

            // Perform validation
            $validator = Validator::make($request->all(), [
                'team_user_name' => 'required|string|max:255',
                'email' => 'email|max:255|unique:team_users,email',
                'phone' => 'string|size:10|unique:team_users,phone',
                'password' => 'required|string|min:8',
                'team_id' => 'required|numeric',
                'team_owner_user_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                $this->dispatchBrowserEvent('show-error-message', ['Validation failed.']);
                $this->errorMessage = json_encode($validator->errors()->all());
                return;
            }

            // Call the store method to handle the request
            $response = (new TeamUserController)->store($request);
            // dd($response);
            if ($response->getStatusCode() === 200) {

                // Get the newly created team member data
                $newMember = $response->getData()->data;
                // Add the new member to the teamMembers array
                $this->teamMembers[] = $newMember;
                $this->successMessage = $response->getData()->message;
                $this->reset(['team_member', 'status', 'successMessage', 'errorMessage']);
                // $this->dispatchBrowserEvent('team-member-created');
                $this->dispatchBrowserEvent('show-success-message', ['Team Member Created Successfully']);
            } else {
                $this->errorMessage = json_encode($response->getData()->errors);
                $this->reset(['status', 'successMessage', 'errorMessage']);
            }
        }



    public function assign($team_user_id, $panel, $permission, $status)
    {
        $teamMemberPermissions = json_decode($this->teamMemberPermissions);
        $teamMemberPermissions->permission = json_decode($teamMemberPermissions->permission);
        $teamMemberPermissions->permission->{$panel}->{$permission} = $status;

        $request = request();
        $request->replace([]);
        $request->merge(['permission' => json_encode($teamMemberPermissions->permission)]);
        // dd($request);
        $query = new TeamUserPermissionController;
        $query = $query->update($request, $teamMemberPermissions->id);

        $status = $query->getStatusCode();
        $query = $query->getData();
        // dd($query);
        if ($status === 200) {
            $this->successMessage = $query->message;
            $query = new TeamUserPermissionController;
            $query = $query->show(json_decode($this->teamMember)->id);

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->teamMemberPermissions = json_encode($query->data);
            } else {
                $this->teamMemberPermissions = "";
                $this->errorMessage = json_encode($query->errors);
                $this->reset(['status', 'successMessage']);
            }
            $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }

    public function view($id)
    {
    // dd($id);
        // $this->update_team_name
        $query = new TeamUserController;
        $query = $query->show($id);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teamMember = json_encode($query->data);
            $query = new TeamUserPermissionController;
            $query = $query->show(json_decode($this->teamMember)->id);

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->teamMemberPermissions = json_encode($query->data);
            } else {
                $this->teamMemberPermissions = "";
                $this->errorMessage = json_encode($query->errors);
                $this->reset(['status', 'successMessage']);
            }
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }

    public function deleteTeamMember($id)
    {
        $query = new TeamUserPermissionController;
        $query = $query->destroy($id);

        $status = $query->getStatusCode();
        $query = $query->getData();
        // dd($query);
        if ($status === 200) {
            $this->successMessage = $query->message;
            $query = new TeamUserController;

            $query = $query->destroy($id);

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->successMessage = $query->message;
            } else {
                $this->errorMessage = json_encode($query->errors ?? $query->message);
                $this->reset(['status', 'successMessage']);
            }
            $this->reset(['status', 'errorMessage']);
        } else {
            $query = new TeamUserController;

            $query = $query->destroy($id);

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->successMessage = $query->message;
            } else {
                $this->errorMessage = json_encode($query->errors ?? $query->message);
                $this->reset(['status', 'successMessage']);
            }
            $this->errorMessage = json_encode($query->errors ?? $query->message);
            $this->reset(['status', 'successMessage']);
        }
        $this->successMessage = $query->message;
        $query = new TeamUserController;

        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teamMembers = $query->data;
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }
    // public $member_id;
    public function showModal(){
        $this->showModal = false;
    }
    public function selectTeamMember($id)
    {
        $this->teamUserId = $id;
        // dd($this->teamUserId);
    }

    // public function hydrate()
    // {
    //     // $this->reset(['status', 'successMessage', 'errorMessage']);
    //     $this->validate([
    //         'password' => 'required|min:8',
    //         'password_confirmation' => 'required|same:password',
    //     ]);
    // }


    protected $rules = [
        'password' => 'required|min:8',
        'password_confirmation' => 'required|same:password',
    ];

    protected $messages = [
        'password.required' => 'The password field is required.',
        'password.min' => 'The password must be at least 8 characters.',
        'password_confirmation.required' => 'The password confirmation field is required.',
        'password_confirmation.same' => 'The password confirmation does not match.',
    ];

    public function closeModal()
    {
        $this->reset(['password', 'password_confirmation', 'errorMessage', 'successMessage']);
        $this->dispatchBrowserEvent('close-modal');
    }
    public function changePassword()
    {
        $this->resetErrorBag();
        $this->validate();

        $request = request();
        $request->merge([
            'password' => $this->password,
            'password_confirmation' => $this->password_confirmation,
            'id' => $this->teamUserId,
        ]);

        $query = new TeamUserController;
        $response = $query->changePassword($request);
        $status = $response->getStatusCode();
        $data = $response->getData();

        if ($status === 200) {
            $this->successMessage = $data->message;
            $this->reset(['password', 'password_confirmation']);
            $this->showModal = false;
            $this->emit('passwordChanged');
            $this->dispatchBrowserEvent('close-modal');

        } else {
            $this->errorMessage = $data->message ?? 'An error occurred while changing the password.';
        }
    }

    public function render()
    {
        $query = new TeamsController;
        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teams = $query->data;
            // $this->teamMembers = $query->data;
            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }

        $query = new TeamUserController;

        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teamMembers = $query->data;
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }


        return view('livewire.setting.team-member.teamMember');
    }
}
