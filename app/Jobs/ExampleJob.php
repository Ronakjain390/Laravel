<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;
 
    /**
     * Create a new job instance.
     */
    public function __construct($message)
    {
        $this->message = $message;

        //Log::info('ExampleJob Message const: ' . $this->message);
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        //dd('ExampleJob Message: ' . $this->message);
        Log::info('ExampleJob Message by vinod: ' . $this->message);
    }
}
