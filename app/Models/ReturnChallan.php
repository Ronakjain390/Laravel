<?php

namespace App\Models;

use App\Models\User;
use App\Models\TagsTable;
use App\Models\Challan;
use App\Models\Receiver;
use App\Models\ReceiverDetails;
use App\Models\ReturnChallanSfp;
use App\Models\ReturnChallanStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use App\Models\ReturnChallanOrderDetail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReturnChallan extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'challan_id', 'challan_series', 'series_num', 'sender_id', 'sender', 'receiver_id', 'team_id','receiver','pdf_url', 'comment','status_comment', 'total', 'round_off', 'total_qty','challan_date','updated_at','created_at'
    ];

    public function senderUser()
    {
        return $this->belongsTo(User::class,  'sender_id','id');
    }

    // FOR NEW DB
    public function receiverUser()
    {
        return $this->belongsTo(User::class, 'receiver_id','id');
    }
    public function receiverDetails()
    {
        return $this->belongsTo(Receiver::class, 'receiver_id', 'id');
    }
    // FOR NEW DB

    // For Old Db
    // public function receiverUser()
    // {
    //     return $this->belongsTo(Receiver::class, 'receiver_id', 'id')->with('user');
    // }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'receiver_user_id', 'id');
    // }
    // For Old DB
    public function challan()
    {
        return $this->belongsTo(Challan::class, 'challan_id', 'id');
    }
    // public function receiverDetails()
    // {
    //     return $this->belongsTo(ReceiverDetails::class, 'receiver_id', 'id');
    // }

    public function orderDetails()
    {
        return $this->hasMany(ReturnChallanOrderDetail::class,'challan_id','id')->orderBy('id');
    }

    //    public function column()
    // {
    //     return $this->hasMany(ReturnChallanOrderColumn::class,'challan_order_detail_id','id')->orderBy('id');
    // }

    public function statuses()
    {
        return $this->hasMany(ReturnChallanStatus::class,'challan_id','id')->orderByDesc('id');
    }

    public function sfp()
    {
        return $this->hasMany(ReturnChallanSfp::class,'challan_id','id')->where('sfp_by_id', Auth::user()->id)->orWhere('sfp_to_id', Auth::user()->id)->orderByDesc('id');
    }

    public function sfpBy()
    {
        return $this->hasMany(ReturnChallanSfp::class, 'challan_id', 'id')->orderByDesc('id');
    }

    public function tableTags()
    {
        return $this->belongsToMany(TagsTable::class, 'return_challan_tags')
                    ->withPivot('panel_id', 'user_id')
                    ->withTimestamps();
    }


}
