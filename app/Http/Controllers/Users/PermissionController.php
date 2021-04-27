<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Users\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $i = 1;

        $categories = Permission::where('parent_id', null)->get();

        $permissions = collect([]);
        foreach ($categories as $category) {
            $permissions->push($category);
            $children = Permission::where('parent_id', $category->id)->get();
            foreach ($children as $child) {
                $child->parent = $category;
                $permissions->push($child);
            }
        }

        $permissions->each(function ($permission) use (&$i) {
            $permission->no = $i;
            $i++;
        });

        return Inertia::render('Dashboard/Users/Permissions/List', compact('categories', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validateWithBag('permissionForm', [
            'name' => 'required|unique:permissions,name',
            'parent_id' => 'nullable|exists:permissions,id',
            'display_name' => 'required|unique:permissions,display_name',
        ]);
        $request->merge(['guard_name' => 'web']);
        $permission = new Permission();
        $permission->fill($request->all());
        $permission->save();

        $role = Role::find(1);
        $role->givePermissionTo($permission);

        session()->flash('message', 'ایجاد سطح دسترسی با موفقیت انجام شد.');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $permissionId = $request->route('permissionId');
        $permission = Permission::find($permissionId);

        if (is_null($permission)) throw new NotFoundHttpException('سطح دسترسی یافت نشد.');

        $permission->fill($request->all());
        $permission->save();

        session()->flash('message', 'بروزرسانی سطح دسترسی با موفقیت انجام شد.');
        return redirect()->back();
    }

    public function destroy(Request $request)
    {
        die('error');
        $permissionId = $request->route('permissionId');
        $permission = Permission::find($permissionId);

        if (is_null($permission)) throw new NotFoundHttpException('سطح دسترسی یافت نشد.');

        $permission->delete();

        session()->flash('message', 'حذف سطح دسترسی با موفقیت انجام شد.');
        return redirect()->back();
    }
}
