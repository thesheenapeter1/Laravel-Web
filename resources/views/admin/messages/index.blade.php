<x-admin-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Messages</h2>
                {{-- 🔴 VULNERABLE SEARCH FORM --}}
                <form method="GET" action="{{ route('admin.messages') }}" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search Message Content..." value="{{ request('search') }}" 
                           class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Search</button>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name/Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reply</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($messages as $message)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $message->name }}</div>
                                    <div class="text-gray-500 text-sm">{{ $message->email }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">{{ $message->message }}</div>
                                    @if($message->reply)
                                        <div class="mt-2 text-xs text-indigo-600 bg-indigo-50 p-2 rounded">
                                            <strong>Replied:</strong> {{ $message->reply }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $message->received_date }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(!$message->reply)
                                        <div x-data="{ open: false }">
                                            <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Reply</button>
                                            
                                            <div x-show="open" class="mt-2">
                                                <form action="{{ route('admin.messages.reply', $message->id) }}" method="POST">
                                                    @csrf
                                                    <textarea name="reply" rows="2" class="w-full text-sm border-gray-300 rounded shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Type reply..."></textarea>
                                                    <button type="submit" class="mt-2 text-xs bg-indigo-600 text-white px-2 py-1 rounded">Send</button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-sm">Replied</span>
                                    @endif
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
