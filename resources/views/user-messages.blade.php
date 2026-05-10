<x-public-layout>
    <div class="py-16 bg-soft-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-10 border-b border-gold-200 pb-4">
                <h1 class="text-5xl md:text-6xl font-serif font-bold text-charcoal-900">My Messages</h1>
                <a href="{{ route('contact') }}" class="bg-charcoal-900 text-white px-6 py-2 hover:bg-gold-500 transition duration-300 font-bold uppercase text-xs tracking-widest shadow-md">
                    Send New Message
                </a>
            </div>

            @if($messages->count() > 0)
                <div class="space-y-8">
                    @foreach($messages as $message)
                        <div class="bg-white shadow-xl border border-gray-100 overflow-hidden">
                            <div class="p-6 md:p-8">
                                <div class="flex justify-between items-start mb-6">
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-widest text-gold-600 mb-2 block">Sent on {{ $message->created_at->format('M d, Y - H:i') }}</span>
                                        <p class="text-xl font-serif text-charcoal-900 leading-relaxed">{{ $message->message }}</p>
                                    </div>
                                    <div class="bg-gold-50 px-3 py-1 border border-gold-200">
                                        <span class="text-[10px] font-bold uppercase tracking-tighter text-gold-700">Message ID: #{{ $message->id }}</span>
                                    </div>
                                </div>

                                @if($message->reply)
                                    <div class="mt-8 pt-8 border-t border-gray-100 pl-6 md:pl-10 border-l-4 border-gold-400 bg-gray-50 p-6">
                                        <div class="flex items-center mb-4">
                                            <div class="w-8 h-8 bg-gold-500 rounded-full flex items-center justify-center mr-3">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l5 5m-5-5l5-5"></path></svg>
                                            </div>
                                            <span class="text-sm font-bold uppercase tracking-widest text-charcoal-900">Admin Response</span>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed italic">"{{ $message->reply }}"</p>
                                    </div>
                                @else
                                    <div class="mt-6 flex items-center text-gray-400">
                                        <svg class="w-4 h-4 mr-2 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span class="text-xs font-medium uppercase tracking-wider">Awaiting response from our team...</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-24 bg-white shadow-lg border border-gray-100">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <p class="text-charcoal-900 text-xl font-serif mb-6">You haven't sent any messages yet.</p>
                    <a href="{{ route('contact') }}" class="inline-block bg-gold-500 text-white px-8 py-3 uppercase tracking-widest font-bold hover:bg-charcoal-900 transition duration-300 shadow-md">Contact Us</a>
                </div>
            @endif
        </div>
    </div>
</x-public-layout>
