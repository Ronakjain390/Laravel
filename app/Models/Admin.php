<?php

namespace App\Models;
use App\Models\Order;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class Admin extends Model implements Authenticatable
{
    use AuthenticatableTrait, HasApiTokens, HasFactory, Notifiable;
    protected $table = 'admins';

    protected $fillable = ['name', 'email', 'password', 'remember_token', 'status'];

    protected $dates = ['created_at', 'updated_at'];

    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    public function plans()
{
    // Fetch all users related to the admin
    $users = $this->users;

    // Initialize an empty array to store the plans
    $plans = [];

    // Iterate through each user and fetch their plans
    foreach ($users as $user) {
        $userPlans = $user->orders()
            ->where('status', 'active')
            ->where('expiry_date', '>', now())
            ->groupBy(
                'user_id',
                'section_id',
                'panel_id',
                'added_by',
                'status'
            )
            ->select(
                'user_id',
                'section_id',
                'panel_id',
                'added_by',
                'status'
            );

        // If there are no active plans for the user, fetch expired plans
        if (!$userPlans->exists()) {
            $userPlans = $user->orders()
                ->where('status', 'expired')
                ->groupBy(
                    'user_id',
                    'section_id',
                    'panel_id',
                    'added_by',
                    'status'
                )
                ->select(
                    'user_id',
                    'section_id',
                    'panel_id',
                    'added_by',
                    'status'
                );
        }

        // Merge the user plans into the overall plans array
        $plans = array_merge($plans, $userPlans->get()->toArray());
    }

    return $plans;
}

}
