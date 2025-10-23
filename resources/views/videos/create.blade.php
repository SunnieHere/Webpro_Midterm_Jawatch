@extends('layouts.app')

@section('title', 'Upload Video')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h1 class="text-2xl font-bold text-white mb-6">Upload a New Video</h1>

    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" class="bg-[#080808] p-6 rounded-lg shadow-md">
        @csrf

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Video Title *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#080808] text-white">
            @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Description</label>
            <textarea name="description" rows="4" 
                      class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#080808] text-white">{{ old('description') }}</textarea>
            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Upload Video File (MP4) *</label>
            <input type="file" name="video" accept="video/mp4,video/quicktime" required
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white">
            <p class="text-white text-xs mt-1">Maximum file size: 500MB</p>
            @error('video') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-white font-semibold mb-2">Custom Thumbnail (Optional)</label>
            <input type="file" name="thumbnail" accept="image/*"
                   class="w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white">
            <p class="text-white text-xs mt-1">If not provided, a thumbnail will be automatically generated from your video</p>
            @error('thumbnail') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold transition">
            Upload Video
        </button>
    </form>
</div>
@endsection
