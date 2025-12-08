<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'supplier_id',
        'inventory_type_id',
        'name',
        'description',
        'image',
        'reordering_level',
        'unit',
        'current_quantity',
        'current_cost_price',
        'current_sale_price',
        'date_time_adjustment',
        'user_id'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function inventoryType()
    {
        return $this->belongsTo(InventoryType::class);
    }
}
