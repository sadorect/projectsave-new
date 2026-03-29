@extends('admin.layouts.app')

@section('title', 'Edit FAQ')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.faqs.update', $faq) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Edit FAQ</h1>
                <p class="text-muted mb-0">{{ $faq->title }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update FAQ</button>
            </div>
        </div>

        @include('admin.faqs._form')
    </form>
</div>
@endsection
