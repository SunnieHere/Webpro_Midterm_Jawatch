@extends('layouts.app')

@section('title', $video->title)

@section('content')
<div class="max-w-7xl mx-auto px-4 flex gap-6">
    {{-- Main video section --}}
    <div class="flex-1">
        <div class="bg-[#121212] rounded-lg shadow-md overflow-hidden mb-6">
            <video class="w-full h-96 bg-black" controls>
                <source src="{{ $video->video_path }}" type="video/mp4">
            </video>
            <div class="p-6">
                <h1 class="text-white text-3xl font-bold mb-2">{{ $video->title }}</h1>

                <div class="flex justify-between items-center">
                    <p class="text-gray-500 text-sm">
                        Uploaded by {{ $video->user->username }} • {{ number_format($video->views) }} views
                    </p>

                    <div class="flex items-center space-x-4">
                        @auth
                            {{-- Like --}}
                            <form method="POST" action="{{ route('videos.like', $video) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="like">
                                <button type="submit"
                                    class="flex items-center space-x-2 px-4 py-2 rounded-lg
                                    {{ $video->isLikedBy(auth()->user()) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}
                                    hover:bg-blue-700 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                    </svg>
                                    <span>{{ $video->likes()->where('liked', true)->count() }}</span>
                                </button>
                            </form>

                            {{-- Dislike --}}
                            <form method="POST" action="{{ route('videos.like', $video) }}" class="inline">
                                @csrf
                                <input type="hidden" name="action" value="dislike">
                                <button type="submit"
                                    class="flex items-center space-x-2 px-4 py-2 rounded-lg
                                    {{ $video->isDislikedBy(auth()->user()) ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }}
                                    hover:bg-red-700 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"/>
                                    </svg>
                                    <span>{{ $video->likes()->where('liked', false)->count() }}</span>
                                </button>
                            </form>
                        @else
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2 text-gray-400">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"/>
                                    </svg>
                                    <span>{{ $video->likes()->where('liked', true)->count() }}</span>
                                </div>
                                <div class="flex items-center space-x-2 text-gray-400">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M18 9.5a1.5 1.5 0 11-3 0v-6a1.5 1.5 0 013 0v6zM14 9.667v-5.43a2 2 0 00-1.105-1.79l-.05-.025A4 4 0 0011.055 2H5.64a2 2 0 00-1.962 1.608l-1.2 6A2 2 0 004.44 12H8v4a2 2 0 002 2 1 1 0 001-1v-.667a4 4 0 01.8-2.4l1.4-1.866a4 4 0 00.8-2.4z"/>
                                    </svg>
                                    <span>{{ $video->likes()->where('liked', false)->count() }}</span>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>

                <p class="text-white text-sm mt-4 break-words">{{ $video->description }}</p>
            </div>

            <div class="flex justify-start space-x-4 p-6">
                @can('update', $video)
                    <a href="{{ route('videos.edit', $video) }}" class="text-blue-500 hover:underline">Edit</a>
                @endcan

                @can('delete', $video)
                    <form action="{{ route('videos.destroy', $video) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="text-red-500 hover:underline"
                            onclick="return confirm('Are you sure you want to delete this video?')">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        {{-- Comments Section --}}
        <div class="bg-[#080808] rounded-lg shadow-md p-6">
            <h2 class="text-white text-xl font-semibold mb-4">Comments</h2>

            @auth
            <form method="POST" action="{{ route('comments.store', $video) }}" class="mb-4">
                @csrf
                <textarea name="content" rows="3" placeholder="Add a comment..." required
                    class="bg-[#121212] text-white w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500"></textarea>
                @error('content')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <button type="submit" class="mt-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Post Comment
                </button>
            </form>
            @else
                <p class="text-gray-500 mb-4">
                    Please <a href="{{ route('login') }}" class="text-blue-600 hover:underline">login</a> to comment.
                </p>
            @endauth

            @forelse ($video->comments as $comment)
                <div class="border-b border-neutral-700 py-3">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="font-semibold text-white">{{ $comment->user->username }}</p>
                            <p class="text-gray-300 mt-1">{{ $comment->content }}</p>
                            <p class="text-gray-400 text-xs mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                        </div>
                        @can('delete', $comment)
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 text-sm hover:underline"
                                    onclick="return confirm('Delete this comment?')">Delete</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No comments yet. Be the first to comment!</p>
            @endforelse
        </div>
    </div>

    {{-- Related Videos Sidebar --}}
    <div class="w-80 flex flex-col gap-4 mt-2">
        @foreach($relatedVideos as $related)
        <a href="{{ route('videos.show', $related->id) }}" class="flex gap-2 hover:bg-[#1a1a1a] p-2 rounded-lg transition">
            <div class="w-40 h-24 bg-gray-700 rounded-md overflow-hidden flex-shrink-0">
                @if ($related->thumbnail_path)
                    <img src="{{ $related->thumbnail_path }}" 
                        alt="{{ $related->title }}" 
                        class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2 6a2 2 0 012-2h6a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14.553 7.106A1 1 0 0014 8v4a1 1 0 00.553.894l2 1A1 1 0 0018 13V7a1 1 0 00-1.447-.894l-2 1z"/>
                        </svg>
                    </div>
                @endif
            </div>
            <div class="flex-1">
                <h2 class="text-sm font-semibold text-white line-clamp-2">{{ $related->title }}</h2>
                <p class="text-xs text-gray-400">{{ $related->user->username }}</p>
                <p class="text-xs text-gray-500">{{ $related->created_at->diffForHumans() }}</p>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
