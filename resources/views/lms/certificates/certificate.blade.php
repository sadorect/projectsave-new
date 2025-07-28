<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate of Completion - {{ $student->name }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Times New Roman', serif;
            background: #f5f5f5;
        }

        .certificate-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .certificate {
            position: relative;
            width: 1683.78px;
            height: 1190.55px;
            background-image: url('{{ asset('images/certificates/certificate-background.png') }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            transform: scale(0.6);
            transform-origin: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Dynamic text overlays positioned based on the original certificate design */
        .student-name {
            position: absolute;
            left: 50%;
            top: 45%;
            transform: translateX(-50%);
            font-size: 48px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            font-family: 'Georgia', serif;
            letter-spacing: 2px;
        }

        .course-title {
            position: absolute;
            left: 50%;
            top: 55%;
            transform: translateX(-50%);
            font-size: 32px;
            color: #34495e;
            text-align: center;
            font-style: italic;
            max-width: 800px;
            line-height: 1.2;
        }

        .completion-date {
            position: absolute;
            left: 50%;
            top: 75%;
            transform: translateX(-50%);
            font-size: 24px;
            color: #2c3e50;
            text-align: center;
            font-weight: 500;
        }

        .certificate-id {
            position: absolute;
            right: 100px;
            bottom: 100px;
            font-size: 16px;
            color: #7f8c8d;
            font-family: 'Courier New', monospace;
        }

        .verification-url {
            position: absolute;
            left: 100px;
            bottom: 100px;
            font-size: 14px;
            color: #7f8c8d;
            font-family: 'Arial', sans-serif;
        }

        .grade-info {
            position: absolute;
            left: 50%;
            top: 65%;
            transform: translateX(-50%);
            font-size: 20px;
            color: #27ae60;
            text-align: center;
            font-weight: bold;
        }

        /* Print styles */
        @media print {
            body {
                background: white;
            }
            
            .certificate-container {
                min-height: auto;
                padding: 0;
            }
            
            .certificate {
                transform: scale(1);
                page-break-inside: avoid;
            }
        }

        /* Responsive design */
        @media (max-width: 1200px) {
            .certificate {
                transform: scale(0.5);
            }
        }

        @media (max-width: 768px) {
            .certificate {
                transform: scale(0.3);
            }
            
            .student-name {
                font-size: 36px;
            }
            
            .course-title {
                font-size: 24px;
            }
            
            .completion-date {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate">
            <!-- Student Name -->
            <div class="student-name">
                {{ strtoupper($student->name) }}
            </div>

            <!-- Course Title -->
            <div class="course-title">
                {{ $course->title }}
            </div>

            <!-- Grade Information (if exam passed) -->
            @if(isset($finalGrade) && $finalGrade >= 70)
            <div class="grade-info">
                Final Grade: {{ number_format($finalGrade, 1) }}%
            </div>
            @endif

            <!-- Completion Date -->
            <div class="completion-date">
                {{ $completionDate->format('F j, Y') }}
            </div>

            <!-- Certificate ID for verification -->
            <div class="certificate-id">
                Certificate ID: {{ $certificateId }}
            </div>

            <!-- Verification URL -->
            <div class="verification-url">
                Verify at: {{ config('app.url') }}/verify/{{ $certificateId }}
            </div>
        </div>
    </div>

    <!-- Print button for testing -->
    <div style="text-align: center; margin: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;">
            Print Certificate
        </button>
        <a href="{{ route('lms.dashboard') }}" style="margin-left: 20px; padding: 10px 20px; background: #95a5a6; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
            Back to Dashboard
        </a>
    </div>
</body>
</html>
