@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif
    
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>News Updates</h3>
            <a href="{{ route('news.create') }}" class="btn btn-primary">
                Add News Update
            </a>
        </div>
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($updates as $update)
                    <tr>
                        <td>{{ date('Y-m-d', strtotime($update->date)) }}</td>
                        <td>{{ $update->title }}</td>
                        <td>
                            <span class="badge badge-{{ $update->is_active ? 'success' : 'secondary' }}">
                                {{ $update->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                           <a href="{{ route('news.edit', $update->id) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('news.destroy', $update) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
    



