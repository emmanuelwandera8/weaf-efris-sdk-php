<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Exceptions\WeafException;

/**
 * Manages product and items catalog-related endpoints.
 */
class Products extends BaseResource
{
    /**
     * Register a new product or service with EFRIS.
     *
     * @param array $productData Product attributes (code, name, tax, type, etc.)
     * @param string|null $tinOverride Optional company TIN to override default
     * @return array Response data
     * @throws WeafException
     */
    public function register(array $productData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/register-product", $productData);
    }

    /**
     * Retrieve all registered goods and services.
     *
     * @param string|null $tinOverride Optional company TIN to override default
     * @return array List of products/services
     * @throws WeafException
     */
    public function list(?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->get("/{$tin}/goods-and-services");
    }

    /**
     * Force synchronization of products with EFRIS.
     *
     * @param string|null $tinOverride Optional company TIN to override default
     * @return array Sync action response
     * @throws WeafException
     */
    public function sync(?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/sync-products");
    }

    /**
     * Retrieve all measure units supported by EFRIS.
     *
     * @param string|null $tinOverride Optional company TIN to override default
     * @return array List of measurement units
     * @throws WeafException
     */
    public function getMeasureUnits(?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->get("/{$tin}/measure-units");
    }
}
