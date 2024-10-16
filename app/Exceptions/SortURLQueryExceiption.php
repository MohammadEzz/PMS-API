<?php

namespace App\Exceptions;

use Exception;
use App\Http\Helpers\Api\ApiMessagesTemplate;

class SortURLQueryExceiption extends Exception
{
    public function render() {
        $message = ApiMessagesTemplate::createResponse(false, 400, $this->getMessage());
        return $message;
    }
}
