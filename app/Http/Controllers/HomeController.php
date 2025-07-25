<?php

namespace App\Http\Controllers;

use App\Models\ArisanHistory;
use App\Models\Feedback;
use App\Models\Group;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\ChargeNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function root()
    {
        $total_group = $this->getTotalGroup();
        $total_shuffle = $this->getTotalShuffle();
        $total_user = $this->getTotalUser();
        $total_subscription = $this->getTotalSubscription();
        $total_feedback = $this->getTotalFeedback();
        $total_last_seen = $this->getTotalLastSeen();

        return view('home.index', compact('total_group', 'total_shuffle', 'total_user', 'total_subscription', 'total_feedback', 'total_last_seen'));
    }

    private function getTotalGroup()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        // Periode sebelumnya: 30–60 hari yang lalu
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $now->copy()->subDays(30);

        $today = Group::where('status', 'active')
            ->whereDate('created_at', Carbon::today())->count();

        $total_last_30_days = Group::where('status', 'active')
            ->whereBetween('created_at', [$last30Start, $last30End])->count();

        $total_prev_30_days = Group::where('status', 'active')
            ->whereBetween('created_at', [$prev30Start, $prev30End])->count();

        // Hitung perbedaan (opsional)
        $remain = $total_last_30_days - $total_prev_30_days;
        $percent = $total_prev_30_days > 0
            ? ($remain / $total_prev_30_days) * 100
            : 0;

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => $total_prev_30_days,
            'percent' => number_format($percent, 2),
        ];
    }

    private function getTotalShuffle()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        // Periode sebelumnya: 30–60 hari yang lalu
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $now->copy()->subDays(30);

        $today = ArisanHistory::whereDate('created_at', Carbon::today())->count();

        $total_last_30_days = ArisanHistory::whereBetween('created_at', [$last30Start, $last30End])->count();

        $total_prev_30_days = ArisanHistory::whereBetween('created_at', [$prev30Start, $prev30End])->count();

        // Hitung perbedaan (opsional)
        $remain = $total_last_30_days - $total_prev_30_days;
        $percent = $total_prev_30_days > 0
            ? ($remain / $total_prev_30_days) * 100
            : 0;

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => $total_prev_30_days,
            'percent' => number_format($percent, 2),
        ];
    }

    private function getTotalUser()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        $today = User::whereDate('created_at', Carbon::today())->count();

        // Periode sebelumnya: 30–60 hari yang lalu
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $now->copy()->subDays(30);

        $total_last_30_days = User::whereBetween('created_at', [$last30Start, $last30End])->count();

        $total_prev_30_days = User::whereBetween('created_at', [$prev30Start, $prev30End])->count();

        // Hitung perbedaan (opsional)
        $remain = $total_last_30_days - $total_prev_30_days;
        $percent = $total_prev_30_days > 0
            ? ($remain / $total_prev_30_days) * 100
            : 0;

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => $total_prev_30_days,
            'percent' => number_format($percent, 2),
        ];
    }

    private function getTotalSubscription()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        $today = Subscription::whereDate('created_at', Carbon::today())->count();

        // Periode sebelumnya: 30–60 hari yang lalu
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $now->copy()->subDays(30);

        $total_last_30_days = Subscription::whereBetween('created_at', [$last30Start, $last30End])->count();

        $total_prev_30_days = Subscription::whereBetween('created_at', [$prev30Start, $prev30End])->count();

        // Hitung perbedaan (opsional)
        $remain = $total_last_30_days - $total_prev_30_days;
        $percent = $total_prev_30_days > 0
            ? ($remain / $total_prev_30_days) * 100
            : 0;

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => $total_prev_30_days,
            'percent' => number_format($percent, 2),
        ];
    }

    private function getTotalFeedback()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        $today = Feedback::whereDate('created_at', Carbon::today())->count();

        // Periode sebelumnya: 30–60 hari yang lalu
        $prev30Start = $now->copy()->subDays(60);
        $prev30End = $now->copy()->subDays(30);

        $total_last_30_days = Feedback::whereBetween('created_at', [$last30Start, $last30End])->count();

        $total_prev_30_days = Feedback::whereBetween('created_at', [$prev30Start, $prev30End])->count();

        // Hitung perbedaan (opsional)
        $remain = $total_last_30_days - $total_prev_30_days;
        $percent = $total_prev_30_days > 0
            ? ($remain / $total_prev_30_days) * 100
            : 0;

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => $total_prev_30_days,
            'percent' => number_format($percent, 2),
        ];
    }

    private function getTotalLastSeen()
    {
        // Periode sekarang: 0–30 hari terakhir
        $now = Carbon::now();
        $last30Start = $now->copy()->subDays(30);
        $last30End = $now;

        $today = User::whereDate('last_seen_at', Carbon::today())->count();

        $total_last_30_days = User::whereBetween('last_seen_at', [$last30Start, $last30End])->count();

        return [
            'today' => $today,
            'last_30_days' => $total_last_30_days,
            'prev_30_start_days' => 0,
            'percent' => 0,
        ];
    }

    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return view('errors.404');
    }

    public function testNotification(Request $request)
    {
        auth()->user()->notify(new ChargeNotification('Hello world', 'Notification Description'));
    }
}
