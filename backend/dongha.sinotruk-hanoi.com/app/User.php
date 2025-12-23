<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes; // use the trait
    protected $dates = ['deleted_at']; // mark this column as a date

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'admin', 'password', 'phone', 'full_name', 'money'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    public function roles() {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    public function getPermissions() {
        $roles = $this->roles;
        $permissions = collect();
        foreach ($roles as $role)
        {
            $permissions = $permissions->concat($role->permissions()->select('name')->get());
        }
        return $permissions;
    }

    public function hasPermission($permission) {
        if ($this->admin)
            return true;
        else{
            $permission = Permission::where("name", $permission)->first();
            $roles_has_permssions = $permission->roles;
            foreach ($roles_has_permssions as $role_has_permssions) {
                if ($this->roles->contains('id', $role_has_permssions->id)) {
                    return true;
                }
            }
            return false;
        }
    }
	public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
