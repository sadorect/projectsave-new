<x-layouts.asom-auth>
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-800">My Certificates</h1>
                <a href="{{ route('asom.welcome') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>

            @if($certificates->isEmpty())
                <div class="text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-certificate text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Certificates Yet</h3>
                    <p class="text-gray-500 mb-6">Complete courses and pass exams to earn certificates</p>
                    <a href="{{ route('asom.welcome') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition duration-200">
                        View Available Courses
                    </a>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($certificates as $certificate)
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-6 border border-blue-200 hover:shadow-lg transition duration-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <i class="fas fa-certificate text-2xl text-indigo-600 mr-3"></i>
                                    <div>
                                        <h3 class="font-semibold text-gray-800">{{ $certificate->course->title }}</h3>
                                        <p class="text-sm text-gray-600">{{ $certificate->certificate_id }}</p>
                                    </div>
                                </div>
                                @if($certificate->is_approved)
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Approved
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-1 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Issued Date:</span>
                                    <span class="font-medium">{{ $certificate->issued_at->format('M j, Y') }}</span>
                                </div>
                                @if($certificate->final_grade)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Final Grade:</span>
                                        <span class="font-medium text-green-600">{{ number_format($certificate->final_grade, 1) }}%</span>
                                    </div>
                                @endif
                                @if($certificate->is_approved && $certificate->approved_at)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Approved:</span>
                                        <span class="font-medium">{{ $certificate->approved_at->format('M j, Y') }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="flex space-x-2">
                                <a href="{{ route('lms.certificates.show', $certificate) }}" 
                                   class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-eye mr-2"></i>View
                                </a>
                                @if($certificate->is_approved)
                                    <a href="{{ route('lms.certificates.download', $certificate) }}" 
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded-lg transition duration-200 text-sm">
                                        <i class="fas fa-download mr-2"></i>Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-layouts.asom-auth>
