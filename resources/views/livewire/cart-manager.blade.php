<?php

use Livewire\Volt\Component;
use Illuminate\Support\Facades\Session;

new class extends Component {
    public $cart = [];
    public $total = 0;

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $this->cart = session()->get('cart', []);
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = 0;
        foreach ($this->cart as $item) {
            $this->total += $item['price'] * $item['quantity'];
        }
    }

    public function updateQuantity($id, $quantity)
    {
        if ($quantity < 1) return;
        
        $this->cart = session()->get('cart', []);
        if (isset($this->cart[$id])) {
            $this->cart[$id]['quantity'] = $quantity;
            session()->put('cart', $this->cart);
            $this->calculateTotal();
        }
    }

    public function removeItem($id)
    {
        $this->cart = session()->get('cart', []);
        if (isset($this->cart[$id])) {
            unset($this->cart[$id]);
            session()->put('cart', $this->cart);
            $this->calculateTotal();
        }
    }
}; ?>

<div>
    @if(count($cart) > 0)
        <div class="bg-white shadow-xl overflow-hidden sm:rounded-none border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gold-500 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Subtotal</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($cart as $id => $details)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-16 w-16 shadow-md border border-gray-100">
                                        <img class="h-16 w-16 object-cover" src="{{ Str::startsWith($details['image'], 'products/') ? asset('storage/' . $details['image']) : asset('images/' . $details['image']) }}" alt="{{ $details['name'] }}">
                                    </div>
                                    <div class="ml-6">
                                        <div class="text-lg font-serif font-medium text-charcoal-900">{{ $details['name'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-700">LKR {{ number_format($details['price'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="number" 
                                       wire:change="updateQuantity('{{ $id }}', $event.target.value)"
                                       value="{{ $details['quantity'] }}" 
                                       class="w-16 border-gray-300 focus:border-gold-500 focus:ring-gold-500 rounded-none text-center" 
                                       min="1">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gold-600">LKR {{ number_format($details['price'] * $details['quantity'], 2) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="removeItem('{{ $id }}')" class="text-gray-400 hover:text-red-600 transition duration-300 p-2 rounded-full hover:bg-red-50" title="Remove Item">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="p-8 bg-gray-50 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="text-2xl font-serif text-charcoal-900">Total: <span class="font-bold text-gold-600">LKR {{ number_format($total, 2) }}</span></div>
                <div>
                    @auth
                        <a href="{{ route('checkout') }}" class="bg-gold-500 text-white px-10 py-4 hover:bg-charcoal-900 hover:text-white font-bold uppercase tracking-widest transition duration-300 shadow-lg inline-block">
                            Proceed to Checkout
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-gray-800 text-white px-10 py-4 hover:bg-gold-500 hover:text-white font-bold uppercase tracking-widest transition duration-300 shadow-lg inline-block">
                            Login to Checkout
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-24 bg-white shadow-lg border border-gray-100">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
            <p class="text-charcoal-900 text-xl font-serif mb-6">Your shopping cart is currently empty.</p>
            <a href="{{ route('shop') }}" class="inline-block bg-gold-500 text-white px-8 py-3 uppercase tracking-widest font-bold hover:bg-charcoal-900 transition duration-300 shadow-md">Continue Shopping</a>
        </div>
    @endif
</div>
