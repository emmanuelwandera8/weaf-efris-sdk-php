<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Exceptions\WeafException;

/**
 * Manages invoice-related endpoints.
 */
class Invoices extends BaseResource
{
    /**
     * Generate a fiscal invoice in EFRIS.
     *
     * @param array $invoiceData The invoice structure payload
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function create(array $invoiceData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/generate-fiscal-invoice", $invoiceData);
    }

    /**
     * Query details/receipt for a previously created invoice.
     *
     * @param string $invoiceNo The invoice number to retrieve
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data containing invoice details
     * @throws WeafException
     */
    public function retrieve(string $invoiceNo, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->get("/{$tin}/invoice-details/{$invoiceNo}");
    }

    /**
     * Query invoice receipts from EFRIS.
     *
     * @param array $queryData Query parameters (e.g. invoiceNo, fdn, date range)
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from query
     * @throws WeafException
     */
    public function queryReceipt(array $queryData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/invoice-receipt-query", $queryData);
    }
}
