<?php

namespace App\Http\Livewire\Dashboard\Stock;

use stdClass;
use App\Models\Product;
use Livewire\Component;
use App\Models\PanelColumn;
use App\Models\ProductUploadLog;
use Illuminate\Http\Request;
use App\Models\ProductDetail;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\V1\Units\UnitsController;
use App\Http\Controllers\V1\Products\ProductController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\PanelColumns\PanelColumnsController;

class Stock extends Component
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
    public $activeTab = 'tab3';
    public $OutData;
    public $from;
    public $to;
    public $modalShow = false;
    public $mainUser;
    public $unitName;
    public $InvoiceColumnDisplayNames;
    public $moveWarehouses;
    public $moveCategories;

    protected $listeners = ['deleteSelected', 'updateWithTax', 'tabChanged'];

    public function tabChanged($tab)
    {
        $this->activeTab = $tab;
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->emit('tabChanged', $tab); // Emit an event for Alpine.js to listen to
    }

    protected $queryString = ['activeTab'];
    public function updatingQueryString($queryString, $old, $new)
    {
        unset($queryString['feature_id']);
        unset($queryString['user_id']);

        return $queryString;
    }

    // public function editStock($id)
    // {
    //     dd($id);
    //     $this->emit('editStock', $id);
    // }

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

        // if (!request()->query('activeTab')) {
        //     $this->activeTab = 'tab3';
        // }
        // $this->activeTab = request()->query('activeTab', 'tab3');
        // $this->activeTab = request()->query('tab', 'tab3');
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
        // Slice the array to get columns from index 3 to 6
        $this->InvoiceColumnDisplayNames = array_slice($this->InvoiceColumnDisplayNames, 3, 4);
        // dd($this->InvoiceColumnDisplayNames);
        // // Add from stock modal data
        // $request = request();
        // $columnFilterDataset = [
        //     'feature_id' => 1,
        //     'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        // ];
        // $request->merge($columnFilterDataset);
        // $PanelColumnsController = new PanelColumnsController;
        // $columnsResponse = $PanelColumnsController->index($request);
        // $columnsData = json_decode($columnsResponse->content(), true);
        // $ColumnDisplayNames = array_map(function ($column) {
        //     return $column['panel_column_display_name'];
        // }, $columnsData['data']);

        // $this->ColumnDisplayNames = $ColumnDisplayNames;
        // $this->fetchProduct();
        // Load the sheets data
        // $this->sheets = ProductUploadLog::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->paginate(20);
    }

    public function downloadFile($filePath)
    {
        // Retrieve the original file name from the database
        $uploadLog = ProductUploadLog::where('file_path', $filePath)->first();

        if (!$uploadLog) {
            abort(404, 'File not found.');
        }

        $originalFileName = $uploadLog->file_name;

        return Storage::disk('s3')->download($filePath, $originalFileName);
    }

    public function closeModal()
    {
        $this->openSearchModal = false;
    }

    public function moveCategoryAndWarehouse()
    {
        // Emit event with the values
        $this->emit('moveCategoryAndWarehouseTriggered', $this->moveCategories, $this->moveWarehouses);
    }

    public function moveSingleCategoryAndWarehouse()
    {
        // Emit event with the values
        $this->emit('moveCategoryAndWarehouseTriggered', $this->moveCategories, $this->moveWarehouses);
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

//    public function updatedActiveTab($value)
//     {
//         $this->reset(['successMessage', 'errorMessage', 'errorData']);
//         $this->resetFilters();
//         if ($value === 'tab1') {
//             $this->emit('reloadTab1', 'tab1');
//         }
//     }
        // public function updatedActiveTab($value)
        // {
        //     // dd($value);
        //     $tabEvents = [
        //         'tab1' => 'reloadTab1',
        //         'tab2' => 'reloadTab2',
        //         'tab3' => 'reloadTab3',
        //         'tab4' => 'reloadTab4',
        //         'tab5' => 'reloadTab5',
        //         // Add more tabs here if needed
        //     ];

        //     if (array_key_exists($value, $tabEvents)) {
        //         $this->emit($tabEvents[$value], $value); // Emit a custom event with the tab name
        //         $this->emit('redirectWithTab', $value); // Emit an event for client-side redirection
        //     }
        // }

    public $errorFileUrl;

    public function productUpload()
    {
        $request = new Request();
        $this->reset('errorFileUrl');
        $requestData = [
            'field_name' => 'value',
            'file' => $this->uploadFile,
        ];
        $request->merge($requestData);

        $productUpload = new ProductController;
        $response = $productUpload->bulkUpload($request);
        $result = $response->getData();
        $this->statusCode = $result->status_code;

        switch ($result->status_code) {
            case 200:
                $this->dispatchBrowserEvent('show-success-message', ['message' => $result->message]);
                $this->reset('uploadFile');
                break;
            case 400:
                $errorMessage = isset($result->errors) ? $result->errors : 'The file contains more than 400 rows. Please upload a file with only 400 rows.';
                $this->dispatchBrowserEvent('show-error-message', ['message' => $errorMessage]);
                break;
            case 422:
                $errors = json_decode($response->content(), true)['errors'];
                $errorFileUrl = $this->createErrorFile($errors);
                $this->errorFileUrl = $errorFileUrl;
                $this->dispatchBrowserEvent('show-error-message', ['message' => 'Validation failed. Download the error report:', 'errorFileUrl' => $errorFileUrl]);
                $this->reset('uploadFile');
                break;
            case 500:
                $this->dispatchBrowserEvent('show-error-message', ['message' => 'Internal server error occurred.']);
                break;
            default:
                $this->dispatchBrowserEvent('show-error-message', ['message' => 'An unknown error occurred.']);
                break;
        }
    }

    private function createErrorFile($errors)
    {
        $content = "Error Report\n\n";
        foreach ($errors as $error) {
            if (is_array($error)) {
                $content .= implode(", ", $error) . "\n";
            } else {
                $content .= $error . "\n";
            }
        }

        $fileName = 'error_report_' . time() . '.txt';
        Storage::put('public/error_reports/' . $fileName, $content);

        return Storage::url('error_reports/' . $fileName);
    }

    private function createErrorFileUpdate($errors)
    {
        $content = "Error Report\n\n";

        foreach ($errors as $row => $rowErrors) {
            $content .= "$row:\n";
            foreach ($rowErrors as $field => $fieldErrors) {
                if (is_array($fieldErrors)) {
                    foreach ($fieldErrors as $error) {
                        $content .= "  - $field: $error\n";
                    }
                } else {
                    $content .= "  - $field: $fieldErrors\n";
                }
            }
            $content .= "\n";
        }

        $fileName = 'error_report_' . time() . '.txt';
        Storage::put('public/error_reports/' . $fileName, $content);

        return Storage::url('error_reports/' . $fileName);
    }

    public function productUpdate()
    {
        $request = new Request();

        $requestData = [
            'field_name' => 'value',
            'file' => $this->updateFile,
            'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
        ];

        $request->merge($requestData);

        $productUpload = new ProductController;
        $response = $productUpload->bulkUpdate($request);
        $result = $response->getData();

        $this->statusCode = $result->status_code;

        $this->reset(['successMessage', 'errorMessage', 'errorFileUrl']);

        switch ($result->status_code) {
            case 200:
                $this->dispatchBrowserEvent('show-success-message', ['message' => $result->message]);
                $this->reset('updateFile');
                break;
            case 422:
                $errors = $result->errors;
                $errorFileUrl = $this->createErrorFileUpdate($errors);
                $this->errorFileUrl = $errorFileUrl;
                $this->dispatchBrowserEvent('show-error-message', [
                    'message' => 'Validation failed. Download the error report:',
                    'errorFileUrl' => $errorFileUrl
                ]);
                $this->reset('updateFile');
                break;
                case 400:
                    $this->dispatchBrowserEvent('show-error-message', ['message' => $result->errors]);
                    $this->reset('updateFile');
                    break;
            case 500:
                $this->dispatchBrowserEvent('show-error-message', ['message' => 'Internal server error occurred.']);
                break;
            default:
                $this->dispatchBrowserEvent('show-error-message', ['message' => 'An unknown error occurred.']);
                break;
        }
    }
    // public function fetchProduct()
    // {
    //     $request = request();
    //     $request->merge([
    //         'article' => $this->Article ?? null,
    //         'location' => $this->location ?? null,
    //         'item_code' => $this->item_code ?? null,
    //         'category' => $this->category ?? null,
    //         'warehouse' => $this->warehouse ?? null,
    //     ]);

    //     $products = new ProductController;
    //     $response = $products->index($request);
    //     $result = $response->getData();
    //     $this->products = $result->data;
    //     // dd($this->products);
    //     $this->articles = [];
    //     foreach ($this->products as $product) {
    //         array_push($this->articles, $product->details[0]->column_value);
    //     }
    //     $this->item_codes = array_unique(array_column($this->products, 'item_code'));
    //     $this->locations = array_unique(array_column($this->products, 'location'));
    //     $this->categories = array_unique(array_column($this->products, 'category'));
    //     $this->warehouses = array_unique(array_column($this->products, 'warehouse'));

    //     $this->statusCode = $result->status_code;
    //     if ($result->status_code !== 200) {
    //         $this->errorMessage = json_encode((array) $result->errors);
    //     }
    //     $this->emit('$refresh');
    // }
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
        // $this->fetchProduct();
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

    public $panelUserColumnDisplayNames = [];

    public $createChallanRequest = [
        'unit' => null,
        'rate' => null,
        'qty' => null,
        'total_amount' => null,
        'item_code' => null,
        'with_tax' => 'false',  // Default to true (With Tax)
        'tax' => null,
        'columns' => [
            [
                'column_name' => '',
                'column_value' => '',
            ]
        ],
    ];
    public function updateWithTax($withTax)
    {
        $this->createChallanRequest['with_tax'] = $withTax;
    }

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
    // For Single Product Store
    public $message;
    public function storeProduct(Request $request)
    {

        $this->validate([
            'createChallanRequest.item_code' => 'required',
            'createChallanRequest.qty' => 'required|numeric',
            'createChallanRequest.columns.0.column_value' => 'required',
            // Add other validation rules as needed
        ], [
            'createChallanRequest.item_code.required' => 'The item code field is required.',
            'createChallanRequest.qty.required' => 'The qty field is required.',
            'createChallanRequest.columns.0.column_value.required' => 'The Article field is required.',
            // Add custom error messages as needed
        ]);
        $this->reset(['successMessage']);
        $columns = [];
        foreach ($this->createChallanRequest['columns'] as $key => $column) {
            if (!empty($this->panelUserColumnDisplayNames[$key])) {
                $columns[] = [
                    'column_name' => $this->panelUserColumnDisplayNames[$key],
                    'column_value' => $column['column_value'] ?? null,
                ];
            }
        }

        $this->createChallanRequest['columns'] = $columns;
        // $request->validate($validatorRules);
        $request->merge($this->createChallanRequest);
        // dd($request);
        $product = new ProductController;
        $response = $product->store($request);
        $result = $response->getData();
        // dd($result);
        $this->statusCode = $result->status_code;
        if ($result->status_code === 200) {
            $this->successMessage = $result->message;
            $this->dispatchBrowserEvent('single-message', [$result->message]);
            $this->reset(['statusCode', 'errorMessage', 'createChallanRequest',]);
        } else {

            // $this->errorMessage = json_encode((array) $result->errors);
            $this->dispatchBrowserEvent('show-error-message', [$this->errorMessage]);
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
    // public function editProduct(Request $request)
    // {
    //     $this->reset(['successMessage']);
    //     $request->merge($this->editChallanRequest);
    //     // dd($request);
    //     $product = new ProductController;
    //     $response = $product->update($request, $request->id);
    //     $result = $response->getData();
    //     // dd($result);
    //     $this->statusCode = $result->status_code;
    //     if ($result->status_code === 200) {
    //         $this->successMessage = $result->message;
    //         $this->reset(['statusCode', 'errorMessage', 'editChallanRequest',]);
    //         // $this->fetchProduct();
    //     } else {
    //         $this->errorMessage = json_encode((array) $result->errors);
    //     }
    // }

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
        // dd(session()->all());
        $sheets = ProductUploadLog::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)->paginate(20);
        // dd($sheets);
        // dd('here');

        // $columnFilterDataset = [
        //     'feature_id' => 1,
        //     'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        // ];
        // $request->merge($columnFilterDataset);

        // $PanelColumnsController = new PanelColumnsController;
        // $columnsResponse = $PanelColumnsController->index($request);
        // $columnsData = json_decode($columnsResponse->content(), true);
        // $ColumnDisplayNames = array_map(function ($column) {
        //     return $column['panel_column_display_name'];
        // }, $columnsData['data']);
        // $this->ColumnDisplayNames = $ColumnDisplayNames;
        // // dd($this->ColumnDisplayNames);

        // $request = request();
        // $columnFilterDataset = [
        //     'feature_id' => 12,
        //     'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,

        // ];
        // $request->merge($columnFilterDataset);
        // $PanelColumnsController = new PanelColumnsController;
        // $columnsResponse = $PanelColumnsController->index($request);
        // $columnsData = json_decode($columnsResponse->content(), true);
        // $ColumnDisplayNames = array_map(function ($column) {
        //     return $column['panel_column_display_name'];
        // }, $columnsData['data']);
        // $this->InvoiceColumnDisplayNames = $ColumnDisplayNames;
        // // dd($this->InvoiceColumnDisplayNames);
        // // dd($this->Invoicel1ColumnDisplayNames);
        // array_push($this->InvoiceColumnDisplayNames, 'item code','category', 'warehouse', 'location', 'unit', 'qty',  'rate',  'Date','Time');


        // $OutData = new ProductController;
        // $response = $OutData->indexOut($request);
        // $result = $response->getData();
        // $this->OutData = $result->data;
        // // dd($this->OutData);

        // $this->InvoiceColumnDisplayNamesTab4 = ['item code','category', 'warehouse', 'location', 'unit', 'qty', 'rate', 'Out Qty', 'Sent To', 'Order No', 'Out Method', 'Date','Time'];
        // // array_push($this->InvoiceColumnDisplayNames, 'item code', 'location', 'unit', 'qty',  'rate', 'Out Qty', 'Out Method', 'Out At');
        // $this->fetchStockOutProduct();

        $unitData = new UnitsController();
        $response = $unitData->index('sender');
        // $this->unit = $response->getdata();
        $this->unit = json_decode($response->getContent(), true);

        return view('livewire.dashboard.stock.stock', [
            // 'InvoiceColumnDisplayNamesTab4' => $this->InvoiceColumnDisplayNamesTab4,
            'sheets' => $sheets,
        ]);
    }
}
