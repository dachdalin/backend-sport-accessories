<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboardService) {}

    /**
     * Display the ecommerce overview dashboard.
     */
    public function index(Request $request): Response
    {
        $filter = $request->string('filter', 'today')->value();
        $from = $request->query('from');
        $to = $request->query('to');

        if (! in_array($filter, ['today', 'this_week', 'this_month', 'this_year', 'custom'], true)) {
            $filter = 'today';
        }

        [$start, $end] = $this->dashboardService->resolveRange($filter, $from, $to);

        return Inertia::render('Dashboard', [
            'filter' => $filter,
            'from' => $from,
            'to' => $to,
            'stats' => Inertia::defer(fn () => $this->dashboardService->stats($start, $end), 'dashboard'),
            'salesChart' => Inertia::defer(fn () => $this->dashboardService->salesChart($start, $end, $filter), 'dashboard'),
            'topProducts' => Inertia::defer(fn () => $this->dashboardService->topProducts($start, $end), 'dashboard'),
        ]);
    }
}
