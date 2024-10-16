<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesItem extends Model
{
    use HasFactory;

    protected $table = 'salesitems';

    protected $guarded = [];
    
    public $timestamps = false;
    
    protected $attributes = [
        'discount' => 0
    ];
    
    public function morphItems() {
        return $this->morphTo(__FUNCTION__, 'bill_type', 'bill_id');
    }
}
