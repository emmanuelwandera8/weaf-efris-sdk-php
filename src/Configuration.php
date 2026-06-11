<?php

namespace Weaf\Efris;

/**
 * Handles SDK configuration parameters.
 */
class Configuration
{
    private string $baseUrl = 'https://weafcompany.com/api';
    private ?string $accessToken = null;
    private ?string $username = null;
    private ?string $password = null;
    private ?string $defaultTin = null;
    private string $environment = 'production'; // 'production' or 'sandbox'
    private int $timeout = 180; // Default timeout of 3 minutes (180s)
    private int $maxRetries = 3;

    public function __construct(array $config = [])
    {
        if (isset($config['baseUrl'])) {
            $this->setBaseUrl($config['baseUrl']);
        }
        if (isset($config['token'])) {
            $this->setAccessToken($config['token']);
        }
        if (isset($config['username'])) {
            $this->setUsername($config['username']);
        }
        if (isset($config['password'])) {
            $this->setPassword($config['password']);
        }
        if (isset($config['defaultTin'])) {
            $this->setDefaultTin($config['defaultTin']);
        }
        if (isset($config['environment'])) {
            $this->setEnvironment($config['environment']);
        }
        if (isset($config['timeout'])) {
            $this->setTimeout($config['timeout']);
        }
        if (isset($config['maxRetries'])) {
            $this->setMaxRetries($config['maxRetries']);
        }
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getDefaultTin(): ?string
    {
        return $this->defaultTin;
    }

    public function setDefaultTin(?string $defaultTin): self
    {
        $this->defaultTin = $defaultTin;
        return $this;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function setEnvironment(string $environment): self
    {
        $this->environment = strtolower($environment) === 'sandbox' ? 'sandbox' : 'production';
        return $this;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getMaxRetries(): int
    {
        return $this->maxRetries;
    }

    public function setMaxRetries(int $maxRetries): self
    {
        $this->maxRetries = max(0, $maxRetries);
        return $this;
    }
}
