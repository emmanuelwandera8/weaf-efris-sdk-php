<?php

namespace Weaf\Efris\Resources;

use Weaf\Efris\Exceptions\WeafException;

/**
 * Manages taxpayer query and validation endpoints.
 */
class Taxpayer extends BaseResource
{
    /**
     * Search taxpayer details in the EFRIS system.
     *
     * @param string $searchTin The TIN to lookup details for
     * @param string|null $tinOverride Optional company TIN override
     * @return array Taxpayer profiles response
     * @throws WeafException
     */
    public function search(string $searchTin, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        $payload = [
            'tin' => $searchTin,
            'ninBrn' => '' // Optional: National ID or Business Registration Number
        ];
        return $this->client->transport()->post("/{$tin}/search-taxpayer", $payload);
    }

    /**
     * Query taxpayer deemed projects info.
     *
     * @param array $queryData Query attributes
     * @param string|null $tinOverride Optional company TIN override
     * @return array Deemed projects details
     * @throws WeafException
     */
    public function queryDeemedProject(array $queryData, ?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->post("/{$tin}/query-taxpayer-deemed-project", $queryData);
    }

    /**
     * Get company registration details as recorded in URA EFRIS.
     *
     * @param string|null $tinOverride Optional company TIN override
     * @return array Registration details
     * @throws WeafException
     */
    public function getRegistrationDetails(?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->get("/{$tin}/registration-details");
    }

    /**
     * Get excise duty configuration.
     *
     * @param string|null $tinOverride Optional company TIN override
     * @return array Excise duty details
     * @throws WeafException
     */
    public function getExciseDuty(?string $tinOverride = null): array
    {
        $tin = $this->getTin($tinOverride);
        return $this->client->transport()->get("/{$tin}/excise-duty");
    }
}
