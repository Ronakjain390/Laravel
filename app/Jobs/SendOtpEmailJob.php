<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class SendOtpEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $otp;

    /**
     * Create a new job instance.
     *
     * @param  string  $email
     * @param  int  $otp
     * @return void
     */
    public function __construct(string $email, int $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Send the OTP to the user's email
            Mail::to($this->email)->send(new OtpMail($this->otp));
        } catch (\Throwable $exception) {
            // Log the exception
            Log::channel('emaillog')->error($exception->getMessage());
    
            // You can also handle the exception in other ways, such as sending a notification or taking appropriate action
    
            // Throw an exception to mark the job as failed (optional)
            throw new \Exception('Failed to send OTP via email');
        }
    }
}

