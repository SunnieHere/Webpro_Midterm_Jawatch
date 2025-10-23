@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h1 class="text-2xl font-bold text-white mb-6">Edit Profile</h1>

    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="bg-[#080808] p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-white">Username</label>
            <input type="text" name="username" value="{{ old('username', auth()->user()->username) }}" required
                   class="bg-[#121212] text-white w-full border-neutral-800 border rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500">
            @error('username') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-white mb-2">Profile Photo</label>
            @if(auth()->user()->profile_photo)
                <img src="{{ auth()->user()->profile_photo }}" alt="Profile" class="w-20 h-20 rounded-full mb-3 object-cover">
            @endif
            <input type="file" name="profile_photo" accept="image/*"
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-red-600 file:text-white hover:file:bg-gray-600">
            @error('profile_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
            Save Changes
        </button>
    </form>
</div>
@endsection
