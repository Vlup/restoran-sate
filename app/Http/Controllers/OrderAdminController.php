<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderAdminController extends Controller
{
    public function index() {
        $title = 'Pesanan Customer';
        $orders = Order::orderBy("created_at", "asc")->with('menus', 'user')->get();
        return view('order.orderAdmin', compact('title', 'orders'));
    }

    public function complete(Order $order) {
        $order->status = 'completed';
        $order->save();

        return redirect()->back()->with('success', 'Order has been completed');
    }

    public function accept(Order $order) {
        $order->status = 'on going';
        $order->save();

        return redirect()->back()->with('success', 'Order has been accepted');
    }

    public function cancel(Order $order) {
        $order->status = 'canceled';
        $order->save();

        return redirect()->back()->with('success', 'Order has been canceled');
    }
}
