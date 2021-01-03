<?php

namespace App\Http\Controllers;

use App\Models\Variables\DeviceType;
use App\Models\Variables\Device;
use App\Models\Variables\DeviceConnectionType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard/Settings/SettingsMain');
    }

    public function devices(Request $request)
    {
        $searchQuery = $request->query('query', null);
        $deviceTypes = DeviceType::with('type')->where(function ($query) use ($searchQuery) {
            if (!is_null($searchQuery)) {
                $query->where('name', 'LIKE', '%' . $searchQuery . '%');
            }
        })->orderBy('name')->get();
        $deviceTypes->each(function ($device) {
            $device->physicalStatus1Count = Device::where('device_type_id', $device->id)->where('physical_status', 1)->count();
            $device->physicalStatus2Count = Device::where('device_type_id', $device->id)->where('physical_status', 2)->count();
        });

        $connectionTypes = DeviceConnectionType::orderBy('id', 'ASC')->get();
        return Inertia::render('Dashboard/Settings/Devices', [
            'searchQuery' => $searchQuery,
            'deviceTypes' => $deviceTypes,
            'deviceConnectionTypes' => $connectionTypes
        ]);
    }

    public function storeDevice(Request $request)
    {
        $request->validateWithBag('deviceForm', [
            'name' => 'required',
            'device_connection_type_id' => 'required'
        ]);

        Devicetype::create($request->all());

        return redirect()->route('dashboard.settings.devices');
    }

    public function updateDevice(Request $request)
    {
        $id = $request->route('deviceId');
        $request->validateWithBag('deviceForm', [
            'name' => 'required',
            'device_connection_type_id' => 'required'
        ]);
        $device = DeviceType::find($id);
        if (is_null($device)) throw new NotFoundHttpException('دستگاه یافت نشد.');
        $device->fill($request->all());
        $device->save();
        return redirect()->route('dashboard.settings.devices');
    }

    public function destroyDevice(Request $request)
    {
        $id = $request->route('deviceId');
        $device = DeviceType::find($id);
        if (is_null($device)) throw new NotFoundHttpException('دستگاه یافت نشد.');
        $device->delete();
        return redirect()->route('dashboard.settings.devices');
    }
}
