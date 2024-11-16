<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PanelSeriesNumber;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\SellerDetails;


class Seller extends Model
{
    use HasFactory;
    protected $table = 'sellers';

    protected $fillable = [
        'user_id',
        'seller_user_id',
        'seller_name',
        'status',
        'seller_special_id',
    ];

    public function details()
    {
        return $this->hasMany(SellerDetails::class, 'seller_id', 'id')->orderByDesc('id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'seller_user_id');
    }


    public function invoiceNumber()
    {
        $invoice = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', 'id')
            ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        if ($invoice == null) {
            $invoice = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', null)
                ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', 1);
        }
        return $invoice;
    }
}
