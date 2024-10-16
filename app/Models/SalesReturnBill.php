<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturnBill extends Model
{
    use HasFactory;

    protected $table = 'salesreturnbills';

    protected $guarded = [];
    
    protected $attributes = [
        'total' => 0.0,
        'discount' => 0.0,
        'paymentamount' => 0.0,
        'editable' => 1,
        'billstatus' => 'underreview'
    ];
    
    public function items() {
        return $this->morphMany(SalesItem::class, 'morphItems', 'bill_type', 'bill_id');
    }
}
