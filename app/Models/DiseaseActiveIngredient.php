<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiseaseActiveIngredient extends Model
{
    use HasFactory;

    protected $table = "disease_activeingredient";

    protected $guarded = [];

    public $timestamps = false;
}
