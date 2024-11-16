<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BulkImportLog;
use Illuminate\Support\Facades\Storage;

class BulkImportLogs extends Component
{
    use WithPagination;

    public $search = '';
    public $type;
    protected $queryString = ['search'];

    public function mount($type){
        $this->type = $type;
        // dd($this->type);
    }
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function downloadFile($logId)
    {
        $log = BulkImportLog::where('user_id', auth()->id())->findOrFail($logId);

        if (Storage::disk('s3')->exists($log->file_url)) {
            return Storage::disk('s3')->download($log->file_url, $log->file_name);
        } else {
            $this->addError('download', 'File not found.');
        }
    }

    public function render()
    {
        $logs = BulkImportLog::where('user_id', auth()->id())
            ->where('type', 'challan')
            ->where(function ($query) {
                $query->where('file_name', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.components.bulk-import-logs', [
            'logs' => $logs,
        ]);
    }
}
