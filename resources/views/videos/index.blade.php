@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="max-w-6xl mx-auto px-4">
    <h1 class="text-3xl font-bold text-white mb-6">Latest Videos</h1>

    @if($videos->isEmpty())
        <p class="text-white">No videos yet. Be the first to upload!</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach ($videos as $video)
                <a href="{{ route('videos.show', $video) }}" class="bg-[#121212] rounded-lg shadow hover:shadow-lg transition overflow-hidden block hover:scale-105 hover:bg-[#080808]">
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
                        <p class="text-white text-sm line-clamp-3 break-words">{{ Str::limit($video->description, 60) }}</p>
                        <p class="text-white text-xs mt-2">By {{ $video->user->username }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
