@extends('admin.layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Create New Post</h5>
                </div>
                <div class="card-body">
                    @include('components.alerts')
                    
                    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Scripture</label>
                            <input type="text" name="scripture" class="form-control @error('scripture') is-invalid @enderror" value="{{ old('scripture') }}">
                            @error('scripture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bible Text</label>
                            <textarea name="bible_text" class="form-control @error('bible_text') is-invalid @enderror" rows="3">{{ old('bible_text') }}</textarea>
                            @error('bible_text')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <input type="text" name="subtitle" class="form-control @error('subtitle') is-invalid @enderror" value="{{ old('subtitle') }}">
                            @error('subtitle')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                      
<div class="mb-3">
    <label class="form-label">Details</label>
    <textarea id="details" name="details" class="form-control @error('details') is-invalid @enderror" rows="5">{{ old('details') }}</textarea>
    @error('details')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                        <div class="mb-3">
                            <label class="form-label">Action Point</label>
                            <textarea name="action_point" class="form-control @error('action_point') is-invalid @enderror" rows="3">{{ old('action_point') }}</textarea>
                            @error('action_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control @error('author') is-invalid @enderror" value="{{ old('author') }}">
                            @error('author')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
<div class="mb-3">
    <label class="form-label">Publication Date & Time</label>
    <input type="datetime-local" name="published_at" class="form-control @error('published_at') is-invalid @enderror" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}">
    @error('published_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
                        <div class="mb-3">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                       <!-- Categories with on-the-fly creation -->
<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label">Categories</label>
        <button type="button" class="btn btn-sm btn-outline-primary" id="add-category-btn">
            <i class="bi bi-plus"></i> Add New
        </button>
    </div>
    <div id="categories-container" class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
        @forelse($categories as $category)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="category_ids[]" 
                       value="{{ $category->id }}" id="category_{{ $category->id }}"
                       {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                <label class="form-check-label" for="category_{{ $category->id }}">
                    {{ $category->name }}
                </label>
            </div>
        @empty
            <p class="text-muted mb-0">No categories available.</p>
        @endforelse
    </div>
    @error('category_ids')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Tags</label>
    <select name="tag_ids[]" class="form-select" multiple size="5">
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tag_ids', [])) ? 'selected' : '' }}>
                {{ $tag->name }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="shareToFacebook" name="share_to_facebook">
                                <label class="custom-control-label" for="shareToFacebook">Share to Facebook</label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Post</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="create-category-form">
                    @csrf
                    <div class="mb-3">
                        <label for="new_category_name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="new_category_name" name="name" required>
                        <div class="invalid-feedback" id="category-name-error"></div>
                    </div>
                    <div class="mb-3">
                        <label for="new_category_description" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="new_category_description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save-category-btn">
                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Create Category
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addCategoryBtn = document.getElementById('add-category-btn');
    const addCategoryModal = new bootstrap.Modal(document.getElementById('addCategoryModal'));
    const createCategoryForm = document.getElementById('create-category-form');
    const saveCategoryBtn = document.getElementById('save-category-btn');
    const categoriesContainer = document.getElementById('categories-container');

    // Open modal
    addCategoryBtn.addEventListener('click', function() {
        addCategoryModal.show();
        document.getElementById('new_category_name').focus();
    });

    // Save category
    saveCategoryBtn.addEventListener('click', function() {
        const formData = new FormData(createCategoryForm);
        const spinner = this.querySelector('.spinner-border');
        
        // Show loading state
        this.disabled = true;
        spinner.classList.remove('d-none');
        
        // Clear previous errors
        document.getElementById('new_category_name').classList.remove('is-invalid');
        document.getElementById('category-name-error').textContent = '';

        fetch('{{ route("admin.posts.create-category") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content || formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new category to the list
                const newCategoryHtml = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="category_ids[]" 
                               value="${data.category.id}" id="category_${data.category.id}" checked>
                        <label class="form-check-label" for="category_${data.category.id}">
                            ${data.category.name}
                        </label>
                    </div>
                `;
                
                // Remove "No categories available" message if it exists
                const noCategories = categoriesContainer.querySelector('p.text-muted');
                if (noCategories) {
                    noCategories.remove();
                }
                
                categoriesContainer.insertAdjacentHTML('beforeend', newCategoryHtml);
                
                // Close modal and reset form
                addCategoryModal.hide();
                createCategoryForm.reset();
                
                // Show success message
                showAlert('Category created successfully!', 'success');
            } else {
                // Show validation errors
                if (data.errors && data.errors.name) {
                    document.getElementById('new_category_name').classList.add('is-invalid');
                    document.getElementById('category-name-error').textContent = data.errors.name[0];
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while creating the category.', 'danger');
        })
        .finally(() => {
            // Reset loading state
            saveCategoryBtn.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    // Allow Enter key to submit category form
    document.getElementById('new_category_name').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveCategoryBtn.click();
        }
    });

    // Helper function to show alerts
    function showAlert(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Insert alert at the top of the card body
        const cardBody = document.querySelector('.card-body');
        cardBody.insertAdjacentHTML('afterbegin', alertHtml);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = cardBody.querySelector('.alert');
            if (alert) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    }
});
</script>
@endpush

@endsection


