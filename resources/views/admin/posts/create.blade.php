@extends('admin.layouts.app')

@section('title', 'Create Post')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Create Post</h1>
                <p class="text-muted mb-0">Write the devotional, set its publication time, and prepare its featured image and taxonomy in one place.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="submit_action" value="draft" class="btn btn-outline-primary">Save Draft</button>
                <button type="submit" name="submit_action" value="publish" class="btn btn-primary">Create Post</button>
            </div>
        </div>

        @include('admin.posts._form', ['allowCategoryCreation' => true])
    </form>
</div>

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
    const addCategoryModalEl = document.getElementById('addCategoryModal');
    const createCategoryForm = document.getElementById('create-category-form');
    const saveCategoryBtn = document.getElementById('save-category-btn');
    const categoriesContainer = document.getElementById('categories-container');

    if (!addCategoryBtn || !addCategoryModalEl || !createCategoryForm || !saveCategoryBtn || !categoriesContainer) {
        return;
    }

    const addCategoryModal = new bootstrap.Modal(addCategoryModalEl);

    addCategoryBtn.addEventListener('click', function() {
        addCategoryModal.show();
        document.getElementById('new_category_name').focus();
    });

    saveCategoryBtn.addEventListener('click', function() {
        const formData = new FormData(createCategoryForm);
        const spinner = this.querySelector('.spinner-border');

        this.disabled = true;
        spinner.classList.remove('d-none');

        document.getElementById('new_category_name').classList.remove('is-invalid');
        document.getElementById('category-name-error').textContent = '';

        fetch('{{ route("admin.posts.create-category") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            if (status === 422) {
                const error = body.errors?.name?.[0] ?? 'Unable to create category.';
                document.getElementById('new_category_name').classList.add('is-invalid');
                document.getElementById('category-name-error').textContent = error;
                return;
            }

            if (!body.success || !body.category) {
                throw new Error(body.message || 'Unable to create category.');
            }

            const category = body.category;
            const wrapper = document.createElement('div');
            wrapper.className = 'form-check';
            wrapper.innerHTML = `
                <input class="form-check-input" type="checkbox" name="category_ids[]" value="${category.id}" id="category_${category.id}" checked>
                <label class="form-check-label" for="category_${category.id}">${category.name}</label>
            `;
            categoriesContainer.appendChild(wrapper);

            createCategoryForm.reset();
            addCategoryModal.hide();
        })
        .catch(() => {
            document.getElementById('new_category_name').classList.add('is-invalid');
            document.getElementById('category-name-error').textContent = 'Failed to create category. Please try again.';
        })
        .finally(() => {
            this.disabled = false;
            spinner.classList.add('d-none');
        });
    });

    document.getElementById('new_category_name').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            saveCategoryBtn.click();
        }
    });
});
</script>
@endpush
@endsection
