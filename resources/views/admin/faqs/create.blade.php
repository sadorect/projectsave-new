@extends('admin.layouts.app')

@section('title', 'Create FAQ')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Create New FAQ</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="details" class="form-label">Details</label>
                    <textarea style="display:none" class="form-control @error('details') is-invalid @enderror" 
                              id="details" name="details">{{ old('details') }}</textarea>
                    @error('details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Create FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    ClassicEditor
        .create(document.querySelector('#details'))
        .catch(error => {
            console.error(error);
        });
</script>
@endpush



@endsection
