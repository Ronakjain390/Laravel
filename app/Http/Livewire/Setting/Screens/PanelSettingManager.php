<?php

namespace App\Http\Livewire\Setting\Screens;

use Livewire\Component;
use App\Models\PanelSettings;
use App\Http\Controllers\V1\User\Auth\UserAuthController;

class PanelSettingManager extends Component
{
    public $panel;
    public $settings = [];
    public $activePlans = [];


    public $editableSettings = [];
    private $globalSettings = [
        'powered_by_the_parchi' => true,
        'tags' => false,
    ];

    private $panelSpecificSettings = [
        'sender' => [
            'self_delivery' => false,
            'self_return' => false,
            'barcode' => false,
            'add_signature' => false,
            'bulk_challan' => false,
        ],
        'receiver' => [
            'barcode' => false,
            'add_signature' => false,
        ],
        'seller' => [
            'barcode' => false,
            'add_signature' => false,
            'bulk_invoice' => false,
        ],
        'buyer' => [
            'barcode' => false,
            'add_signature' => false,
        ],
        'grn' => [
            'barcode' => false,
            'add_signature' => false,
        ],
    ];

    public function mount($panel)
    {
        $this->panel = $panel;
        $this->loadActivePlans();
        // dd($this->loadActivePlans());
        $this->loadSettings();
    }

    private function loadActivePlans()
    {
        $request = request();
        $query = new UserAuthController;
        $response = $query->userActivePlan($request);
        $response = $response->getData();
        $activePlan = json_decode(json_encode($response->user), true);
        $this->activePlans = $activePlan[ucfirst($this->panel)];
        // dd($activePlan, ucfirst($this->panel));
        // dd($this->activePlans);
    }
    private function loadSettings()
    {
        $this->availableSettings = array_merge(
            $this->globalSettings,
            $this->panelSpecificSettings[$this->panel] ?? []
        );

        $panelSettings = PanelSettings::getOrCreate(auth()->id());
        $savedSettings = $panelSettings->settings[$this->panel] ?? [];

        // Merge saved settings with available settings
        $this->settings = array_merge($this->availableSettings, $savedSettings);

        // Update settings based on active plans
        $this->updateSettingsBasedOnPlans();
    }

    private function updateSettingsBasedOnPlans()
    {
        $hasSilverPlan = $this->hasSilverPlan();
        $hasBasicPlan = $this->hasBasicPlan();
        foreach ($this->settings as $key => $value) {
            if ($key === 'barcode' || $key === 'add_signature') {
                if (!$hasSilverPlan) {
                    $this->settings[$key] = false;
                }
            }
            if ($key === 'powered_by_the_parchi') {
                $this->settings[$key] = true; // Always set to true
            }
            if ($key === 'tags') {
                $this->settings[$key] = false; // Always set to false
            }
        }

        $this->editableSettings = $this->settings;

        if (!$hasSilverPlan) {
            unset($this->editableSettings['barcode']);
            unset($this->editableSettings['add_signature']);
        }
        if (!$hasBasicPlan) {
            // Remove these from editable settings, but keep their values in $this->settings
            unset($this->editableSettings['powered_by_the_parchi']);
            unset($this->editableSettings['tags']);
        }
    }

    private function hasSilverPlan()
    {
        return $this->hasPlan('Silver');
    }

    private function hasBasicPlan()
    {
        return $this->hasPlan('Basic');
    }

    private function hasPlan($planName)
    {
        if (!is_array($this->activePlans)) {
            return false;
        }

        foreach ($this->activePlans as $plan) {
            if (isset($plan['plan']['plan_name']) && $plan['plan']['plan_name'] === $planName && isset($plan['status']) && $plan['status'] === 'active') {
                return true;
            }
        }

        return false;
    }

    public $errorMessage = [];

    public function updateSetting($key)
    {
        // dd($key);
        if (($key === 'barcode' || $key === 'add_signature') && !$this->hasSilverPlan()) {
            $this->errorMessage[$key] = "To use {$key}, you need a Silver Plan.";
            $this->settings[$key] = false;
            return;
        }

        if (($key === 'powered_by_the_parchi' || $key === 'tags') && !$this->hasBasicPlan()) {
            $this->errorMessage[$key] = "To use {$key}, you need a Basic Plan.";
            $this->settings[$key] = false;
            return;
        }

        $panelSettings = PanelSettings::getOrCreate(auth()->id());
        $panelSettings->setSetting($this->panel, $key, $this->settings[$key]);
        $this->emit('settingsUpdated');
    }

    public function render()
    {
        return view('livewire.setting.screens.panel-setting-manager', [
            'settings' => $this->settings,
            'editableSettings' => $this->editableSettings,
        ]);
    }
}
