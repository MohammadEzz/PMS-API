<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = "purchasereturnitems";

    public function bill() {
        return $this->belongsTo(PurchaseReturnBill::class, 'purchasereturnbill_id');
    }

    public function purchaseItem() {
        return $this->belongsTo(PurchaseItem::class, 'purchaseitem_id');
    }
}
 