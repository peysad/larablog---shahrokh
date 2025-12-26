import './bootstrap';

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