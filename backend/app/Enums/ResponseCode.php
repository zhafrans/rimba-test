<?php

namespace App\Enums;

enum ResponseCode: int
{
    case SUCCESS = 200;
    case CREATED = 201;
    case BAD_REQUEST = 400;
    case NOT_FOUND = 404;
    case VALIDATION_ERROR = 422;
    case INTERNAL_ERROR = 500;

    public function message(): string
    {
        return match($this) {
            self::SUCCESS => 'Request processed successfully',
            self::CREATED => 'Resource created successfully',
            self::BAD_REQUEST => 'Bad request',
            self::NOT_FOUND => 'Resource not found',
            self::VALIDATION_ERROR => 'Validation failed',
            self::INTERNAL_ERROR => 'Internal server error occurred',
        };
    }

    public function code(): int
    {
        return $this->value;
    }
}