<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Constants\SettingType;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = Setting::all();
        $data = [];
        foreach ($settings as $item) {
            $data[$item->key] = $item->value;
        }

        $setting_types = SettingType::toArray();

        return view('admin.settings.configuration', compact('data', 'setting_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keys = $request->keys ?? [];
        $values = $request->values ?? [];
        $descriptions = $request->descriptions ?? [];

        DB::beginTransaction();

        foreach ($keys as $index => $item) {
            $setting = Setting::where('key', $item)->first();
            if($setting){
                $setting->value = $values[$index];
                $setting->description = $descriptions[$index];
                $setting->save();
            }else if(in_array($item, SettingType::toArray())){
                Setting::create([
                    'key' => $item,
                    'value' => $values[$index],
                    'description' => $descriptions[$index]
                ]);
            }
        }

        DB::commit();

        session()->flash('success', 'Configuration successfully updated');
        return redirect()->back();
    }
}
