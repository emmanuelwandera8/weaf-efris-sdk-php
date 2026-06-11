<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Exceptions\WeafException;

/**
 * Manages inventory adjustment and stock transfer endpoints.
 */
class Stock extends BaseResource
{
    /**
     * Record a stock increase (e.g. local purchase, imports).
     *
     * @param array $stockData Details of the stock intake
     * @param string|null $tinOverride Optional company TIN override
     * @return array Response data
     * @throws WeafException
     */
    public function increase(array $stockData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/increase-stock", $stockData);
    }

    /**
     * Record a stock decrease (e.g. adjustments, sales, write-offs).
     *
     * @param array $stockData Details of the stock reduction
     * @param string|null $tinOverride Optional company TIN override
     * @return array Response data
     * @throws WeafException
     */
    public function decrease(array $stockData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/decrease-stock", $stockData);
    }

    /**
     * Record a stock transfer between location points.
     *
     * @param array $transferData Details of the transfer transaction
     * @param string|null $tinOverride Optional company TIN override
     * @return array Response data
     * @throws WeafException
     */
    public function transfer(array $transferData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/transfer-stock", $transferData);
    }
}
