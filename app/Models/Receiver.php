<?php

namespace App\Models;

use App\Models\User;
use App\Models\ReceiverDetails;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receiver extends Model
{
    use HasFactory;

    protected $table = 'receivers';

    protected $fillable = [
        'user_id',
        'receiver_user_id',
        'receiver_name',
        'status',
        'receiver_special_id',
    ];

    public function details()
    {
        return $this->hasMany(ReceiverDetails::class, 'receiver_id','id')->orderByDesc('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'receiver_user_id');
    }

    public function seriesNumber()
    {
        $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', 'id')
            ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        // dd($series);
            if ($series == null) {
            $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', null)
                ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1");
        }
        return $series;
    }

    // public function users()
    // {
    //     return $this->belongsTo(User::class, 'id');
    // }

    // public function seriesNumbers()
    // {
    //     $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', User::class, 'id')
    //     ->with('user') // Add this line
    //         ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
    //     // dd($series);
    //         if ($series == null) {
    //         $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', null)
    //         ->with('users') // Add this line
    //             ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
    //             ->where('default', "1");
    //     }
    //     return $series;
    // }

  
}
