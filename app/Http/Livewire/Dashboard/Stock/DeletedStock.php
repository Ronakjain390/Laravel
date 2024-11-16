<?php

namespace App\Http\Livewire\Dashboard\Stock;

use Livewire\Component;
use App\Models\Product;
use Illuminate\Http\Request;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class DeletedStock extends Component
{
    use WithPagination;
    protected $paginationTheme = 'tailwind';
    public  $articles = [], $articleSearch = [], $locations = [], $categories = [], $warehouse = [], $item_codes = [],   $Article, $location, $item_code, $category;
    public $errorMessage, $successMessage, $statusCode;
    public $article, $from, $to;

    public function mount()
    {
        $request = request();
        $UserResource = new UserAuthController;
        $response = $UserResource->user_details($request);
        $response = $response->getData();
        if ($response->success == "true") {
            $this->mainUser = json_encode($response->user);
            // dd($this->mainUser);
            // $this->successMessage = $response->message;
            // $this->reset(['errorMessage']);
        } else {
            $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
        }
        // $this->ColumnDisplayNames = ['Article', 'HSN', 'Details', 'Category','Warehouse','Locaiton', 'Unit', 'Qty', 'Rate', 'Date', 'Time', 'Action'];
        $PanelColumnsController = new PanelColumnsController;

        $request->merge([
            'feature_id' => 1,
            // Auth::guard(Auth::getDefaultDriver())->user()->id
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


        $request->merge([
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ]);
        $columnsUserResponse = $PanelColumnsController->index($request);
        $columnsUserData = json_decode($columnsUserResponse->content(), true);

        $filteredUserColumns = array_filter($columnsUserData['data'], function ($column) {
            return $column['feature_id'] == 1;
        });
        $panelUserColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $filteredUserColumns);

        $this->panelUserColumnDisplayNames = $panelUserColumnDisplayNames;

        // Add from stock modal data
        $request = request();
        $columnFilterDataset = [
            'feature_id' => 1,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        ];
        $request->merge($columnFilterDataset);
        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);

        $this->ColumnDisplayNames = $ColumnDisplayNames;
    }

    public function render()
    {
        $request = request();
        $this->emitSelf('componentUpdated');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;

        $filters = [
            'article' => $this->Article,
            'item_code' => $this->item_code,
            'warehouse' => $this->warehouse,
            'location' => $this->location,
            'category' => $this->category,
            'from_date' => $this->from,
            'to_date' => $this->to,
        ];

        // Apply filters from the request to the query
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $request->merge([$key => $value]);
            }
        }

        $query = Product::query()->with('details')->onlyTrashed();

        // Filter by user_id
        $query->where('user_id', $userId);

        // Add a where clause to the query to filter out products where qty is not equal to 0
        $query->where('qty', '!=', 0);

        // Apply filters dynamically
        if (!empty($this->Article)) {
            $query->whereHas('details', function ($q) {
                $q->where('column_value', $this->Article);
            });
        }
        if (!empty($this->item_code)) {
            $query->where('item_code', $this->item_code);
        }
        if (!empty($this->location)) {
            $query->where('location', $this->location);
        }
        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }
        if (!empty($this->warehouse)) {
            $query->where('warehouse', $this->warehouse);
        }

        // Fetch filtered results
        $products = $query->get();
        // dd($products);
        // Fetch unique values based on the filtered results
        $this->articles = $products->pluck('details.0.column_value')->unique()->filter()->values()->toArray();
        $this->item_codes = $products->pluck('item_code')->unique()->filter()->values()->toArray();
        $this->locations = $products->pluck('location')->unique()->filter()->values()->toArray();
        $this->categories = $products->pluck('category')->unique()->filter()->values()->toArray();
        $this->warehouses = $products->pluck('warehouse')->unique()->filter()->values()->toArray();

        // Apply further filters to the paginated results
        if (!empty($this->item_code)) {
            $query->where('item_code', $this->item_code);
        }
        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }
        if (!empty($this->warehouse)) {
            $query->where('warehouse', $this->warehouse);
        }
        if (!empty($this->location)) {
            $query->where('location', $this->location);
        }

        // Fetch paginated results
        $products = $query->paginate(50);
        return view('livewire.dashboard.stock.deleted-stock', [
            'deletedProducts' => $products,
            'columnDisplayNames' => $this->ColumnDisplayNames,
        ]);
    }


}
