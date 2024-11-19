<x-layouts.app>
    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>My Profile</h2>
                </div>
                <div class="col-12">
                    <a href="{{ route('home') }}">Home</a>
                    <a href="">Profile</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->

    <div class="container my-5">
        <div class="row">
            <div class="col-lg-4">
                <div class="profile-sidebar">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <img src="{{ auth()->user()->avatar ?? asset('frontend/img/default-avatar.png') }}" 
                                     alt="Profile Picture" 
                                     class="rounded-circle profile-image">
                                <h4 class="mt-3">{{ auth()->user()->name }}</h4>
                            </div>
                            <div class="profile-menu">
                                <a href="#profile-info" class="active">Profile Information</a>
                                <a href="#security">Security Settings</a>
                                <a href="#notifications">Notification Preferences</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="profile-content">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('patch')

                                <div class="form-group">
                                    <label>Profile Picture</label>
                                    <input type="file" class="form-control" name="avatar">
                                </div>

                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                                </div>

                                <button type="submit" class="btn btn-custom">Update Profile</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
