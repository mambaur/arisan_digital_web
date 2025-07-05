<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaticWebController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function privacy()
    {
        return view('statics.privacy');
    }

    /**
     * Display a listing of the resource.
     */
    public function termCondition()
    {
        return view('statics.term-condition');
    }
}
