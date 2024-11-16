<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Order;
use App\Models\Units;
use App\Models\TeamUser;
use App\Models\UserDetails;
use App\Models\Payment;
use App\Models\PhoneVerifications;
use App\Models\EmailVerifications;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users';

    // Assuming the 'id' column is the primary key and is auto-incrementing
    protected $primaryKey = 'id';

    // Assuming the 'id' column is an auto-incrementing integer
    protected $keyType = 'int';

    public $timestamps = true;

    // Fillable columns
    protected $fillable = [
        'special_id',
        'name',
        'email',
        'password',
        'device_token',
        'address',
        'pincode',
        'company_name',
        'phone',
        'gst_number',
        'pancard',
        'state',
        'city',
        'bank_name',
        'branch_name',
        'bank_account_no',
        'ifsc_code',
        'tan',
        'remember_token',
        'status',
        'sender',
        'receiver',
        'seller',
        'buyer',
        'receipt_note',
        'email_verified_at',
        'first_time',
        'added_by',
        'test_users',
        'permissions',
        'payment_status',
        'barcode',
        'tags',
        'self_delivery',

    ];

    // Hidden columns (not shown when the model is converted to an array or JSON)
    protected $hidden = [
        'password',
        // 'remember_token',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];



    public function details()
    {
        return $this->hasMany(UserDetails::class, 'user_id', 'id')->orderByDesc('id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
    public function teams()
    {
        return $this->belongsToMany('App\Team');
    }

    // Assuming the 'team_users' table has a foreign key 'user_id' referencing the 'id' column of the 'users' table
    public function teamUsers()
    {
        return $this->hasMany(TeamUser::class, 'team_user_id', 'id');
    }

    // Assuming the 'orders' table has a foreign key 'user_id' referencing the 'id' column of the 'users' table
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'id');
    }

    // Assuming the 'orders' table has a foreign key 'user_id' referencing the 'id' column of the 'users' table
    public function payments()
    {
        return $this->hasMany(Payment::class, 'user_id', 'id');
    }

    // Assuming the 'plans' table has a foreign key 'enterprise_user_id' referencing the 'id' column of the 'users' table
    public function enterprisePlans()
    {
        return $this->hasMany(Plan::class, 'enterprise_user_id', 'id');
    }

    public function seriesNumber()
    {
        $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', 'id')
            ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id);
        if ($series == null) {
            $series = $this->hasOne(PanelSeriesNumber::class, 'assigned_to_id', null)
                ->where('panel_series_numbers.user_id', Auth::getDefaultDriver() == 'team-user' ? Auth::guard(Auth::getDefaultDriver())->user()->team_owner_user_id : Auth::guard(Auth::getDefaultDriver())->user()->id)
                ->where('default', "1");
        }
        return $series;
    }

    public function emailVerification()
    {
        return $this->hasOne(EmailVerifications::class, 'user_id', 'id');
    }

    public function phoneVerification()
    {
        return $this->hasOne(PhoneVerifications::class, 'user_id', 'id');
    }
    // public function plans()
    // {
    //     $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
    //         ->where('status', 'active')
    //         ->where('expiry_date', '>', now())
    //         ->groupBy(
    //             'user_id',
    //             'section_id',
    //             'panel_id',
    //             'added_by',
    //             'status'
    //         )
    //         ->select(
    //             'user_id',
    //             'section_id',
    //             'panel_id',
    //             'added_by',
    //             'status'
    //         );

    //     // If there is no active plan, fetch expired orders with details
    //     if (!$activePlan->exists()) {
    //         $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
    //             ->where('status', 'expired')
    //             ->groupBy(
    //                 'user_id',
    //                 'section_id',
    //                 'panel_id',
    //                 'added_by',
    //                 'status'
    //             )
    //             ->select(
    //                 'user_id',
    //                 'section_id',
    //                 'panel_id',
    //                 'added_by',
    //                 'status'
    //             );

    //         return $activePlan;
    //     }

    //     return $activePlan;
    // }


    // public function plansActive()
    public function plans()
    {
        $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
            // ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->groupBy(
                'id',
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            )
            ->select(
                'id',
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            );
            // dd($activePlan);
        // If there is no active plan, fetch expired orders with details
        if (!$activePlan->exists()) {
            $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
                ->where('status', 'expired')
                ->groupBy(
                    'id',
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                )
                ->select(
                    'id',
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                );
                return $activePlan;
            }
            // dd($activePlan);

        return $activePlan;
    }

    // Expired Plans
    public function plansExpired()
    {
        $expiredPlan = $this->hasMany(Order::class, 'user_id', 'id')
            // ->where('status', 'active')
            ->where('expiry_date', '<', now())
            ->groupBy(
                'id',
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            )
            ->select(
                'id',
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            );
            // dd($expiredPlan);
        // If there is no active plan, fetch expired orders with details
        if (!$expiredPlan->exists()) {
            $expiredPlan = $this->hasMany(Order::class, 'user_id', 'id')
                ->where('status', 'expired')
                ->groupBy(
                    'id',
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                )
                ->select(
                    'id',
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                );
                return $expiredPlan;
            }
            // dd($expiredPlan);

        return $expiredPlan;
    }


    //     Order History
    public function plansActive()
    {
        $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->groupBy(
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            )
            ->select(
                'user_id',
                'section_id',
                'panel_id',
                'plan_id',
                'added_by',
                'purchase_date',
                'expiry_date',
                'status'
            );


        // If there is no active plan, fetch expired orders with details
        if (!$activePlan->exists()) {
            $activePlan = $this->hasMany(Order::class, 'user_id', 'id')
                ->where('status', 'expired')
                ->groupBy(
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                )
                ->select(
                    'user_id',
                    'section_id',
                    'panel_id',
                    'plan_id',
                    'added_by',
                    'purchase_date',
                    'expiry_date',
                    'status'
                );


            return $activePlan;
        }


        return $activePlan;
    }

    public function walletLogs()
    {
        return $this->hasMany(WalletLog::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class, 'user_id', 'id');
    }

    public function units()
    {
        return $this->hasMany(Units::class, 'user_id');
    }
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'sender' => 'boolean',
        'permissions' => 'array',
    ];
}
