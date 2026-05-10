<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    public function downloadInvoice($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        // Ensure user only downloads their own invoice, or is an Admin
        if ((int) $order->user_id !== (int) Auth::id() && (int) Auth::user()->role !== 1) {
            abort(403);
        }

        $pdf = Pdf::loadView('orders.invoice', compact('order'));
        return $pdf->download('invoice-'.$order->id.'.pdf');
    }

    public function reviewOrder($id)
    {
        $order = Order::with('items.product')->findOrFail($id);
        
        if ($order->user_id !== Auth::id() || $order->status !== 'completed') {
            abort(403);
        }

        return view('customer.reviews.create', compact('order'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // FOR TESTING: Simulate Admin if unthenticated
        if (!$user) {
            $user = new \stdClass();
            $user->role = 1; // Simulate Admin
            $user->id = 1;
        }

        if ($user->role == 1) {
            // Admin: All orders with search
            $query = Order::with('user');

            if ($request->has('search') && $request->search != '') {
                $search = $request->get('search');
                // Exact match for Order ID, but partial match for customer details
                $query->where(function($q) use ($search) {
                    $q->where('id', $search)
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%")
                      ->orWhere('total_amount', 'like', "%{$search}%");
                });
            }

            $orders = $query->latest('ordered_date')->get();
            return view('admin.orders.index', compact('orders'));
        } else {
            // Customer: Dashboard (Recent Orders + Stats)
            $orders = Order::where('user_id', $user->id)->latest('ordered_date')->get();
            $totalOrders = $orders->count();
            $totalSpent = $orders->sum('total_amount');
            $recentOrders = $orders->take(5);
            return view('customer.dashboard', compact('totalOrders', 'totalSpent', 'recentOrders'));
        }
    }

     
    public function show($id)
    {
         
        $order = Order::findOrFail($id); 
        
        // [SECURITY: SQL INJECTION PROTECTION]
        // UNSAFE EXAMPLE: $orderItems = DB::select("SELECT * FROM order_items WHERE order_id = $id");
        // PROTECTED VERSION: Using Eloquent ORM which uses PDO parameter binding securely.
        $orderItems = \App\Models\OrderItem::where('order_id', $id)->get();
        return view('admin.orders.show', compact('order', 'orderItems'));
    }

    public function myOrders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)->latest('ordered_date')->get();
        $totalOrders = $orders->count();
        return view('customer.orders', compact('orders', 'totalOrders'));
    }

    public function checkout()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $details) {
            $total += $details['price'] * $details['quantity'];
        }
        $user = Auth::user();
        return view('checkout', compact('cart', 'total', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Checkout logic (from Cart)
        $cart = session()->get('cart', []);
        
        if(empty($cart)) {
            return redirect()->route('cart')->with('error', 'Cart is empty');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'payment_method' => 'required|in:cod,card',
            'card_number' => 'required_if:payment_method,card|nullable|regex:/^[0-9]{16}$/',
            'expiry' => [
                'required_if:payment_method,card',
                'nullable',
                'regex:/^[0-9]{2}\/[0-9]{2}$/',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->payment_method === 'card' && $value) {
                        [$month, $year] = explode('/', $value);
                        $now = now();
                        $currentMonth = $now->month;
                        $currentYear = (int) $now->format('y');

                        if ((int)$year < $currentYear || ((int)$year === $currentYear && (int)$month < $currentMonth)) {
                            $fail('The card has expired.');
                        }
                        if ((int)$month < 1 || (int)$month > 12) {
                            $fail('Invalid month.');
                        }
                    }
                },
            ],
            'cvv' => 'required_if:payment_method,card|nullable|regex:/^[0-9]{3}$/',
        ]);

        $totalAmount = 0;
        foreach($cart as $id => $details) {
            $totalAmount += $details['price'] * $details['quantity'];
        }

        $deliveryFee = 0;
        if ($request->payment_method === 'cod') {
            $deliveryFee = 300;
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $totalAmount + $deliveryFee,
            'delivery_fee' => $deliveryFee,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'ordered_date' => now(),
        ]);

        foreach($cart as $id => $details) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $id,
                'quantity' => $details['quantity'],
                'price' => $details['price'],
            ]);
        }
        
        // Clear cart
        session()->forget('cart');

        // Send Email Notification
        try {
            $user = Auth::user();
            $paymentInfo = $request->payment_method === 'cod' ? 'Cash on Delivery (LKR 300 Fee Applied)' : 'Paid by Card';
            \Illuminate\Support\Facades\Mail::raw("New Order Placed!\n\nOrder ID: #{$order->id}\nCustomer: {$request->customer_name}\nPhone: {$request->customer_phone}\nAddress: {$request->customer_address}\nPayment Method: {$paymentInfo}\nTotal Amount: LKR " . number_format($totalAmount + $deliveryFee, 2), function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('New Order Placed - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Order placed successfully!');
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        // Admin updates status
        if (Auth::user()->role == 1) {
            $request->validate([
                'status' => 'required|in:pending,cancelled,completed'
            ]);
            $order->update(['status' => $request->status]);

            // Send Email Notification
            try {
                \Illuminate\Support\Facades\Mail::raw("Order Status Updated!\n\nOrder ID: #{$order->id}\nNew Status: " . ucfirst($request->status), function ($mail) {
                    $mail->to('thesheenapeter2006@gmail.com')
                         ->subject('Order Status Updated - Aura by Kiyara');
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
            }

            return redirect()->back()->with('success', 'Order status updated.');
        } 
        
        // Customer updates details (Address/Phone) if Pending
        if (Auth::user()->id == $order->user_id && $order->status == 'pending') {
            $request->validate([
                'delivery_address' => 'required|string',
                'phone' => 'required|regex:/^0[0-9]{9}$/',
            ]);
            
            $user = Auth::user();
            $user->update([
                'address' => $request->delivery_address, 
                'phone' => $request->phone
            ]);

            // Send Email Notification
            try {
                \Illuminate\Support\Facades\Mail::raw("Order Delivery Details Updated!\n\nOrder ID: #{$order->id}\nCustomer: {$user->name} ({$user->email})\nNew Address: {$request->delivery_address}\nNew Phone: {$request->phone}", function ($mail) {
                    $mail->to('thesheenapeter2006@gmail.com')
                         ->subject('Order Details Updated - Aura by Kiyara');
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
            }
            
            return redirect()->back()->with('success', 'Order details updated.');
        }

        return abort(403);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Order::findOrFail($id);

        // Ensure the order belongs to the authenticated user
        if (Auth::id() !== $order->user_id) {
            return abort(403, 'Unauthorized action.');
        }

        // Ensure the order status is pending
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'You cannot delete an order that is not pending.');
        }

        // Delete order items
        DB::table('order_items')->where('order_id', $order->id)->delete();

        // Delete the order
        $orderId = $order->id;
        $order->delete();

        // Send Email Notification
        try {
            $user = Auth::user();
            \Illuminate\Support\Facades\Mail::raw("Order Deleted!\n\nOrder ID: #{$orderId}\nCustomer: {$user->name} ({$user->email})", function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('Order Deleted - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'Order deleted successfully!');
    }

    public function adminCreate()
    {
        $products = Product::all();
        return view('admin.orders.create', compact('products'));
    }

    public function adminStore(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'payment_method' => 'required|in:cod,card',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalAmount = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            $totalAmount += $product->price * $item['quantity'];
            $orderItemsData[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ];
        }

        $deliveryFee = ($request->payment_method === 'cod') ? 300 : 0;

        $order = Order::create([
            'user_id' => Auth::id(), // Admin ID
            'total_amount' => $totalAmount + $deliveryFee,
            'delivery_fee' => $deliveryFee,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'customer_address' => $request->customer_address,
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'ordered_date' => now(),
        ]);

        foreach ($orderItemsData as $itemData) {
            $itemData['order_id'] = $order->id;
            OrderItem::create($itemData);
        }

        // Send Email Notification
        try {
            $paymentInfo = $request->payment_method === 'cod' ? 'Cash on Delivery (LKR 300 Fee Applied)' : 'Paid by Card';
            \Illuminate\Support\Facades\Mail::raw("New Manual Order Created by Admin!\n\nOrder ID: #{$order->id}\nCustomer: {$request->customer_name}\nEmail: {$request->customer_email}\nPhone: {$request->customer_phone}\nAddress: {$request->customer_address}\nPayment Method: {$paymentInfo}\nTotal Amount: LKR " . number_format($totalAmount + $deliveryFee, 2), function ($mail) {
                $mail->to('thesheenapeter2006@gmail.com')
                     ->subject('New Manual Order - Aura by Kiyara');
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Mail sending failed: ' . $e->getMessage());
        }

        return redirect()->route('admin.orders.index')->with('success', 'Manual order created successfully!');
    }
}
