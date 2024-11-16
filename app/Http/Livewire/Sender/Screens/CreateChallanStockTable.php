<?php

namespace App\Http\Livewire\Sender\Screens;

use Livewire\Component; 
use App\Http\Controllers\Products\ProductController;
use Illuminate\Http\Request;

class CreateChallanStockTable extends Component
{
    public $isOpen = false;
    public $open = false;
    public $selectedProducts = [];
    public $inputsDisabled = true;
    public $inputsResponseDisabled = true;
    protected $products;
    protected $articles;
    protected $item_codes;
    protected $locations;

    public $listeners = [
        'loadStockTable' => 'loadStockData',
        'filterProducts' => 'filterProductsData',
    ];

    public function loadStockData($request)
    {
        $products = new ProductController;
        $response = $products->index($request);
        $result = $response->getData();
        $this->products = (array) $result->data;
        $this->articles = collect($this->products)->pluck('details.0.column_value')->all();
        $this->item_codes = collect($this->products)->pluck('item_code')->unique()->all();
        $this->locations = collect($this->products)->pluck('location')->unique()->all();
    }

    public function filterProductsData($filters)
    {
        $request = new Request();
        $request->merge($filters);
        $this->loadStockData($request);
    }

    public function render()
    {
        return view('livewire.sender.screens.create-challan-stock-table');
    }
}
