<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Profiles\LicenseType;
use App\Models\Variables\Psp;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LicenseController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $licenses = LicenseType::with('psp')->where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();

        $psps = Psp::where('status', 1)->orderBy('name', 'ASC')->get();
        return Inertia::render('Dashboard/Settings/LicenseTypes', [
            'searchQuery' => $searchQuery,
            'licenses' => $licenses,
            'psps' => $psps
        ]);
    }

    public function store(Request $request)
    {
        $request->validateWithBag('licenseForm', [
            'name' => 'required',
            'psp_id' => 'required|exists:psps,id',
            'key' => 'required|unique:license_types,key',
            'status' => 'required'
        ]);

        $license = LicenseType::create($request->all());

        return redirect()->route('dashboard.settings.licenses.list');
    }

    public function update(Request $request)
    {
        $id = $request->route('licenseId');
        $request->validateWithBag('licenseForm', [
            'name' => 'required',
            'psp_id' => 'required|exists:psps,id',
            'key' => 'required|unique:license_types,key,' . $id,
            'status' => 'required'
        ]);
        $license = LicenseType::find($id);
        if (is_null($license)) throw new NotFoundHttpException('اطلاعات مدرک مورد نظر یافت نشد.');
        $license->fill($request->all());
        $license->save();

        return redirect()->route('dashboard.settings.licenses.list');
    }

    public function destroy(Request $request)
    {
        $id = $request->route('licenseId');
        $license = LicenseType::find($id);
        if (is_null($license)) throw new NotFoundHttpException('اطلاعات مدرک مورد نظر یافت نشد.');
        $license->delete();
        return redirect()->route('dashboard.settings.licenses.list');
    }
}
