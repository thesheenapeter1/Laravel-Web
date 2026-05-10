<x-public-layout>
    <div class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-5xl md:text-6xl font-serif font-bold text-gray-900 uppercase">Contact Us</h1>
                <p class="mt-4 text-gray-600">We'd love to hear from you! Get in touch with us.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Info -->
                <div class="bg-white p-8 shadow-sm rounded-lg">
                    <h2 class="text-xl font-bold mb-6 text-gray-900">Get in Touch</h2>
                    
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <span class="text-2xl mr-4">📍</span>
                            <div>
                                <h3 class="font-medium text-gray-900">Address</h3>
                                <p class="text-gray-600">5th Lane, Colombo 03, Sri Lanka</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                             <span class="text-2xl mr-4">📞</span>
                            <div>
                                <h3 class="font-medium text-gray-900">Phone</h3>
                                <p class="text-gray-600">0112345678</p>
                            </div>
                        </div>

                         <div class="flex items-start">
                            <span class="text-2xl mr-4">✉️</span>
                            <div>
                                <h3 class="font-medium text-gray-900">Email</h3>
                                <p class="text-gray-600">info@aurabykiyara.com</p>
                            </div>
                        </div>

                         <div class="flex items-start">
                             <span class="text-2xl mr-4">🕒</span>
                            <div>
                                <h3 class="font-medium text-gray-900">Business Hours</h3>
                                <p class="text-gray-600">Mon - Fri: 9:00 AM - 6:00 PM</p>
                                <p class="text-gray-600">Sat: 9:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <div class="bg-white p-8 shadow-sm rounded-lg">
                    <h2 class="text-xl font-bold mb-6 text-gray-900">Send us a Message</h2>
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        {{-- 
                            CSRF Protection: 
                            This verifies that the person submitting the form is actually 
                            on your website, preventing "Cross-Site Request Forgery".
                        --}}
                        @csrf
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        </div>

                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                            <textarea name="message" id="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required></textarea>
                        </div>

                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-medium">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-public-layout>
