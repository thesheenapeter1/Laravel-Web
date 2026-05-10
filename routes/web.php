<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\HomeController;

// Global Security & Experience Middlewares
// 'security.headers': Applied to all web routes to add premium safety headers.
Route::middleware(['security.headers'])->group(function () {

// Public Routes

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/shop', [ProductController::class, 'shop'])->name('shop');
Route::get('/shop/{id}', [ProductController::class, 'show'])->name('shop.show');

Route::view('/about', 'about')->name('about');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');

// Prevent Spam (Security): 
// We use 'throttle:5,1' which means "allow only 5 attempts per 1 minute".
// This prevents bots from flooding your inbox with automated messages.
Route::post('/contact/send', [ContactController::class, 'sendMessage'])
    ->middleware('throttle:5,1')
    ->name('contact.send');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::patch('/update-cart', [CartController::class, 'update'])->name('update.cart');
Route::delete('/remove-from-cart', [CartController::class, 'remove'])->name('remove.from.cart');


// Customer Routes
Route::middleware(['auth', 'verified', 'role:2', 'no_back_history'])->group(function () {
    Route::get('/dashboard', [OrderController::class, 'index'])->name('dashboard');
    Route::get('/checkout', [OrderController::class, 'checkout'])
        ->middleware('cart.not_empty')
        ->name('checkout');
    Route::post('/place-order', [OrderController::class, 'store'])
        ->middleware('cart.not_empty')
        ->name('place.order');
    Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('my.orders');
    Route::get('/orders/{id}/review', [OrderController::class, 'reviewOrder'])->name('orders.review');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin', 'no_back_history'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/messages', [AdminController::class, 'messages'])->name('messages');
    Route::post('/messages/{id}/reply', [AdminController::class, 'replyMessage'])->name('messages.reply');
    
    // Products CRUD
    Route::resource('products', ProductController::class);
    
    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [OrderController::class, 'adminCreate'])->name('orders.create');
    Route::post('/orders/store', [OrderController::class, 'adminStore'])->name('orders.store');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');

    // Reviews
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews.index');
    Route::delete('/reviews/{id}', [AdminController::class, 'deleteReview'])->name('reviews.delete');
});

/*
// Original Secure Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    // ...
});
*/

// Profile & User Features
Route::middleware('auth')->group(function () {
    Route::get('/orders/{id}/invoice', [OrderController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::get('/add-to-cart/{id}', [CartController::class, 'addToCart'])->name('add.to.cart');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::get('/wishlist/add/{product}', [WishlistController::class, 'add'])->name('wishlist.add');
    Route::delete('/wishlist/remove/{wishlist}', [WishlistController::class, 'remove'])->name('wishlist.remove');

    // Messaging
    Route::get('/my-messages', [ContactController::class, 'userMessages'])->name('user.messages');

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

}); // End of Security Headers Group

