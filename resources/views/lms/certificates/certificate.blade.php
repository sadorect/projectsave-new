<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $certificate?->certificate_id ?? 'Certificate' }} — {{ $student?->name ?? 'Student' }}</title>
  @php
    $logoPath = App\Models\CertificateSetting::get('logo_path', 'frontend/img/logo.png');
    $primaryColor = App\Models\CertificateSetting::get('primary_color', '#ff3b30');
    $secondaryColor = App\Models\CertificateSetting::get('secondary_color', '#ff6b6b');
    $fontFamily = App\Models\CertificateSetting::get('font_family', 'Helvetica Neue');
    $orgName = App\Models\CertificateSetting::get('organization_name', config('app.name', 'ASOM'));
    $directorName = App\Models\CertificateSetting::get('director_name', 'Program Director');
    $directorTitle = App\Models\CertificateSetting::get('director_title', 'Director');
    $directorCredentials = App\Models\CertificateSetting::get('director_credentials', '');
    $directorOrganization = App\Models\CertificateSetting::get('director_organization', '');
    $directorSignaturePath = App\Models\CertificateSetting::get('director_signature_path', '');
    $directorSignatureWidth = App\Models\CertificateSetting::get('director_signature_width', 150);
    $directorSignatureHeight = App\Models\CertificateSetting::get('director_signature_height', 75);
    $registrarName = App\Models\CertificateSetting::get('registrar_name', 'Registrar');
    $registrarTitle = App\Models\CertificateSetting::get('registrar_title', 'Registrar');
    $registrarSignaturePath = App\Models\CertificateSetting::get('registrar_signature_path', '');
    $registrarSignatureWidth = App\Models\CertificateSetting::get('registrar_signature_width', 150);
    $registrarSignatureHeight = App\Models\CertificateSetting::get('registrar_signature_height', 75);
    
    // Handle PDF vs browser view
    $isPdfView = isset($isPdf) && $isPdf;
  @endphp
  <style>
    @page { size: A4 landscape; margin: 0 }
    html,body{
        height:100%;
        margin:0;
        padding:0;
        font-family: '{{ $fontFamily }}', Arial, sans-serif;
        @if($isPdfView)
            background:#fff;
        @else
            background:#f4f6fb;
        @endif
    }
    
    .wrap{width:297mm;height:210mm;display:flex;align-items:center;justify-content:center;padding:10mm;box-sizing:border-box}
    
    .card{
      width:100%;height:100%;
      @if($isPdfView)
        /* PDF version with solid fallback background and dark text */
        background: {{ $primaryColor }};
        color: #fff;
      @else
        /* Browser version with gradient */
        background:linear-gradient(135deg,{{ $primaryColor }} 0%, {{ $secondaryColor }} 60%);
        color:#fff;
      @endif
      border-radius:8px;padding:18mm;box-sizing:border-box;position:relative;overflow:hidden;display:flex;align-items:center;justify-content:center;
      @if(!$isPdfView)
        box-shadow: 0 12px 30px rgba(2,6,23,0.35);
      @endif
    }
    
    @if(!$isPdfView)
        /* decorative shapes only for browser view */
        .card:before{content:'';position:absolute;right:-20%;top:-10%;width:60%;height:60%;background:rgba(255,255,255,0.06);transform:rotate(25deg);border-radius:50%}
        .card:after{content:'';position:absolute;left:-10%;bottom:-20%;width:50%;height:50%;background:rgba(255,255,255,0.03);border-radius:20%}
    @endif

    .header{display:flex;align-items:center;gap:18px}
    .logo{
        width:84px;height:84px;background:#fff;border-radius:10px;display:flex;align-items:center;justify-content:center;
        color:{{ $primaryColor }};font-weight:700;font-size:20px
    }
    .org{font-size:18px;font-weight:700;letter-spacing:1px}

    .title{margin-top:22px;font-size:36px;font-weight:800;letter-spacing:1px}

    .presented{margin-top:18px;font-size:14px;
        @if($isPdfView)
            color: #fff;
        @else
            color:rgba(255,255,255,0.95);
        @endif
    }
    .student{margin-top:6px;font-size:48px;font-weight:900;letter-spacing:1px}

    .course{
        margin-top:10px;font-size:22px;font-style:italic;max-width:75%;margin-left:auto;margin-right:auto;
        @if($isPdfView)
            color: #fff;
        @else
            color:rgba(255,255,255,0.98);
        @endif
    }

    .meta{
        display:flex;justify-content:space-between;align-items:center;margin-top:22px;
        @if($isPdfView)
            color: #fff;
        @else
            color:rgba(255,255,255,0.9);
        @endif
    }

    .left-meta{font-size:14px}
    .right-meta{font-size:13px;font-family:monospace}

    .ribbon{
        position:absolute;right:26px;top:24px;padding:8px 12px;border-radius:6px;font-weight:700;
        @if($isPdfView)
            background: rgba(255,255,255,0.2);
            color: #fff;
        @else
            background:rgba(255,255,255,0.12);
        @endif
    }

    .signatures{position:absolute;bottom:26mm;left:24mm;right:24mm;display:flex;justify-content:space-between;align-items:flex-end}
    .sig{width:40%;text-align:center}
    .sig .line{
        width:80%;margin:0 auto 6px;
        @if($isPdfView)
            border-top:2px solid #fff;
        @else
            border-top:2px solid rgba(255,255,255,0.85);
        @endif
    }
    .sig .signature-image{margin:0 auto 6px;display:block}
    .sig .who{font-weight:700}
    .sig .title{
        font-size:13px;
        @if($isPdfView)
            color: #fff;
        @else
            color:rgba(255,255,255,0.95);
        @endif
    }

    .verify{
        position:absolute;left:24mm;top:24mm;font-size:12px;
        @if($isPdfView)
            color: #fff;
        @else
            color:rgba(255,255,255,0.9);
        @endif
    }

        /* Print/PDF adjustments - Ensure proper contrast for PDF generation */
        @media print, (-webkit-print-color-adjust: exact){
            body{background:#fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact;}
            .wrap{padding:0}
            .card{
                box-shadow:none;
                border-radius:0;
                background: {{ $primaryColor }} !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: #fff !important;
            }
            .card *, .presented, .student, .course, .org, .title, .ribbon, .verify, .sig .who, .sig .title, .meta{
                color: #fff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .logo{
                background: #fff !important;
                color: {{ $primaryColor }} !important;
            }
            .sig .line{
                border-top:2px solid #fff !important;
            }
            button{display:none}
        }

        @media (max-width:900px){
            .student{font-size:28px}
            .title{font-size:26px}
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card" role="document" aria-label="Certificate">
            <div class="ribbon">{{ optional($certificate)->certificate_id ?? 'N/A' }}</div>
            <div class="verify">Verify: {{ rtrim(config('app.url',''), '/') }}/verify/{{ optional($certificate)->certificate_id ?? '' }}</div>

      <div class="header">
        <div class="logo">
          @if(file_exists(public_path($logoPath)))
            <img src="{{ asset($logoPath) }}" alt="Logo" style="max-width:100%;max-height:100%;object-fit:contain;">
          @else
            {{ strtoupper(substr($orgName,0,3)) }}
          @endif
        </div>
        <div>
          <div class="org">{{ $orgName }}</div>
          <div class="title">Certificate of Completion</div>
        </div>
      </div>            <div class="presented">This is to certify that</div>
            <div class="student">{{ strtoupper($student?->name ?? $certificate?->user?->name ?? 'Student') }}</div>

            <div class="presented">has successfully completed</div>
            <div class="course">{{ optional($course)->title ?? $certificate?->course_title ?? 'ASOM Diploma' }}</div>

            <div class="meta">
                <div class="right-meta" style="text-align: center; margin: 0 auto;">
                    Issued: {{ optional($certificate?->issued_at)->format('F j, Y') ?? (optional($completionDate)->format('F j, Y') ?? 'Pending') }}
                </div>
            </div>

            <div class="signatures" aria-hidden="true">
                <div class="sig">
                    @if($directorSignaturePath && file_exists(public_path($directorSignaturePath)))
                        <img src="{{ asset($directorSignaturePath) }}" 
                             alt="Director Signature" 
                             class="signature-image"
                             style="width: {{ $directorSignatureWidth }}px; height: {{ $directorSignatureHeight }}px; object-fit: contain;">
                    @else
                        <div class="line"></div>
                    @endif
                    <div class="who">{{ $directorName }}@if($directorCredentials), {{ $directorCredentials }}@endif</div>
                    <div class="title">{{ $directorTitle }}@if($directorOrganization), {{ $directorOrganization }}@endif</div>
                </div>

                <div class="sig">
                    @if($registrarSignaturePath && file_exists(public_path($registrarSignaturePath)))
                        <img src="{{ asset($registrarSignaturePath) }}" 
                             alt="Registrar Signature" 
                             class="signature-image"
                             style="width: {{ $registrarSignatureWidth }}px; height: {{ $registrarSignatureHeight }}px; object-fit: contain;">
                    @else
                        <div class="line"></div>
                    @endif
                    <div class="who">{{ $registrarName }}</div>
                    <div class="title">{{ $registrarTitle }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(!$isPdfView)
        <div style="text-align:center;margin:10px">
            <button onclick="window.print()" style="padding:10px 14px;border-radius:6px;background:#fff;border:none;cursor:pointer">Print / Save PDF</button>
            <a href="{{ route('lms.dashboard') }}" style="margin-left:8px;padding:10px 14px;background:#e6e9f2;color:#111;text-decoration:none;border-radius:6px">Back</a>
        </div>
    @endif
</body>
</html>
