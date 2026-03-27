@extends('layouts.user')

@section('title', 'Delete Account')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4">Delete Account</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="text-danger mb-4">Delete Your Account</h5>
                    @if($pendingDeletionRequest)
                        <div class="alert alert-warning">
                            <strong>Pending review:</strong> your latest deletion request was submitted on {{ $pendingDeletionRequest->created_at->format('M d, Y H:i') }} and is awaiting admin action.
                        </div>
                    @endif
                    <p>This action will:</p>
                    <ul>
                        <li>Delete all your personal information</li>
                        <li>Remove your partnership records</li>
                        <li>Cancel any active subscriptions</li>
                    </ul>
                    
                    <form method="POST" action="{{ route('user.account.deletion.request') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Reason for Deletion (Optional)</label>
                            <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm your password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        
                        <x-math-captcha />

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="confirm" name="confirm_deletion" value="1" required>
                                <label class="form-check-label" for="confirm">
                                    I understand this action cannot be undone
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger"
                                {{ $pendingDeletionRequest ? 'disabled' : '' }}
                                onclick="return confirm('Are you sure you want to delete your account?')">
                            Request Account Deletion
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
