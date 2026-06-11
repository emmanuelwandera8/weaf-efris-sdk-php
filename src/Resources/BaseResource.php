<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Client;

/**
 * Base Resource class that namespaced API managers extend.
 */
abstract class BaseResource
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Helper to get the correct TIN context for the request.
     */
    protected function getTin(?string $customTin = null): string
    {
        $tin = $customTin ?? $this->client->config()->getDefaultTin();
        if (empty($tin)) {
            throw new \InvalidArgumentException("Company TIN must be supplied either via the Configuration defaultTin or passed explicitly.");
        }
        return $tin;
    }
}
