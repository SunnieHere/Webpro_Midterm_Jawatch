@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <div class="bg-[#080808] rounded-lg shadow-md p-6 mb-6 flex flex-col sm:flex-row items-center sm:items-start sm:space-x-6">
        {{-- Profile Photo --}}
        @if(auth()->user()->profile_photo)
            <img src="{{ asset('storage/' . auth()->user()->profile_photo) }}" 
                 alt="Profile Photo" 
                 class="w-32 h-32 rounded-full object-cover border-2 border-gray-300">
        @else
            <div class="w-32 h-32 bg-gray-300 rounded-full flex items-center justify-center">
                <span class="text-4xl font-bold text-gray-700">
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
            <p class="text-white">You haven’t uploaded any videos yet.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                @foreach (auth()->user()->videos as $video)
                <a href="{{ route('videos.show', $video) }}" class="bg-[#080808] rounded-lg shadow hover:shadow-lg transition overflow-hidden block">
                    @if ($video->thumbnail_path)
                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                    @else
                        <img src="{{ asset('images/default-thumbnail.jpg') }}" alt="No Thumbnail" class="w-full h-48 object-cover">
                    @endif
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-white">{{ $video->title }}</h3>
                        <p class="text-white text-sm">{{ Str::limit($video->description, 60) }}</p>
                        <p class="text-white text-xs mt-2">By {{ $video->user->username }}</p>
                    </div>
                </a>
            @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
