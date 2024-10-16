<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function debit() {
        return $this->morphOne(Debit::class, 'morphItems', 'creditor_type', 'creditor_id');
    }
}
