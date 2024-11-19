@extends('layouts.user')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
  <div class="row">
      <div class="col-12">
          <h1 class="h3 mb-4">My Profile</h1>
      </div>
  </div>
@php
    $user = \App\Models\User::all();
@endphp
  @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show">
          <ul class="mb-0">
              @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
              @endforeach
          </ul>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
  @endif

  <!-- Rest of the profile form -->


    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <img src="{{ auth()->user()->avatar ?? asset('frontend/img/default-avatar.png') }}" 
                                     class="rounded-circle mb-3" 
                                     style="width: 150px; height: 150px; object-fit: cover;">
                                <div class="mb-3">
                                    <input type="file" name="avatar" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="tel" name="phone" class="form-control" value="{{ auth()->user()->phone }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Birthday</label>
                          <input type="date" name="birthday" class="form-control" 
                                 value="{{ auth()->user()->birthday ? \Carbon\Carbon::parse(auth()->user()->birthday)->format('Y-m-d') : '' }}">
                      </div>
                      
                      <div class="mb-3">
                          <label class="form-label">Wedding Anniversary</label>
                          <input type="date" name="wedding_anniversary" class="form-control" 
                                 value="{{ auth()->user()->wedding_anniversary ? \Carbon\Carbon::parse(auth()->user()->wedding_anniversary)->format('Y-m-d') : '' }}">
                          <small class="text-muted">Optional</small>
                      </div>
                      
                        <div class="mb-3">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="4">{{ auth()->user()->bio }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Account Status</h5>
                    <p class="mb-2">Member since: {{ auth()->user()->created_at->format('M d, Y') }}</p>
                    <p>Email Status: {{ auth()->user()->email_verified_at ? 'Verified' : 'Not Verified' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
