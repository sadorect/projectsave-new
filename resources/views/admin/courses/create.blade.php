@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3>Create New Course</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('admin.courses.form')
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Create Course</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endsection
