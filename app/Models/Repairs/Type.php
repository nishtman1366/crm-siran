<?php

namespace App\Models\Repairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use HasFactory;

    protected $table = 'repair_types';

    protected $fillable = ['name', 'status'];

    protected $appends = ['statusText'];

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
}
