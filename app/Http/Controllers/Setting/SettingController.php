<?php

namespace App\Http\Controllers\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingUpdateManyRequest;
use App\Http\Requests\SettingUpdateRequest;
use App\Services\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    //
    protected $settingService;

    // ? Dependency Injection of SettingService
    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }


    // ? Display a listing of the resource.
    public function index()
    {
        $settings = $this->settingService->all();
        return view('setting.index', compact('settings'));
    }


    // ? Update the specified resource in storage.
    public function update(SettingUpdateRequest $request)
    {
        $data = $request->validated();
        $this->settingService->set($data['key'], $data['value']);
        if ($request->hasFile('images')) {
            $this->settingService->uploadSettingImages($this->settingService->get($data['key']), $request->file('images'));
        }
        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    // ? Update multiple settings at once.
    public function updateMany(SettingUpdateManyRequest $request)
    {
        $data = $request->except('_token');
        $this->settingService->setMany($data);
        $this->settingService->setManyWithImages($data);
        return redirect()->back()->with('success', 'Settings updated successfully');
    }



}
