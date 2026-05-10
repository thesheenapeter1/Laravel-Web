<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-800">Manage Orders</h2>
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <form method="GET" action="{{ route('admin.orders.index') }}" class="flex-1 md:flex-none">
                        <div class="relative">
                            <input type="text" name="search" placeholder="Search Order ID, Name, Phone..." value="{{ request('search') }}" 
                                   class="w-full md:w-64 pl-4 pr-4 py-2 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                    </form>
                    <a href="{{ route('admin.orders.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition shadow-md whitespace-nowrap">Create Manual Order</a>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">#{{ $order->id }}</td>
                                {{-- 
                                     Note: When using vulnerable raw SQL (DB::select), relations like ->user won't be loaded automatically.
                                     We must handle the null case to prevent the view from crashing during the demo.
                                --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $order->customer_name ?? optional($order->user)->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $order->customer_phone ?? optional($order->user)->phone ?? 'N/A' }}<br>
                                    <span class="text-xs text-gray-400">{{ optional($order->user)->email ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ Str::limit($order->customer_address ?? optional($order->user)->address ?? 'N/A', 30) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">LKR {{ number_format($order->total_amount, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH') <!-- OrderController uses update method. Resource usually uses PUT/PATCH. -->
                                        <!-- Wait, web.php defines Route::patch, but route name 'orders.update' -->
                                        <select name="status" onchange="this.form.submit()" class="text-sm rounded border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 
                                            {{ $order->status === 'completed' ? 'bg-green-50 text-green-800' : '' }}
                                            {{ $order->status === 'pending' ? 'bg-yellow-50 text-yellow-800' : '' }}
                                            {{ $order->status === 'cancelled' ? 'bg-red-50 text-red-800' : '' }}
                                        ">
                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $order->ordered_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('orders.invoice', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 font-bold">PDF</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
