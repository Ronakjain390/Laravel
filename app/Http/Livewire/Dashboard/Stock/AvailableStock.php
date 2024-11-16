<?php

namespace App\Http\Livewire\Dashboard\Stock;

use Livewire\Component;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\StockUpdateNotification;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class AvailableStock extends Component
{
    protected $paginationTheme = 'tailwind';
    use WithPagination;

    public $listeners = ['deleteMultiple', 'deleteProduct', 'moveMultipleStock','updateFilter','moveMultipleStockEvent' => 'handleMoveMultipleStock', 'actions' => 'handleMessage', ];

    public $article = [];
    public $item_code = [];
    public $location = [];
    public $category = [];
    public $warehouse = [];
    public $stockInPage = [], $allStock = [];
    public $selectedStock = null;
    public $availableStocks = [];
    public $mainUser;
    public $successMessage;
    public $errorMessage;
    public $statusCode;
    public $panelColumnDisplayNames = [];
    public $panelUserColumnDisplayNames = [];
    public $ColumnDisplayNames = [];
    public $InvoiceColumnDisplayNames = [];
    public $singleStockMoveData, $moveQty, $singleStockModal = false;
    public $moveCategories, $moveWarehouses, $moveLocations, $quantityOption, $allQty, $newQty;
    public $newQuantitiesWithIds = [];
    public $openSearchModal = false;
    public $singleModalopen = false;
    public $searchModalHeading;
    public $searchModalButtonText;
    public $MergedColumnDisplayNames;
    public $searchModalAction;
    public $selectedIds = [];
    // Single public property to handle dynamic filters
    public $filters = [];
    public $paginate = 50;

    // Edit Product
    public $editChallanRequest = array(
        'unit' => null,
        'rate' => null,
        'qty' => null,
        'tax' => null,
        'with_tax' => false,
        'total_amount' => null,
        'item_code' => null,
        'columns' => [
            [
                'column_name' => '',
                'column_value' => '',
            ]
        ],
    );

    public function updateFilter($property, $value)
    {
        $this->filters[$property] = $value;
    }

    public function handleMessage($message)
    {
        // Show the success message
        $this->dispatchBrowserEvent('show-success-message', [$message]);
        // Refresh the products list
        // $this->products = $this->getProductsProperty();
    }

    public function closeModal()
    {
        $this->openSearchModal = false;
        $this->singleStockModal = false;
        $this->reset(['statusCode', 'errorMessage', 'moveCategories', 'moveWarehouses', 'quantityOption', 'allQty', 'newQty']);
    }

    // Single Stock move
    public function singleStockMove($seriesData)
    {
        // dd($seriesData);
        $this->singleStockModal = true;
        $seriesData = json_decode($seriesData);
        $this->singleStockMoveData = (array)$seriesData;

    }

    public function singleStockMoveConfirm()
    {
        // Extract data
        $singleStockMoveData = $this->singleStockMoveData;
        $moveQty = $this->moveQty;
        $newCategory = $this->moveCategories;
        $newWarehouse = $this->moveWarehouses;
        $newLocation = $this->moveLocations;

        // Check if any changes are made
        $changes = [];
        if ($newCategory && $newCategory !== $singleStockMoveData['category']) {
            $changes['category'] = $newCategory;
        }
        if ($newWarehouse && $newWarehouse !== $singleStockMoveData['warehouse']) {
            $changes['warehouse'] = $newWarehouse;
        }
        if ($newLocation && $newLocation !== $singleStockMoveData['location']) {
            $changes['location'] = $newLocation;
        }

        // If there are changes but no quantity specified, update only the changed fields in the existing record
        if (!empty($changes) && empty($moveQty)) {
            Product::where('id', $singleStockMoveData['id'])->update($changes);
            $this->singleStockModal = false;
            $this->dispatchBrowserEvent('show-success-message', ['message' => 'Stock updated successfully.']);
        }
        // If there are changes and a quantity is specified
        elseif (!empty($changes) && !empty($moveQty)) {
            // If moving full quantity, update the existing record
            if ($moveQty == $singleStockMoveData['qty']) {
                $changes['qty'] = $moveQty;
                Product::where('id', $singleStockMoveData['id'])->update($changes);
                $this->singleStockModal = false;
                $this->dispatchBrowserEvent('show-success-message', ['message' => 'Stock moved successfully.']);
            }
            // If moving partial quantity, create a new entry
            else {
                $newStockData = $singleStockMoveData;
                foreach ($changes as $key => $value) {
                    $newStockData[$key] = $value;
                }
                $newStockData['qty'] = $moveQty;
                unset($newStockData['id']);

                // Generate a new unique item_code
                $existingItemCodes = Product::where('item_code', 'LIKE', $singleStockMoveData['item_code'] . '-%')
                                            ->pluck('item_code')
                                            ->toArray();

                $suffix = 65; // ASCII for 'A'
                do {
                    $newItemCode = $singleStockMoveData['item_code'] . '-' . chr($suffix);
                    $suffix++;
                } while (in_array($newItemCode, $existingItemCodes));

                $newStockData['item_code'] = $newItemCode;

                // Save the new stock entry
                $newProduct = Product::create($newStockData);

                // Copy related ProductDetail records
                $originalProduct = Product::with('details')->find($singleStockMoveData['id']);
                foreach ($originalProduct->details as $detail) {
                    $newDetail = $detail->replicate();
                    $newDetail->product_id = $newProduct->id;
                    $newDetail->save();
                }

                // Update the old quantity by reducing the moved quantity
                $remainingQty = $singleStockMoveData['qty'] - $moveQty;
                Product::where('id', $singleStockMoveData['id'])->update(['qty' => $remainingQty]);

                $this->singleStockModal = false;
                $this->dispatchBrowserEvent('show-success-message', ['message' => 'Stock moved successfully.']);
            }
        }
        else {
            // No changes were made
            $this->dispatchBrowserEvent('show-error-message', ['message' => 'No changes were made.']);
        }

        // Reset the form fields
        $this->reset(['moveQty', 'moveCategories', 'moveWarehouses', 'moveLocations']);
    }


    public function mount()
    {
        $request = request();
        // $this->allStock = $this->productsQuery->pluck('id')->toArray();


        // $this->availableStocks = $this->productsQuery->pluck('id')->toArray();

        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->mainUser = json_encode($response->user);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }

        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);

        $filteredColumns = array_filter($columnsData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredColumns);
        $this->panelColumnDisplayNames = $panelColumnDisplayNames;

        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

        $columnFilterDataset = [
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];
        $request->merge($columnFilterDataset);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        $this->ColumnDisplayNames = $ColumnDisplayNames;
        $columnFilterDataset = [
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];
        $request->merge($columnFilterDataset);
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->InvoiceColumnDisplayNames = array_slice($ColumnDisplayNames, 3, 4);

        // Filter out empty values from InvoiceColumnDisplayNames
        $filteredInvoiceColumnDisplayNames = array_filter($this->InvoiceColumnDisplayNames, function ($value) {
            return !empty($value);
        });

        // Merge ColumnDisplayNames and filtered InvoiceColumnDisplayNames
        $this->MergedColumnDisplayNames = array_merge($this->ColumnDisplayNames, $filteredInvoiceColumnDisplayNames);
    }

    // public function getProductsQueryProperty()
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //     $query = Product::with('details')
    //         ->where('user_id', $userId)
    //         ->where('qty', '!=', 0)
    //         ->latest()
    //         ->when(!empty($this->article), function ($query) {
    //             $query->whereHas('details', function ($q) {
    //                 $q->where('column_name', 'Article')->whereIn('column_value', $this->article);
    //             });
    //         })
    //         ->when(!empty($this->item_code), function ($query) {
    //             $query->whereIn('item_code', $this->item_code);
    //         })
    //         ->when(!empty($this->location), function ($query) {
    //             $query->whereIn('location', $this->location);
    //         })
    //         ->when(!empty($this->category), function ($query) {
    //             $query->whereIn('category', $this->category);
    //         })
    //         ->when(!empty($this->warehouse), function ($query) {
    //             $query->whereIn('warehouse', $this->warehouse);
    //         });

    //     // Apply dynamic filters
    //     foreach ($this->filters as $column => $value) {
    //         if (!empty($value)) {
    //             $query->whereHas('details', function ($q) use ($column, $value) {
    //                 $q->where('column_name', $column)->whereIn('column_value', (array) $value);
    //             });
    //         }
    //     }

    //     return $query;
    // }

    public function resetFilters()
    {
        $this->article = [];
        $this->item_code = [];
        $this->location = [];
        $this->category = [];
        $this->warehouse = [];
    }

    // public function getProductsProperty()
    // {
    //     $products = $this->productsQuery->paginate($this->paginate);

    //     $this->stockInPage = $products->pluck('id')->toArray();

    //     return $products;
    // }


    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->delete(); // This will set the deleted_at column instead of permanently deleting the record

            // Send stock update email
            $action = 'delete';
            $count = 1; // Single product deleted
            $userName = Auth::getDefaultDriver() == 'team-user'
                ? Auth::guard('team-user')->user()->team_user_name
                : Auth::user()->name;
            $teamMemberName = Auth::getDefaultDriver() == 'team-user'
                ? Auth::guard('team-user')->user()->team_user_name
                : null;

            $this->sendStockUpdateEmail($action, $count, $userName, $teamMemberName);

            $this->dispatchBrowserEvent('show-success-message', ['message' => 'Product deleted successfully']);
        }
    }
    private function sendStockUpdateEmail($action, $count, $userName, $teamMemberName = null)
    {
        $timestamp = now();
        $data = [
            'action' => $action,
            'count' => $count,
            'userName' => $userName,
            'teamMemberName' => $teamMemberName,
            'timestamp' => $timestamp,
        ];

        if (Auth::getDefaultDriver() == 'team-user') {
            // Team member is logged in
            $teamUser = Auth::guard('team-user')->user();
            $adminUser = User::find($teamUser->team_owner_user_id);

            // Send email to admin (team owner)
            if ($adminUser && $adminUser->email) {
                Mail::to($adminUser->email)->send(new StockUpdateNotification($data));
            }

            // Send email to team member
            if ($teamUser->email) {
                $teamMemberData = $data;
                $teamMemberData['userName'] = $teamUser->team_user_name;
                $teamMemberData['teamMemberName'] = null; // No need to show team member name in their own email
                Mail::to($teamUser->email)->send(new StockUpdateNotification($teamMemberData));
            }
        } else {
            // Regular user is logged in
            $user = Auth::user();
            if ($user && $user->email) {
                Mail::to($user->email)->send(new StockUpdateNotification($data));
            }
        }
    }

    public function deleteMultiple($ids)
    {
        // dd($ids);
        $request = request();
        $products = new ProductController;
        $response = $products->bulkDestroy($request, $ids);
        $result = $response->getData();
        // dd($result);
        // Handle the response and update component data accordingly
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', ['message' => $result->message]);
            $this->successMessage = $result->message;

        } else {
            $this->dispatchBrowserEvent('show-error-message', ['message' => $result->message]);
            $this->errorMessage = json_encode((array) $result->errors);
        }
    }

    public function updatedPaginate()
    {
        $this->stockInPage = $this->products->pluck('id')->toArray();
    }

    public function selectChallanSeries($seriesData)
    {
        // dd($seriesData);
        $seriesData = json_decode($seriesData);
        $this->reset(['editChallanRequest']);
        $this->editChallanRequest = (array)$seriesData;
        // dd($this->editChallanRequest);

    }
    public function resetChallanSeries()
    {
        $this->reset(['editChallanRequest']);
    }



    public function moveMultipleStock($id)
    {
        // dd( $id);
        $this->reset(['successMessage', 'errorMessage']);
        $this->openSearchModal = true;
        $this->searchModalHeading = 'Move Stock';
        $this->searchModalButtonText = 'Move';
        $this->searchModalAction = 'moveStockConfirm';
        $this->selectedIds = $id;
    }
    public function editProduct(Request $request)
    {
        $this->reset(['successMessage']);
        $request->merge($this->editChallanRequest);
        // dd($request);
        $product = new ProductController;
        $response = $product->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        if ($result->status_code === 200) {
            $this->dispatchBrowserEvent('show-success-message', ['message' => $result->message]);
            // $this->successMessage = $result->message;
            $this->dispatchBrowserEvent('close-edit-modal'); // Dispatch event to close the modal

            $this->reset(['statusCode', 'errorMessage', 'editChallanRequest',]);
            // $this->fetchProduct();
        } else {
            $this->errorMessage = json_encode((array) $result->errors);
        }
    }


