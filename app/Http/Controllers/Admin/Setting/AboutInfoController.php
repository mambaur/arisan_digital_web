<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Constants\SettingType;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AboutInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::where('main_configuration', '0')->get();
        $data = [];

        foreach ($settings as $item) {
            $data[] = $item;
        }

        return view('admin.settings.about-and-info', compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keys = $request->keys ?? [];
        $values = $request->values ?? [];

        DB::beginTransaction();

        foreach ($keys as $index => $item) {
            $setting = Setting::where('key', $item)->first();
            if (@$setting) {
                $setting->value = @$values[$index];
                $setting->save();
            }
        }

        DB::commit();

        session()->flash('success', 'About & Info successfully updated');
        return redirect()->back();
    }
}
