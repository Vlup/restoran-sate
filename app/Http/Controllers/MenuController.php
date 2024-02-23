<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'slug' => 'required|unique:menus',
            'description' => 'required|max:255',
            'price' => 'required|numeric|min:500',
            'image' => 'image|file|max:2048',
        ]);

        if($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('menu-images');
        }

        $validatedData['stock'] = true;

        Menu::create($validatedData); 

        return redirect('/')->with('success', 'New Menu has been added!');

    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        return view('edit', [
            'title' => 'Edit Menu',
            'menu' => $menu,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $rules = [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'price' => 'required|numeric|min:500',
            'image' => 'image|file|max:2048',
        ];

        if ($request->slug !== $menu->slug) {
            $rules['slug'] = 'required|unique:menus';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('image')) {
            if($request->oldImage) {
                Storage::delete($request->oldImage);
            }
            $validatedData['image'] = $request->file('image')->store('menu-images');
        }

        Menu::where('id', $menu->id)
                ->update($validatedData);

        return redirect('/')->with('success', 'Menu has been updated');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        if($menu->image) {
            Storage::delete($menu->image);
        }

        Menu::destroy($menu->id);
        return redirect('/')->with('success', 'Menu has been deleted!');
    }

    public function isAvailable(Menu $menu)
    {
        if($menu->stock){
            $menu->stock = false;
        } else {
            $menu->stock = true;
        }
        $menu->save();
        return redirect('/')->with('success', 'Menu has been updated');
    }

    public function checkSlug(Request $request)
    {
        $slug = SlugService::createSlug(Menu::class, 'slug', $request->name);
        return response()->json(['slug' => $slug]);
    }
}
