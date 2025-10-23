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
                        <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="{{ $video->title }}" class="w-full h-48 object-cover">
                    @else
                        <img src="{{ asset('images/default-thumbnail.jpg') }}" alt="No Thumbnail" class="w-full h-48 object-cover">
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