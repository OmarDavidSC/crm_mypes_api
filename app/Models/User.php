<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{

    use SoftDeletes;
    protected $table = 'users';
    protected $fillable = [
        'id',
        'foto_id',
        'name',
        'paternal_surname',
        'maternal_surname',
        'username',
        'email',
        'password',
        'status',
    ];

    public function companies(){
        return $this->belongsToMany(Company::class, 'user_company_role')->withPivot('role_id')->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_company_role')->withPivot('company_id')->withTimestamps();
    }
}
