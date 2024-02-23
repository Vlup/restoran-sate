<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderUserController extends Controller
{
    public function index()
    {
        $title = 'Pesanan';
        $orders = Order::with('menus')->where('user_id', Auth::user()->id)->get();
        return view('order.index', compact('title', 'orders'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'total' => 'required|numeric|min:500',
        ]);
        
        $validatedData['user_id'] = Auth::user()->id;
        $validatedData['status'] = 'pending';
    
        $user = Auth::user();
        $order = Order::create($validatedData);
    
        $allMenu = [];
        for ($i = 0; $i < count($request->menus); $i++) {
            $allMenu[$request->menus[$i]] = ['qty' => $request->qty[$i]]; 
        }

        $order->menus()->attach($allMenu);
        $user->menus()->detach();
        
        return redirect()->back()->with('success', 'Order has been made!');
    }
}
