<?php

use App\Http\Controllers\BasketController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderAdminController;
use App\Http\Controllers\OrderUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Models\Menu;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth');

Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest');

Route::middleware('auth')->group(function() {
    Route::get('/', function () {
        return view('index', [
            'title' => 'Home',
            'menus' => Menu::orderBy('name')->paginate(8)
        ]);
    });
    
    Route::get('/profile', [UserController::class, 'index']);
    Route::put('/profile/email', [UserController::class, 'updateEmail']);
    Route::put('/profile/pw', [UserController::class, 'updatePassword']);
    Route::put('/profile/image', [UserController::class, 'uploadImage']);
    
    Route::resource('/baskets', BasketController::class)->except('create', 'show', 'edit','destroy', 'update');
    Route::delete('/basket/{menu:slug}', [BasketController::class, 'detach']);
    Route::post('/basket/{menu:slug}', [BasketController::class, 'update']);
    Route::delete('/baskets', [BasketController::class, 'delete']);
    
    Route::get('/order', [OrderUserController::class, 'index']);
    Route::post('/order', [OrderUserController::class, 'store']);
    
    Route::get('/history', [HistoryController::class, 'index']);

    Route::middleware('admin')->group(function() {
        Route::get('/menus/checkSlug', [MenuController::class, 'checkSlug']);
        Route::resource('/menus', MenuController::class)->except('index', 'create', 'show');
        Route::patch('/menus/{menu:slug}/is-available', [MenuController::class, 'isAvailable']);

        Route::get('/adminOrder', [OrderAdminController::class, 'index']);
        Route::patch('/order-accept/{order:id}', [OrderAdminController::class, 'accept']);
        Route::patch('/order-cancel/{order:id}', [OrderAdminController::class, 'cancel']);
        Route::patch('/order-done/{order:id}', [OrderAdminController::class, 'complete']);
    });
});

