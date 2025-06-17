<?php

namespace App\Http\Controllers\API\V2\Settings;

use App\Constants\SettingType;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Svg\Tag\Rect;

class SettingController extends Controller
{
    /**
     * Setting
     */
    public function index($key)
    {
        $validate = Validator::make(['key' => $key], [
            'key' => 'required|in:' . SettingType::validation(),
        ]);

        if ($validate->fails()) {
            $error = $validate->errors()->first();
            return response()->json(
                [
                    'status' => 'failed',
                    'message' => $error,
                ],
                200
            );
        }

        $setting = Setting::select('id', 'key', 'value', 'description')->where('key', $key)->first();
        if (!$setting) {
            return abort(404, 'Pengaturan tidak ditemukan');
        }

        return response()->json([
            'message' => 'Pengaturan berhasil didapatkan.',
            'data' => $setting
        ]);
    }


    /**
     * Get All Settings
     */
    public function all()
    {
        $settings = Setting::all();
        $data = null;
        foreach ($settings as $item) {
            $data[$item->key] = [
                'id' => $item->id,
                'title' => $item->title,
                'key' => $item->key,
                'value' => $item->value,
                'description' => $item->description,
            ];
        }

        return response()->json([
            'message' => 'Pengaturan berhasil didapatkan.',
            'data' => $data
        ]);
    }
}
