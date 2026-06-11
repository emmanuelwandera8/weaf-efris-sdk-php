<?php

namespace Weaf\Efris\Http;

use Weaf\Efris\Configuration;
use Weaf\Efris\Exceptions\WeafApiException;
use Weaf\Efris\Exceptions\WeafException;
use Weaf\Efris\Exceptions\WeafValidationException;

/**
 * Handles HTTP communications with the WEAF API.
 */
class Transport
{
    private Configuration $config;

    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * Perform a GET request.
     *
     * @throws WeafException
     */
    public function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * Perform a POST request.
     *
     * @throws WeafException
     */
    public function post(string $endpoint, ?array $payload = null): array
    {
        return $this->request('POST', $endpoint, $payload);
    }

    /**
     * Internal request executor using cURL with error mappings and automatic retries.
     *
     * @throws WeafException
     */
    private function request(string $method, string $endpoint, ?array $payload = null): array
    {
        $url = $this->config->getBaseUrl() . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if ($this->config->getAccessToken() !== null) {
            $headers[] = 'Authorization: Bearer ' . $this->config->getAccessToken();
        }

        $maxRetries = $this->config->getMaxRetries();
        $retryCount = 0;
        
        while (true) {
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => $this->config->getTimeout(),
                CURLOPT_HTTPHEADER => $headers,
            ]);

            switch (strtoupper($method)) {
                case 'POST':
                    curl_setopt($ch, CURLOPT_POST, true);
                    if ($payload !== null) {
                        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                    }
                    break;
                default:
                    curl_setopt($ch, CURLOPT_HTTPGET, true);
                    break;
            }

            $responseBody = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // Handle cURL errors (e.g. timeout, DNS resolution failure)
            if ($curlError) {
                if ($retryCount < $maxRetries) {
                    $retryCount++;
                    usleep($retryCount * 500000); // Exponential backoff: 0.5s, 1s, 1.5s
                    continue;
                }
                throw new WeafException("cURL Network Error: " . $curlError);
            }

            // Retry on transient server errors (HTTP 502/503/504)
            if (($httpCode === 502 || $httpCode === 503 || $httpCode === 504) && $retryCount < $maxRetries) {
                $retryCount++;
                usleep($retryCount * 1000000); // 1s, 2s, 3s
                continue;
            }

            return $this->parseResponse($responseBody, $httpCode);
        }
    }

    /**
     * Parse and validate the response structure.
     *
     * @throws WeafException
     */
    private function parseResponse(string $responseBody, int $httpCode): array
    {
        $data = json_last_error() === JSON_ERROR_NONE ? json_decode($responseBody, true) : null;
        if ($data === null) {
            $data = json_decode($responseBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new WeafException("Invalid JSON response from server. HTTP Code: {$httpCode}. Raw Body: " . substr($responseBody, 0, 500));
            }
        }

        // Check returnCode status structure
        if (!isset($data['status']['returnCode'])) {
            throw new WeafException("Malformed response payload. Missing status returnCode.");
        }

        $returnCode = $data['status']['returnCode'];
        $returnMessage = $data['status']['returnMessage'] ?? 'Unknown API Error';

        if ($returnCode !== '00') {
            // Check for validation error Specifically
            if ($returnCode === '01') {
                $validationErrors = $data['data']['errors'] ?? [];
                throw new WeafValidationException($returnMessage, $validationErrors, $data);
            }

            throw new WeafApiException(
                "API request failed with code {$returnCode}: {$returnMessage}",
                $returnCode,
                $data,
                $httpCode
            );
        }

        return $data;
    }
}
