<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate Verification - ProjectSave International Ministry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .certificate-card {
            border: 3px solid #198754;
            border-radius: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }
        .verification-badge {
            background: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }
        .invalid-badge {
            background: linear-gradient(135deg, #dc3545 0%, #f86c6c 100%);
        }
        .certificate-seal {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="display-6 fw-bold text-primary">
                        <i class="bi bi-shield-check"></i>
                        Certificate Verification
                    </h1>
                    <p class="lead text-muted">
                        ProjectSave International Ministry - ASOM Program
                    </p>
                </div>

                @if($certificate)
                    <!-- Valid Certificate -->
                    <div class="certificate-card p-4 mb-4">
                        <!-- Verification Status -->
                        <div class="text-center mb-4">
                            <div class="certificate-seal">
                                <i class="bi bi-check-circle-fill text-white fs-1"></i>
                            </div>
                            <h3 class="mt-3 mb-2">
                                <span class="badge verification-badge text-white px-4 py-2 fs-6">
                                    <i class="bi bi-shield-check"></i> VERIFIED CERTIFICATE
                                </span>
                            </h3>
                            <p class="text-muted">This certificate is authentic and issued by ProjectSave International Ministry</p>
                        </div>

                        <!-- Certificate Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Certificate Information</h5>
                                <div class="mb-2">
                                    <strong>Certificate ID:</strong>
                                    <code class="ms-2">{{ $certificate->certificate_id }}</code>
                                </div>
                                <div class="mb-2">
                                    <strong>Type:</strong>
                                    @if($certificate->course_id)
                                        <span class="badge bg-secondary ms-2">{{ SAMPLE CERTIFICATE }}</span>
                                    @else
                                        <span class="badge bg-primary ms-2">
                                            <i class="bi bi-mortarboard"></i> Diploma in Ministry
                                        </span>
                                    @endif
                                </div>
                                <div class="mb-2">
                                    <strong>Final Grade:</strong>
                                    <span class="badge bg-success ms-2">{{ $certificate->final_grade }}%</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Issued Date:</strong>
                                    <span class="ms-2">{{ $certificate->issued_at->format('F d, Y') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="fw-bold mb-3">Graduate Information</h5>
                                <div class="mb-2">
                                    <strong>Name:</strong>
                                    <span class="ms-2">{{ $certificate->user->name }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Email:</strong>
                                    <span class="ms-2">{{ $certificate->user->email }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Completion Date:</strong>
                                    <span class="ms-2">{{ $certificate->completed_at->format('F d, Y') }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Program:</strong>
                                    <span class="ms-2">ASOM (Archippus School of Ministry)</span>
                                </div>
                            </div>
                        </div>

                        @if(!$certificate->course_id)
                            <!-- Diploma Program Details -->
                            <div class="mt-4 p-3 bg-white rounded">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-mortarboard text-primary"></i>
                                    Diploma in Ministry Program
                                </h6>
                                <p class="mb-2 text-muted">
                                    This diploma certifies that the graduate has successfully completed all required coursework 
                                    in the Archippus School of Ministry (ASOM) program, including:
                                </p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Bible Introduction</li>
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Hermeneutics</li>
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Ministry Vitals</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled mb-0">
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Spiritual Gifts & Ministry</li>
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Biblical Counseling</li>
                                            <li><i class="bi bi-check-circle text-success me-2"></i>Homiletics</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Institution Information -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h6 class="fw-bold mb-1">ProjectSave International Ministry</h6>
                                    <p class="mb-0 text-muted">
                                        <i class="bi bi-geo-alt"></i> Archippus School of Ministry (ASOM)
                                    </p>
                                </div>
                                <div class="col-md-4 text-end">
                                    <img src="/path/to/ministry/logo.png" alt="Ministry Logo" class="img-fluid" style="max-height: 60px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Footer -->
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i>
                            This verification was generated on {{ now()->format('F d, Y \a\t g:i A') }}
                        </small>
                    </div>
                @else
                    <!-- Invalid Certificate -->
                    <div class="text-center">
                        <div class="certificate-seal bg-danger">
                            <i class="bi bi-x-circle-fill text-white fs-1"></i>
                        </div>
                        <h3 class="mt-3 mb-2">
                            <span class="badge invalid-badge text-white px-4 py-2 fs-6">
                                <i class="bi bi-shield-x"></i> INVALID CERTIFICATE
                            </span>
                        </h3>
                        <div class="alert alert-danger">
                            <h5 class="alert-heading">Certificate Not Found</h5>
                            <p class="mb-2">
                                The certificate ID you provided could not be verified. This could mean:
                            </p>
                            <ul class="mb-3">
                                <li>The certificate ID is incorrect or contains typos</li>
                                <li>The certificate has not been approved yet</li>
                                <li>The certificate does not exist in our system</li>
                            </ul>
                            <hr>
                            <p class="mb-0">
                                If you believe this is an error, please contact ProjectSave International Ministry directly.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-house"></i> Return to Home
                    </a>
                    @if($certificate)
                        <button onclick="window.print()" class="btn btn-outline-secondary">
                            <i class="bi bi-printer"></i> Print Verification
                        </button>
                    @endif
                </div>

                <!-- Anti-fraud Notice -->
                <div class="mt-5 p-3 bg-warning bg-opacity-10 border border-warning rounded">
                    <h6 class="fw-bold text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Anti-Fraud Notice
                    </h6>
                    <p class="mb-0 small">
                        This verification system is the only official way to verify certificates issued by 
                        ProjectSave International Ministry's ASOM program. Any certificate not verifiable 
                        through this system should be considered invalid. For questions or to report 
                        suspected fraud, please contact us directly.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Styles -->
    <style media="print">
        body { margin: 0; }
        .btn, .mt-4, .mt-5 { display: none !important; }
        .certificate-card { border: 2px solid #000 !important; }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
