<?php

namespace Weaf\Efris\Exceptions;

/**
 * Exception thrown when the WEAF EFRIS API returns an error status code.
 */
class WeafApiException extends WeafException
{
    protected string $returnCode;
    protected ?array $responsePayload;

    public function __construct(string $message, string $returnCode, ?array $responsePayload = null, int $httpCode = 0)
    {
        parent::__construct($message, $httpCode);
        $this->returnCode = $returnCode;
        $this->responsePayload = $responsePayload;
    }

    /**
     * Get the EFRIS Return Code (e.g. '02', '04', '99').
     */
    public function getReturnCode(): string
    {
        return $this->returnCode;
    }

    /**
     * Get the full raw API response body.
     */
    public function getResponsePayload(): ?array
    {
        return $this->responsePayload;
    }
}
