<?php

namespace App\Http\Livewire\Components;
use Illuminate\Support\Facades\Auth;
use App\Models\TagsTable;
use App\Models\PurchaseOrder;
use App\Models\GoodsReceipt;
use App\Models\Invoice;
use App\Models\Estimates;
use App\Models\Challan;
use Livewire\WithPagination;
use App\Models\ReturnChallan;
use App\Models\InvoiceStatus;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;


use Livewire\Component;

class TagComponent extends Component
{
    use WithPagination;
    public $openSearchModal = false;
    public $searchTerm = '';
    public $selectedTags = [];
    public $searchModalHeading = '';
    public $searchModalButtonText = '';
    public $searchModalAction = '';
    public $panelId;
    public $tableId;
    public $itemId;

    protected $listeners = ['openTagModal'];

    // This method will handle the event when it is triggered
    public function openTagModal($itemId, $action)
    {
        // dd($itemId, $action);
        $this->tagModal($itemId, $action);
    }


    public function mount($panelId, $tableId)
    {
        // dd( $panelId, $tableId);
        $this->panelId = $panelId;
        $this->tableId = $tableId;
    }

    // public function createTag()
    // {
    //     $userId = Auth::id();
    //     $tag = TagsTable::create([
    //         'name' => $this->searchTerm,
    //         'user_id' => $userId,
    //         'panel_id' => $this->panelId,
    //         'table_id' => $this->tableId,
    //     ]);

    //     $this->selectedTags[] = $tag->id;
    //     $this->searchTerm = '';
    // }

    // public function saveTags($newTags)
    // {
    //     // dd($newTags, $this->selectedTags);

    //     // Define a mapping of panelId and tableId to model class
    //     $modelMapping = [
    //         1 => [
    //             1 => \App\Models\Challan::class,
    //             2 => \App\Models\ReturnChallan::class,
    //         ],
    //         2 => [
    //             3 => \App\Models\ReturnChallan::class,
    //             4 => \App\Models\Challan::class,
    //         ],
    //         3 => [
    //             5 => \App\Models\Invoice::class,
    //             6 => \App\Models\PurchaseOrder::class,
    //         ],
    //         4 => [
    //             7 => \App\Models\PurchaseOrder::class,
    //             8 => \App\Models\Invoice::class,
    //         ],
    //         // Add other panelId mappings here if needed
    //     ];

    //     // Ensure itemId is an array
    //     $this->itemId = is_array($this->itemId) ? $this->itemId : [$this->itemId];

    //     // Get the user ID
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
    //     $selectedTagIds = $this->selectedTags;
    //     dd($selectedTagIds);
    //     // Check if the panelId and tableId are valid
    //     if (isset($modelMapping[$this->panelId]) && isset($modelMapping[$this->panelId][$this->tableId])) {
    //         $modelClass = $modelMapping[$this->panelId][$this->tableId];

    //         foreach ($this->itemId as $itemId) {
    //             // Fetch the model instance dynamically
    //             $modelInstance = $modelClass::find($itemId);

    //             $pivotData = [];
    //             foreach ($selectedTagIds as $tagId) {
    //                 $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => $this->panelId, 'table_id' => $this->tableId];
    //             }

    //             // Attach the selected tags to the model instance with additional pivot data
    //             if ($modelInstance) {
    //                 $modelInstance->tableTags()->sync($pivotData);
    //             }
    //         }
    //     }

    //     $this->closeTagModal();
    //     $this->emit('actions', 'Tags saved successfully.');
    //     $this->emit('resetSelection');
    // }

