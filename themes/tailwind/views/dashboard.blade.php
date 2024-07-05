<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">List of Users</h3>
                    <ul class="space-y-4">
                        @foreach ($users as $user)
                            <li class="flex items-center">
                                {{-- <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-4"> --}}
                                <a href="{{ route('chat', $user) }}"> <span>{{ $user->name }}</span></a>
                            </li>
                            <li><span>{{ $user->email }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
