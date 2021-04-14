<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'mobile',
        'email',
        'password',
        'level',
        'parent_id',
        'status',
        'company_name',
        'company_logo',
    ];

    /**
     * The attributes that should be hidden for arrays.
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'statusText', 'levelText', 'companyLogoUrl'
    ];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 0:
                return 'غیرفعال';
            case 1:
                return 'فعال';
            case 2:
                return 'معلق';
        }
    }


    public function getLevelTextAttribute()
    {
        switch ($this->attributes['level']) {
            case 'SUPERUSER':
                return 'مدیرکل';
            case 'ADMIN':
                return 'مدیر سیستم';
            case 'AGENT':
                return 'نماینده';
            case 'MARKETER':
                return 'بازاریاب';
            case 'TECHNICAL':
                return 'کارشناس فنی';
        }
    }

    public function getCompanyLogoUrlAttribute()
    {
        if (!is_null($this->attributes['company_logo'])) {
            return url('storage') . '/companies/' . $this->attributes['id'] . '/' . $this->attributes['company_logo'];
        }
        return systemConfig('COMPANY_LOGO');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function belongsToUser(User $user)
    {
        if (!is_null($this->parent)) if ($user->id == $this->parent->id) return true;

        return false;
    }

    public function isSuperUser()
    {
        if ($this->attributes['level'] == 'SUPERUSER') return true;

        return false;
    }

    public function isAdmin()
    {
        if ($this->attributes['level'] == 'ADMIN') return true;

        return false;
    }

    public function isAgent()
    {
        if ($this->attributes['level'] == 'AGENT') return true;

        return false;
    }

    public function isMarketer()
    {
        if ($this->attributes['level'] == 'MARKETER') return true;

        return false;
    }

    public function isOffice()
    {
        if ($this->attributes['level'] == 'OFFICE') return true;

        return false;
    }

    public function isTechnical()
    {
        if ($this->attributes['level'] == 'TECHNICAL') return true;

        return false;
    }

    protected function defaultProfilePhotoUrl()
    {
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }
}