    public function saveTags($newTags)
    {
        // Get the user ID
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        // Initialize an array to store the tag IDs to be saved
        $tagsToSave = [];

        // Process new tags
        foreach ($newTags as $tag) {
            if (isset($tag['id'])) {
                if (is_numeric($tag['id'])) {
                    // Existing tag, add to tagsToSave
                    $tagsToSave[] = $tag['id'];
                } elseif (strpos($tag['id'], 'new_') === 0) {
                    // This is a new tag, create it in the database
                    $newTag = TagsTable::firstOrCreate(
                        ['name' => $tag['name'], 'panel_id' => $this->panelId, 'table_id' => $this->tableId],
                        ['user_id' => $userId]
                    );
                    $tagsToSave[] = $newTag->id;
                }
            }
        }

        // Update $this->selectedTags with only the unique tags
        $this->selectedTags = array_unique($tagsToSave);

        // Ensure itemId is an array
        $this->itemId = is_array($this->itemId) ? $this->itemId : [$this->itemId];

        // Define a mapping of panelId and tableId to model class
        $modelMapping = [
            1 => [
                1 => \App\Models\Challan::class,
                2 => \App\Models\ReturnChallan::class,
            ],
            2 => [
                3 => \App\Models\ReturnChallan::class,
                4 => \App\Models\Challan::class,
            ],
            3 => [
                5 => \App\Models\Invoice::class,
                6 => \App\Models\PurchaseOrder::class,
            ],
            4 => [
                7 => \App\Models\PurchaseOrder::class,
                8 => \App\Models\Invoice::class,
            ],
            6 => [
                11 => \App\Models\Estimates::class,
            ],

        ];

        // Check if the panelId and tableId are valid
        if (isset($modelMapping[$this->panelId]) && isset($modelMapping[$this->panelId][$this->tableId])) {
            $modelClass = $modelMapping[$this->panelId][$this->tableId];

            foreach ($this->itemId as $itemId) {
                // Fetch the model instance dynamically
                $modelInstance = $modelClass::find($itemId);

                $pivotData = [];
                foreach ($this->selectedTags as $tagId) {
                    $pivotData[$tagId] = ['user_id' => $userId, 'panel_id' => $this->panelId, 'table_id' => $this->tableId];
                }

                // Sync the selected tags to the model instance with additional pivot data
                if ($modelInstance) {
                    $modelInstance->tableTags()->sync($pivotData);
                }
            }
        }

        $this->closeTagModal();
        $this->emit('actions', 'Tags saved successfully.');
        $this->emit('resetSelection');
    }

    public function closeTagModal()
    {
        $this->isOpen = false;
        $this->openSearchModal = false;
        $this->reset(['itemId', 'searchModalHeading', 'searchModalButtonText', 'searchModalAction', 'selectedTags']);
    }

    public function tagModal($itemId, $action)
    {
        if($action == 'addTags') {
            $this->itemId = $itemId;
            $this->openSearchModal = true;
            $this->searchModalHeading = 'Add Tag';
            $this->searchModalButtonText = 'Add';
            $this->searchModalAction = 'saveTags';
        }

        $this->selectedTags = [];

        if (!is_array($itemId)) {
            $itemId = [$itemId];
        }

        foreach ($itemId as $id) {
            if($this->panelId == 1 && $this->tableId == 1) {
                $challan = \App\Models\Challan::find($id);
                // dd($challan);
                if ($challan && $challan->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $challan->tableTags->pluck('id')->toArray());
                }
            }elseif($this->panelId == 1 && $this->tableId == 2) {
                $returnChallan = \App\Models\ReturnChallan::find($id);
                // dd($returnChallan);
                if ($returnChallan && $returnChallan->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $returnChallan->tableTags->pluck('id')->toArray());
                }
            }elseif($this->panelId == 2 && $this->tableId == 3) {
                $returnChallan = \App\Models\ReturnChallan::find($id);
                if ($returnChallan && $returnChallan->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $returnChallan->tableTags->pluck('id')->toArray());
                }
            }elseif($this->panelId == 2 && $this->tableId == 4) {
                $challan = \App\Models\Challan::find($id);
                if ($challan && $challan->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $challan->tableTags->pluck('id')->toArray());
                }
            } elseif($this->panelId == 3 && $this->tableId == 6) {
                $purchaseOrder = \App\Models\PurchaseOrder::find($id);
                if ($purchaseOrder && $purchaseOrder->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $purchaseOrder->tableTags->pluck('id')->toArray());
                }
            } elseif($this->panelId == 3 && $this->tableId == 5) {
                $invoice = \App\Models\Invoice::find($id);
                if ($invoice && $invoice->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $invoice->tableTags->pluck('id')->toArray());
                }
            } elseif($this->panelId == 4 && $this->tableId == 7) {
                $goodsReceipt = \App\Models\PurchaseOrder::find($id);
                if ($goodsReceipt && $goodsReceipt->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $goodsReceipt->tableTags->pluck('id')->toArray());
                }
            } elseif($this->panelId == 4 && $this->tableId == 8) {
                $invoice = \App\Models\Invoice::find($id);
                if ($invoice && $invoice->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $invoice->tableTags->pluck('id')->toArray());
                }
            } elseif ($this->panelId == 6 && $this->tableId == 11) {
                $estimate = \App\Models\Estimates::find($id);
                if ($estimate && $estimate->tableTags) {
                    $this->selectedTags = array_merge($this->selectedTags, $estimate->tableTags->pluck('id')->toArray());
                }
            }
        }
        // dd($this->selectedTags);
        $this->selectedTags = array_unique($this->selectedTags);
    }

    public function render()
    {
        $userId = Auth::id();
        $tags = TagsTable::where('panel_id', $this->panelId)
            ->where('table_id', $this->tableId)
            ->where('user_id', $userId)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->get();

        return view('livewire.components.tag-component', [
            'allTags' => $tags,
        ]);
    }
}
