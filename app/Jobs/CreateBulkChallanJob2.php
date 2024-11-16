<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\PanelSeriesNumber;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\ChallanOrderDetail;
use App\Models\ChallanOrderColumn;
use App\Models\Product;
use App\Models\UserDetails;
use App\Services\PDFGeneratorService;
use App\Models\PlanFeatureUsageRecord;

class CreateBulkChallanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $rows;

    public function __construct($rows)
    {
        $this->rows = $rows;
    }

    public function handle()
    {
        DB::beginTransaction();

        try {
            // Extract the first row for receiver details
            $firstRow = $this->rows[0];

            // Fetch receiver details
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
                throw new \Exception('Please assign the receiver first.');
            }

            // Series number and latest series number fetching logic
            $series_number = null;
            if ($receiver->assigned_to_id == null) {
                $series_number = PanelSeriesNumber::where('user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                    ->where('default', "1")
                    ->where('panel_id', '1')
                    ->first();
            }
            
            $latestSeriesNum = Challan::where('challan_series', $series_number->series_number)
                ->where('sender_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->max(DB::raw('CAST(series_num AS UNSIGNED)'));
            $seriesNum = $latestSeriesNum ? $latestSeriesNum + 1 : 1;

            $userDetail = UserDetails::where('user_id', $receiver->receiver_user_id)
                ->where('location_name', $firstRow['address'])
                ->first();

            $challanData = [
                'challan_series' => $receiver->assigned_to_id ? $receiver->series_number : $series_number->series_number,
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

            foreach ($this->rows as $rowData) {
                $product = Product::where('item_code', $rowData['item_code'])->with('details')->first();

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

                $productUpdate = Product::where('item_code', $rowData['item_code'])->first();
                if ($productUpdate) {
                    $newQty = max(0, $productUpdate->qty - $rowData['order']);
                    $productUpdate->update(['qty' => $newQty]);
                }

                foreach ($rowData as $columnName => $columnValue) {
                    if (
                        $columnName !== 'challan_series' && $columnName !== 'challan_date'
                        && $columnName !== 'receiver_special_id' && $columnName !== 'comment'
                        && $columnName !== 'different challans' && $columnName !== 'unit'
                        && $columnName !== 'rate' && $columnName !== 'qty'
                        && $columnName !== 'total_amount' && $columnName !== 'address'
                    ) {
                        $orderColumn = new ChallanOrderColumn([
                            'challan_order_detail_id' => $orderDetail->id,
                            'column_name' => $columnName,
                            'column_value' => $columnValue,
                        ]);
                        $orderColumn->save();
                    }
                }
            }

            $PlanFeatureUsageRecord = new PlanFeatureUsageRecord();
            $PlanFeatureUsageRecordResponse = $PlanFeatureUsageRecord->updateUsageCount(1, 1);

            if (!$PlanFeatureUsageRecordResponse) {
                throw new \Exception('Usage count is over, please recharge.');
            }

            $pdfGenerator = new PDFGeneratorService();
            $response = $pdfGenerator->generateChallanPDF($challan);

            $responseArray = $response->original;

            if (is_array($responseArray) && isset($responseArray['status_code']) && $responseArray['status_code'] === 200) {
                $challan->pdf_url = $responseArray['pdf_url'];
                $challan->save();
            } else {
                throw new \Exception('Error generating PDF: ' . json_encode($responseArray));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}

