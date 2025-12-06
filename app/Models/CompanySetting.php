<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'company_logo',
        'company_address',
        'company_phone',
        'company_email',
        'company_tax_id',
    ];

    /**
     * Get the company settings (singleton pattern)
     */
    public static function getSettings()
    {
        return self::first() ?? self::create([
            'company_name' => 'Calva Pharma',
            'company_logo' => 'assets/images/calva.jpg',
            'company_address' => '123 Main Street, City, Province, ZIP Code',
            'company_phone' => '(123) 456-7890',
            'company_email' => 'bcdotg@gmail.com',
            'company_tax_id' => '409-604-053-00000',
        ]);
    }
}
