<x-customer-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">
                <h2 class="text-3xl font-serif font-black text-charcoal-900 mb-8 border-b pb-4">
                    Review Order #{{ $order->id }}
                </h2>

                <p class="text-gray-600 mb-10 italic">
                    Thank you for your purchase! Please share your thoughts on the products you received.
                </p>

                <div class="space-y-12">
                    @foreach($order->items as $item)
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 shadow-sm flex flex-col md:flex-row gap-8">
                            <!-- Product Info -->
                            <div class="w-full md:w-1/3">
                                <img src="{{ Str::startsWith($item->product->image, 'products/') ? asset('storage/' . $item->product->image) : asset('images/' . $item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="w-full h-48 object-cover rounded-lg shadow-md mb-4">
                                <h3 class="text-xl font-bold text-charcoal-900">{{ $item->product->name }}</h3>
                                <p class="text-gold-600 font-bold">LKR {{ number_format($item->price, 2) }}</p>
                            </div>

                            <!-- Review Form -->
                            <div class="w-full md:w-2/3 bg-charcoal-900 text-white p-6 rounded-lg shadow-lg">
                                <h4 class="text-lg font-bold mb-4 text-gold-500">Your Experience</h4>
                                <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    
                                    <div>
                                        <label class="block text-sm font-bold mb-2">Rating</label>
                                        <select name="rating" class="w-full bg-charcoal-800 border-none text-white p-2 rounded focus:ring-2 focus:ring-gold-500" style="background-color: #1a1a1a; color: white;">
                                            <option value="5">5 Stars - Divine</option>
                                            <option value="4">4 Stars - Elegant</option>
                                            <option value="3">3 Stars - Pleasant</option>
                                            <option value="2">2 Stars - Subtle</option>
                                            <option value="1">1 Star - Not for me</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold mb-2">Comment</label>
                                        <textarea name="comment" rows="3" class="w-full bg-charcoal-800 border-none text-white p-2 rounded focus:ring-2 focus:ring-gold-500" placeholder="What did you love about this scent?" required style="background-color: #1a1a1a; color: white;"></textarea>
                                    </div>

                                    <button type="submit" class="w-full bg-gold-500 hover:bg-gold-600 text-white font-bold py-2 rounded transition duration-300">
                                        Submit Review
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 text-center">
                    <a href="{{ route('my.orders') }}" class="text-charcoal-900 font-bold hover:text-gold-600 underline">
                        Back to My Orders
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-customer-layout>
