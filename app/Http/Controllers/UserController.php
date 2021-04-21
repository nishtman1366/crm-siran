<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->route('type', null);
        $type = strtoupper($type);
        $user = Auth::user();

        $this->checkUserPermissions($type, $user);

        $users = User::with('parent')->where(function ($query) use ($type, $user) {
            $query->where('level', $type);
            if ($user->level != 'SUPERUSER') {
                if ($type != 'ADMIN') {
                    $query->where('parent_id', $user->id);
                }
            }
        })
            ->orderBy('id', 'ASC')->paginate();

        $paginatedLinks = paginationLinks($users);

        $usersType = $this->returnUsersType($type);

        $type = strtolower($type);

        return Inertia::render('Dashboard/Users/UsersList', [
            'type' => $type,
            'usersType' => $usersType,
            'users' => $users,
            'paginatedLinks' => $paginatedLinks
        ]);
    }

    public function create(Request $request)
    {
        $type = $request->route('type', null);
        $type = strtoupper($type);
        $user = Auth::user();

        $this->checkUserPermissions($type, $user);

        $usersType = $this->returnUsersType(strtoupper($type));

        $statuses = [
            ['id' => 0, 'name' => 'ثبت شده'],
            ['id' => 1, 'name' => 'تایید شده'],
            ['id' => 2, 'name' => 'معلق'],
        ];
        $agents = [];
        if ($type == 'marketer') {
            $agents = User::where('parent_id', $user->id)->where('level', 'AGENT')->get();
        }

        return Inertia::render('Dashboard/Users/CreateUser', [
            'type' => $type,
            'userType' => $usersType,
            'statuses' => $statuses,
            'agents' => $agents
        ]);
    }

    public function store(Request $request)
    {
        $authenticatedUser = Auth::user();
        $type = $request->route('type');
        $type = strtoupper($type);
        $request->validateWithBag('userForm', [
            'parent_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'mobile' => 'required|digits:11',
            'status' => 'required',
        ]);

        $this->checkUserPermissions($type, $authenticatedUser);

        $request->merge(['level' => $type]);
        $user = User::create($request->all());

        $user->assignRole(strtolower($type));

        if ($request->hasFile('companyLogo')) {
            Storage::disk('public')->delete('companies/' . $user->id . '/' . $user->company_logo);
            $name = $request->file('companyLogo')->store('public/companies/' . $user->id);
            $user->company_logo = basename($name);
            $user->save();
        }

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function view(Request $request)
    {
        $userId = $request->route('id');
        $user = User::find($userId);
        if (is_null($user)) throw new NotFoundHttpException('اطلاعات کاربر مورد نظر یافت نشد.');
        $authenticatedUser = Auth::user();
        if (!$user->belongsToUser($authenticatedUser)) {
            throw new UnauthorizedHttpException('', 'شما اجازه مشاهده این اطلاعات را ندارید.');
        }

        $type = $request->route('type', null);
        $usersType = $this->returnUsersType(strtoupper($type));
        $statuses = [
            ['id' => 0, 'name' => 'ثبت شده'],
            ['id' => 1, 'name' => 'تایید شده'],
            ['id' => 2, 'name' => 'معلق'],
        ];
        $agents = [];
        if ($type == 'marketer') {
            $agents = User::where('parent_id', $user->id)->where('level', 'AGENT')->get();
        }

        return Inertia::render('Dashboard/Users/EditUser', [
            'selectedUser' => $user,
            'type' => $type,
            'userType' => $usersType,
            'statuses' => $statuses,
            'agents' => $agents
        ]);
    }

    public function update(Request $request)
    {
        $userId = $request->route('id');
        $request->validateWithBag('userForm', [
            'parent_id' => 'required',
            'name' => 'required',
            'username' => 'required|unique:users,id,' . $userId,
            'password' => 'nullable|min:6',
            'mobile' => 'required|digits:11',
            'status' => 'required',
        ]);

        $user = User::find($userId);
        if (is_null($user)) throw new NotFoundHttpException('اطلاعات کاربر مورد نظر یافت نشد.');

        $authenticatedUser = Auth::user();
        if (!$user->belongsToUser($authenticatedUser)) {
            throw new UnauthorizedHttpException('', 'شما اجازه مشاهده این اطلاعات را ندارید.');
        }

        if ($request->hasFile('companyLogo')) {
            Storage::disk('public')->delete('companies/' . $user->id . '/' . $user->company_logo);
            $name = $request->file('companyLogo')->store('public/companies/' . $user->id);
            $request->merge(['company_logo' => basename($name)]);
        }

        if ($request->has('deleteCompanyLogo') && $request->get('deleteCompanyLogo')) {
            Storage::disk('public')->delete('companies/' . $user->id . '/' . $user->company_logo);
            $request->merge(['company_logo' => null]);
        }

        $user->fill($request->all());
        $user->save();

        $type = $request->route('type');

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function destroy(Request $request)
    {
        $userId = $request->route('id');
        $type = $request->route('type');

        $user = User::find($userId);
        if (is_null($user)) throw new NotFoundHttpException('اطلاعات کاربر مورد نظر یافت نشد.');
        $authenticatedUser = Auth::user();
        if (!$user->belongsToUser($authenticatedUser)) {
            throw new UnauthorizedHttpException('', 'شما اجازه مشاهده این اطلاعات را ندارید.');
        }
        $user->delete();

        return redirect()->route('dashboard.users.list', ['type' => $type]);
    }

    public function returnUsersType($type)
    {
        switch ($type) {
            case 'ADMIN':
                return 'مدیر سیستم';
            case 'AGENT':
                return 'نماینده';
            case 'MARKETER':
                return 'بازاریاب';
        }
    }

    public function checkUserPermissions($type, $user)
    {
        $type = strtoupper($type);
        switch ($type) {
            case 'ADMIN':
                $roles = ['superuser'];
                break;
            case 'AGENT':
            case 'OFFICE':
            case 'TECHNICAL':
                $roles = ['superuser', 'admin'];
                break;
            case 'MARKETER':
                $roles = ['superuser', 'admin', 'agent'];
                break;
        }
        if (!$user->hasRole($roles)) {
            throw new UnauthorizedHttpException('', 'دسترسی به این بخش برای شما امکان پذیر نمی باشد.');
        }

        return true;
    }
}
