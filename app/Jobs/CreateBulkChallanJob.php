<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Challan;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\PlanFeatureUsageRecord;
use App\Models\FeatureTopupUsageRecord;
use Illuminate\Support\Facades\Storage;
use App\Models\ChallanOrderDetail;
use App\Models\ChallanOrderColumn;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\DB;
use App\Models\ChallanStatus;
use App\Services\PDFServices\PDFWhatsAppService;
use App\Models\Product;
use App\Services\PDFServices\PDFGeneratorService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\UploadLog;
use Illuminate\Http\Request;

class CreateBulkChallanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $logId;

    public function __construct($filePath, $logId)
    {
        $this->filePath = $filePath;
        $this->logId = $logId;
        Log::info('Job constructor called', ['filePath' => $filePath, 'logId' => $logId]);

    }

    public function handle()
    {
        Log::info('Handle method called', ['logId' => $this->logId]);


        $handle = fopen($this->filePath, "r");

        if (!$handle) {
            UploadLog::find($this->logId)->update(['status' => 'Failed']);
            Log::error('Unable to open file', ['filePath' => $this->filePath]);
            return;
        }

        DB::beginTransaction();

        try {
            Log::info('File opened successfully', ['filePath' => $this->filePath]);

            $header = fgetcsv($handle, 1000, ",");
            Log::info('CSV header read', ['header' => $header]);

            $dataGroupedByChallan = [];
            $challanIds = [];
            $challanReceiverMap = [];

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                Log::info('Reading CSV row', ['data' => $data]);

                $data = array_pad($data, count($header), null);

                if (empty(trim($data[0]))) {
                    UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => "The 'different invoices' field cannot be empty."]);
                    Log::error("The 'different invoices' field cannot be empty", ['data' => $data]);
                    return;
                }

                if (!is_numeric($data[6]) || !is_numeric($data[7])) {
                    UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => "The 'rate' and 'qty' fields must be numeric."]);
                    Log::error("The 'rate' and 'qty' fields must be numeric", ['data' => $data]);
                    return;
                }

                $rowData = array_combine($header, $data);
                $differentChallan = $rowData['different challans'];
                $receiverSpecialId = $rowData['receiver_special_id'];

                if (isset($challanReceiverMap[$differentChallan]) && $challanReceiverMap[$differentChallan] !== $receiverSpecialId) {
                    UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => "Challan number '{$differentChallan}' cannot be used for different receiver_special_id '{$receiverSpecialId}'."]);
                    Log::error("Challan number '{$differentChallan}' cannot be used for different receiver_special_id '{$receiverSpecialId}'");
                    return;
                }

                $challanReceiverMap[$differentChallan] = $receiverSpecialId;

                if (!isset($dataGroupedByChallan[$differentChallan])) {
                    $dataGroupedByChallan[$differentChallan] = [];
                }

                $dataGroupedByChallan[$differentChallan][] = $rowData;
            }

            foreach ($dataGroupedByChallan as $differentChallan => $rows) {
                $firstRow = $rows[0];

                Log::info('Processing challan', ['differentChallan' => $differentChallan, 'firstRow' => $firstRow]);

                $receiver = User::where('special_id', $firstRow['receiver_special_id'])
                    ->join('receivers', 'receivers.receiver_user_id', 'users.id')
                    ->leftJoin('panel_series_numbers', function ($join) {
                        $join->on('panel_series_numbers.assigned_to_id', '=', 'receivers.id')
                            ->orWhere(function ($query) {
                                $query->on('panel_series_numbers.user_id', '=', 'receivers.id')
                                    ->where('panel_series_numbers.section_id', '=', '1');
                            });
                    })
                    ->join('receiver_detail', 'receiver_detail.receiver_id', '=', 'receivers.id')
                    ->where('receivers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                    ->select('users.*', 'receivers.*', 'panel_series_numbers.*', 'receiver_detail.id as receiver_detail_id')
                    ->first();

                if ($receiver === null) {
                    UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => 'Please assign the receiver first.']);
                    Log::error('Receiver not assigned', ['receiver_special_id' => $firstRow['receiver_special_id']]);
                    return;
                }

                if ($receiver->assigned_to_id == null) {
                    $series_number = PanelSeriesNumber::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                        ->where('default', "1")
                        ->where('panel_id', '1')
                        ->first();
                    $series_number_value = $series_number ? $series_number->series_number : null;
                }

                $latestSeriesNum = Challan::where('challan_series', $series_number->series_number)
                    ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                    ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
                $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

                $userDetail = UserDetails::where('user_id', $receiver->receiver_user_id)
                    ->where('location_name', $firstRow['address'])
                    ->first();

                $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
                $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount(1, 1);
                
                Log::info('PlanFeatureUsageRecordResponse', ['PlanFeatureUsageRecordResponse' => $PlanFeatureUsageRecordResponse]);

                $challanData = [
                    'challan_series' => $receiver->assigned_to_id ? $receiver->series_number : $series_number_value,
                    'challan_date' => (new \DateTime($firstRow['challan_date']))->format('Ymd'),
                    'receiver_id' => $receiver->receiver_user_id,
                    'receiver_detail_id' => $receiver->receiver_detail_id,
                    'user_detail_id' => $userDetail->id ?? null,
                    'receiver_special_id' => $firstRow['receiver_special_id'],
                    'receiver' => $receiver->receiver_name,
                    'comment' => $firstRow['comment'] ?? null,
                    'series_num' => $seriesNum,
                    'sender_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'sender' => Auth::user()->name,
                    'total' => 0,
                    'total_qty' => 0,
                ];

                $challan = new Challan($challanData);
                $challan->save();
                Log::info('Challan created', ['challan_id' => $challan->id]);
                $challanIds[] = $challan->id;

                $status = new ChallanStatus([
                    'challan_id' => $challan->id,
                    'user_id' => Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id,
                    'user_name' => Auth::guard('web')->check() ? Auth::guard('web')->user()->name : (Auth::guard('user')->check() ? Auth::guard('user')->user()->name : Auth::guard('team-user')->user()->user->name),
                    'status' => 'draft',
                    'comment' => 'Challan created successfully',
                ]);
                $status->save();

                $importTotalQty = 0;
                $importTotalAmount = 0;

                foreach ($rows as $rowData) {
                    Log::info('Processing row', ['rowData' => $rowData]);

                    if (array_key_exists('item_code', array_flip($header))) {
                        $product = Product::where('item_code', $rowData['item_code'])->with('details')->first();
                        Log::info('Product fetched', ['product' => $product]);

                        $orderDetail = new ChallanOrderDetail([
                            'challan_id' => $challan->id,
                            'unit' => $product->unit,
                            'rate' => $product->rate,
                            'qty' => $rowData['order'],
                            'total_amount' => $product->rate * $rowData['order'],
                        ]);
                        $orderDetail->save();

                        $importTotalQty += $rowData['order'];
                        $importTotalAmount += $orderDetail->total_amount;

                        $challan->total_qty += $orderDetail->qty;
                        $challan->total += $orderDetail->total_amount;
                        $challan->save();

                        if (isset($rowData['item_code'])) {
                            $productUpdate = Product::where('item_code', $rowData['item_code'])->first();
                            if ($productUpdate) {
                                $newQty = max(0, $productUpdate->qty - $rowData['order']);
                                $productUpdate->update(['qty' => $newQty]);
                                Log::info('Product updated', ['productUpdate' => $productUpdate]);
                            }
                        }
                    } else {
                        $orderDetail = new ChallanOrderDetail([
                            'challan_id' => $challan->id,
                            'unit' => $rowData['order'],
                            'rate' => $rowData['order'],
                            'qty' => $rowData['order'],
                            'total_amount' => floatval($rowData['order']) * floatval($rowData['order']),
                        ]);
                        $orderDetail->save();

                        $importTotalQty += floatval($rowData['order']);
                        $importTotalAmount += floatval($orderDetail->total_amount);

                        $challan->total_qty += floatval($orderDetail->qty);
                        $challan->total += floatval($orderDetail->total_amount);
                        $challan->save();

                        foreach ($rowData as $columnName => $columnValue) {
                            if (
                                $columnName !== 'challan_series' && $columnName !== 'challan_date'
                                && $columnName !== 'receiver_special_id' && $columnName !== 'comment'
                                && $columnName !== 'different challans' && $columnName !== 'different challans'
                                && $columnName !== 'unit' && $columnName !== 'rate'
                                && $columnName !== 'qty' && $columnName !== 'total_amount' 
                                && $columnName !== 'address'
                            ) {
                                $orderColumn = new ChallanOrderColumn([
                                    'challan_order_detail_id' => $orderDetail->id,
                                    'column_name' => $columnName,
                                    'column_value' => $columnValue,
                                ]);
                                $orderColumn->save();
                                Log::info('Order column saved', ['orderColumn' => $orderColumn]);
                            }
                        }
                    }
                }

                if (!$PlanFeatureUsageRecordResponse) {
                    UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => 'Usage count is over, please recharge.']);
                    Log::error('Usage count is over, please recharge');
                    return;
                }

                $challan->save();
            }

            DB::commit();
            fclose($handle);

            UploadLog::find($this->logId)->update(['status' => 'Completed']);
            Log::info('Job completed successfully', ['challanIds' => $challanIds]);

        } catch (\Exception $e) {
            Log::error('Error in CreateBulkChallanJob', [
                'filePath' => $this->filePath,
                'logId' => $this->logId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            DB::rollback();
            fclose($handle);
            UploadLog::find($this->logId)->update(['status' => 'Failed', 'message' => $e->getMessage()]);
        }
    }
}
