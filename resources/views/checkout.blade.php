<x-public-layout>
    <div class="py-16 bg-soft-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl md:text-6xl font-serif font-bold mb-10 text-charcoal-900 border-b border-gold-200 pb-4">Checkout</h1>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">
                <!-- Checkout Form -->
                <div class="bg-white p-8 shadow-xl border border-gray-100">
                    <h2 class="text-2xl font-serif font-bold text-charcoal-900 mb-8 uppercase tracking-widest">Billing Details</h2>
                    
                    <form action="{{ route('place.order') }}" method="POST" id="checkout-form">
                        @csrf
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Full Name</label>
                                <input type="text" name="customer_name" value="{{ $user->name }}" required class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Contact Number</label>
                                <input type="text" name="customer_phone" value="{{ $user->phone }}" required class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3" placeholder="e.g. 0771234567">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Delivery Address</label>
                                <textarea name="customer_address" rows="3" required class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3">{{ $user->address }}</textarea>
                            </div>

                            <div class="pt-6 border-t border-gray-100">
                                <h3 class="text-lg font-serif font-bold text-charcoal-900 mb-4 uppercase tracking-widest">Payment Method</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="cod" id="cod" checked class="text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-300">
                                        <label for="cod" class="ml-3 block text-sm font-medium text-gray-700">Cash on Delivery (COD)</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="radio" name="payment_method" value="card" id="card" class="text-gold-500 focus:ring-gold-500 h-4 w-4 border-gray-300">
                                        <label for="card" class="ml-3 block text-sm font-medium text-gray-700">Pay by Card</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Details (Hidden by default) -->
                            <div id="card-details" class="hidden space-y-4 pt-4 animate-fade-in">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Card Number</label>
                                    <input type="text" name="card_number" class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3" placeholder="XXXX XXXX XXXX XXXX">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">Expiry Date</label>
                                        <input type="text" name="expiry" class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3" placeholder="MM/YY">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 uppercase tracking-wider mb-2">CVV</label>
                                        <input type="text" name="cvv" class="w-full border-gray-300 focus:border-gold-500 focus:ring-gold-500 p-3" placeholder="XXX">
                                    </div>
                                </div>
                            </div>

                            <!-- COD Message -->
                            <div id="cod-message" class="bg-gold-50 p-4 border-l-4 border-gold-500 text-gold-700 font-bold">
                                <p>RS 300 will be added to your total bill as a delivery fee for Cash on Delivery.</p>
                            </div>

                            <div class="pt-8">
                                <button type="submit" class="w-full bg-charcoal-900 text-white px-10 py-4 hover:bg-gold-500 transition duration-300 font-bold uppercase tracking-widest shadow-xl">
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 p-8 h-fit shadow-md">
                    <h2 class="text-2xl font-serif font-bold text-charcoal-900 mb-8 uppercase tracking-widest border-b border-gray-200 pb-4">Order Summary</h2>
                    
                    <div class="space-y-4 mb-8">
                        @foreach($cart as $details)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 border border-gray-200 rounded-sm overflow-hidden mr-4">
                                        <img class="h-full w-full object-cover" src="{{ Str::startsWith($details['image'], 'products/') ? asset('storage/' . $details['image']) : asset('images/' . $details['image']) }}" alt="{{ $details['name'] }}">
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $details['name'] }} x {{ $details['quantity'] }}</span>
                                </div>
                                <span class="font-bold text-charcoal-900 text-sm">LKR {{ number_format($details['price'] * $details['quantity'], 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-4 border-t border-gray-200 pt-6">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Subtotal</span>
                            <span class="font-bold">LKR {{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600" id="summary-delivery">
                            <span>Delivery Fee</span>
                            <span class="font-bold text-gold-600">LKR 300.00</span>
                        </div>
                        <div class="flex justify-between items-center text-2xl font-serif font-bold text-charcoal-900 pt-4 border-t border-gray-200">
                            <span>Total</span>
                            <span class="text-gold-600" id="final-total">LKR {{ number_format($total + 300, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const codRadio = document.getElementById('cod');
        const cardRadio = document.getElementById('card');
        const cardDetails = document.getElementById('card-details');
        const codMessage = document.getElementById('cod-message');
        const deliveryFeeSpan = document.querySelector('#summary-delivery span:last-child');
        const finalTotalSpan = document.getElementById('final-total');
        
        const cardNumberInput = document.querySelector('input[name="card_number"]');
        const expiryInput = document.querySelector('input[name="expiry"]');
        const cvvInput = document.querySelector('input[name="cvv"]');
        const checkoutForm = document.getElementById('checkout-form');

        const subtotal = {{ $total }};

        function updateDisplay() {
            if (codRadio.checked) {
                cardDetails.classList.add('hidden');
                codMessage.classList.remove('hidden');
                deliveryFeeSpan.textContent = 'LKR 300.00';
                finalTotalSpan.textContent = 'LKR ' + (subtotal + 300).toLocaleString(undefined, {minimumFractionDigits: 2});
                
                // Clear validation for hidden fields
                cardNumberInput.setCustomValidity('');
                expiryInput.setCustomValidity('');
                cvvInput.setCustomValidity('');
            } else {
                cardDetails.classList.remove('hidden');
                codMessage.classList.add('hidden');
                deliveryFeeSpan.textContent = 'LKR 0.00';
                finalTotalSpan.textContent = 'LKR ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
            }
        }

        // Auto-slash for Expiry MM/YY
        expiryInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            e.target.value = value;
        });

        // Form Validation logic
        checkoutForm.addEventListener('submit', function(e) {
            if (cardRadio.checked) {
                let hasError = false;

                // Card Number: 16 digits
                const cardNum = cardNumberInput.value.replace(/\D/g, '');
                if (cardNum.length !== 16) {
                    alert('Card number must be exactly 16 digits.');
                    hasError = true;
                }

                // CVV: 3 digits
                const cvv = cvvInput.value.replace(/\D/g, '');
                if (cvv.length !== 3) {
                    alert('CVV must be exactly 3 digits.');
                    hasError = true;
                }

                // Expiry Check: MM/YY
                const expiry = expiryInput.value;
                if (expiry.length === 5) {
                    const [month, year] = expiry.split('/').map(n => parseInt(n));
                    const now = new Date();
                    const currentMonth = now.getMonth() + 1;
                    const currentYear = parseInt(now.getFullYear().toString().slice(-2));

                    if (year < currentYear || (year === currentYear && month < currentMonth)) {
                        alert('The card has expired. Please use a valid card.');
                        hasError = true;
                    }
                    
                    if (month < 1 || month > 12) {
                        alert('Invalid month in expiry date.');
                        hasError = true;
                    }
                } else {
                    alert('Expiry date must be in MM/YY format.');
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                }
            }
        });

        codRadio.addEventListener('change', updateDisplay);
        cardRadio.addEventListener('change', updateDisplay);
        
        // Initialize
        updateDisplay();
    </script>
</x-public-layout>
