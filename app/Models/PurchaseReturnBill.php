<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnBill extends Model
{
    use HasFactory;

    protected $table = "purchasereturnbills";

    protected $guarded = [];

    protected $attributes = [
        "total" => 0.0,
        "editable" => 1,
        "billstatus" => "underreview"
    ];


    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i A',
        'updated_at' => 'datetime:Y-m-d h:i A',
        'issuedate' => 'date:Y-m-d',
    ];
    
    public function items() {
        return $this->hasMany(PurchaseReturnItem::class, 'purchasereturnbill_id');
    }

    public function purchaseBill() {
        return $this->belongsTo(PurchaseBill::class, 'purchasebill_id');
    }
}
