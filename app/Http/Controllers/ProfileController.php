<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'profile_photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $uploadedFileUrl = Cloudinary::upload(
                $request->file('profile_photo')->getRealPath(),
                ['folder' => 'profile_photos']
            )->getSecurePath();

            $user->profile_photo = $uploadedFileUrl;
        }

        $user->username = $request->username;
        $user->save();

        return redirect()
            ->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }
}
