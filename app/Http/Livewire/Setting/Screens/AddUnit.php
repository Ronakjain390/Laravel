<?php

namespace App\Http\Livewire\Setting\Screens;
use App\Models\Units;
use App\Http\Controllers\V1\Units\UnitsController;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class AddUnit extends Component
{
    public $isOpen = false;
    public $unit;
    public $search = '';
    public $units = [];
    public $selectedUnits = [];
    public $unselectedUnits = [];
    public $panel_type;
    public $shortName;

    protected $listeners = ['saveUnits'];

    public function mount($panel_type){
        $this->panel_type = $panel_type;
    }

    public function loadUnits()
    {
        // dd($this->panel_type);
        $showUnit = new UnitsController();
        $unitsCollection = $showUnit->index($this->panel_type)->original;

        $this->units = $unitsCollection->map(function ($unit) {
            return [
                'id' => $unit->id,
                'unit' => $unit->unit,
                'short_name' => $unit->short_name,
                'is_default' => $unit->is_default,
            ];
        })->toArray();
        // dd($this->units);
    }
    public function openModal()
    {
        $this->isOpen = true;
        $this->loadUnits();
    }

    public function updatedSearch()
    {
        $this->fetchUnits();
    }

    public function saveUnits($selectedUnits, $unselectedUnits)
    {
        // dd($selectedUnits, $unselectedUnits);
        $request = request();
        $this->selectedUnits = $selectedUnits;
        $request->merge([
            'selectedUnits' => $selectedUnits,
            'unselectedUnits' => $unselectedUnits
        ]);

        $showUnit = new UnitsController();
        $unitsCollection = $showUnit->showHideUnits($request);
        // session()->flash('message', 'Units updated successfully');
        $this->closeModal();
        $this->dispatchBrowserEvent('show-success-message', ['message' => 'Units saved successfully']);
    }

    public function addUnit()
    {
        $request = request();
        $this->validate([
            'unit' => 'required',
        ]);

        $data = [
            'unit' => $this->unit,
            'short_name' => $this->shortName,
            'user_id' => auth()->user()->id,
            'status' => 'active',
            'panel_type' => $this->panel_type,
            'is_default' => 1,
        ];
        $request->merge($data);
        $createUnit = new UnitsController();
        $createUnit->store($request);

        $this->reset(['unit', 'shortName']);
        $this->dispatchBrowserEvent('fields-reset');
        $this->dispatchBrowserEvent('show-success-message', ['message' => 'Unit added successfully!']);


        // session()->flash('message', 'Unit added successfully');
    }



    public function closeModal()
    {
        $this->isOpen = false;
    }


    public function render()
    {

        return view('livewire.setting.screens.add-unit', [
            'units' => $this->units,
        ]);
    }
}
