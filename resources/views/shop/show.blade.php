<x-public-layout>
    <div class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <!-- Image -->
                <div class="bg-gray-50 overflow-hidden shadow-2xl p-4">
                    <img src="{{ Str::startsWith($product->image, 'products/') ? asset('storage/' . $product->image) : asset('images/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-auto object-cover border border-gray-100">
                </div>

                <!-- Details -->
                <div class="flex flex-col justify-center">
                    <div class="mb-8">
                        <span class="text-gold-600 uppercase tracking-[0.3em] text-sm font-black">Exquisite Collection</span>
                        <h1 class="text-5xl md:text-6xl font-serif font-black text-charcoal-900 mt-4 mb-6 leading-tight">{{ $product->name }}</h1>
                        <p class="text-4xl font-black text-gold-600">LKR {{ number_format($product->price, 2) }}</p>
                    </div>
                    
                    <div class="prose prose-xl text-gray-700 mb-10 border-t-2 border-b-2 border-gold-100 py-8 leading-relaxed italic">
                        <p>{{ $product->description }}</p>
                    </div>
                    <div class="mt-8 flex items-center space-x-4">
                        <a href="{{ route('add.to.cart', $product->id) }}" class="inline-block bg-gold-500 text-white px-10 py-3 text-sm uppercase tracking-widest font-bold hover:bg-charcoal-900 hover:text-white transition duration-300 shadow-lg text-center rounded-sm">
                            Add to Cart
                        </a>
                        <a href="{{ route('wishlist.add', $product->id) }}" class="inline-block bg-white {{ Auth::check() && Auth::user()->isInWishlist($product->id) ? 'text-red-500 border-red-500' : 'text-gold-500 border-gold-500' }} border-2 px-4 py-2.5 hover:bg-gold-500 hover:text-white transition duration-300 shadow-md text-center rounded-sm" title="Add to Wishlist">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="{{ Auth::check() && Auth::user()->isInWishlist($product->id) ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </a>
                    </div>
                    
                    <div class="mt-8 flex items-center space-x-4 text-sm text-gray-500">
                        <div class="flex items-center"><svg class="w-5 h-5 mr-2 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Authentic</div>
                        <div class="flex items-center"><svg class="w-5 h-5 mr-2 text-gold-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Long Lasting</div>
                    </div>
                </div>
            </div>
        <!-- Reviews Section -->
        <div class="mt-20 border-t pt-16">
            <h2 class="text-3xl font-serif font-black text-charcoal-900 mb-10">Customer Reviews</h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- List Reviews -->
                <div class="space-y-8">
                    @forelse($product->reviews as $review)
                    <div class="bg-gray-50 p-6 rounded-lg shadow-sm border border-gray-100">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="font-bold text-charcoal-900">{{ $review->user->name }}</p>
                                <div class="text-gold-500 text-xs mt-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $review->rating ? '★' : '☆' }}
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 italic">{{ $review->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-600 italic leading-relaxed">"{{ $review->comment }}"</p>
                    </div>
                    @empty
                    <p class="text-gray-500 italic">No reviews yet for this masterpiece.</p>
                    @endforelse
                </div>

                <!-- Add Review Form -->
                <div>
                    @auth
                    @php
                        $hasBought = \App\Models\Order::where('user_id', auth()->id())
                            ->where('status', 'completed')
                            ->whereHas('items', function($q) use ($product) {
                                $q->where('product_id', $product->id);
                            })->exists();
                    @endphp

                    @if($hasBought)
                    <div class="bg-charcoal-900 text-white p-8 rounded-lg shadow-xl">
                        <h3 class="text-xl font-bold mb-6 text-gold-500">Share Your Experience</h3>
                        <form action="{{ route('reviews.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <div>
                                <label class="block text-sm font-bold mb-2">Rating</label>
                                <select name="rating" class="w-full bg-charcoal-800 border-none text-white p-3 rounded focus:ring-2 focus:ring-gold-500" style="background-color: #1a1a1a; color: white;">
                                    <option value="5">5 - Divine</option>
                                    <option value="4">4 - Elegant</option>
                                    <option value="3">3 - Pleasant</option>
                                    <option value="2">2 - Subtle</option>
                                    <option value="1">1 - Not for me</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold mb-2">Your Review</label>
                                <textarea name="comment" rows="4" class="w-full bg-charcoal-800 border-none text-white p-3 rounded focus:ring-2 focus:ring-gold-500" placeholder="Write your thoughts..." required style="background-color: #1a1a1a; color: white;"></textarea>
                            </div>
                            <button type="submit" class="w-full bg-gold-500 hover:bg-gold-600 text-white font-bold py-3 px-6 rounded transition duration-300">
                                Submit Review
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="bg-gray-100 p-8 rounded-lg border-2 border-dashed border-gray-300 text-center">
                        <p class="text-gray-600">You can review this product once you have purchased and received it.</p>
                    </div>
                    @endif
                    @else
                    <div class="bg-gray-100 p-8 rounded-lg border-2 border-dashed border-gray-300 text-center">
                        <p class="text-gray-600">Please <a href="{{ route('login') }}" class="text-gold-600 font-bold underline">Login</a> to leave a review.</p>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
