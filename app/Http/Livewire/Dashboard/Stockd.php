<?php

namespace App\Http\Livewire\Dashboard;

use stdClass;
use App\Models\Product;
use Livewire\Component;
use App\Models\PanelColumn;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class Stockd extends Component
{
    use WithFileUploads;

    public $ColumnDisplayNames = [];
    public $tableTdData = [];
    public $selectedItems = [];
    public $products, $articles = [], $articleSearch = [], $locations = [], $categories = [], $warehouse = [], $item_codes, $Article, $location, $item_code, $category;
    public $uploadFile;
    public $updateFile;
    public $statusCode;
    public $successMessage;
    public $errorMessage;
    public $errorData;
    public $activeTab;
    public $OutData;
    public $from;
    public $to;
    public $modalShow = false;
    public $mainUser;
    public $unitName;
    protected $queryString = ['activeTab'];

    protected $listeners = ['deleteSelected'];

    public function updatingQueryString($queryString, $old, $new)
    {
        unset($queryString['feature_id']);
        unset($queryString['user_id']);

        return $queryString;
    }
    public function updateVariable($variable, $value)
    {
            // Add from and to dates to the request
        $request = request();
        $request->merge([
            'from' => $this->from ?? null,
            'to' => $this->to ?? null,
        ]);

        $this->{$variable} = $value;
        $this->emit('refreshDropdown');
    }
    public function mount(Request $request)
    {
        $UserResource = new UserAuthController;
            $response = $UserResource->user_details($request);
            $response = $response->getData();
            if ($response->success == "true") {
                $this->mainUser = json_encode($response->user);
                // dd($this->mainUser);
                // $this->successMessage = $response->message;
                $this->reset(['errorMessage']);
            } else {
                $this->errorMessage = json_encode($response->errors ?? [[$response->message]]);
            }

        if (!request()->query('activeTab')) {
            $this->activeTab = 'tab1';
        }
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
        $this->fetchProduct();
    }

    public function modalShow()
    {
        $this->modalShow = true;
    }
    public function modalHide()
    {
        $this->modalShow = false;
    }

    public function resetFilters()
    {
        $this->Article = null;
        $this->location = null;
        $this->item_code = null;
        $this->category = null;
        $this->warehouse = null;
        $this->outMethod = null;
        $this->from = null;
        $this->to = null;
    }

    public function updatedActiveTab($value)
    {
        // dd($value);
        // reset the success and error messages
        $this->reset(['successMessage', 'errorMessage', 'errorData']);
        $this->resetFilters();
        // Fetch products whenever the active tab is updated
        if ($value === 'tab3') {
            // dd('tab3');
            // $this->fetchProduct();

        }
    }
    public function productUpload()
    {
        $request = new Request();
        // Merge the file data with the existing request data
        $requestData = [
            'field_name' => 'value', // Replace with your other request data
            'file' => $this->uploadFile,
        ];
        // dd($this);

        $request->merge($requestData);
        $productUpload = new ProductController;
        $response = $productUpload->bulkUpload($request);
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // Reset the success and error messages
        $this->reset(['successMessage', 'errorMessage', 'errorData']);

        switch ($result->status_code) {
            case 200:
                $this->successMessage = $result->message;
                $this->reset('uploadFile');
                break;
            case 400:
                $this->errorMessage = 'File upload failed.';
                break;
            case 422:
                $this->errorMessage = 'Validation failed:';
                $this->reset('uploadFile');
                $errors = json_decode($response->content(), true)['errors'];
                $this->errorData = collect($errors)->flatten()->toJson();
                break;
            case 500:
                $this->errorMessage = 'Internal server error occurred.';
                break;
            default:
                $this->errorMessage = 'An unknown error occurred.';
                break;
        }
    }

    public function productUpdate()
    {

        // Create a new Request instance
        $request = new Request();

        // Merge the file data with the existing request data
        $requestData = [
            'field_name' => 'value', // Replace with your other request data
            'file' => $this->updateFile,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $request->merge($requestData);
        // dd($request);
        $productUpload = new ProductController;
        $response = $productUpload->bulkUpdate($request);
        // dd( $productUpload->bulkUpdate($request));
        $result = $response->getData();

        // Set the status code and message received from the result
        $this->statusCode = $result->status_code;

        // Reset the success and error messages
        $this->reset(['successMessage', 'errorMessage']);

        switch ($result->status_code) {
            case 200:
                $this->successMessage = $result->message;
                $this->reset('updateFile');
                break;
            case 422:
                $this->errorMessage = 'Validation failed:';
                $errors = json_decode($response->content(), true)['errors'];
                $this->errorData = collect($errors)->flatten()->toJson();
                $this->reset('updateFile');
                break;
            case 500:
                $this->errorMessage = 'Internal server error occurred.';
                break;
            default:
                $this->errorMessage = 'An unknown error occurred.';
                break;
        }
    }

    public function fetchProduct()
    {
        $request = request();
        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
            'category' => $this->category ?? null,
            'warehouse' => $this->warehouse ?? null,
        ]);

        $products = new ProductController;
        $response = $products->index($request);
        $result = $response->getData();
        $this->products = $result->data;
        // dd($this->products);
        $this->articles = [];
        foreach ($this->products as $product) {
            array_push($this->articles, $product->details[0]->column_value);
        }
        $this->item_codes = array_unique(array_column($this->products, 'item_code'));
        $this->locations = array_unique(array_column($this->products, 'location'));
        $this->categories = array_unique(array_column($this->products, 'category'));
        $this->warehouses = array_unique(array_column($this->products, 'warehouse'));

        $this->statusCode = $result->status_code;
        if ($result->status_code !== 200) {
            $this->errorMessage = json_encode((array) $result->errors);
        }
        $this->emit('$refresh');
    }
    public $outMethods = [];
    public $outDates = [];
    public $details = [];
    public $productsLog = [];
    public function fetchStockOutProduct()
    {
        $request = new request();
        // $request->replace([]);

        $request->merge([
            'article' => $this->Article ?? null,
            'location' => $this->location ?? null,
            'item_code' => $this->item_code ?? null,
            'category' => $this->category ?? null,
            'warehouse' => $this->warehouse ?? null,
        ]);

        $products = new ProductController;
        $response = $products->indexOut($request);
        $result = $response->getData();
        $this->productsLog = $result->data;

        $this->articlesOut = [];
        $this->item_codes_out = [];
        $this->locationsOut = [];
        $out_dates = [];
        $outMethods = [];
        $challan_series = [];
        $challan_series_num = [];

        foreach ($this->productsLog as $product) {
            $details = $product->product->details;
            foreach ($details as $detail) {
                if ($detail->column_name == 'Article') {
                    $this->articlesOut[] = $detail->column_value;
                }
            }
            $challan = $product->challan;
            $this->challan_series = $challan->challan_series;
            $this->challan_series_num = $challan->series_num;
            // dd($product->out_method);
            $this->item_codes_out[] = $product->product->item_code;
            $this->locationsOut[] = $product->product->location;
            $out_dates[] = $product->out_at;
            $outMethods[] = $product->out_method;
        }

        $this->articlesOut = array_unique($this->articles);
        // dd($this->articlesOut);
        $this->item_codes_out = array_unique($this->item_codes_out);
        $this->locationsOutOut = array_unique($this->locationsOut);
        $this->out_dates = array_unique($out_dates);
        $this->outMethods = array_unique($outMethods);
        // $this->challan_series[] = $challan->challan_series;
        // $this->challan_series_num[] = $challan->series_num;

        $this->statusCode = $result->status_code;
        if ($result->status_code !== 200) {
            $this->errorMessage = json_encode((array) $result->errors);
        }
        $this->emit('$refresh');
    }

    public function deleteProduct($id)
    {
        $controller = new product;
        $controller->destroy($id);
        $this->fetchProduct();
    }

    // BULK DELETE

    public function deleteSelected($selectedIds)
    {
        $this->reset(['successMessage', 'errorMessage']);
        // Proceed with your deletion logic
        $products = new ProductController;
        $response = $products->bulkDestroy($selectedIds);
        $result = $response->getData();

        // Handle the response and update component data accordingly
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->resetFiltersAfterDeletion();
            // $this->initSelectAllCheckbox();
            // $this->products = array_diff($this->products, $selectedIds);

        } else {
            $this->errorMessage = json_encode((array) $result->errors);
        }
    }
    protected function resetFiltersAfterDeletion()
    {
        // Assuming 'Article' is a filter you want to reset. Replace or add any filters you need to reset.
        $this->updateVariable('Article', null);
        $this->updateVariable('location', null);
        $this->updateVariable('item_code', null);
        $this->updateVariable('category', null);
        $this->updateVariable('warehouse', null);
        $this->updateVariable('outMethod', null);
        $this->updateVariable('from', null);
        $this->updateVariable('to', null);

    }



    public $createChallanRequest = array(
        'unit' => null,
        'rate' => null,
        'qty' => null,
        'total_amount' => null,
        'item_code' => null,
        'columns' => [
            [
                'column_name' => '',
                'column_value' => '',
            ]
        ],
    );
    // For Single Product Store
    public $message;
    public function storeProduct(Request $request)
    {
        // $validatorRules = [
        //     'item_code' => 'required',
        // ];

        // // Validate the "Article" field if it exists in the request
        // if ($request->has('createChallanRequest.columns')) {
        //     foreach ($request->input('createChallanRequest.columns') as $column) {
        //         if (isset($column['column_name']) && $column['column_name'] === 'Article') {
        //             $validatorRules['createChallanRequest.columns.*.column_value'] = 'required';
        //             break;
        //         }
        //     }
        // }
        $this->reset(['successMessage']);
        // $request->validate($validatorRules);
        $request->merge($this->createChallanRequest);
        $product = new ProductController;
        $response = $product->store($request);
        $result = $response->getData();

        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'errorMessage', 'createChallanRequest',]);
        } else {
            $this->errorMessage = json_encode((array) $result->errors);
        }
    }

    // Edit Product
    public $editChallanRequest = array(
        'unit' => null,
        'rate' => null,
        'qty' => null,
        'total_amount' => null,
        'item_code' => null,
        'columns' => [
            [
                'column_name' => '',
                'column_value' => '',
            ]
        ],
    );
    public function editProduct(Request $request)
    {
        $this->reset(['successMessage']);
        $request->merge($this->editChallanRequest);
        // dd($request);
        $product = new ProductController;
        $response = $product->update($request, $request->id);
        $result = $response->getData();
        // dd($result);
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['statusCode', 'errorMessage', 'editChallanRequest',]);
            $this->fetchProduct();
        } else {
            $this->errorMessage = json_encode((array) $result->errors);
        }
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

    public $product_name,   $product_article, $unit, $qty, $rate, $product_date, $product_time, $unitButton = false;

    // public function createNewUnit()
    // {
    //     // dd('here');
    //     $request = new Request();
    //     $request->merge([
    //         'unit' => $this->unitName,
    //         'status' => 'active',
    //     ]);
    //     $product = new UnitsController;
    //     $response = $product->store($request);
    //     $result = $response->getData();
    //     dd($result);
    //     $this->statusCode = $result->status_code;
    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         $this->reset(['unitName']);
    //     } else {
    //         $this->errorMessage = json_encode((array) $result->errors);
    //     }
    // }
    public function createNewUnit()
    {
        $request = new Request();
        $request->merge([
            'short_name' => $this->unitName,
            'status' => 'active',
        ]);

        $product = new UnitsController;
        $response = $product->store($request);
        $result = $response->getData();

        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->reset(['unitName']);
            $this->emit('unitCreated', $result->unit);
        } else {
            $errors = $result->error;
            if (isset($errors->unit)) {
                foreach ($errors->unit as $error) {
                    $this->errorMessage('unitName', $error);
                }
            } else {
                $this->errorMessage('error', 'An error occurred while creating the unit.');
            }
        }
    }

    public function updatedUnitName($value)
    {
        if (!empty($value)) {
            $this->activeUnitButton();
        }
    }

    public function activeUnitButton()
    {
        $this->unitButton = true;
    }

    public function render()
    {
        $request = request();
        // dd('here');

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
        // dd($this->ColumnDisplayNames);

        $request = request();
        $columnFilterDataset = [
            'feature_id' => 12,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        ];
        $request->merge($columnFilterDataset);
        $PanelColumnsController = new PanelColumnsController;
        $columnsResponse = $PanelColumnsController->index($request);
        $columnsData = json_decode($columnsResponse->content(), true);
        $ColumnDisplayNames = array_map(function ($column) {
            return $column['panel_column_display_name'];
        }, $columnsData['data']);
        $this->InvoiceColumnDisplayNames = $ColumnDisplayNames;
        // dd($this->InvoiceColumnDisplayNames);
        // dd($this->Invoicel1ColumnDisplayNames);
        array_push($this->InvoiceColumnDisplayNames, 'item code','category', 'warehouse', 'location', 'unit', 'qty',  'rate',  'Date','Time');


        $this->fetchProduct();
        // dump($this->updateFile);

        // $request->replace([]);
        // $request = new request();
        $OutData = new ProductController;
        // $request->merge([
        //     'article' => $this->articlesOut ?? null,
        //     'location' => $this->location ?? null,
        //     'item_code' => $this->item_code ?? null,

        // ]);
        $response = $OutData->indexOut($request);
        $result = $response->getData();
        $this->OutData = $result->data;
        // dd($this->OutData);

        $this->InvoiceColumnDisplayNamesTab4 = ['item code','category', 'warehouse', 'location', 'unit', 'qty', 'rate', 'Out Qty', 'Sent To', 'Order No', 'Out Method', 'Date','Time'];
        // array_push($this->InvoiceColumnDisplayNames, 'item code', 'location', 'unit', 'qty',  'rate', 'Out Qty', 'Out Method', 'Out At');
        $this->fetchStockOutProduct();

        $unitData = new UnitsController();
        $response = $unitData->index();
        // $this->unit = $response->getdata();
        $this->unit = json_decode($response->getContent(), true);

        return view('livewire.dashboard.stock.stock', [
            'InvoiceColumnDisplayNamesTab4' => $this->InvoiceColumnDisplayNamesTab4,
        ]);
    }
}
