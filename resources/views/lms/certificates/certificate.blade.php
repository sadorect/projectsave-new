<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $certificate?->certificate_id ?? 'Certificate' }} — {{ $student?->name ?? 'Student' }}</title>
    @php
        $logoPath = App\Models\CertificateSetting::get('logo_path', 'frontend/img/logo.png');
        $primaryColor = App\Models\CertificateSetting::get('primary_color', '#bfa35a');
        $secondaryColor = App\Models\CertificateSetting::get('secondary_color', '#d7c47a');
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
        /* Page setup */
        @page { size: A4 landscape; margin: 0 }
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: '{{ $fontFamily }}', Arial, sans-serif;
            background: #f6f3eb;
            color: #1b1b1b;
        }

        /* Canvas */
        .wrap { width: 297mm; height: 210mm; display: flex; align-items: center; justify-content: center; padding: 8mm; box-sizing: border-box; }
        .certificate {
            width: 100%; height: 100%; position: relative; box-sizing: border-box; background: #fcfbf7;
            /* elegant double frame using pure borders (PDF-safe) */
            border: 0.8mm solid {{ $primaryColor }};
            padding: 10mm; /* spacing from outer frame */
        }
        .certificate-inner {
            position: relative; height: 100%; width: 100%; box-sizing: border-box; border: 0.4mm solid {{ $secondaryColor }};
            padding: 14mm 18mm 20mm; /* inner spacing */
        }

        /* Watermark (PDF-friendly absolute element) */
        .watermark {
            position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none;
        }
        .watermark .mark {
            opacity: 0.06; width: 120mm; height: 120mm; background-repeat: no-repeat; background-position: center; background-size: contain;
            filter: none; /* keep dompdf happy */
        }

        /* Header */
        .header { position: relative; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: 8mm; }
        .logo {
            width: 24mm; height: 24mm; border: 0.4mm solid {{ $secondaryColor }}; border-radius: 2mm; background: #fff; display: flex; align-items: center; justify-content: center;
            color: {{ $primaryColor }}; font-weight: 700; font-size: 6mm; box-sizing: border-box;
        }
        .org-block { display: flex; flex-direction: column; }
        .org-name { font-weight: 800; font-size: 6mm; letter-spacing: 0.2mm; color: #222; }
        .org-sub { font-size: 3.4mm; color: #555; }

        .badge { text-align: right; font-size: 3.2mm; color: #666; }
        .badge .id { display: inline-block; padding: 1.2mm 2.4mm; border: 0.3mm dashed {{ $secondaryColor }}; border-radius: 1.2mm; color: #444; }

        /* Title area */
        .title-wrap { text-align: center; margin-top: 6mm; }
        .cert-title { font-family: 'Georgia', 'Times New Roman', serif; font-weight: 800; font-size: 12mm; letter-spacing: 0.5mm; color: {{ $primaryColor }}; }
        .subtitle { margin-top: 1.5mm; font-size: 4mm; color: #555; }

        /* Recipient */
        .recipient-block { text-align: center; margin-top: 8mm; }
        .preline { font-size: 3.6mm; color: #444; }
        .recipient { margin-top: 2mm; font-family: 'Georgia', 'Times New Roman', serif; font-size: 11mm; font-weight: 800; color: #222; letter-spacing: 0.4mm; }
        .statement { margin-top: 2mm; font-size: 3.8mm; color: #444; }
        .course { margin-top: 2.5mm; font-size: 5mm; font-style: italic; color: #111; }

        /* Meta */
        .meta { margin-top: 8mm; display: flex; align-items: center; justify-content: space-between; font-size: 3.4mm; color: #333; }
        .verify { font-size: 3.2mm; color: #666; }

        /* Signatures row */
        .signatures { position: absolute; left: 18mm; right: 18mm; bottom: 16mm; display: flex; align-items: flex-end; justify-content: space-between; gap: 12mm; }
        .sig { flex: 1 1 0; text-align: center; }
        .signature-image { display: block; margin: 0 auto 2mm; }
        .sig .line { width: 75%; margin: 0 auto 2mm; border-top: 0.4mm solid #333; }
        .sig .who { font-weight: 700; color: #222; font-size: 3.6mm; }
        .sig .title { color: #555; font-size: 3.2mm; }

        /* Print adjustments */
        @media print, (-webkit-print-color-adjust: exact) {
            html, body { background: #ffffff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .wrap { padding: 0; }
            .certificate, .certificate-inner { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            button { display: none; }
        }

        /* Responsive preview (browser only) */
        @media screen and (max-width: 1100px) {
            .recipient { font-size: 8mm; }
            .cert-title { font-size: 10mm; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="certificate" role="document" aria-label="Certificate">
            <div class="certificate-inner">
                {{-- Watermark logo (if any) --}}
                <div class="watermark" aria-hidden="true">
                    @if($logoPath && file_exists(public_path($logoPath)))
                        <div class="mark" style="background-image: url('{{ asset($logoPath) }}');"></div>
                    @else
                        <div class="mark" style="background-image: none;"></div>
                    @endif
                </div>

                <div class="header">
                    <div class="brand">
                        <div class="logo">
                            @if($logoPath && file_exists(public_path($logoPath)))
                                <img src="{{ asset($logoPath) }}" alt="{{ $orgName }} Logo" style="max-width: 90%; max-height: 90%; object-fit: contain;" />
                            @else
                                {{ strtoupper(substr($orgName,0,3)) }}
                            @endif
                        </div>
                        <div class="org-block">
                            <div class="org-name">{{ $orgName }}</div>
                            <div class="org-sub">{{ App\Models\CertificateSetting::get('organization_tagline', 'Raising Competent New Testament Ministers') }}</div>
                        </div>
                    </div>

                    <div class="badge">
                        <div>Certificate ID</div>
                        <div class="id">{{ optional($certificate)->certificate_id ?? 'N/A' }}</div>
                    </div>
                </div>

                <div class="title-wrap">
                    <div class="cert-title">Certificate of Completion</div>
                    <div class="subtitle">This certificate acknowledges the achievement described below</div>
                </div>

                <div class="recipient-block">
                    <div class="preline">This is to certify that</div>
                    <div class="recipient">{{ strtoupper($student?->name ?? $certificate?->user?->name ?? 'STUDENT') }}</div>
                    <div class="statement">has successfully completed</div>
                    <div class="course">{{ optional($course)->title ?? $certificate?->course_title ?? 'Diploma in Ministry' }}</div>
                </div>

                        <div class="meta">
                            <div>
                                <div>
                                    <strong>Issued:</strong>
                                    {{ optional($certificate?->issued_at)->format('F j, Y') ?? (optional($completionDate)->format('F j, Y') ?? 'Pending') }}
                                </div>
                                @if(isset($finalGrade) && $finalGrade !== null)
                                    <div>
                                        <strong>Final Grade:</strong> {{ number_format($finalGrade, 1) }}%
                                    </div>
                                @endif
                            </div>
                            <div class="verify">
                                Verify at: {{ $certificate?->verification_url ?? (isset($certificateId) ? route('certificates.public.verify', $certificateId) : '') }}
                            </div>
                        </div>

                <div class="signatures" aria-hidden="true">
                    <div class="sig">
                        @if($directorSignaturePath && file_exists(public_path($directorSignaturePath)))
                            <img src="{{ asset($directorSignaturePath) }}" alt="Director Signature" class="signature-image" style="width: {{ $directorSignatureWidth }}px; height: {{ $directorSignatureHeight }}px; object-fit: contain;" />
                        @else
                            <div class="line"></div>
                        @endif
                        <div class="who">{{ $directorName }}@if($directorCredentials), {{ $directorCredentials }}@endif</div>
                        <div class="title">{{ $directorTitle }}@if($directorOrganization), {{ $directorOrganization }}@endif</div>
                    </div>

                    <div class="sig">
                        @if($registrarSignaturePath && file_exists(public_path($registrarSignaturePath)))
                            <img src="{{ asset($registrarSignaturePath) }}" alt="Registrar Signature" class="signature-image" style="width: {{ $registrarSignatureWidth }}px; height: {{ $registrarSignatureHeight }}px; object-fit: contain;" />
                        @else
                            <div class="line"></div>
                        @endif
                        <div class="who">{{ $registrarName }}</div>
                        <div class="title">{{ $registrarTitle }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!$isPdfView)
        <div style="text-align:center;margin:10px">
            <button onclick="window.print()" style="padding:10px 14px;border-radius:6px;background:#fff;border:1px solid #ddd;cursor:pointer">Print / Save PDF</button>
            <a href="{{ route('lms.dashboard') }}" style="margin-left:8px;padding:10px 14px;background:#eee;color:#111;text-decoration:none;border-radius:6px;border:1px solid #ddd">Back</a>
        </div>
    @endif
</body>
</html>
