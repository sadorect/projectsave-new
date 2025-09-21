<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateSettingsController extends Controller
{
    /**
     * Display certificate settings form
     */
    public function index()
    {
        $settings = [
            'logo_path' => CertificateSetting::get('logo_path', 'frontend/img/logo.png'),
            'primary_color' => CertificateSetting::get('primary_color', '#ff3b30'),
            'secondary_color' => CertificateSetting::get('secondary_color', '#ff6b6b'),
            'font_family' => CertificateSetting::get('font_family', 'Helvetica Neue'),
            'organization_name' => CertificateSetting::get('organization_name', config('app.name', 'ASOM')),
            'organization_tagline' => CertificateSetting::get('organization_tagline', 'Certificate Services'),
            'director_name' => CertificateSetting::get('director_name', 'Program Director'),
            'director_title' => CertificateSetting::get('director_title', 'Director'),
            'director_credentials' => CertificateSetting::get('director_credentials', ''),
            'director_organization' => CertificateSetting::get('director_organization', ''),
            'director_signature_path' => CertificateSetting::get('director_signature_path', ''),
            'director_signature_width' => CertificateSetting::get('director_signature_width', 150),
            'director_signature_height' => CertificateSetting::get('director_signature_height', 75),
            'registrar_name' => CertificateSetting::get('registrar_name', 'Registrar'),
            'registrar_title' => CertificateSetting::get('registrar_title', 'Registrar'),
            'registrar_signature_path' => CertificateSetting::get('registrar_signature_path', ''),
            'registrar_signature_width' => CertificateSetting::get('registrar_signature_width', 150),
            'registrar_signature_height' => CertificateSetting::get('registrar_signature_height', 75),
        ];

        return view('admin.certificate-settings.index', compact('settings'));
    }

    /**
     * Update certificate settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'organization_name' => 'nullable|string|max:255',
            'organization_tagline' => 'nullable|string|max:255',
            'director_name' => 'nullable|string|max:255',
            'director_title' => 'nullable|string|max:255',
            'director_credentials' => 'nullable|string|max:255',
            'director_organization' => 'nullable|string|max:255',
            'director_signature_width' => 'nullable|integer|min:50|max:300',
            'director_signature_height' => 'nullable|integer|min:25|max:150',
            'registrar_name' => 'nullable|string|max:255',
            'registrar_title' => 'nullable|string|max:255',
            'registrar_signature_width' => 'nullable|integer|min:50|max:300',
            'registrar_signature_height' => 'nullable|integer|min:25|max:150',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'director_signature_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'registrar_signature_image' => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('certificates', 'public');
            CertificateSetting::set('logo_path', 'storage/' . $logoPath, 'file', 'Certificate logo image path');
        }

        // Handle director signature image upload
        if ($request->hasFile('director_signature_image')) {
            $directorSignaturePath = $request->file('director_signature_image')->store('certificates/signatures', 'public');
            CertificateSetting::set('director_signature_path', 'storage/' . $directorSignaturePath, 'file', 'Director signature image path');
        }

        // Handle registrar signature image upload
        if ($request->hasFile('registrar_signature_image')) {
            $registrarSignaturePath = $request->file('registrar_signature_image')->store('certificates/signatures', 'public');
            CertificateSetting::set('registrar_signature_path', 'storage/' . $registrarSignaturePath, 'file', 'Registrar signature image path');
        }

        // Update other settings
        $settings = [
            'primary_color' => 'Primary certificate color',
            'secondary_color' => 'Secondary certificate color',
            'font_family' => 'Certificate font family',
            'organization_name' => 'Organization name on certificate',
            'organization_tagline' => 'Organization tagline/subtitle under name',
            'director_name' => 'Director signature name',
            'director_title' => 'Director title',
            'director_credentials' => 'Director credentials',
            'director_organization' => 'Director organization',
            'director_signature_width' => 'Director signature width in pixels',
            'director_signature_height' => 'Director signature height in pixels',
            'registrar_name' => 'Registrar signature name',
            'registrar_title' => 'Registrar title',
            'registrar_signature_width' => 'Registrar signature width in pixels',
            'registrar_signature_height' => 'Registrar signature height in pixels',
        ];

        foreach ($settings as $key => $description) {
            if ($request->filled($key)) {
                CertificateSetting::set($key, $request->input($key), 'string', $description);
            }
        }

        return redirect()->back()->with('success', 'Certificate settings updated successfully.');
    }
}
