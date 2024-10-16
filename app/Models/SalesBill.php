<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesBill extends Model
{
    use HasFactory;

    protected $table = 'salesbills';

    protected $guarded = [];

    protected $attributes = [
        'total' => 0.0,
        'editable' => 1,
        'discount' => 0.0,
        'paymentamount' => 0.0,
        'paidstatus' => 'unpaid',
        'billstatus' => 'underreview'
    ];

    public function items() {
        return $this->morphMany(SalesItem::class, 'morphItems', 'bill_type', 'bill_id');
    }
}
