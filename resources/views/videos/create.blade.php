@extends('layouts.app')

@section('title', 'Upload Video')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h1 class="text-2xl font-bold text-white mb-6">Upload a New Video</h1>

    <form id="uploadForm" class="bg-[#080808] p-6 rounded-lg shadow-md">
        @csrf

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Video Title *</label>
            <input type="text" name="title" id="title" required
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#121212] text-white">
            <p class="text-red-500 text-sm mt-1 hidden" id="titleError"></p>
        </div>

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Description</label>
            <textarea name="description" id="description" rows="4" 
                      class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent bg-[#121212] text-white"></textarea>
            <p class="text-red-500 text-sm mt-1 hidden" id="descriptionError"></p>
        </div>

        <div class="mb-4">
            <label class="block text-white font-semibold mb-2">Upload Video File (MP4) *</label>
            <input type="file" name="video" id="video" accept="video/mp4,video/quicktime" required
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-red-600 file:text-white hover:file:bg-red-700">
            <p class="text-gray-400 text-xs mt-1">Maximum file size: 500MB</p>
            <p id="videoInfo" class="text-sm text-blue-400 mt-1"></p>
            <p class="text-red-500 text-sm mt-1 hidden" id="videoError"></p>
        </div>

        <div class="mb-6">
            <label class="block text-white font-semibold mb-2">Custom Thumbnail (Optional)</label>
            <input type="file" name="thumbnail" id="thumbnail" accept="image/*"
                   class="w-full border border-neutral-800 rounded-lg px-4 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent text-white file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:bg-red-600 file:text-white hover:file:bg-gray-600">
            <p class="text-gray-400 text-xs mt-1">If not provided, a thumbnail will be automatically generated</p>
            <p id="thumbnailInfo" class="text-sm text-blue-400 mt-1"></p>
            <p class="text-red-500 text-sm mt-1 hidden" id="thumbnailError"></p>
        </div>

        <button type="submit" id="submitBtn" class="w-full bg-red-600 text-white py-3 rounded-lg hover:bg-red-700 font-semibold transition">
            Upload Video
        </button>
    </form>
</div>

<!-- Upload Progress Modal - Black Theme -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-90 hidden items-center justify-center z-50">
    <div class="bg-[#080808] border border-gray-800 rounded-lg p-8 max-w-md w-full mx-4 shadow-2xl">
        <!-- Status Icon -->
        <div class="flex justify-center mb-4">
            <div id="loadingIcon">
                <svg class="animate-spin h-16 w-16 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div id="successIcon" class="hidden">
                <svg class="h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div id="errorIcon" class="hidden">
                <svg class="h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Message -->
        <h3 class="text-xl font-bold text-white mb-2 text-center" id="modalTitle">Uploading Video...</h3>
        <p class="text-gray-400 mb-4 text-center" id="modalMessage">Please wait while we process your file</p>
        
        <!-- Progress Bar -->
        <div class="w-full bg-gray-800 rounded-full h-3 mb-2">
            <div id="progressBar" class="bg-red-600 h-3 rounded-full transition-all duration-300" style="width: 0%"></div>
        </div>
        <p class="text-sm text-gray-300 text-center mb-4" id="progressText">0%</p>
        
        <!-- Action Button -->
        <button id="modalBtn" class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 font-semibold transition hidden">
            View Video
        </button>
        
        <p class="text-sm text-gray-500 text-center" id="warningText">Please don't close this window.</p>
    </div>
</div>

<script>
    const uploadForm = document.getElementById('uploadForm');
    const uploadModal = document.getElementById('uploadModal');
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalBtn = document.getElementById('modalBtn');
    const submitBtn = document.getElementById('submitBtn');
    const loadingIcon = document.getElementById('loadingIcon');
    const successIcon = document.getElementById('successIcon');
    const errorIcon = document.getElementById('errorIcon');
    const warningText = document.getElementById('warningText');

    // File info display
    document.getElementById('video').addEventListener('change', function(e) {
        updateFileInfo(e.target, 'videoInfo');
    });

    document.getElementById('thumbnail').addEventListener('change', function(e) {
        updateFileInfo(e.target, 'thumbnailInfo');
    });

    function updateFileInfo(input, targetId) {
        const target = document.getElementById(targetId);
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
            target.textContent = `Selected: ${file.name} (${sizeMB} MB)`;
        } else {
            target.textContent = '';
        }
    }

    // Handle form submission
    uploadForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Clear previous errors
        document.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));

        // Get form data
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('title', document.getElementById('title').value);
        formData.append('description', document.getElementById('description').value);
        
        const videoFile = document.getElementById('video').files[0];
        const thumbnailFile = document.getElementById('thumbnail').files[0];
        
        if (videoFile) {
            formData.append('video', videoFile);
        }
        if (thumbnailFile) {
            formData.append('thumbnail', thumbnailFile);
        }

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

        // Show modal
        showModal();

        // Create AJAX request
        const xhr = new XMLHttpRequest();

        // Track upload progress
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                updateProgress(percentComplete);
            }
        });

        // Handle completion
        xhr.addEventListener('load', function() {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    showSuccess(response.message || 'Video uploaded successfully!', response.redirect);
                } catch (e) {
                    // If response is redirect HTML, extract redirect URL
                    const redirectMatch = xhr.responseURL;
                    if (redirectMatch) {
                        showSuccess('Video uploaded successfully!', redirectMatch);
                    } else {
                        showError('Upload completed but redirect failed');
                    }
                }
            } else if (xhr.status === 422) {
                // Validation errors
                const errors = JSON.parse(xhr.responseText);
                showValidationErrors(errors.errors);
                hideModal();
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                showError('Upload failed. Please try again.');
            }
        });

        // Handle errors
        xhr.addEventListener('error', function() {
            showError('Network error. Please check your connection.');
        });

        xhr.addEventListener('abort', function() {
            showError('Upload cancelled.');
        });

        // Send request
        xhr.open('POST', '{{ route("videos.store") }}');
        xhr.send(formData);
    });

    function showModal() {
        uploadModal.classList.remove('hidden');
        uploadModal.classList.add('flex');
        loadingIcon.classList.remove('hidden');
        successIcon.classList.add('hidden');
        errorIcon.classList.add('hidden');
        modalBtn.classList.add('hidden');
        warningText.classList.remove('hidden');
        modalTitle.textContent = 'Uploading Video...';
        modalMessage.textContent = 'Please wait while we process your file';
        progressBar.style.width = '0%';
        progressText.textContent = '0%';
    }

    function hideModal() {
        uploadModal.classList.add('hidden');
        uploadModal.classList.remove('flex');
    }

    function updateProgress(percent) {
        progressBar.style.width = percent + '%';
        progressText.textContent = percent + '%';
        
        if (percent === 100) {
            modalTitle.textContent = 'Processing...';
            modalMessage.textContent = 'Generating thumbnail and finalizing upload';
        }
    }

    function showSuccess(message, redirectUrl) {
        loadingIcon.classList.add('hidden');
        successIcon.classList.remove('hidden');
        modalTitle.textContent = 'Upload Complete!';
        modalMessage.textContent = message;
        progressBar.style.width = '100%';
        progressText.textContent = '100%';
        warningText.classList.add('hidden');
        modalBtn.classList.remove('hidden');
        modalBtn.textContent = 'View Video';
        
        modalBtn.onclick = function() {
            window.location.href = redirectUrl;
        };

        // Auto redirect after 2 seconds
        setTimeout(() => {
            window.location.href = redirectUrl;
        }, 2000);
    }

    function showError(message) {
        loadingIcon.classList.add('hidden');
        errorIcon.classList.remove('hidden');
        modalTitle.textContent = 'Upload Failed';
        modalMessage.textContent = message;
        warningText.classList.add('hidden');
        modalBtn.classList.remove('hidden');
        modalBtn.textContent = 'Try Again';
        modalBtn.classList.remove('bg-red-600', 'hover:bg-red-700');
        modalBtn.classList.add('bg-gray-600', 'hover:bg-gray-700');
        
        modalBtn.onclick = function() {
            hideModal();
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        };
    }

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