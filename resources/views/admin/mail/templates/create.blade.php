@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Create Email Template</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mail-templates.store') }}" method="POST">
                @csrf
                @include('admin.mail.templates.form')
                <button type="submit" class="btn btn-primary">Create Template</button>
            </form>
        </div>
    </div>
</div>
@endsection
