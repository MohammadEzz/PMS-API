<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function activeIngredients() {
        return $this->belongsToMany(ActiveIngredient::class,
        "disease_activeingredient",
        "disease_id",
        "activeingredient_id")
        ->withPivot('id', 'order');
    }
}
