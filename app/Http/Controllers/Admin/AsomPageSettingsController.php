<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Lms\AsomPageSettings;
use Illuminate\Http\Request;

class AsomPageSettingsController extends Controller
{
    public function edit()
    {
        $pageContent = AsomPageSettings::current();

        return view('admin.asom-page.edit', compact('pageContent'));
    }

    public function update(Request $request)
    {
        $current = AsomPageSettings::current();

        // Merge submitted fields into the existing content structure
        $updated = $this->mergeSubmitted($current, $request->all());

        AsomPageSettings::save($updated);

        return redirect()->route('admin.asom-page.edit')
            ->with('success', 'ASOM page settings updated.');
    }

    private function mergeSubmitted(array $current, array $input): array
    {
        // Remove CSRF token and method spoofing fields
        unset($input['_token'], $input['_method']);

        return array_replace_recursive($current, $input);
    }
}
