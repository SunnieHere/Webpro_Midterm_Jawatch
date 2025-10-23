@extends('layouts.app')

@section('title', 'Edit Video')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h1 class="text-2xl font-bold text-white mb-6">Edit Video</h1>

    <form id="editForm" class="bg-[#080808] p-6 rounded-lg shadow-md">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Video Title</label>
            <input type="text" name="title" id="title" value="{{ $video->title }}" required
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#121212] text-white">
            <p class="text-red-500 text-sm mt-1 hidden" id="titleError"></p>
        </div>

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" 
                      class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#121212] text-white">{{ $video->description }}</textarea>
            <p class="text-red-500 text-sm mt-1 hidden" id="descriptionError"></p>
        </div>

        @if($video->thumbnail_path)
        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Current Thumbnail</label>
            <img src="{{ asset('storage/' . $video->thumbnail_path) }}" alt="Current thumbnail" class="w-64 h-36 object-cover rounded-lg border border-neutral-800" id="currentThumbnail">
        </div>
        @endif

        <div class="mb-6">
           <label class="block text-white font-semibold mb-2">Custom Thumbnail (Optional)</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-red-600 file:text-white hover:file:bg-gray-600">
            <p id="thumbnailInfo" class="text-sm text-blue-400 mt-1"></p>
            <p class="text-red-500 text-sm mt-1 hidden" id="thumbnailError"></p>
        </div>

        <button type="submit" id="submitBtn" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-blue-700 font-semibold transition">
            Save Changes
        </button>
    </form>
</div>

<!-- Loading Modal - Black Theme -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50">
    <div class="bg-[#080808] border border-gray-800 rounded-lg p-8 max-w-md w-full mx-4 shadow-2xl">
        <div class="flex justify-center mb-4">
            <div id="loadingIcon">
                <svg class="animate-spin h-16 w-16 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div id="successIcon" class="hidden">
                <svg class="h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        
        <h3 class="text-xl font-bold text-white mb-2 text-center" id="modalTitle">Saving Changes...</h3>
        <p class="text-gray-400 mb-4 text-center" id="modalMessage">Please wait</p>
        
        <div class="w-full bg-gray-800 rounded-full h-3 mb-2">
            <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <p class="text-sm text-gray-300 text-center mb-4" id="progressText">0%</p>
    </div>
</div>

<script>
    const editForm = document.getElementById('editForm');
    const uploadModal = document.getElementById('uploadModal');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const submitBtn = document.getElementById('submitBtn');
    const loadingIcon = document.getElementById('loadingIcon');
    const successIcon = document.getElementById('successIcon');

    document.getElementById('thumbnail').addEventListener('change', function(e) {
        const target = document.getElementById('thumbnailInfo');
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            target.textContent = `Selected: ${file.name} (${sizeMB} MB)`;
        } else {
            target.textContent = '';
        }
    });

    editForm.addEventListener('submit', function(e) {
        e.preventDefault();

        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));

        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PUT');
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        
        const thumbnailFile = document.getElementById('thumbnail').files[0];
        if (thumbnailFile) {
            formData.append('thumbnail', thumbnailFile);
        }

        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        uploadModal.classList.remove('hidden');
        uploadModal.classList.add('flex');

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                progressBar.style.width = percentComplete + '%';
                progressText.textContent = percentComplete + '%';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                loadingIcon.classList.add('hidden');
                successIcon.classList.remove('hidden');
                modalTitle.textContent = 'Changes Saved!';
                modalMessage.textContent = 'Redirecting...';
                progressBar.style.width = '100%';
                progressText.textContent = '100%';

                setTimeout(() => {
                    window.location.href = '{{ route("videos.show", $video) }}';
                }, 1000);
            } else if (xhr.status === 422) {
                const errors = JSON.parse(xhr.responseText);
                showValidationErrors(errors.errors);
                uploadModal.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });

        xhr.open('POST', '{{ route("videos.update", $video) }}');
        xhr.send(formData);
    });

    function showValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const errorElement = document.getElementById(field + 'Error');
            if (errorElement) {
                errorElement.textContent = messages[0];
                errorElement.classList.remove('hidden');
            }
        }
    }
</script>
@endsection