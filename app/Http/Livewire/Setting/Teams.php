<?php

namespace App\Http\Livewire\Setting;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\V1\Panel\PanelController;
use App\Http\Controllers\V1\Teams\TeamsController;
use App\Http\Controllers\V1\TeamUser\TeamUserController;
use App\Http\Controllers\V1\TeamUserPermission\TeamUserPermissionController;

class Teams extends Component
{
    public $successMessage, $errorMessage;
    public $teams;
    public $team_name;
    public $team;
    public $team_id,$email;
    public $team_owner_user_id;
    public $view_preference;
    public $status = 'active';
    public $activeTab;

    public  $teamMembers, $teamMember, $teamMemberPermissions, $panelsData, $password_confirmation, $password, $member_id;


    public $showModal = false;
    protected $queryString = ['activeTab'];

    public function updatedActiveTab($value)
    {
        if ($value === 'tab1') {
            $this->emit('reloadTab1', 'tab1'); // Emit a custom event with the tab name
        }
    }
    public $team_member = array(
        'team_user_name' => '',
        'team_name' => '',
        'email' => null,
        'password' => null,
        'team_user_address' => '',
        'team_user_pincode' => null,
        'phone' => '',
        'team_user_state' => '',
        'team_user_city' => '',
        'team_id' => '',
        'team_owner_user_id' => '',
        'status' => 'active',
    );

    public $uniqueKey;



public function updatingQueryString($queryString, $old, $new)
{
    // dd($queryString, $old, $new);
    // Update the $uniqueKey based on the tab change or dropdown selection
    $this->uniqueKey = uniqid();

    return $queryString;
}
    protected $rules = [
        'team_name' => 'required|string|',


    ];
    protected $messages = [
        'team_name.required' => 'The team name is required.',

    ];
    public function mount()
    {
        $query = new TeamsController;
        $query = $query->index();

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->teams = $query->data;
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }

        if (!request()->query('activeTab')) {
            $this->activeTab = 'tab1';
        }

    }

    public function createTeam()
    {
        $this->validate();

        $this->team_owner_user_id = Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id ?? Auth::guard(Auth::getDefaultDriver())->user()->id;

        $request = request()->merge([
            "team_name" => $this->team_name,
            "team_owner_user_id" => $this->team_owner_user_id,
            "view_preference" => $this->view_preference,
            "status" => $this->status
        ]);

        $query = (new TeamsController)->store($request);

        if ($query->getStatusCode() === 200) {
            $this->successMessage = $query->getData()->message;

            $this->teams = (new TeamsController)->index()->getData()->data;

            $this->resetFields();
        } else {
            $this->errorMessage = json_encode($query->getData()->errors);
            $this->resetFields();
        }
    }

    private function resetFields()
    {
        $this->reset(['team_name', 'view_preference', 'team_owner_user_id', 'status']);
    }


    public function updateTeam()
    {
        $this->team_owner_user_id = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $request = request();
        $request->replace([]);
        $request->merge(["team_name" => $this->team['update_team_name']]);
        // dd($request);
        $query = new TeamsController;
        $query = $query->update($request, $this->team['team_id']);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->successMessage = $query->message;
            $query = new TeamsController;

            $query = $query->index();

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->teams = $query->data;
            } else {
                $this->errorMessage = json_encode($query->errors);
                $this->reset(['status', 'successMessage']);
            }
            $this->reset(['team', 'team_owner_user_id', 'status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
        $this->showModal = false;
    }


    public function deleteTeam($id)
    {
        // dd($request);
        $query = new TeamsController;
        $query = $query->destroy($id);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->successMessage = $query->message;
            $query = new TeamsController;

            $query = $query->index();

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->teams = $query->data;
            } else {
                $this->errorMessage = json_encode($query->errors);
                $this->reset(['status', 'successMessage']);
            }
            $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->message);
            $this->reset(['status', 'successMessage']);
        }
    }

    public function view($id)
    {

        // $this->update_team_name
        $query = new TeamsController;
        $query = $query->show($id);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->team = json_encode($query->data);
            // dd($this->update_team_name);

            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }

    public function edit($id)
    {

        // $this->update_team_name
        $query = new TeamsController;
        $query = $query->show($id);

        $status = $query->getStatusCode();
        $query = $query->getData();

        if ($status === 200) {
            $this->showModal = true;
            // dd($query->data->team_name);
            $query->data = (object) $query->data;
            // dd($query->data);
            $this->team['update_team_name'] = $query->data->team_name;
            $this->team['team_id'] = $query->data->id;
            // dd($this->update_team_name);

            // $this->successMessage = $query->message;
            // $this->reset(['status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage']);
        }
    }

    // TEAM MEMBER CODE
    public function selectTeam($team)
    {
        $this->team_member['team_name'] = $team['team_name'];
        $this->team_member['team_id'] = $team['id'];
        $this->team_member['team_owner_user_id'] = $team['team_owner_user_id'];
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
    }

    public function createTeamMember()
    {
        // $this->validate();

        $request = request();
        // $request->replace([]);
        // $this->team_member['password'] = Hash::make($this->team_member['password']);
        // $request->password = Hash::make($request->password);

        $request->merge($this->team_member);
        // dd($request);

        $query = new TeamUserController;
        $query = $query->store($request);

        $status = $query->getStatusCode();
        // dd($status);
        $query = $query->getData();

        if ($status === 200) {
            $this->successMessage = $query->message;
            $query = new TeamUserController;

            $query = $query->index();

            $status = $query->getStatusCode();
            $query = $query->getData();

            if ($status === 200) {
                $this->teamMembers = $query->data;
                $this->reset(['status', 'successMessage', 'errorMessage']);
            } else {
                $this->errorMessage = json_encode($query->errors);
                $this->reset(['status', 'successMessage', 'errorMessage']);

            }
            $this->reset(['team_member', 'status', 'errorMessage']);
        } else {
            $this->errorMessage = json_encode($query->errors);
            $this->reset(['status', 'successMessage', 'errorMessage']);
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
        return view('livewire.setting.teams.teams');
    }
}
