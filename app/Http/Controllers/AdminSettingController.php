<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Http\Requests\StoreAdminSettingRequest;
use App\Http\Requests\UpdateAdminSettingRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class AdminSettingController extends Controller
{
    public function index(): View|Factory|Application
    {
        return view('pages.settings.index', ['settings' => AdminSetting::all()]);
    }

    public function create(): View|Factory|Application
    {
        return view('pages.settings.create');
    }

    public function store(StoreAdminSettingRequest $request)
    {
        $setting = AdminSetting::create($request->validated());

        return redirect()->route('settings.index')->with('success', 'Settings created successfully.')->with('setting', $setting);
    }

    public function show(AdminSetting $adminSetting)
    {
        //
    }

    public function edit(AdminSetting $adminSetting)
    {
        //
    }

    public function update(UpdateAdminSettingRequest $request, AdminSetting $adminSetting)
    {
        $adminSetting->update($request->validated());
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function destroy(AdminSetting $adminSetting)
    {
        $adminSetting->delete();
        return redirect()->route('settings.index')->with('success', 'Settings deleted successfully.');
    }
}
