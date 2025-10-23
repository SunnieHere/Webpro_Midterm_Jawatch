@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-[#080808] rounded-lg shadow-md p-6 mb-6 flex flex-col sm:flex-row items-center sm:items-start sm:space-x-6">
        {{-- Profile Photo --}}
        @if(auth()->user()->profile_photo)
            <img src="{{ auth()->user()->profile_photo }}" alt="Profile Photo" class="rounded-full w-32 h-32 object-cover">
        @else
            <div class="w-32 h-32 bg-gray-600 rounded-full flex items-center justify-center">
                <span class="text-4xl font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </div>
        @endif

        {{-- Profile Info --}}
        <div class="mt-4 sm:mt-0">
            <h2 class="text-2xl font-bold text-white">{{ auth()->user()->username }}</h2>
            <p class="text-white">{{ auth()->user()->email }}</p>

            <div class="mt-4 flex space-x-3">
                <a href="{{ route('profile.edit') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                   Edit Profile
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-[#080808] rounded-lg shadow-md p-6">
        <h3 class="text-xl font-semibold text-white mb-4">Your Uploaded Videos</h3>

        @if(auth()->user()->videos->isEmpty())
            <p class="text-white">You haven't uploaded any videos yet.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach (auth()->user()->videos as $video)
                <a href="{{ route('videos.show', $video) }}" class="bg-[#121212] rounded-lg shadow hover:shadow-lg transition overflow-hidden block">
                    @if ($video->thumbnail_path)
                        <img src="{{ $video->thumbnail_path }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                            </svg>
                        </div>
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-white">{{ $video->title }}</h3>
                        <p class="text-white text-sm">{{ Str::limit($video->description, 60) }}
                            </p>
                        <p class="text-white text-xs mt-2">{{ number_format($video->views) }} views</p>
                    </div>
                </a>
            @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
````
