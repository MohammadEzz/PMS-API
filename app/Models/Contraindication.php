<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contraindication extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function drug() {
        return $this->belongsTo(Drug::class);
    }
}
