<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugAlternative extends Model
{
    use HasFactory;

    protected $table = "drugalternatives";

    protected $guarded = [];

    public $timestamps = false;

    public function drug() {
        return $this->belongsTo(Drug::class);
    }
}
