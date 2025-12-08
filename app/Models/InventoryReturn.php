<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_out_id',
        'remarks',
        'quantity',
        'date_time_adjustment',
    ];

    protected $casts = [
        'date_time_adjustment' => 'datetime',
    ];

    // Relationship to inventory history (the original "in" transaction)
    public function inventoryHistory()
    {
        return $this->belongsTo(InventoryHistory::class, 'inventory_out_id');
    }
}
