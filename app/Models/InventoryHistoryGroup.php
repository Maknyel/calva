<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistoryGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_amount',
        'dr_number',
        'discount_percentage',
        'discounted_amount',
        'grand_total_amount',
        'date_time_adjustment',
        'customer_name',
        'customer_address',
        'customer_phone',
        'payment_method',
        'amount_paid',
    ];
}
