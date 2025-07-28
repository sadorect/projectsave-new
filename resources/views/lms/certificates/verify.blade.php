<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center">
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900">
                    Certificate Verification
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Verify the authenticity of ASOM certificates
                </p>
            </div>

            @if($found)
                <div class="bg-white shadow-lg rounded-lg p-8">
                    <div class="text-center mb-6">
                        <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-green-800 mb-2">Certificate Verified ✓</h3>
                        <p class="text-gray-600">This certificate is authentic and has been verified.</p>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Certificate ID</label>
                                <p class="text-gray-900 font-mono text-sm bg-gray-50 p-2 rounded">{{ $certificate->certificate_id }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Student Name</label>
                                <p class="text-gray-900">{{ $certificate->user->name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Course</label>
                                <p class="text-gray-900"> SAMPLE CERTIFICATE </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Completion Date</label>
                                <p class="text-gray-900">{{ $certificate->completed_at->format('F j, Y') }}</p>
                            </div>

                            @if($certificate->final_grade)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Final Grade</label>
                                <p class="text-gray-900">{{ number_format($certificate->final_grade, 1) }}%</p>
                            </div>
                            @endif
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                @if($certificate->is_approved)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-2"></i>Approved & Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-2"></i>Pending Approval
                                    </span>
                                @endif
                            </div>

                            @if($certificate->is_approved && $certificate->approver)
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Approved By</label>
                                <p class="text-gray-900">{{ $certificate->approver->name }}</p>
                                <p class="text-sm text-gray-500">{{ $certificate->approved_at->format('F j, Y \a\t g:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white shadow-lg rounded-lg p-8">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-times-circle text-red-600 text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-red-800 mb-2">Certificate Not Found</h3>
                        <p class="text-gray-600 mb-4">
                            The certificate ID "{{ $certificateId }}" could not be verified.
                        </p>
                        <p class="text-sm text-gray-500">
                            Please check the certificate ID and try again, or contact the issuing institution.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Verification Form -->
            <div class="bg-white shadow-lg rounded-lg p-6 mt-8">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Verify Another Certificate</h4>
                <form action="{{ route('certificates.verify', ['certificateId' => 'PLACEHOLDER']) }}" method="GET" onsubmit="return updateAction()">
                    <div class="mb-4">
                        <label for="certificate_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Certificate ID
                        </label>
                        <input type="text" 
                               id="certificate_id" 
                               name="certificate_id" 
                               placeholder="Enter certificate ID (e.g., ASOM-ABC12345-2024)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                               required>
                    </div>
                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-md transition duration-200">
                        <i class="fas fa-search mr-2"></i>Verify Certificate
                    </button>
                </form>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500">
                    ← Back to Login
                </a>
            </div>
        </div>
    </div>

    <script>
        function updateAction() {
            const form = event.target;
            const certificateId = document.getElementById('certificate_id').value;
            if (certificateId) {
                form.action = form.action.replace('PLACEHOLDER', certificateId);
            }
            return true;
        }
    </script>
</body>
</html>
