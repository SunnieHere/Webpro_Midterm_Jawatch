@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-[#121212] px-4">
    <div class="bg-[#080808] p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-white mb-6">Login to Your Account</h2>

        @if (session('error'))
            <div class="bg-red-100 text-red-700 p-2 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-white">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-[#080808] text-white">
                @error('email') <p class="text-red-500 bg-[#080808] text-white text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-white">Password</label>
                <input type="password" name="password" required
                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 bg-[#080808] text-white">
                @error('password') <p class="text-red-500 bg-[#080808] text-white text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700">
                Login
            </button>
        </form>

        <p class="text-center text-gray-600 text-sm mt-4">
            Don’t have an account?
            <a href="{{ route('register') }}" class="text-red-600 hover:underline">Register here</a>
        </p>
    </div>
</div>
@endsection
