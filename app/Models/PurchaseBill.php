<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseBill extends Model
{
    use HasFactory;

    protected $table = "purchasebills";

    protected $guarded = [];

    protected $attributes = [
        "total" => 0.0,
        "editable" => 1,
        "paidstatus" => "unpaid",
        "billstatus" => "underreview"
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i A',
        'updated_at' => 'datetime:Y-m-d h:i A',
        'issuedate' => 'date:Y-m-d',
    ];

    public function setIssuedateAttribute($value)
    {
        $this->attributes['issuedate'] = (new Carbon($value))->format('Y-m-d');
    }

    public function items() {
        return $this->hasMany(PurchaseItem::class, "purchasebill_id");
    }

    public function inventoryItems() {
        return $this->hasManyThrough(
            Inventory::class, 
            PurchaseItem::class, 
            'purchasebill_id', 
            'purchaseitem_id');
    }
}
