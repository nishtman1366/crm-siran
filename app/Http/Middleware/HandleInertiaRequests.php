<?php

namespace App\Http\Middleware;

use App\Models\Form;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HandleInertiaRequests
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->has('message')) {
            Inertia::share('message', $request->session()->get('message'));
        }

        $configs = [];
        Setting::orderBy('id', 'ASC')->get()->each(function ($item) use (&$configs) {
            $configs[Str::camel(strtolower($item->key))] = $item->value;
        });
        if ($request->user()) {
            $user = $request->user();
            if ($user->isAdmin()) {
                if (!is_null($user->company_logo)) $configs['companyLogo'] = $user->companyLogoUrl;
                if (!is_null($user->company_name)) $configs['companyName'] = $user->company_name;
            } elseif ($user->isAgent()) {
                $admin = $user->parent;
                if (!is_null($admin->company_logo)) $configs['companyLogo'] = $admin->companyLogoUrl;
                if (!is_null($admin->company_name)) $configs['companyName'] = $admin->company_name;
            } elseif ($user->isMarketer()) {
                $admin = $user->parent;
                if ($admin->isAgent()) $admin = $admin->parent;
                if (!is_null($admin->company_logo)) $configs['companyLogo'] = $admin->companyLogoUrl;
                if (!is_null($admin->company_name)) $configs['companyName'] = $admin->company_name;
            }
        }

        Inertia::share('configs', $configs);

        return $next($request);
    }
}
