<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    use HasFactory;

    protected $table = "druginteractions";

    protected $guarded = [];

    public $timestamps = false;

    public function activeIngredient() {
        return $this->belongsTo(ActiveIngredient::class);
    }
}
