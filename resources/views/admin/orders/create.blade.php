<x-admin-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-8 uppercase tracking-widest">Create Manual Order</h2>

            <div class="bg-white shadow-xl rounded-lg overflow-hidden border border-gray-100">
                <form action="{{ route('admin.orders.store') }}" method="POST" class="p-8 space-y-8">
                    @csrf
                    
                    <!-- Customer Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <h3 class="text-lg font-serif font-bold text-indigo-600 border-b border-indigo-100 pb-2 mb-4">Customer Details</h3>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Customer Name</label>
                            <input type="text" name="customer_name" required class="w-full border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Customer Email</label>
                            <input type="email" name="customer_email" class="w-full border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500" placeholder="customer@example.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Contact Number</label>
                            <input type="text" name="customer_phone" required class="w-full border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500" placeholder="e.g. 0771234567">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Delivery Address</label>
                            <textarea name="customer_address" rows="3" required class="w-full border-gray-300 rounded focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-serif font-bold text-indigo-600 border-b border-indigo-100 pb-2 mb-4">Products</h3>
                        <div id="product-items" class="space-y-4">
                            <div class="flex gap-4 items-end bg-gray-50 p-4 rounded border border-gray-200 product-row">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Select Product</label>
                                    <select name="items[0][product_id]" required class="w-full border-gray-300 rounded text-sm">
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (LKR {{ number_format($product->price, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="w-32">
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quantity</label>
                                    <input type="number" name="items[0][quantity]" value="1" min="1" required class="w-full border-gray-300 rounded text-sm">
                                </div>
                                <button type="button" class="bg-red-50 text-red-600 p-2 rounded hover:bg-red-100 remove-product invisible">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                        </div>
                        <button type="button" id="add-product" class="mt-4 text-indigo-600 font-bold text-sm flex items-center hover:text-indigo-800">
                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Add Another Product
                        </button>
                    </div>

                    <!-- Payment Method -->
                    <div class="pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-serif font-bold text-indigo-600 border-b border-indigo-100 pb-2 mb-4">Payment</h3>
                        <div class="flex gap-8">
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="cod" id="cod" checked class="text-indigo-600 focus:ring-indigo-500">
                                <label for="cod" class="ml-2 text-sm font-medium text-gray-700">Cash on Delivery (LKR 300 Fee)</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" name="payment_method" value="card" id="card" class="text-indigo-600 focus:ring-indigo-500">
                                <label for="card" class="ml-2 text-sm font-medium text-gray-700">Card Payment (LKR 0 Fee)</label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8">
                        <button type="submit" class="w-full bg-indigo-600 text-white px-8 py-4 rounded-lg font-bold uppercase tracking-widest hover:bg-indigo-700 transition shadow-lg">
                            Finalize and Place Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let productCount = 1;
        const addBtn = document.getElementById('add-product');
        const container = document.getElementById('product-items');

        addBtn.addEventListener('click', () => {
            const newRow = container.querySelector('.product-row').cloneNode(true);
            
            // Reset values and update indices
            const select = newRow.querySelector('select');
            const input = newRow.querySelector('input');
            const removeBtn = newRow.querySelector('.remove-product');
            
            select.name = `items[${productCount}][product_id]`;
            input.name = `items[${productCount}][quantity]`;
            input.value = 1;
            
            removeBtn.classList.remove('invisible');
            removeBtn.addEventListener('click', () => newRow.remove());
            
            container.appendChild(newRow);
            productCount++;
        });
    </script>
</x-admin-layout>
