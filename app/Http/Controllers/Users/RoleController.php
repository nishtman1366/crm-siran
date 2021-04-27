<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Users\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $i = 1;
        $roles = Role::with('permissions')->orderBy('id', 'ASC')->get()->each(function ($role) use (&$i) {
            $role->no = $i;
            $i++;
        });

        $categories = Permission::with('children')->where('parent_id', null)->get();

        return Inertia::render('Dashboard/Users/Roles/List', compact('roles', 'categories'));
    }

    public function create(Request $request)
    {
        $role = new Role();
        $role->fill($request->all());
        $role->save();

        session()->flash('message', 'ایجاد گروه کاربری با موفقیت انجام شد.');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $roleId = $request->route('roleId');
        $role = Role::find($roleId);

        if (is_null($role)) throw new NotFoundHttpException('گروه کاربری یافت نشد.');

        $role->fill($request->all());
        $role->save();

        session()->flash('message', 'بروزرسانی گروه کاربری با موفقیت انجام شد.');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        die('error');
        $roleId = $request->route('roleId');
        $role = Role::find($roleId);

        if (is_null($role)) throw new NotFoundHttpException('گروه کاربری یافت نشد.');

        $role->delete();

        session()->flash('message', 'حذف گروه کاربری با موفقیت انجام شد.');
        return redirect()->back();
    }

    public function permissions(Request $request)
    {
        $roleId = $request->route('roleId');

        $role = Role::find($roleId);
        if (is_null($role)) throw new NotFoundHttpException('گروه کاربری مورد نظر یافت نشد.');
        $list = $request->get('permissions');
        $permissions = Permission::whereIn('id', $list)->get();

        $role->syncPermissions($permissions);

        session()->flash('message', 'ذخیره سطوح دسترسی با موفقیت انجام شد.');
        return redirect()->back();
    }
}
