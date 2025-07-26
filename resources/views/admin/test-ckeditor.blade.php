@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>CKEditor Test</h2>
    <form>
        <div class="form-group">
            <label for="test-editor">Test Editor</label>
            <textarea id="test-editor" name="content">Test content for CKEditor</textarea>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded');
    console.log('ClassicEditor available:', typeof ClassicEditor !== 'undefined');
    
    if (typeof ClassicEditor !== 'undefined') {
        const element = document.querySelector('#test-editor');
        if (element) {
            console.log('Element found:', element);
            ClassicEditor
                .create(element)
                .then(editor => {
                    console.log('CKEditor initialized successfully', editor);
                })
                .catch(error => {
                    console.error('CKEditor initialization error:', error);
                });
        } else {
            console.error('Test editor element not found');
        }
    } else {
        console.error('ClassicEditor not available');
    }
});
</script>
@endsection
