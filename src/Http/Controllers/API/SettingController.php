<?php

namespace Dimimo\PoolForum\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Dimimo\PoolForum\Models\Setting;

class SettingController
{
    public function index()
    {
        return Setting::all();
    }

    public function show(Setting $setting)
    {
        return $setting;
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.create')
                ->withErrors($validator)
                ->withInput();
        }
        Setting::create($data);

        return redirect()->route('settings.index')->with('pool-forum-status', 'Setting created!');
    }

    public function edit(Setting $setting)
    {
        return view('pool-forum::settings.edit', compact('setting'));
    }

    public function update(Setting $setting)
    {
        $data = request()->all();
        $validator = Validator::make($data, [
            'key' => 'required',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.edit', ['setting' => $setting])
                ->withErrors($validator)
                ->withInput();
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->route('settings.index')->with('pool-forum-status', 'Setting updated!');
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('settings.index')->with('pool-forum-status', 'Setting destroyed!');
    }
}
