<?php

namespace App\Models;

use App\Models\User;
use App\Models\TagsTable;
use App\Models\EstimateSfps;
use App\Models\EstimateStatus;
use App\Models\BuyerDetails;
use App\Models\EstimateOrderColumn;
use Illuminate\Support\Facades\Auth;
use App\Models\EstimateOrderDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estimates extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'estimate_series', 'series_num', 'seller_id', 'seller', 'buyer_id', 'buyer','buyer_detail_id','pdf_url', 'status_comment','team_id','deleted_at', 'comment','calculate_tax', 'total', 'round_off','purchase_order_series', 'estimate_date','eway_bill_no'
    ];

    public function sellerUser()
    {
        return $this->belongsTo(User::class, 'seller_id','id');
    }

    public function buyerUser()
    {
        return $this->belongsTo(User::class, 'buyer_id','id');
    }

    public function buyerDetails()
    {
        return $this->belongsTo(BuyerDetails::class, 'buyer_detail_id','id');
    }

    public function orderDetails()
    {
        return $this->hasMany(EstimateOrderDetail::class,'estimate_id','id')->orderByDesc('id');
    }
    public function statuses()
    {
        return $this->hasMany(EstimateStatus::class,'estimate_id','id')->orderByDesc('id');
    }

    public function sfp()
    {
        return $this->hasMany(EstimateSfps::class, 'estimate_id', 'id')->orderByDesc('id');
    }

    public function sfpBy()
    {
        return $this->hasMany(EstimateSfps::class, 'estimate_id', 'id')->orderByDesc('id');
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'estimate_tags', 'estimate_id', 'tags_table_id')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
