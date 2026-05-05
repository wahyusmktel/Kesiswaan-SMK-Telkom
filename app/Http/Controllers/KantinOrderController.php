<?php

namespace App\Http\Controllers;

use App\Models\KantinOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KantinOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = KantinOrder::where('kantin_id', Auth::id())->with('student', 'items');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        } else {
            // By default, don't show completed/cancelled unless asked
            if (!$request->has('status')) {
                $query->whereIn('status', ['pending', 'preparing', 'ready']);
            }
        }

        $orders = $query->latest()->paginate(10);

        return view('pages.kantin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, KantinOrder $order)
    {
        if ($order->kantin_id !== Auth::id()) abort(403);

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled'
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan #' . $order->order_number . ' berhasil diperbarui menjadi ' . strtoupper($request->status));
    }

    public function printReceipt(KantinOrder $order)
    {
        if ($order->kantin_id !== Auth::id()) abort(403);
        $order->load('student', 'items');

        return view('pages.kantin.orders.receipt', compact('order'));
    }

    // API Endpoint for notifications (polling)
    public function pendingCount()
    {
        $count = KantinOrder::where('kantin_id', Auth::id())->where('status', 'pending')->count();
        return response()->json(['count' => $count]);
    }
}
