<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class VideoController extends Controller
{
    public function index()
    {
        $videos = Video::latest()->paginate(12);
        return view('videos.index', compact('videos'));
    }

    public function create()
    {
        return view('videos.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'video' => 'required|file|mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska|max:512000',
                'thumbnail' => 'nullable|image|max:2048',
            ]);

            // Upload video to Cloudinary
            $uploadedVideo = Cloudinary::uploadVideo($request->file('video')->getRealPath(), [
                'folder' => 'videos',
                'resource_type' => 'video'
            ]);
            $videoPath = $uploadedVideo->getSecurePath();

            // Upload thumbnail to Cloudinary if user provided one
            if ($request->hasFile('thumbnail')) {
                $uploadedThumb = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'thumbnails'
                ]);
                $thumbPath = $uploadedThumb->getSecurePath();
            } else {
                // Generate placeholder and upload to Cloudinary
                $localThumbPath = $this->generatePlaceholderThumbnail($request->title);
                if ($localThumbPath) {
                    $uploadedThumb = Cloudinary::upload($localThumbPath, [
                        'folder' => 'thumbnails'
                    ]);
                    $thumbPath = $uploadedThumb->getSecurePath();
                    
                    // Delete local placeholder
                    @unlink($localThumbPath);
                } else {
                    $thumbPath = null;
                }
            }

            $video = Video::create([
                'user_id' => Auth::id(),
                'title' => $request->title,
                'description' => $request->description,
                'video_path' => $videoPath,
                'thumbnail_path' => $thumbPath,
            ]);

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully!',
                'redirect' => route('videos.show', $video)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Video upload failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePlaceholderThumbnail($title)
    {
        try {
            $width = 1280;
            $height = 720;
            $image = imagecreatetruecolor($width, $height);

            $colors = [
                ['start' => [239, 68, 68], 'end' => [220, 38, 38]],
                ['start' => [59, 130, 246], 'end' => [37, 99, 235]],
                ['start' => [16, 185, 129], 'end' => [5, 150, 105]],
                ['start' => [249, 115, 22], 'end' => [234, 88, 12]],
                ['start' => [168, 85, 247], 'end' => [147, 51, 234]],
            ];

            $selectedColor = $colors[array_rand($colors)];

            for ($i = 0; $i < $height; $i++) {
                $ratio = $i / $height;
                $r = $selectedColor['start'][0] + ($selectedColor['end'][0] - $selectedColor['start'][0]) * $ratio;
                $g = $selectedColor['start'][1] + ($selectedColor['end'][1] - $selectedColor['start'][1]) * $ratio;
                $b = $selectedColor['start'][2] + ($selectedColor['end'][2] - $selectedColor['start'][2]) * $ratio;
                
                $color = imagecolorallocate($image, $r, $g, $b);
                imagefilledrectangle($image, 0, $i, $width, $i + 1, $color);
            }

            $white = imagecolorallocate($image, 255, 255, 255);
            $wrappedTitle = wordwrap($title, 30, "\n");
            $lines = explode("\n", $wrappedTitle);
            
            $fontSize = 5;
            $lineHeight = 20;
            $startY = ($height / 2) - (count($lines) * $lineHeight / 2);
            
            foreach ($lines as $index => $line) {
                $textWidth = imagefontwidth($fontSize) * strlen($line);
                $x = ($width - $textWidth) / 2;
                $y = $startY + ($index * $lineHeight);
                imagestring($image, $fontSize, $x, $y, $line, $white);
            }

            $playSize = 80;
            $centerX = $width / 2;
            $centerY = $height / 2 + 100;
            
            $triangle = [
                $centerX - $playSize / 2, $centerY - $playSize / 2,
                $centerX - $playSize / 2, $centerY + $playSize / 2,
                $centerX + $playSize / 2, $centerY
            ];
            
            imagefilledpolygon($image, $triangle, 3, $white);

            // Save to temp location
            $tempPath = sys_get_temp_dir() . '/' . uniqid() . '.jpg';
            imagejpeg($image, $tempPath, 90);
            imagedestroy($image);

            return $tempPath;
        } catch (\Exception $e) {
            \Log::error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function show(Video $video)
    {
        $video->increment('views');
        
        $relatedVideos = Video::where('id', '!=', $video->id)
            ->latest()
            ->take(6)
            ->get();

        return view('videos.show', compact('video', 'relatedVideos'));
    }

    public function edit(Video $video)
    {
        $this->authorize('update', $video);
        return view('videos.edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        try {
            $this->authorize('update', $video);

            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'thumbnail' => 'nullable|image|max:2048',
            ]);

            if ($request->hasFile('thumbnail')) {
                // Upload new thumbnail to Cloudinary
                $uploadedThumb = Cloudinary::upload($request->file('thumbnail')->getRealPath(), [
                    'folder' => 'thumbnails'
                ]);
                $video->thumbnail_path = $uploadedThumb->getSecurePath();
            }

            $video->title = $request->title;
            $video->description = $request->description;
            $video->save();

            // Always return JSON for AJAX requests
            return response()->json([
                'success' => true,
                'message' => 'Video updated successfully!',
                'redirect' => route('videos.show', $video)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Video update failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Update failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Video $video)
    {
        $this->authorize('delete', $video);

        // Note: Cloudinary files don't need to be manually deleted
        // They can be managed through Cloudinary dashboard
        
        $video->delete();

        return redirect()->route('home')->with('success', 'Video deleted successfully!');
    }

    public function toggleLike(Request $request, Video $video)
    {
        $request->validate(['action' => 'required|in:like,dislike']);

        $like = Like::firstOrNew([
            'video_id' => $video->id,
            'user_id' => $request->user()->id,
        ]);

        if ($like->exists && $like->liked === ($request->action === 'like')) {
            $like->delete();
        } else {
            $like->liked = ($request->action === 'like');
            $like->save();
        }

        return back();
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        $videos = Video::where('title', 'like', "%{$query}%")
                    ->orWhereHas('user', function ($q) use ($query) {
                        $q->where('username', 'like', "%{$query}%");
                    })
                    ->latest()
                    ->get();

        return view('videos.index', compact('videos', 'query'));
    }
}
