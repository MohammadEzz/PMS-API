<?php

namespace App\Models;

use App\Http\Helpers\Api\ApiField;
use App\Http\Helpers\Api\ApiFilter;
use App\Http\Helpers\Api\ApiSort;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Debit extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function morphItems() {
        return $this->morphTo(__FUNCTION__, 'creditor_type', 'creditor_id');
    }
}
