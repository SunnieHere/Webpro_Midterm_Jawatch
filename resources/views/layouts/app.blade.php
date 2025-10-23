<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'JaWatch - Video Platform')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#121212]">
    <nav class="bg-[#080808] shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-white">JaWatch</a>
                </div>
                
                <form action="{{ route('videos.search') }}" method="GET" class="flex items-center space-x-2">
                    <input 
                        type="text" 
                        name="query" 
                        placeholder="Search videos..." 
                        value="{{ request('query') }}"
                        class="bg-[#1e1e1e] text-white px-3 py-1 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700">
                        Search
                    </button>
                </form>

                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('videos.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            Upload Video
                        </a>
                        <a href="{{ route('profile.show') }}" class="flex items-center space-x-2">
                            @if(auth()->user()->profile_photo)
                                <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                                    alt="Profile" class="w-8 h-8 rounded-full">
                            @else
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-gray-600 font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <span class="text-white">{{ auth()->user()->username }}</span>
                        </a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-white hover:text-red-500 transition-colors duration-200">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-white hover:text-red-500 transition-colors duration-200">Login</a>
                        <a href="{{ route('register') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            Sign Up
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <main class="py-8">
        @yield('content')
    </main>
</body>
</html>