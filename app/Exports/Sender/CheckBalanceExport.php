<?php

namespace App\Exports\Sender;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Auth;
use App\Models\Challan;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class CheckBalanceExport implements FromCollection, WithHeadings, WithMapping
{
    use Exportable;
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request; 
    }
  
    public function map($organizedData): array
    {
    //  dd($organizedData);  
        return [
            'Challan Id' => $organizedData['Challan Id'],
            'Receiver' => $organizedData['Receiver'],
            'Sent Date' => date('d-m-Y', strtotime($organizedData['Sent Date'])),
            'Challan No.' => $organizedData['Challan No.'],
            'Article' => $organizedData['Article'],
            'QTY Sent' => $organizedData['QTY Sent'],
            'Challan Status' => $organizedData['Challan Status'],
            'Recvd Challan No.' => $organizedData['Recvd Challan No.'] ?? null,
            'Recvd Date' => isset($organizedData['Recvd Date']) ? date('d-m-Y', strtotime($organizedData['Recvd Date'])) : null,
            'RecvArticle' => $organizedData['RecvArticle'] ?? null,
            'Recvd QTY' => $organizedData['Recvd QTY'] ?? null,
            'Return Challan Status' => $organizedData['Return Challan Status'] ?? null,
            'Balance' => $organizedData['Balance'],
            'Margin QTY' => $organizedData['Margin QTY'] ?? null,
        ];
    }
   
  
    public function headings(): array
    {
        return [
            'Challan Id',
            'Receiver',
            'Sent Date',
            'Challan No.',
            'Article',
            'Qty Sent',
            'Sent Status',
            'Received Challan No.',
            'Received Date',
            'Received Article',
            'Received Qty',
            'Received Status',
            'Balance',
            'Margin QTY',
        ];
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $request = $this->request;
        // dd($request);
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        
        $query = Challan::query()->where('sender_id', $userId)->orderByDesc('id');
        // dd($query);
        // Filter by specific receiver_id if provided in the request
          // Filter by specific receiver_id if provided in the request
        // Filter by specific receiver_id if provided in the request
        if ($request->has('receiver_id') && $request->receiver_id !== null) {
            $query->where('receiver_id', $request->receiver_id);
        }

        // Filter by date
        if ($request->from_date !== null && $request->to_date !== null) {
            $from = $request->from_date;
            $to = $request->to_date;

            $query->whereBetween('challan_date', [$from, $to]);
        }

        // Filter by challan_series
        if ($request->challan_series !== null) {
            $searchTerm = $request->challan_series;
            $lastDashPos = strrpos($searchTerm, '-');

            if ($lastDashPos !== false) {
                $series = substr($searchTerm, 0, $lastDashPos);
                $num = substr($searchTerm, $lastDashPos + 1);

                $query->where('challan_series', $series)
                    ->where('series_num', $num);
            }
        }


        
        // Handle export options
        if ($request->all_data == 'all_data') {
            $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])->get();
                // dd($challans);
        } elseif ($request->filtered_data == 'filtered_data') {
            $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
                ->select('challans.*')->get();
                // dd($challans);
        } elseif ($request->current_page == 'current_page') {
            $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
                ->select('challans.*')->forPage($this->page, 50)->get();
                // dd($challans);
        } else {
            $challans = $query->with(['receiverUser', 'statuses', 'receiverDetails', 'orderDetails', 'orderDetails.columns', 'sfp'])
                ->select('challans.*')->paginate(25);
                // dd($challans);
        }

        // Log the fetched data
        Log::info('Fetched Challans: ' . json_encode($challans));

        $organizedData = [];
        $totalBalance = 0;

        foreach ($challans as $challan) {
            $challanStatus = $challan->statuses->first() ? $challan->statuses->first()->status : '';
            if ($challanStatus == 'draft' || $challanStatus == 'reject') {
                continue;
            }
            $receiver = $challan->receiver;
            $sentDate = date('Y-m-d', strtotime($challan->created_at));
            $challanNo = $challan->challan_series . '-' . $challan->series_num;

            foreach ($challan->orderDetails as $orderDetail) {
                $articleSent = $orderDetail->columns->first() ? $orderDetail->columns->first()->column_value : '';
                $qtySent = $orderDetail->qty;
                $balance = $orderDetail->remaining_qty;
                $totalBalance += $balance;

                $organizedDataItem = [
                    'Challan Id' => $orderDetail->id,
                    'Receiver' => $receiver,
                    'Sent Date' => $sentDate,
                    'Challan No.' => $challanNo,
                    'Article' => $articleSent,
                    'QTY Sent' => $qtySent,
                    'Challan Status' => $challanStatus,
                    'Balance' => $balance, 
                ];

                $returnChallanCount = count($challan->returnChallan);
                $currentReturnChallanIndex = 0;
                $hasReturnChallan = false;

                if ($returnChallanCount > 0) {
                    foreach ($challan->returnChallan as $returnChallan) {
                        $currentReturnChallanIndex++;
                        $recvdChallanNo = $returnChallan->challan_series . '-' . $returnChallan->series_num;
                        $returnChallanStatus = $returnChallan->statuses->first() ? $returnChallan->statuses->first()->status : '';

                        foreach ($returnChallan->orderDetails as $returnOrderDetail) {
                            if ($returnOrderDetail && $returnOrderDetail->columns->first() && $articleSent == $returnOrderDetail->columns->first()->column_value) {
                                $articleRecvd = $returnOrderDetail->columns->first()->column_value;
                                $recvdDate = date('Y-m-d', strtotime($returnOrderDetail->created_at));
                                $recvdQty = $returnOrderDetail->qty;

                                if ($request->has('recvdfrom') && $request->has('recvdto')) {
                                    $recvdfrom = $request->recvdfrom;
                                    $recvdto = $request->recvdto;
                                    if ($recvdDate < $recvdfrom || $recvdDate > $recvdto) {
                                        continue;
                                    }
                                }

                                $returnDataItem = $organizedDataItem;
                                $returnDataItem['Recvd Challan No.'] = $recvdChallanNo;
                                $returnDataItem['RecvArticle'] = $articleRecvd;
                                $returnDataItem['Recvd Date'] = $recvdDate;
                                $returnDataItem['Recvd QTY'] = $recvdQty;
                                $returnDataItem['Return Challan Status'] = $returnChallanStatus;
                                $returnDataItem['Margin QTY'] = $currentReturnChallanIndex == $returnChallanCount ? $orderDetail->margin : null;
                                $returnDataItem['Balance'] = $currentReturnChallanIndex == $returnChallanCount ? $balance : null;
                                $returnDataItem['Action'] = '';
                                $organizedData[] = $returnDataItem;
                                $hasReturnChallan = true;
                            }
                        }
                    }
                }

                if (!$hasReturnChallan) {
                    $organizedData[] = $organizedDataItem;
                }
            }
        }
        // dd($organizedData);
        // Log the organized data
        Log::info('Organized Data: ' . json_encode($organizedData));

        return collect($organizedData);
    }
}
