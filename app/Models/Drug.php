<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Drug extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    public function contraindications() {
        return $this->hasMany(Contraindication::class);
    }

    public function drugAlternatives() {
        return $this->hasMany(DrugAlternative::class);
    }

    public function alternatives() {
        return $this->hasMany(DrugAlternative::class);
    }

    public function activeIngredients() {
        return $this->belongsToMany(ActiveIngredient::class,
        'drug_activeingredient',
        'drug_id',
        'activeingredient_id')->withPivot(['id', 'concentration', 'format', 'order']);
    }

}
