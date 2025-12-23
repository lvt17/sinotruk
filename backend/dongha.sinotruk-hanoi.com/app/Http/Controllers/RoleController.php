<?php

namespace App\Http\Controllers;

use App\Job;
use App\Role;
use App\RoleHasPermission;
use App\User;
use App\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => Role::orderBy('name')->get(),
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:users|max:255',
        ]);
        Role::create([
            'name' => $request->name,
        ]);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);
        $item = Role::find($id);
        $item->name = $request->name;
        $item->save();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $item = Role::find($id);
        $item->users()->detach();
        $item->permissions()->detach();
        $item->delete();
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function users(Request $request, Role $role) {
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $role->users,
            ]);
        }
    }

    public function storeUser(Request $request, Role $role)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $user = User::find($request->id);
        if (!$user->roles->contains('name', $role->name)) {
            $user->roles()->save($role);
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Người dùng đã được thêm thành công',
                ]);
            }
        }
    }

    public function storeAllUser(Request $request, Role $role)
    {
        $users = User::all();
        foreach ($users as $user) {
            if (!$user->admin) {
                if (!$user->roles->contains('name', $role->name)) {
                    $user->roles()->save($role);
                }
            }
        }
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Người dùng đã được thêm thành công',
            ]);
        }
    }

    public function destroyUser(Request $request, Role $role, $id)
    {
        $user = User::find($id);
        $user->roles()->detach($role);
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function permissions(Request $request, Role $role) {
        //Get permission trong role
        $rolePermissions = RoleHasPermission::where('role_id', $role->id)->get();
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            if (
                $rolePermissions->first(function ($value) use ($permission) {
                    return $value->permission_id == $permission->id;
                }) != null
            )
            {
                $permission->hasPermission = true;
            }
        }
        if ($request->expectsJson()) {
            return response()->json([
                'items' => $permissions,
            ]);
        }
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        if ($request->hasPermission)    //Thêm permission
        {
            $rolePermissions = RoleHasPermission::where([['role_id', $role->id], ['permission_id', $request->id]])->first();
            if ($rolePermissions == null)
            {
                RoleHasPermission::create([
                    'permission_id' => $request->id,
                    'role_id' => $role->id,
                ]);
            }
        }
        else //Xóa permission
        {
            $rolePermissions = RoleHasPermission::where([['role_id', $role->id], ['permission_id', $request->id]])->first();
            if ($rolePermissions != null)
            {
                $rolePermissions->delete();
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }
    }

    public function updateAllPermissions(Request $request, Role $role)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $job = Job::find($request->id);
        $permissions = $job->permissions;
        if ($request->hasPermission) {
            foreach ($permissions as $permission) {
                $rolePermissions = RoleHasPermission::where([['role_id', $role->id], ['permission_id', $permission->id]])->first();
                //dd($rolePermissions);
                if ($rolePermissions == null)
                {
                    RoleHasPermission::create([
                        'permission_id' => $permission->id,
                        'role_id' => $role->id,
                    ]);
                }
            }

        }
        else
        {
            foreach ($permissions as $permission) {
                $rolePermissions = RoleHasPermission::where([['role_id', $role->id], ['permission_id', $permission->id]])->first();
                if ($rolePermissions != null)
                {
                    $rolePermissions->delete();
                }
            }
        }
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 1,
            ]);
        }

    }
}
