<?php

namespace App\Http\Controllers\Admin\Subscription;

use App\Http\Controllers\Admin\Subscription\DataGrid\SubscriptionDataGrid;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.subscriptions.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function data(SubscriptionDataGrid $grid, Request $request)
    {
        return $grid->render();
    }
}
