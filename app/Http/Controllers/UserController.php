<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    
    public function index()
    {
        $title = 'Profile';
        return view('profile.index', compact('title'));
    }

    public function updateEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email:dns',
            'password' => 'required'
        ];

        $validatedData = $request->validate($rules);

        if($validatedData['email'] === Auth::user()->email){
            return redirect()->back()->with('error', 'New e-mail cannot be same as your current e-mail.');
        }
        if(Hash::check($validatedData['password'], Auth::user()->password)){
            /** @var \App\Models\MyUserModel $user **/
            $user = Auth::user();
            $user->email = $validatedData['email'];
            $user->save();
            return redirect()->back()->with('success', 'Email has been changed!');
        } 
        return redirect()->back()->with('error', 'Wrong Password!');
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required|min:8|max:255'
        ];

        $validatedData = $request->validate($rules);

        if(Hash::check($validatedData['old_password'], Auth::user()->password)) {
            /** @var \App\Models\MyUserModel $user **/
            $user = Auth::user();
            $user->password = Hash::make($validatedData['new_password']);
            $user->save();
            return redirect()->back()->with('success', 'Password has been changed!');
        }

        return redirect()->back()->with('error', 'Wrong Password!');

    }

    public function uploadImage(Request $request)
    {
        $rules = [
            'image' => 'image|required|file|max:2048',
        ];

        $validatedData = $request->validate($rules);

        if($request->file('image')){
            $validatedData['image'] = $request->file('image')->store('user-images');
        }

        /** @var \App\Models\MyUserModel $user **/
        $user = Auth::user();
        $user->image = $validatedData['image'];
        $user->save();
        return redirect()->back()->with('success', 'Profile picture has been changed!');
    }
}
