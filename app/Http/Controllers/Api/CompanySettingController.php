<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use Illuminate\Http\Request;

class CompanySettingController extends Controller
{
    /**
     * Get company settings
     */
    public function index()
    {
        $settings = CompanySetting::getSettings();

        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    /**
     * Update company settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'company_logo' => 'nullable|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'company_tax_id' => 'nullable|string|max:255',
        ]);

        $settings = CompanySetting::first();

        if (!$settings) {
            $settings = CompanySetting::create($validated);
        } else {
            $settings->update($validated);
        }

        return response()->json([
            'success' => true,
            'message' => 'Company settings updated successfully',
            'data' => $settings
        ]);
    }
}
