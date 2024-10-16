<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActiveIngredient extends Model
{
    use HasFactory;

    protected $table = "activeingredients";

    protected $guarded = [];

    public $timestamps = false;

    public function drugs() {
        return $this->belongsToMany(Drug::class);
    }

    public function diseases() {
        return $this->belongsToMany(Disease::class);
    }

    public function drugInteractions() {
        $linkWithActiveIngredient1 = $this->hasMany(DrugInteraction::class, 'activeingredient1')->get();
        $linkWithActiveIngredient2 = $this->hasMany(DrugInteraction::class, 'activeingredient2')->get();
        return [$linkWithActiveIngredient1, $linkWithActiveIngredient2];
    }

}
