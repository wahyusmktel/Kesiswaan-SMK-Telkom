<?php

namespace App\Http\Controllers;

use App\Models\KantinMenu;
use App\Models\KantinOrder;
use App\Models\KantinOrderItem;
use App\Models\KantinProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FoodOrderController extends Controller
{
    public function index()
    {
        // Get all open kantins
        $kantins = KantinProfile::where('is_open', true)->with('user')->get();
        
        // Get popular or random available menus
        $menus = KantinMenu::where('is_available', true)
            ->whereHas('user.kantinProfile', function($q) {
                $q->where('is_open', true);
            })
            ->inRandomOrder()
            ->limit(12)
            ->get();

        return view('pages.food-order.index', compact('kantins', 'menus'));
    }

    public function kantin($id)
    {
        $kantin = KantinProfile::with('user')->findOrFail($id);
        
        if (!$kantin->is_open) {
            return redirect()->route('food-order.index')->with('error', 'Mohon maaf, kantin ini sedang tutup.');
        }

        $menus = KantinMenu::where('user_id', $kantin->user_id)
            ->where('is_available', true)
            ->orderBy('category')
            ->get()
            ->groupBy('category');

        return view('pages.food-order.kantin', compact('kantin', 'menus'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'kantin_id' => 'required|exists:users,id',
            'payment_method' => 'required|in:qris,cash,saldo',
            'notes' => 'nullable|string',
            'items' => 'required|string', // JSON string of items
        ]);

        $items = json_decode($request->items, true);
        
        if (empty($items)) {
            return back()->with('error', 'Keranjang pesanan kosong.');
        }

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            foreach ($items as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }

            // Create Order
            $order = KantinOrder::create([
                'kantin_id' => $request->kantin_id,
                'student_id' => Auth::id(), // Can be any user/role
                'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            // Create Order Items
            foreach ($items as $item) {
                KantinOrderItem::create([
                    'kantin_order_id' => $order->id,
                    'kantin_menu_id' => $item['id'],
                    'menu_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            return redirect()->route('food-order.success', $order->id)->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }
    }

    public function success(KantinOrder $order)
    {
        if ($order->student_id !== Auth::id()) abort(403);
        
        return view('pages.food-order.success', compact('order'));
    }

    public function history()
    {
        $orders = KantinOrder::where('student_id', Auth::id())
            ->with('kantin.kantinProfile')
            ->latest()
            ->paginate(10);
            
        return view('pages.food-order.history', compact('orders'));
    }
}
