<?php

namespace App\Exceptions;

use App\Http\Helpers\Api\ApiMessagesTemplate;
use Exception;

class URLParameterException extends Exception
{
    public function render() {
        $message = ApiMessagesTemplate::createResponse(false, 400, $this->getMessage());
        return $message;
    }
}
