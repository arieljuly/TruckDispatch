<div>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Notifications</h1>
                    <p class="mt-1 text-sm text-gray-600">View and manage your notifications</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    @if($unreadCount > 0)
                        <button wire:click="markAllAsRead"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Mark All as Read
                        </button>
                    @endif
                </div>
            </div>

            @if (session()->has('message'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded-md">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('message') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Notifications List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-medium text-gray-900">Your Notifications</h3>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <div class="p-6 hover:bg-gray-50 transition-colors duration-150 {{ !$notification->is_read ? 'bg-blue-50/30' : '' }}">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            @if(!$notification->is_read)
                                                <span class="inline-flex h-2 w-2 rounded-full bg-blue-600"></span>
                                            @endif
                                            <h3 class="text-md font-semibold text-gray-900 {{ !$notification->is_read ? 'text-blue-900' : '' }}">
                                                {{ $notification->title }}
                                            </h3>
                                        </div>
                                        <p class="text-gray-600 mt-2">{{ $notification->message }}</p>
                                        <div class="flex items-center gap-4 mt-3">
                                            <p class="text-sm text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </p>
                                            @if(!$notification->is_read)
                                                <button wire:click="markAsRead('{{ $notification->id }}')"
                                                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                                    Mark as read
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <p class="text-gray-500 mt-2">No notifications yet.</p>
                            <p class="text-sm text-gray-400 mt-1">When you receive notifications, they will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>