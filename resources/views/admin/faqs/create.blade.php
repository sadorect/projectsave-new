@extends('admin.layouts.app')

@section('title', 'Create FAQ')

@section('content')
<div class="container-fluid">
    @include('components.alerts')

    <form action="{{ route('admin.faqs.store') }}" method="POST">
        @csrf

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-1">Create FAQ</h1>
                <p class="text-muted mb-0">Write a clear answer, structure it with rich text, and choose whether it should go live immediately.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create FAQ</button>
            </div>
        </div>

        @include('admin.faqs._form')
    </form>
</div>
@endsection
