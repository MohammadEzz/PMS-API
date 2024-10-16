<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $table = "purchaseitems";

    protected $guarded = [];

    protected $attributes = [
        "bonus" => 0
    ];

    public function bill() {
        return $this->belongsTo(Purchasebill::class, "purchasebill_id", "id");
    }

    public function drug() {
        return $this->belongsTo(Drug::class, "drug_id", "id");
    }
}
