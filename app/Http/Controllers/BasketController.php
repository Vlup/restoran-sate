<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use App\Models\Basket;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreBasketRequest;
use App\Http\Requests\UpdateBasketRequest;
use Illuminate\Contracts\Support\ValidatedData;

class BasketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = 'Keranjang';
        $baskets = User::with('menus')->where('id', Auth::user()->id)->first();

        return view ('basket.index', compact('title', 'baskets'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBasketRequest $request): RedirectResponse
    {
        $rules = [
            'menu_id' => 'required',
            'qty' => 'required|numeric|min:1',
        ];

        $user = Auth::user();
        $validatedData = $request->validate($rules);
        $menu = Menu::where('id', $validatedData['menu_id'])->first();
        
        $hasOrder = $user->menus()->where('menu_id', $menu->id)->first();
        if($hasOrder == NULL) {
            $user->menus()->attach($menu, ['qty' => $validatedData['qty']]);
        } else {
            $user->menus()->updateExistingPivot($menu->id, ['qty' => $validatedData['qty'] + $hasOrder->basket->qty]);
        }

        return redirect()->back()->with('success', 'Berhasil menambahkan menu ke keranjang!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {   
        $validatedData = $request->validate([
            'qty' => 'required|numeric|min:1',
        ]);
        $user = Auth::user();
        $user->menus()->updateExistingPivot($menu->id, ['qty' => $validatedData['qty']]);
        return redirect()->back()->with('success', 'updated'); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Basket $basket): RedirectResponse
    {
        $user = Auth::user();
        $user->menus()->detach();
        return redirect()->back()->with('success', 'Cleared'); 
    }

    public function detach(Menu $menu): RedirectResponse
    {
        $user = Auth::user();
        $menu->users()->detach($user);

        return redirect()->back()->with('success', 'deleted'); 
    }
}
