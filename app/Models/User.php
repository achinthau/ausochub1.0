<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    protected $connection = "mysql";
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_context',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'break_started_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public static function boot()
    {
        parent::boot();
        self::created(function ($model) {

            if ($model->user_type_id > 1) {
                $agent = new Agent;
                $agent->username = $model->user_name;
                $agent->password = $model->email;
                $agent->fname = $model->name;
                $agent->lname = $model->name;
                $agent->usertype = $model->userType->title;
                $agent->DOB = Carbon::now();
                $agent->NIC = $model->nic;
                $agent->gender = $model->gender;
                $agent->email = $model->email;
                $agent->addressNo = $model->address;
                // $agent->addressStreet=$model->name;
                // $agent->addressCity=$model->name;
                $agent->numberone = $model->phone;
                // $agent->numbertwo=$model->name;
                // $agent->bu=$model->name;
                // $agent->extension=$model->name;
                $agent->createdat = Carbon::now();
                $agent->updatedat = Carbon::now();
                $agent->updatedby = Auth::id();
                $agent->status = 1;
                $agent->save();

                $model->agent_id = $agent->id;
                $model->save();
            }
        });
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }



    public function userType()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }

    public function getOnBreakAttribute()
    {
        return $this->break_started_at != null;
    }

    public function getHasQueueAttribute()
    {
        return $this->currentQueues()->active()->count() > 0;
    }


    public function skills()
    {
        return $this->hasOne(AgentSkill::class, 'agentid', 'agent_id');
    }

    public function currentQueues()
    {
        return $this->hasMany(AgentQueueStatus::class, 'agentid', 'agent_id');
    }

    public function scopeHasExtension($query)
    {
        return $query->whereNotNull('agent_id');
    }

    public function scopeNoAssigned($query)
    {
        $query->whereNull('extension');
    }

    public function getTenantContextAttribute($value)
    {
        return $value;
    }

    public function agentLogins()
    {
        return $this->hasMany(AgentLogin::class, 'user_id');
    }


    // public function isLoggedIn()
    // {
    //     return optional($this->agentLogins()->latest('login_time')->first())->logout_time === null;
    // }

    public function isLoggedIn()
{
    $latestLogin = $this->agentLogins()->latest('login_time')->first();

    return $latestLogin && $latestLogin->logout_time === null;
}


    public function crmDepartment()
    {
        return $this->belongsTo(CrmDepartment::class, 'department_id');
    }

    public function callbacks()
{
    return $this->hasMany(CallbackCustomer::class);
}

public function company()
{
    return $this->belongsTo(Company::class);
}

}
