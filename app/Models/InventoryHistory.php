<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'distributor_id',
        'supplier_id',
        'inventory_type_id',
        'invinorout',
        'name',
        'description',
        'image',
        'reordering_level',
        'unit',
        'quantity_sold',
        'cost_price_sold',
        'sale_price_cost',
        'total',
        'return_quantity',
        'inventory_group_id',
        'date_time_adjustment',
        'expiration_date_time',
    ];

    protected $casts = [
        'expiration_date_time' => 'datetime',
    ];


    // Relationships
    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function inventoryType()
    {
        return $this->belongsTo(InventoryType::class);
    }

    public function inventoryGroup()
    {
        return $this->belongsTo(InventoryHistoryGroup::class, 'inventory_group_id');
    }
}
