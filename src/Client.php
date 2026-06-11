<?php

namespace Weaf\Efris;

use Weaf\Efris\Http\Transport;
use Weaf\Efris\Exceptions\WeafApiException;
use Weaf\Efris\Exceptions\WeafException;
use Weaf\Efris\Resources\Invoices;
use Weaf\Efris\Resources\Products;
use Weaf\Efris\Resources\Stock;
use Weaf\Efris\Resources\Taxpayer;

/**
 * The core client class for interaction with the WEAF EFRIS API.
 */
class Client
{
    private Configuration $config;
    private Transport $transport;

    // Namespaces
    public Invoices $invoices;
    public Products $products;
    public Stock $stock;
    public Taxpayer $taxpayer;

    /**
     * Client constructor.
     *
     * @param array|Configuration $config Config array or Configuration instance
     */
    public function __construct($config = [])
    {
        if ($config instanceof Configuration) {
            $this->config = $config;
        } else {
            $this->config = new Configuration($config);
        }

        $this->transport = new Transport($this->config);

        // Instantiate Resource categories
        $this->invoices = new Invoices($this);
        $this->products = new Products($this);
        $this->stock = new Stock($this);
        $this->taxpayer = new Taxpayer($this);
    }

    /**
     * Get the active Configuration instance.
     */
    public function config(): Configuration
    {
        return $this->config;
    }

    /**
     * Get the active Http Transport instance.
     */
    public function transport(): Transport
    {
        return $this->transport;
    }

    /**
     * Generate a new API access token dynamically.
     *
     * @param int $expiryDays Days until token expires (1-365)
     * @param string $tokenName Label name for the token
     * @return string Generated token key
     * @throws WeafException
     */
    public function generateAccessToken(int $expiryDays = 30, string $tokenName = 'PHP SDK Integration Token'): string
    {
        $username = $this->config->getUsername();
        $password = $this->config->getPassword();

        if (empty($username) || empty($password)) {
            throw new \InvalidArgumentException("Username and Password must be set in Configuration to generate token.");
        }

        $payload = [
            'username' => $username,
            'password' => $password,
            'expiry_days' => $expiryDays,
            'token_name' => $tokenName
        ];

        $response = $this->transport->post('/v1/auth/generate-token', $payload);

        if (isset($response['data']['token'])) {
            $token = $response['data']['token'];
            $this->config->setAccessToken($token);
            return $token;
        }

        throw new WeafApiException("Token generation failed: " . ($response['status']['returnMessage'] ?? 'Unknown Error'), '99', $response);
    }

    /**
     * Validate an existing API access token.
     *
     * @param string $token Token value to validate
     * @return bool True if valid, false otherwise
     * @throws WeafException
     */
    public function validateToken(string $token): bool
    {
        $payload = ['token' => $token];
        try {
            $response = $this->transport->post('/v1/auth/validate-token', $payload);
            return isset($response['data']['token_valid']) && $response['data']['token_valid'] === true;
        } catch (WeafException $e) {
            return false;
        }
    }

    /**
     * Refresh an existing API access token.
     *
     * @param string $currentToken Token value to refresh
     * @return string Refreshed/new token value
     * @throws WeafException
     */
    public function refreshToken(string $currentToken): string
    {
        $payload = ['token' => $currentToken];
        $response = $this->transport->post('/v1/auth/refresh-token', $payload);

        if (isset($response['data']['token'])) {
            $newToken = $response['data']['token'];
            $this->config->setAccessToken($newToken);
            return $newToken;
        }

        throw new WeafApiException("Token refresh failed: " . ($response['status']['returnMessage'] ?? 'Unknown Error'), '99', $response);
    }
}
