import './bootstrap';

// Configuration values should be passed from your Laravel backend
// Example of how to pass from Laravel view:
// <script>
//     window.LaravelConfig = {
//         maxUploadSize: {{ config('image.max_upload_size', 5120) }},
//         allowedMimes: {{ json_encode(config('image.allowed_mimes', ['image/jpeg', 'image/png'])) }}
//     };
// </script>
// Make sure this config object is available before this script runs
const imageConfig = window.LaravelConfig || {
    maxUploadSize: 5120, // Default 5MB in KB
    allowedMimes: ['image/jpeg', 'image/png'] // Default allowed types
};

// Auto-generate slug from title
document.getElementById('title')?.addEventListener('input', function(e) {
    const slug = e.target.value
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
        .replace(/\s+/g, '-') // Replace spaces with hyphens
        .trim();
    
    const slugInput = document.getElementById('slug');
    if (slugInput && !slugInput.dataset.manuallyEdited) {
        slugInput.value = slug;
        document.getElementById('slug-preview').textContent = slug;
    }
});

// Mark slug as manually edited if user changes it
document.getElementById('slug')?.addEventListener('input', function(e) {
    e.target.dataset.manuallyEdited = 'true';
});

// Character count for excerpt
document.getElementById('excerpt')?.addEventListener('input', function(e) {
    const count = e.target.value.length;
    document.getElementById('excerpt-count').textContent = count;
    if (count > 500) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});

// Reading time estimate
document.getElementById('body')?.addEventListener('input', function(e) {
    const wordsPerMinute = 200;
    const wordCount = e.target.value.trim().split(/\s+/).length;
    const readingTime = Math.max(1, Math.ceil(wordCount / wordsPerMinute));
    document.getElementById('reading-time').textContent = readingTime;
});

// Image preview
document.getElementById('featured_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            if (preview) {
                preview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Auto-generate slug for categories/tags
document.querySelectorAll('#name').forEach(input => {
    input.addEventListener('input', function(e) {
        const slugField = document.getElementById('slug');
        if (slugField && !slugField.dataset.manuallyEdited) {
            const slug = e.target.value
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .trim();
            slugField.value = slug;
            const preview = document.getElementById('slug-preview');
            if (preview) preview.textContent = slug;
        }
    });
});

// Character counter for description
document.getElementById('description')?.addEventListener('input', function(e) {
    const count = e.target.value.length;
    const counter = document.getElementById('desc-count');
    if (counter) counter.textContent = count;
    
    if (count > 1000) {
        e.target.classList.add('is-invalid');
    } else {
        e.target.classList.remove('is-invalid');
    }
});

// Prevent deletion if has posts (client-side hint)
document.querySelectorAll('form[action*="destroy"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        const hasPosts = this.dataset.postsCount > 0;
        if (hasPosts) {
            e.preventDefault();
            alert('This item has associated posts and cannot be deleted.');
        }
    });
});

// Image Preview Function
window.previewImage = function(event, previewId) {
    const preview = document.getElementById(previewId);
    const file = event.target.files[0];
    
    if (!file) {
        preview.style.display = 'none';
        return;
    }

    // Validate file size using the config object
    const maxSize = imageConfig.maxUploadSize; // in KB
    if (file.size > maxSize * 1024) {
        alert(`File size must be less than ${maxSize} KB`);
        event.target.value = '';
        preview.style.display = 'none';
        return;
    }

    // Validate file type using the config object
    const allowedTypes = imageConfig.allowedMimes;
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select an image file.');
        event.target.value = '';
        preview.style.display = 'none';
        return;
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
        
        // Auto-hide current image preview if uploading new
        if (previewId === 'image-preview-new') {
            const currentPreview = document.getElementById('current-image');
            if (currentPreview) {
                currentPreview.closest('.current-image-preview').style.display = 'none';
            }
        }
    };
    reader.readAsDataURL(file);
};

// Clear image preview when delete checkbox is checked
document.getElementById('delete_image')?.addEventListener('change', function(e) {
    const currentPreview = document.getElementById('current-image');
    const newUpload = document.getElementById('featured_image');
    
    if (this.checked) {
        if (currentPreview) {
            currentPreview.closest('.current-image-preview').classList.add('opacity-25');
        }
        newUpload.disabled = true;
    } else {
        if (currentPreview) {
            currentPreview.closest('.current-image-preview').classList.remove('opacity-25');
        }
        newUpload.disabled = false;
    }
});