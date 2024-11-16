<?php

namespace App\Http\Livewire\Dashboard\Stock;
use App\Models\Product;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockMovement extends Component
{
    public $category;
    public $warehouse;
    public $selectedProducts = [];
    public $isOpen = false;
    public $selectedIds = [];
    public $products = [];
    public $newQuantities = [];
    public $showConfirmationModal = false;
    public $selectedOption = null;
    public $selectedNewOption = '';


    protected $listeners = ['openAdditiveModal' => 'showConfirmationDialog'];

    // public function handleMessage($message)
    // {
    //     // Show the success message
    //     $this->dispatchBrowserEvent('show-success-message', [$message]);
    //     $this->emit('updateStock');
    //     $this->closeModal();
    // }

    public function showConfirmationDialog($data)
    {
        // dd($data);
        $this->selectedIds = $data;
        $this->showConfirmationModal = true;
    }

    public function selectOption($option)
    {
        // dd($option);
        $this->selectedOption = $option;
        $this->showConfirmationModal = false;
        $this->openModal();
    }

    public function cancelConfirmation()
    {
        $this->showConfirmationModal = false;
        $this->selectedIds = [];
        $this->selectedOption = null;
    }

    public $newOptions = [];
    public $availableOptions = [];
    public $otherOptions = [];


    public function openModal()
    {
        if (!$this->selectedOption) {
            return;
        }

        $query = Product::whereIn('id', $this->selectedIds)
            ->with(['details' => function ($query) {
                $query->select('id', 'product_id', 'column_name', 'column_value');
            }])
            ->select('id', 'item_code', 'unit', 'qty', $this->selectedOption);

        $this->products = $query->get();

        // Get all unique options for the selected category
        $this->availableOptions = $this->products->pluck($this->selectedOption)->unique()->values()->toArray();

        // Get all possible options
        $allOptions = ['warehouse', 'location', 'rack', 'bin']; // Add or remove options as needed

        // Remove the currently selected option from the list
        $this->otherOptions = array_values(array_diff($allOptions, [$this->selectedOption]));

        // Initialize newOptions with current values
        // $this->newOptions = $this->products->pluck($this->selectedOption, 'id')->toArray();

        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        // Fetch all the selectedOptions data
        $query = Product::where('user_id', $userId)
        ->select($this->selectedOption);
        $this->newOptions = $query->get()->pluck($this->selectedOption)->unique()->values()->toArray();

        $this->isOpen = true;
        $this->dispatchBrowserEvent('open-additive-quantity-modal', ['option' => $this->selectedOption]);
    }

    public function mount($selectedIds)
    {
        // dd($selectedIds);
        $this->selectedIds = $selectedIds; // Initialize the selectedIds
    }

    // public function openModal($data)
    // {
    //     // The $data is already an array of product IDs
    //     $selectedIds = $data;

    //     // Fetch the product data associated with the selected IDs
    //     $this->products = Product::whereIn('id', $selectedIds)->with('details')->get();

    //     // Set modal visibility
    //     $this->isOpen = true;

    //     // Optionally dispatch a browser event to open the modal
    //     $this->dispatchBrowserEvent('open-additive-quantity-modal');
    // }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->newQuantities = [];
    }

    public $updatedQuantities = [];
    public function updateQuantity($productId, $quantity)
    {
        // dd($productId, $quantity);
        // Store the updated quantity in the array
        $this->updatedQuantities[$productId] = $quantity;
    }

    public  $showMsg = false;

    public function updateQuantities()
    {
        if (in_array($this->selectedNewOption, $this->availableOptions)) {
            $errorMessage = 'You cannot move stock to the same ' . $this->selectedOption . ' as it already exists.';
            $this->closeModal();
            $this->reset('newQuantities', 'selectedOption', 'selectedNewOption');
            $this->dispatchBrowserEvent('show-error-message', ['message' => $errorMessage]);
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }

        DB::beginTransaction();
        try {
            $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

            foreach ($this->selectedIds as $productId) {
                $product = Product::find($productId);
                if (!$product) {
                    continue; // Skip if product not found
                }

                // Check if there's an updated quantity for this product
                $updatedQuantity = $this->updatedQuantities[$productId] ?? null;

                if ($updatedQuantity === null || $updatedQuantity == 0) {
                    // If no quantity specified or quantity is 0, just update the selectedOption
                    $product->update([$this->selectedOption => $this->selectedNewOption]);
                } else {
                    // If quantity is specified, create a new record and update the original
                    $newItemCode = $this->generateNewItemCode($product->item_code);

                    $newProduct = Product::create([
                        'item_code' => $newItemCode,
                        'unit' => $product->unit,
                        'qty' => $updatedQuantity,
                        'user_id' => $userId,
                        $this->selectedOption => $this->selectedNewOption,
                    ]);

                    // Decrease the quantity from the original product
                    $product->qty -= $updatedQuantity;
                    $product->save();

                    // Copy related ProductDetail records
                    foreach ($product->details as $detail) {
                        $newDetail = $detail->replicate();
                        $newDetail->product_id = $newProduct->id;
                        $newDetail->save();
                    }
                }
            }

            DB::commit();

            $this->closeModal();
            $successMessage = 'Stock moved successfully and options updated.';
            $this->selectedIds = [];
            $this->updatedQuantities = [];
            $this->isOpen = false;
            $this->newQuantities = [];
            $this->dispatchBrowserEvent('show-success-message', ['message' => $successMessage]);

            return [
                'success' => true,
                'message' => $successMessage
            ];

        } catch (\Exception $e) {
            $this->closeModal();
            $errorMessage = 'An error occurred while moving stock: ' . $e->getMessage();
            $successMessage = 'Stock moved successfully and options updated.';
            $this->selectedIds = [];
            $this->updatedQuantities = [];
            $this->isOpen = false;
            $this->newQuantities = [];
            $this->dispatchBrowserEvent('show-error-message', ['message' => $errorMessage]);
            return [
                'success' => false,
                'message' => $errorMessage
            ];
        }
    }

    private function generateNewItemCode($baseItemCode)
    {
        $existingItemCodes = Product::where('item_code', 'LIKE', $baseItemCode . '-%')
            ->pluck('item_code')
            ->toArray();

        $suffix = 65; // ASCII for 'A'
        do {
            $newItemCode = $baseItemCode . '-' . chr($suffix);
            $suffix++;
        } while (in_array($newItemCode, $existingItemCodes));

        return $newItemCode;
    }

    public function render()
    {
        // dd('dafs');
        return view('livewire.dashboard.stock.stock-movement',[
            'products' => $this->products,
        ]);
    }
}
