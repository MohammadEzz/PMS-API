<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $table = "inventory";

    protected $guarded = [];

    public $timestamps = false;

    public function lastPrice() {
        return Price::query()->where('prices.drug_id', '=', $this->drug_id)->orderBy('id', 'desc')->first();
    }

    public function drug() {
        return $this->belongsTo(Drug::class);
    }
}
