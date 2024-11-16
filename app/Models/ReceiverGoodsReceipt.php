<?php

namespace App\Models;

use App\Models\User;
use App\Models\ReceiverGoodsReceiptsDetails;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiverGoodsReceipt extends Model
{
    use HasFactory;
    protected $table = 'receiver_goods_receipts';

    protected $fillable = [
        'user_id', 
        'receiver_name',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function details()
    {
        return $this->hasMany(ReceiverGoodsReceiptsDetails::class, 'receiver_id','id')->orderByDesc('id');
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
}
