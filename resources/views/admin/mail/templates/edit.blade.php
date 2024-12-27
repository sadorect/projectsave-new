@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Edit Email Template</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.mail-templates.update', $mailTemplate) }}" method="POST">
                @csrf
                @method('PUT')
                @include('admin.mail.templates.form')
                <button type="submit" class="btn btn-primary">Update Template</button>
            </form>
        </div>
    </div>
</div>
@endsection
