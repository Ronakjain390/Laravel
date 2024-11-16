<?php

namespace App\Models;

use App\Models\User;
use App\Models\TagsTable;
use App\Models\Receivers;
use App\Models\ChallanSfp;
use App\Models\ProductLog;
use App\Models\UserDetails;
use App\Models\ChallanStatus;
use App\Models\ReceiverDetails;
use App\Models\ChallanOrderColumn;
use App\Models\ChallanDelivery;
use App\Models\ChallanOrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Challan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'challan_series', 'series_num', 'sender_id', 'sender', 'receiver_id', 'receiver_detail_id', 'user_detail_id','status_comment',  'receiver', 'pdf_url', 'comment', 'total', 'round_off','total_qty', 'pdf_url', 'challan_date', 'created_at', 'updated_at', 'deleted_at', 'additional_phone_number', 'signature', 'team_id'
    ];

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function userDetails()
    {
        return $this->belongsTo(UserDetails::class, 'user_detail_id', 'id');
    }

    // FOR OLD DB
    // public function receiverUser()
    // {
    //     return $this->belongsTo(Receiver::class, 'receiver_id', 'id');
    // }

    // public function receiverUser()
    // {
    //     return $this->belongsTo(Receiver::class, 'receiver_id', 'id');
    // }

    // // Indirect relationship: YourModel has one User through Receiver
    // public function associatedUser()
    // {
    //     return $this->hasOne(User::class, 'id', 'receiver_user_id');
    // }
    // FOR NEW DB
    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
    public function receiverDetails()
    {
        return $this->belongsTo(ReceiverDetails::class, 'receiver_detail_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasMany(ChallanOrderDetail::class, 'challan_id', 'id');
    }

        public function productLogs()
    {
        return $this->hasMany(ProductLog::class);
    }
    // public function column()
    // {
    //     return $this->hasMany(ChallanOrderColumn::class, 'challan_order_detail_id', 'id');
    // }
    // public function column()
    // {
    //     // Indirect relationship through ChallanOrderDetail
    //     return $this->hasManyThrough(
    //         ChallanOrderColumn::class,
    //         ChallanOrderDetail::class,
    //         'challan_id', // Foreign key on ChallanOrderDetail table
    //         'challan_order_detail_id', // Foreign key on ChallanOrderColumn table
    //         'id', // Local key on Challan table
    //         'id' // Local key on ChallanOrderDetail table
    //     );
    // }
    // public function column()
    // {
    //     // Direct relationship to ChallanOrderDetail
    //     $details = $this->hasMany(ChallanOrderDetail::class, 'challan_id', 'id')->get();

    //     // For each detail, load the columns
    //     $details->each(function ($detail) {
    //         $detail->columns = $detail->hasMany(ChallanOrderColumn::class, 'challan_order_detail_id', 'id')->get();
    //     });

    //     return $details;
    // }
    public function statuses()
    {
        return $this->hasMany(ChallanStatus::class, 'challan_id', 'id')->orderByDesc('id');
    }

    public function latestStatus()
    {
        return $this->hasOne(ChallanStatus::class, 'challan_id', 'id')->latest();
    }

    public function sfp()
    {
        return $this->hasMany(ChallanSfp::class)->where('sfp_by_id', Auth::user()->id)->orWhere('sfp_to_id', Auth::user()->id)->orderByDesc('id');
    }

    public function sfpBy()
    {
        return $this->hasMany(ChallanSfp::class, 'challan_id', 'id')->orderByDesc('id');
    }

    public function returnChallan()
    {
        return $this->hasMany(ReturnChallan::class, 'challan_id', 'id')->orderBy('id');
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'challan_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }

    public function deliveryStatus()
    {
        return $this->belongsToMany(ChallanDelivery::class, 'challan_delivery_statuses');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}
