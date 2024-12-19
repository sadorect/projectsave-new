@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Edit Lesson</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.lessons.update', $lesson) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.lessons.form')
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                    <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection