<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Exceptions\WeafException;

/**
 * Manages invoice, receipt, and credit note-related endpoints.
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
     * Generate a fiscal invoice preview (simulates invoice creation without committing it).
     *
     * @param array $invoiceData The invoice structure payload
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function createPreview(array $invoiceData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/generate-fiscal-invoice-preview", $invoiceData);
    }

    /**
     * Generate a fiscal receipt in EFRIS.
     *
     * @param array $receiptData The receipt structure payload (uses unitOfMeasure)
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function createReceipt(array $receiptData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/generate-fiscal-receipt", $receiptData);
    }

    /**
     * Apply for a Credit Note (to cancel or refund a previously generated invoice/receipt).
     *
     * @param array $creditNoteData The credit note application details
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function applyCreditNote(array $creditNoteData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/apply-for-creditnote", $creditNoteData);
    }

    /**
     * Query existing credit notes.
     *
     * @param array $queryData Query filters
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function queryCreditNotes(array $queryData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/query-creditnotes", $queryData);
    }

    /**
     * Query incoming purchase invoices.
     *
     * @param array $queryData Query parameters
     * @param string|null $tinOverride Optional company TIN to override client default
     * @return array Response data from WEAF API
     * @throws WeafException
     */
    public function queryPurchaseInvoices(array $queryData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/query-purchase-invoices", $queryData);
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
