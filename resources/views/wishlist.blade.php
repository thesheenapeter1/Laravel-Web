<x-public-layout>
    <div class="py-16 bg-soft-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-10 text-charcoal-900 border-b border-gold-200 pb-4">My Wishlist</h1>

            @if($wishlists->count() > 0)
                <div class="bg-white shadow-xl overflow-hidden sm:rounded-none border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gold-500 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Product</th>
                                <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider">Price</th>
                                <th class="px-6 py-4 text-right text-xs font-bold uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($wishlists as $wishlist)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-16 w-16 shadow-md border border-gray-100">
                                                <img class="h-16 w-16 object-cover" src="{{ Str::startsWith($wishlist->product->image, 'products/') ? asset('storage/' . $wishlist->product->image) : asset('images/' . $wishlist->product->image) }}" alt="{{ $wishlist->product->name }}">
                                            </div>
                                            <div class="ml-6">
                                                <div class="text-lg font-serif font-medium text-charcoal-900">{{ $wishlist->product->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-700">LKR {{ number_format($wishlist->product->price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end items-center space-x-4">
                                            <a href="{{ route('add.to.cart', $wishlist->product->id) }}" class="bg-gold-500 text-white px-4 py-2 hover:bg-charcoal-900 transition duration-300 font-bold uppercase text-xs tracking-widest shadow-md">
                                                Add to Cart
                                            </a>
                                            <form action="{{ route('wishlist.remove', $wishlist->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-gray-400 hover:text-red-600 transition duration-300 p-2 rounded-full hover:bg-red-50" title="Remove from Wishlist">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-24 bg-white shadow-lg border border-gray-100">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <p class="text-charcoal-900 text-xl font-serif mb-6">Your wishlist is currently empty.</p>
                    <a href="{{ route('shop') }}" class="inline-block bg-gold-500 text-white px-8 py-3 uppercase tracking-widest font-bold hover:bg-charcoal-900 transition duration-300 shadow-md">Explore Products</a>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
