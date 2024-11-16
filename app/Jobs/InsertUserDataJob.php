<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\V1\Buyers\BuyersController;
use App\Http\Controllers\V1\Profile\ProfileController;
use App\Http\Controllers\V1\User\Auth\UserAuthController;
use App\Http\Controllers\V1\Receivers\ReceiversController;
use App\Http\Controllers\V1\PanelSeriesNumber\PanelSeriesNumberController;

class InsertUserDataJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $timeout = 7200;
    public $users;
    public function __construct($collection)
    {
        $this->users = $collection;
        foreach ($this->users as $user) {
            DB::transaction(function () use ($user) {
                try {
                    // dd('try');
                    $UserAuthController = new ProfileController();
                    $UserAuthController->importUser($user);

                    return true;

                } catch (\Throwable $exception) {
                    // dd('catch');
                    // Log the exception
                    Log::channel('error')->error($exception->getMessage());
                    throw new \Exception('Failed to send import users');
                }
            });
        }
    }

    public function handle()
    {
        // foreach ($this->users as $user) {
        //     DB::transaction(function () use ($user) {
        //         try {
        //             $UserAuthController = new ProfileController();
        //             $UserAuthController->importUser($user);

        //             return true;

        //         } catch (\Throwable $exception) {
        //             // Log the exception
        //             Log::channel('error')->error($exception->getMessage());
        //             throw new \Exception('Failed to send import users');
        //         }
        //     });
        // }
    }
}
