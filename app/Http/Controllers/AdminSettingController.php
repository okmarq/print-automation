<?php

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use App\Http\Requests\StoreAdminSettingRequest;
use App\Http\Requests\UpdateAdminSettingRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

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

    public function update(UpdateAdminSettingRequest $request, AdminSetting $adminSetting): RedirectResponse
    {
        $adminSetting->update($request->validated());
        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }

    public function destroy(AdminSetting $adminSetting): RedirectResponse
    {
        $adminSetting->delete();
        return redirect()->route('settings.index')->with('success', 'Settings deleted successfully.');
    }
}
