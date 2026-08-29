<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteLead;
use Illuminate\Contracts\View\View;

class OrderController extends Controller
{
    public function index(): View
    {
        $commercialRequests = WebsiteLead::query()
            ->whereIn('type', ['order', 'quote']);

        $startOrderCount = (clone $commercialRequests)
            ->where('type', 'order')
            ->where('offer', 'START')
            ->count();

        $businessOrderCount = (clone $commercialRequests)
            ->where('type', 'order')
            ->where('offer', 'BUSINESS')
            ->count();

        $quoteRequestCount = (clone $commercialRequests)
            ->where('type', 'quote')
            ->count();

        $orders = $commercialRequests
            ->with('payments')
            ->latest()
            ->paginate(20);

        return view('admin.orders.index', compact(
            'orders',
            'startOrderCount',
            'businessOrderCount',
            'quoteRequestCount',
        ));
    }
}
