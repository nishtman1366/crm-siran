<?php

namespace App\Models\Profiles;

use App\Models\Variables\Psp;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'psp_id', 'key', 'file_name', 'required', 'status'];

    protected $appends = ['statusText', 'requireText'];

    public function getStatusTextAttribute()
    {
        switch ($this->attributes['status']) {
            case 1:
            default:
                return 'فعال';
                break;
            case 0:
                return 'غیرفعال';
                break;
        }
    }

    public function getRequireTextAttribute()
    {
        switch ($this->attributes['required']) {
            case 1:
            default:
                return 'دارد';
                break;
            case 0:
                return 'ندارد';
                break;
        }
    }

    public function psp()
    {
        return $this->hasOne(Psp::class, 'id', 'psp_id');
    }
}
