<?php

namespace Weaf\Efris\Exceptions;

/**
 * Exception thrown when validation fails on the request data.
 */
class WeafValidationException extends WeafApiException
{
    protected array $errors = [];

    public function __construct(string $message, array $errors = [], ?array $responsePayload = null)
    {
        parent::__construct($message, '01', $responsePayload, 400);
        $this->errors = $errors;
    }

    /**
     * Get the validation errors list (field => messages).
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
