<?php

namespace Bike\Partner\Exception\Logic;

use Bike\Partner\Error\ErrorCode;

class LogicException extends \Exception implements LogicExceptionInterface
{
    public function __construct($message = null)
    {
        if (!$message) {
            $message = '出错了';
        }

        parent::__construct($message, ErrorCode::LOGIC_ERROR);
    }
}
