<?php

namespace App\Jobs;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendOtpSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @param  array  $data
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
  
public function handle()
{
    try {
        // Send the OTP via SMS using the Textlocal API
        $response = Http::post('https://api.textlocal.in/send/', $this->data);

        // Check if SMS was sent successfully
        if ($response->ok()) {
            // Process the successful response
            // ...
        } else {
            // Log the error response
            Log::channel('otplog')->error($response->body());

            // You can also handle the error response in other ways, such as sending a notification or taking appropriate action

            // Throw an exception to mark the job as failed (optional)
            throw new \Exception('Failed to send OTP via SMS');
        }
    } catch (\Throwable $exception) {
        // Log the exception
        Log::channel('otplog')->error($exception->getMessage());

        // You can also handle the exception in other ways, such as sending a notification or taking appropriate action

        // Throw an exception to mark the job as failed (optional)
        throw new \Exception('Failed to send OTP via SMS');
    }
}

}

