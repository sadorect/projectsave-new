<x-layouts.lms>
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        {{ session('warning') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="mb-4">{{ $course->title }}</h1>
                
                @if($course->featured_image)
                    <img src="{{ Storage::disk('s3')->url($course->featured_image) }}" 
                         alt="{{ $course->title }}" 
                         class="img-fluid rounded mb-4"
                         onerror="this.src='{{ asset('frontend/img/course-placeholder.jpg') }}'; this.onerror=null;">
                @else
                    <img src="{{ asset('frontend/img/course-placeholder.jpg') }}" 
                         alt="{{ $course->title }}" 
                         class="img-fluid rounded mb-4">
                @endif

                <!-- Course Overview Tabs -->
                <div class="course-tabs mb-4">
                    <ul class="nav nav-tabs" id="courseTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#description">Description</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#objectives">Objectives</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#outcomes">Outcomes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#evaluation">Evaluation</a>
                        </li>
                    </ul>

                    <div class="tab-content p-3 border border-top-0">
                        <div class="tab-pane fade show active" id="description">
                            {!! $course->description !!}
                        </div>
                        <div class="tab-pane fade" id="objectives">
                            {!! $course->objectives !!}
                        </div>
                        <div class="tab-pane fade" id="outcomes">
                            {!! $course->outcomes !!}
                        </div>
                        <div class="tab-pane fade" id="evaluation">
                            {!! $course->evaluation !!}
                        </div>
                    </div>
                </div>

                <!-- Course Resources Section -->
                @if($isEnrolled)
                    <div class="course-resources">
                        <h3 class="mb-4"><i class="fas fa-book-reader"></i> Course Resources</h3>
                        
                        <!-- Recommended Books -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4 class="mb-0"><i class="fas fa-book"></i> Recommended Books</h4>
                            </div>
                            <div class="card-body">
                                {!! $course->recommended_books !!}
                            </div>
                        </div>

                        <!-- Course Materials -->
                        @if($course->documents)
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="mb-0"><i class="fas fa-file-alt"></i> Course Materials</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach(json_decode($course->documents) as $document)
                                            <div class="col-md-6 mb-3">
                                                <div class="document-card">
                                                    <i class="fas fa-file-pdf document-icon"></i>
                                                    <div class="document-info">
                                                        <h5>{!! $document->name !!}</h5>
                                                        <small>{{ number_format($document->size / 1048576, 2) }} MB</small>
                                                        <a href="{{ $document->path }}" class="btn btn-sm btn-primary mt-2" download>
                                                            <i class="fas fa-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Enrollment Actions -->
                <div class="course-actions mt-4">
                    @if($isEnrolled)
                        <a href="{{ route('lms.lessons.index', $course->slug) }}" class="btn btn-primary">Continue Learning</a>
                        <form action="{{ route('lms.courses.unenroll', $course->slug) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Unenroll</button>
                        </form>
                    @else
                        <form action="{{ route('lms.courses.enroll', $course->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success">Enroll Now</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
    .document-card {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .document-icon {
        font-size: 2rem;
        color: #dc3545;
        margin-bottom: 10px;
    }

    .nav-tabs .nav-link.active {
        font-weight: bold;
    }
    </style>
</x-layouts.lms>
