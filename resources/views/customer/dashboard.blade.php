<x-customer-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Profile Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8 flex items-center justify-between border-l-8 border-gold-500">
                <div class="flex items-center gap-6">
                    <div>
                        <h2 class="text-2xl font-black text-charcoal-900">Welcome back, {{ Auth::user()->name }}!</h2>
                        <p class="text-gray-500 italic">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <a href="/user/profile" class="bg-charcoal-900 text-white px-6 py-2 rounded-full font-bold hover:bg-gold-500 transition duration-300">
                    Edit Profile Details
                </a>
            </div>

            <!-- Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Orders -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-blue-500">
                    <div class="text-gray-900 text-sm font-bold uppercase tracking-wider">Orders Completed</div>
                    <div class="text-3xl font-black text-blue-600 mt-2">{{ Auth::user()->orders()->where('status', 'completed')->count() }}</div>
                </div>
                 <!-- Total Spent -->
                 <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-green-500">
                    <div class="text-gray-900 text-sm font-bold uppercase tracking-wider">Total Invested</div>
                    <div class="text-2xl font-black text-green-600 mt-2">LKR {{ number_format($totalSpent, 2) }}</div>
                </div>
                <!-- Wishlist -->
                <a href="{{ route('wishlist') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-red-500 hover:bg-red-50 transition group">
                    <div class="text-gray-900 text-sm font-bold uppercase tracking-wider group-hover:text-red-600 transition">My Favorites</div>
                    <div class="text-3xl font-black text-red-600 mt-2">{{ Auth::user()->wishlists->count() }}</div>
                </a>
                <!-- Messages -->
                <a href="{{ route('user.messages') }}" class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-b-4 border-gold-500 hover:bg-gold-50 transition group">
                    <div class="text-gray-900 text-sm font-bold uppercase tracking-wider group-hover:text-gold-600 transition">Contact History</div>
                    <div class="text-3xl font-black text-gold-600 mt-2">{{ Auth::user()->messages->count() }}</div>
                </a>
            </div>

            <!-- Recent Orders Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Recent Orders</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ordered Date</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentOrders as $order)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">#{{ $order->id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">LKR {{ number_format($order->total_amount, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $order->ordered_date->format('d M Y, h:i A') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('orders.invoice', $order->id) }}" class="text-gold-600 hover:text-gold-900 font-bold">Download PDF</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-customer-layout>
