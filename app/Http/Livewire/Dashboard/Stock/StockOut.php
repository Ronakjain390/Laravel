<?php

namespace App\Http\Livewire\Dashboard\Stock;

use Livewire\Component;
use App\Models\ProductLog;
use App\Models\Product;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use App\Exports\ExportChallanJob;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class StockOut extends Component
{
    protected $paginationTheme = 'tailwind';
    use WithPagination;
    public  $articles = [], $articleSearch = [], $locations = [], $categories = [], $warehouse = [], $item_codes = [],   $Article, $location, $item_code, $category, $out_method;
    public $errorMessage, $successMessage, $statusCode;
    public $article, $from, $to;
    public $filters = [];
    public $paginate = 50;
    public $MergedColumnDisplayNames;

    public function mount()
    {
        $request = request();
        $this->allStock = $this->productsQuery->pluck('id')->toArray();
        $this->stockInPage = $this->products->pluck('id')->toArray();

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

        $OutData = new ProductController;
        $response = $OutData->indexOut($request);
        $result = $response->getData();
        $this->OutData = $result->data;

        $this->InvoiceColumnDisplayNamesTab4 = ['#','Article', 'HSN','Details', 'item code','category', 'warehouse', 'location', 'unit', 'qty', 'rate', 'Out Qty', 'Sent To', 'Order No', 'Out Method', 'Date','Time'];
    }


    public function getProductsQueryProperty()
    {
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $query = Product::with('details')
            ->where('user_id', $userId)
            ->where('qty', '!=', 0)
            ->latest()
            ->when(!empty($this->article), function ($query) {
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

        return $query;
    }

    public function getProductsProperty()
    {
        $products = $this->productsQuery->paginate($this->paginate);

        $this->stockInPage = $products->pluck('id')->toArray();

        return $products;
    }

    public function updateVariable($variable, $value)
    {
        // dd($variable, $value);
            // Add from and to dates to the request
        $request = request();
        // $request->merge([
        //     'from' => $this->from ?? null,
        //     'to' => $this->to ?? null,
        // ]);

        $this->{$variable} = $value;
        // dd($this->{$variable}, $variable, $value);
    }


    // Method to reset all filters
    public function resetFilters()
        {
            $this->reset(['Article', 'item_code', 'warehouse', 'location', 'category', 'from', 'to']);
        }

    // Computed property to check if any filter is applied
    public function getHasFiltersProperty()
    {
        return !empty($this->Article) || !empty($this->item_code) || !empty($this->warehouse) || !empty($this->location) || !empty($this->category) || !empty($this->from) || !empty($this->to);
    }

    // public function updatedPage()
    // {
    //     $this->dispatchBrowserEvent('page-updated', ['ids' => $this->getProductIds()]);
    // }

    // private function getProductIds()
    // {
    //     return Product::paginate(10, ['*'], 'page', $this->page)->pluck('id');
    // }

    public function exportData()
    {
        return Excel::download(new ExportChallanJob($this->filters()), 'product_logs.xlsx');
    }

    private function filters()
    {
        $query = ProductLog::query();

        if (!empty($this->Article)) {
            $query->whereHas('product.details', function ($q) {
                $q->where('column_value', $this->Article);
            });
        }
        if (!empty($this->item_code)) {
            $query->whereHas('product', function ($q) {
                $q->where('item_code', $this->item_code);
            });
        }
        if (!empty($this->location)) {
            $query->whereHas('product', function ($q) {
                $q->where('location', $this->location);
            });
        }
        if (!empty($this->category)) {
            $query->whereHas('product', function ($q) {
                $q->where('category', $this->category);
            });
        }
        if (!empty($this->warehouse)) {
            $query->whereHas('product', function ($q) {
                $q->where('warehouse', $this->warehouse);
            });
        }
        if (!empty($this->from)) {
            $query->whereDate('out_at', '>=', $this->from);
        }
        if (!empty($this->to)) {
            $query->whereDate('out_at', '<=', $this->to);
        }

        return $query->get();
    }

    public function render()
    {
        $request = request();
        // $this->emitSelf('componentUpdated');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

    //     $filters = [
    //         'article' => $this->Article,
    //         'item_code' => $this->item_code,
    //         'warehouse' => $this->warehouse,
    //         'location' => $this->location,
    //         'category' => $this->category,
    //         'from_date' => $this->from,
    //         'to_date' => $this->to,
    //         'out_method' => $this->out_method,
    //     ];

    //     // Apply filters from the request to the query
    //     foreach ($filters as $key => $value) {
    //         if ($value !== null) {
    //             $request->merge([$key => $value]);
    //         }
    //     }


        $query = ProductLog::query()->with( 'product', 'product.details',  'challan');

    //     // Filter by user_id
    //     $query->where('user_id', $userId);



    //     if (!empty($this->Article)) {
    //         $query->whereHas('product.details', function ($q) {
    //             $q->where('column_value', $this->Article);
    //         });
    //     }
    //     if (!empty($this->item_code)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('item_code', $this->item_code);
    //         });
    //     }
    //     if (!empty($this->location)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('location', $this->location);
    //         });
    //     }
    //     if (!empty($this->category)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('category', $this->category);
    //         });
    //     }
    //     if (!empty($this->warehouse)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('warehouse', $this->warehouse);
    //         });
    //     }
    //     if (!empty($this->from)) {
    //         $query->whereDate('out_at', '>=', $this->from);
    //     }
    //     if (!empty($this->to)) {
    //         $query->whereDate('out_at', '<=', $this->to);
    //     }


    //     // Fetch filtered results
    //     $products = $query->paginate(50);
    //     // dd($products);
    //     // Fetch unique values based on the filtered results
    //     $this->articles = $products->pluck('product.details.0.column_value')->unique()->filter()->values()->toArray();
    //     $this->item_codes = $products->pluck('product.item_code')->unique()->filter()->values()->toArray();
    //     $this->locations = $products->pluck('product.location')->unique()->filter()->values()->toArray();
    //     $this->categories = $products->pluck('product.category')->unique()->filter()->values()->toArray();
    //     $this->warehouses = $products->pluck('product.warehouse')->unique()->filter()->values()->toArray();
    // // dd( $this->articles, $this->item_codes, $this->locations, $this->categories, $this->warehouses);
    //     // Apply further filters to the paginated results

    //     if (!empty($this->item_code)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('item_code', $this->item_code);
    //         });
    //     }
    //     if (!empty($this->location)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('location', $this->location);
    //         });
    //     }
    //     if (!empty($this->category)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('category', $this->category);
    //         });
    //     }
    //     if (!empty($this->warehouse)) {
    //         $query->whereHas('product', function ($q) {
    //             $q->where('warehouse', $this->warehouse);
    //         });
    //     }
    //     $dynamicFilters = [];
    //     foreach ($this->MergedColumnDisplayNames as $index => $columnName) {
    //         if ($index >= 3 && !empty($columnName)) {
    //             $dynamicFilters[$columnName] = ProductDetail::whereHas('product', function ($query) use ($userId) {
    //                 $query->where('user_id', $userId);
    //             })->where('column_name', $columnName)->distinct()->pluck('column_value');
    //         }
    //     }

        // dd('sdgfs');
        // Fetch paginated results
        $products = $query->latest()->paginate(50);
        //    dd($this->InvoiceColumnDisplayNamesTab4);
        return view('livewire.dashboard.stock.stock-out', [
            'productsOut' => $products,
            'columnDisplayNamesOut' => $this->InvoiceColumnDisplayNamesTab4,
            // 'dynamicFilters' => $dynamicFilters,
        ]);
    }
}
