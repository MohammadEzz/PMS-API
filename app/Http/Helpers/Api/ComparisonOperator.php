<?php

namespace App\Http\Helpers\Api;

class ComparisonOperator {

    const COMPARISON = [
        'eq' => '=',
        'neq' => '<>',
        'gt' => '>',
        'gte' => '>=',
        'lt' => '<',
        'lte' => '<=',
        'in' => 'IN',
        'nin' => 'NOT IN',
        'btw' => 'BETWEEN',
        'nbtw' => 'NOT BETWEEN',
        'like' => 'LIKE',
        '_'=>'_',
    ];
}
