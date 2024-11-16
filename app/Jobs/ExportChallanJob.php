<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\ChallanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportChallanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $requestData;

    /**
     * Create a new job instance.
     */
    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $query = Challan::query()->orderByDesc('id');
        $userId = Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id;
        $challans = $query->where('sender_id', $userId)->with('statuses', 'receiverDetails')->select('challans.*')->paginate(100, null, null, request()->page ?? 1);
    
        $exportedData = [];
    
        foreach ($challans as $key => $challan) {
            $rowData['id'] = ++$key;
            $rowData['challan_series'] = $challan->challan_series;
            $rowData['Time'] = Carbon::parse($challan->created_at)->format('h:i A');
            $rowData['Date'] = Carbon::parse($challan->created_at)->format('j F Y');
            $rowData['sender'] = $challan->sender;
            $rowData['receiver'] = $challan->receiver;
            $rowData['qty'] = $challan->total_qty;
            $rowData['total_amount'] = $challan->total;
            $rowData['status'] = '';
    
            if ($challan->statuses->isNotEmpty()) {
                $status = $challan->statuses[0]->status;
                $user_name = $challan->statuses[0]->user_name;
    
                if ($status == 'draft') {
                    $rowData['status'] = 'Not Sent';
                } elseif ($status == 'sent') {
                    $rowData['status'] = 'Sent';
                } elseif ($status == 'self_accept') {
                    $rowData['status'] = 'Self Delivered';
                } elseif ($status == 'accept') {
                    $rowData['status'] = 'Accepted By ' . $user_name;
                } elseif ($status == 'reject') {
                    $rowData['status'] = 'Rejected By ' . $user_name;
                } elseif ($status == 'return') {
                    $rowData['status'] = 'Self Returned';
                }
            }
    
            $rowData['comment'] = $challan->comment;
    
            $exportedData[] = $rowData;
        }
    
        $filePath = 'temp/' . uniqid() . '.csv';
        Storage::disk('local')->put($filePath, $this->generateCsvFile($exportedData));
    
        $fileName = 'exported_challans.csv';
        $contentType = 'text/csv';
    
        $response = new Response();
        $response->header('Content-Type', $contentType);
        $response->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->setCharset('UTF-8');
        $response->setContent(Storage::disk('local')->get($filePath));
        Storage::disk('local')->delete($filePath);
    
        return $response;
    }

    /**
     * Generate CSV file content from an array of data.
     */
    private function generateCsvFile($data)
    {
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, array_keys($data[0])); // Write the header row
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);

        return stream_get_contents($handle);
    }

}
