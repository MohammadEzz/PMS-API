<?php

namespace App\Http\Helpers\Api;

class ComparisonOperator {

    const COMPARISON = [
        '_'=>'_',
        'gte' => '>=',
        'gt' => '>',
        'lte' => '<=',
        'lt' => '<',
        'eq' => '=',
        'neq' => '<>',
        'in' => 'IN',
        'nin' => 'NOT IN',
        'btw' => 'BETWEEN',
        'nbtw' => 'NOT BETWEEN',
        'like' => 'LIKE',
    ];
}
