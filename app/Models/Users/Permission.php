<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends \Spatie\Permission\Models\Permission
{
    protected $fillable = ['name', 'display_name', 'parent_id','guard_name'];


    public function children()
    {
        return $this->hasMany(Permission::class, 'parent_id', 'id');
    }
}
