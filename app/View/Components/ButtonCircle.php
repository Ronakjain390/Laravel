<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonCircle extends Component
{
    public $primary;
    public $icon;
    public $clickAction;
    public $id;

    /**
     * Create a new component instance.
     */
    public function __construct($primary = false, $icon = '', $clickAction = '')
    {
        $this->primary = $primary;
        $this->icon = $icon;
        $this->clickAction = $clickAction;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        // dd('here', $this->id);
        return view('components.button-circle');
    }
}
