<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        // Check if user has bought this product and order is completed
        $hasBought = Order::where('user_id', Auth::id())
            ->where('status', 'completed')
            ->whereHas('items', function($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })->exists();

        if (!$hasBought) {
            return back()->with('error', 'You can only review products you have purchased and received.');
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Send Email Notification to Admin
        try {
            $user = Auth::user();
            $product = \App\Models\Product::find($request->product_id);
            $stars = str_repeat('★', $request->rating) . str_repeat('☆', 5 - $request->rating);
            
            \Illuminate\Support\Facades\Mail::raw("New Product Review Received!\n\nProduct: {$product->name}\nCustomer: {$user->name} ({$user->email})\nRating: {$stars} ({$request->rating}/5)\nComment: {$request->comment}", function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('New Product Review - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Review Mail failed: ' . $e->getMessage());
        }

        return back()->with('success', 'Thank you for your review!');
    }
}
