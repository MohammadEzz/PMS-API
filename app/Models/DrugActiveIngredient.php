<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugActiveIngredient extends Model
{
    use HasFactory;

    protected $table = 'drug_activeingredient';

    public $timestamps = false;
}