//     public $filteredItemIds = [];

//     public function updatedArticle()
// {
//     $this->updateFilteredItemIds();
// }

//     public function updatedItemCode()
//     {
//         $this->updateFilteredItemIds();
//     }
//     public function updatedLocation()
//     {
//         $this->updateFilteredItemIds();
//     }
//     public function updatedCategory()
//     {
//         $this->updateFilteredItemIds();
//     }
//     public function updatedWarehouse()
//     {
//         $this->updateFilteredItemIds();
//     }

//     private function updateFilteredItemIds()
//     {
//         $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

//         $query = Product::where('user_id', $userId);

//         // Apply your filters here
//         if ($this->article) {
//             $query->whereHas('productDetails', function ($q) {
//                 $q->whereIn('column_value', $this->article);
//             });
//         }
//         if ($this->item_code) {
//             $query->whereIn('item_code', $this->item_code);
//         }
//         if ($this->location) {
//             $query->whereIn('location', $this->location);
//         }
//         if ($this->category) {
//             $query->whereIn('category', $this->category);
//         }
//         if ($this->warehouse) {
//             $query->whereIn('warehouse', $this->warehouse);
//         }

//         // Fetch filtered results
//         $this->productsQuery = $query;
//         $this->products = $this->productsQuery->paginate($this->stockInPage);
//         // Update the filtered item IDs based on the updated query
//         $this->updateFilteredItemIds();


