<?php

namespace App\Http\Controllers\API\V2\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Setting
     */
    public function index($key)
    {
        $setting = Setting::select('id', 'key', 'value', 'description')->where('key', $key)->first();
        if(!$setting){
            return abort(404, 'Pengaturan tidak ditemukan');
        }

        /**
         * Key: value
         * mobile_version: 1.0.0
         * is_maintenance: 1
         */

        return response()->json([
            'message' => 'Pengaturan berhasil didapatkan.',
            'data' => $setting
        ]);
    }
}
