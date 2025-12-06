<?php

use App\Models\CompanySetting;

if (!function_exists('company_settings')) {
    /**
     * Get company settings from database
     * Returns the company settings object with all configuration
     *
     * @return \App\Models\CompanySetting|null
     */
    function company_settings()
    {
        return CompanySetting::getSettings();
    }
}

if (!function_exists('company_name')) {
    /**
     * Get the company name
     *
     * @return string
     */
    function company_name()
    {
        $settings = company_settings();
        return $settings ? $settings->company_name : config('app.name', 'Laravel');
    }
}

if (!function_exists('company_logo')) {
    /**
     * Get the company logo path
     *
     * @param bool $fullUrl Whether to return full URL or just path
     * @return string
     */
    function company_logo($fullUrl = false)
    {
        $settings = company_settings();
        $logoPath = $settings && $settings->company_logo
            ? $settings->company_logo
            : 'assets/images/calva.jpg';

        return $fullUrl ? asset($logoPath) : $logoPath;
    }
}

if (!function_exists('company_address')) {
    /**
     * Get the company address
     *
     * @return string
     */
    function company_address()
    {
        $settings = company_settings();
        return $settings ? $settings->company_address : '';
    }
}

if (!function_exists('company_phone')) {
    /**
     * Get the company phone number
     *
     * @return string|null
     */
    function company_phone()
    {
        $settings = company_settings();
        return $settings ? $settings->company_phone : null;
    }
}

if (!function_exists('company_email')) {
    /**
     * Get the company email
     *
     * @return string|null
     */
    function company_email()
    {
        $settings = company_settings();
        return $settings ? $settings->company_email : null;
    }
}

if (!function_exists('company_tax_id')) {
    /**
     * Get the company tax ID
     *
     * @return string|null
     */
    function company_tax_id()
    {
        $settings = company_settings();
        return $settings ? $settings->company_tax_id : null;
    }
}