//         $this->filteredItemIds = $query->pluck('id')->toArray();
//     }
    public $sortField = null;
    public $sortDirection = null;

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        // Reset pagination to the first page when sorting
        $this->resetPage();
    }


    public function render()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Product::with('details')
            ->where('user_id', $userId)
            ->where('qty', '!=', 0);

        // Apply filters
        $query->when(!empty($this->article), function ($query) {
            $query->whereHas('details', function ($q) {
                $q->where('column_name', 'Article')->whereIn('column_value', $this->article);
            });
        })
        ->when(!empty($this->item_code), function ($query) {
            $query->whereIn('item_code', $this->item_code);
        })
        ->when(!empty($this->location), function ($query) {
            $query->whereIn('location', $this->location);
        })
        ->when(!empty($this->category), function ($query) {
            $query->whereIn('category', $this->category);
        })
        ->when(!empty($this->warehouse), function ($query) {
            $query->whereIn('warehouse', $this->warehouse);
        });

        // Apply dynamic filters
        foreach ($this->filters as $column => $value) {
            if (!empty($value)) {
                $query->whereHas('details', function ($q) use ($column, $value) {
                    $q->where('column_name', $column)->whereIn('column_value', (array) $value);
                });
            }
        }

        $this->allItemIds = $query->pluck('id')->toArray();

        // Apply sorting
        if ($this->sortField) {
            // Sort by total_qty as an integer
            if ($this->sortField === 'qty') {
                $query->orderByRaw('CAST(total_qty AS UNSIGNED) ' . $this->sortDirection);
            } else {
                $query->orderBy($this->sortField, $this->sortDirection);
            }
        }

        // Fetch paginated results
        $products = $query->paginate(50);

        // Fetch distinct values for filters
        $articles = ProductDetail::whereHas('product', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })->distinct()->pluck('column_value');

        $item_codes = Product::where('user_id', $userId)->distinct()->pluck('item_code');
        $locations = Product::where('user_id', $userId)->distinct()->pluck('location');
        $categories = Product::where('user_id', $userId)->distinct()->pluck('category');
        $warehouses = Product::where('user_id', $userId)->distinct()->pluck('warehouse');

        // Dynamically create filters from index 3 onwards for additional columns
        $dynamicFilters = [];
        foreach ($this->MergedColumnDisplayNames as $index => $columnName) {
            if ($index >= 3 && !empty($columnName)) {
                $dynamicFilters[$columnName] = ProductDetail::whereHas('product', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->where('column_name', $columnName)->distinct()->pluck('column_value');
            }
        }

        // Return data to the view
        return view('livewire.dashboard.stock.available-stock', [
            'availableStock' => $products,
            'articles' => $articles,
            'item_codes' => $item_codes,
            'locations' => $locations,
            'categories' => $categories,
            'warehouses' => $warehouses,
            'dynamicFilters' => $dynamicFilters,
        ]);
    }

    // public function render()
    // {
    //     $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //     // Apply the current filters to the products query
    //     $filteredProductsQuery = $this->productsQuery;

    //     // Fetch the distinct values for filters based on the current filtered products
    //     $articles = ProductDetail::whereHas('product', function ($query) use ($filteredProductsQuery) {
    //         $query->whereIn('id', $filteredProductsQuery->pluck('id'));  // Only consider the filtered product IDs
    //     })->distinct()->pluck('column_value');

    //     $item_codes = Product::whereIn('id', $filteredProductsQuery->pluck('id'))->distinct()->pluck('item_code');
    //     $locations = Product::whereIn('id', $filteredProductsQuery->pluck('id'))->distinct()->pluck('location');
    //     $categories = Product::whereIn('id', $filteredProductsQuery->pluck('id'))->distinct()->pluck('category');
    //     $warehouses = Product::whereIn('id', $filteredProductsQuery->pluck('id'))->distinct()->pluck('warehouse');

    //     // Pass the filtered data to the view
    //     return view('livewire.dashboard.stock.available-stock', [
    //         'availableStock' => $this->products,  // This will call the getProductsProperty() method
    //         'articles' => $articles,
    //         'item_codes' => $item_codes,
    //         'locations' => $locations,
    //         'categories' => $categories,
    //         'warehouses' => $warehouses,
    //     ]);
    // }


}
