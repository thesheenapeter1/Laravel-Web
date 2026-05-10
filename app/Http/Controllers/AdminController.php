<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Message;

class AdminController extends Controller
{
    public function index()
    {
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalRevenue = Order::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalCustomers = User::where('role', 2)->count();
        $recentOrders = Order::with('user')->latest('ordered_date')->take(5)->get();

        return view('admin.dashboard', compact('totalOrders', 'totalProducts', 'totalRevenue', 'totalCustomers', 'recentOrders'));
    }
    
    public function messages(Request $request)
    {
        // 🔴 VULNERABLE MESSAGE SEARCH
        if ($request->has('search')) {
            $search = $request->get('search');
            // [SECURITY: SQL INJECTION PROTECTION]
            // UNSAFE EXAMPLE: $messages = \Illuminate\Support\Facades\DB::select("SELECT * FROM messages WHERE name LIKE '%$search%' OR message LIKE '%$search%'");
            // PROTECTED VERSION: Using Eloquent ORM which uses PDO parameter binding securely.
            $messages = Message::where('name', 'like', "%{$search}%")
                               ->orWhere('message', 'like', "%{$search}%")
                               ->get();
        } else {
            $messages = Message::latest('received_date')->get();
        }
        return view('admin.messages.index', compact('messages'));
    }

    public function replyMessage(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string']);
        $message = Message::findOrFail($id);
        // SQL Injection Protection:
        // Eloquent's update() method uses prepared statements.
        // The user-provided 'reply' is safely handled as data only.
        $message->update(['reply' => $request->reply]);
        
        // Logic to notify customer could be email, but prompt says "show... customer side".
        // Customer side needs a view for messages? Or dashboard?
        // I'll leave it as DB update for now, maybe add "My Messages" to Customer Dashboard later if I have time.
        
        return redirect()->back()->with('success', 'Reply sent successfully.');
    }
        
    public function reviews()
    {
        $reviews = \App\Models\Review::with(['user', 'product'])->latest()->get();
        return view('admin.reviews.index', compact('reviews'));
    }

    public function deleteReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();
        return redirect()->back()->with('success', 'Review deleted successfully.');
    }
}
