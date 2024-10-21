<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Laravel\Scout\Searchable;

class Drug extends Model
{
    use HasFactory, Searchable;

    protected $guarded = [];

    // protected $with = ['option'];

    public function contraindications() {
        return $this->hasMany(Contraindication::class);
    }

    public function drugAlternatives() {
        return $this->hasMany(DrugAlternative::class);
    }

    public function alternatives() {
        return $this->hasMany(DrugAlternative::class);
    }

    public function createdBy(): HasOne {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    public function option(): HasOne {
        return $this->hasOne(Option::class, 'id', 'type');
    }

    public function activeIngredients() {
        return $this->belongsToMany(ActiveIngredient::class,
        'drug_activeingredient',
        'drug_id',
        'activeingredient_id')->withPivot(['id', 'concentration', 'format', 'order']);
    }

}
