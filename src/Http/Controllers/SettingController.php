<?php

namespace Dimimo\PoolForum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Dimimo\PoolForum\Models\Setting;

class SettingController
{
    public function index()
    {
        $settings = Setting::all();

        return view('pool-forum::tw.settings.index', compact('settings'));
    }

    public function show(Setting $setting)
    {
        return view('pool-forum::tw.settings.show', compact('setting'));
    }

    public function create()
    {
        return view('pool-forum::tw.settings.create');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => 'required|unique:settings,key',
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('settings.create')
                ->withErrors($validator)
                ->withInput();
        }
        Setting::create($data);

        return redirect()->route('forum.settings.index')->with('pool-forum-status', __('pool-forum::words.record_created'));
    }

    public function edit(Setting $setting)
    {
        return view('pool-forum::tw.settings.edit', compact('setting'));
    }

    public function update(Request $request, Setting $setting)
    {
        $data = $request->all();
        $validator = Validator::make($data, [
            'key' => [
                'required',
                Rule::unique('settings')->ignore($setting->key, 'key'),
            ],
            'value' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->route('forum.settings.edit', ['setting' => $setting])
                ->withErrors($validator)
                ->withInput();
        }

        $setting->fill($data);
        $setting->save();

        return redirect()->route('forum.settings.index')->with('pool-forum-status', __('pool-forum::words.record_updated'));
    }

    public function destroy(Setting $setting)
    {
        $setting->delete();

        return redirect()->route('forum.settings.index')->with('pool-forum-status', __('pool-forum::words.record_destroyed'));
    }
}
