# WEAF EFRIS PHP SDK

A modern, object-oriented PHP SDK for integrating with the WEAF EFRIS API. This SDK makes it simple to communicate with Uganda Revenue Authority's Electronic Fiscal Receipting and Invoicing System (EFRIS) with standard namespaces, strong exception handling, namespaced resources, and robust network resilience out of the box.

---

## Features

*   **Clean Namespaces:** Grouped operations under logical properties (`invoices`, `products`, `stock`, `taxpayer`).
*   **Token Lifecycle Helpers:** Simplified API Token generation, validation, and refresh.
*   **Strict Exception Handling:** Specialized exceptions (`WeafValidationException`, `WeafApiException`) returning exact validation errors and status codes.
*   **Automatic Retries:** Auto-retries on connection timeouts and transient gateway issues.
*   **PSR-4 Compliant:** Standard namespaces and easily autoloadable in modern frameworks (Laravel, Symfony, etc.).

---

## Installation

### 1. Installation via Local Composer Repository
If you've placed this SDK inside your main project directory, you can add it to your main project's `composer.json` using local repository mapping:

```json
"repositories": [
    {
        "type": "path",
        "url": "weaf-efris-sdk-php"
    }
],
"require": {
    "weaf/efris-sdk-php": "*@dev"
}
```

Then run:
```bash
composer update weaf/efris-sdk-php
```

### 2. Manual Installation
If not using Composer autoloading, you can register a simple manual PSR-4 autoloader:

```php
spl_autoload_register(function ($class) {
    $prefix = 'Weaf\\Efris\\';
    $base_dir = __DIR__ . '/weaf-efris-sdk-php/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});
```

---

## Quick Start Guide

### 1. Client Initialization

Initialize with a pre-generated authentication token and a default TIN context:

```php
use Weaf\Efris\Client;

$client = new Client([
    'token' => 'YOUR_64_CHARACTER_API_TOKEN',
    'defaultTin' => '1000251604',
    'environment' => 'sandbox' // or 'production'
]);
```

Alternatively, initialize using account credentials to generate tokens dynamically:

```php
$client = new Client([
    'username' => 'services@weafcompany.com',
    'password' => 'SecurePassword123',
    'defaultTin' => '1000251604',
    'environment' => 'sandbox'
]);

// Dynamically generate a token valid for 30 days
$token = $client->generateAccessToken(30, 'POS Web Integration');
```

---

## Common Use Cases

### 1. Search Taxpayer Details

```php
try {
    $taxpayer = $client->taxpayer->search('1000251605');
    print_r($taxpayer['data']);
} catch (\Weaf\Efris\Exceptions\WeafException $e) {
    echo "Error searching taxpayer: " . $e->getMessage();
}
```

### 2. Generate Fiscal Invoice

```php
use Weaf\Efris\Exceptions\WeafValidationException;
use Weaf\Efris\Exceptions\WeafApiException;

$invoicePayload = [
    'data' => [
        'sellerDetails' => [
            'placeOfBusiness' => 'Kampala Road',
            'referenceNo' => 'REF-' . time()
        ],
        'basicInformation' => [
            'operator' => 'Cashier 01',
            'currency' => 'UGX',
            'invoiceType' => 1,
            'invoiceKind' => 1,
            'paymentMode' => '101',
            'invoiceIndustryCode' => '101'
        ],
        'buyerDetails' => [
            'buyerTin' => '1000251605',
            'buyerBusinessName' => 'Test Business Customer',
            'buyerType' => '0'
        ],
        'itemsBought' => [
            [
                'itemCode' => 'ITEM001',
                'quantity' => 1,
                'unitPrice' => 100000,
                'total' => 100000,
                'taxForm' => '101',
                'taxRule' => 'STANDARD',
                'netAmount' => 84745.76,
                'taxAmount' => 15254.24,
                'grossAmount' => 100000
            ]
        ]
    ]
];

try {
    $response = $client->invoices->create($invoicePayload);
    $invoiceNo = $response['data']['invoiceNo'];
    $fdn = $response['data']['fdn'];
    
    echo "Fiscal Invoice Generated: {$invoiceNo} (FDN: {$fdn})";
} catch (WeafValidationException $e) {
    echo "Validation failed: " . $e->getMessage() . "\n";
    print_r($e->getErrors()); // Key-value map of field validation messages
} catch (WeafApiException $e) {
    echo "API request error (Code {$e->getReturnCode()}): " . $e->getMessage();
} catch (\Exception $e) {
    echo "General system error: " . $e->getMessage();
}
```

### 3. Product Catalog Management

```php
// Register new products with EFRIS (takes a nested 'products' array)
$client->products->register([
    'products' => [
        [
            'goodsName' => 'Custom API Software Package',
            'goodsCode' => 'SW-PKG-01',
            'measureUnit' => 'PCE',
            'unitPrice' => '150000',
            'currency' => '101',
            'commodityCategoryId' => '10111301',
            'haveExciseTax' => '102',
            'description' => 'Custom software application package integration API',
            'stockPrewarning' => '10',
            'havePieceUnit' => '102'
        ]
    ]
]);

// List registered products
$products = $client->products->list();
```

### 4. Stock Adjustments

```php
// Record local inventory intake (Stock In)
$client->stock->increase([
    'remarks' => 'Monthly inventory intake',
    'stockInDate' => date('Y-m-d'),
    'stockInType' => '102', // 102 = Local Purchase
    'stockInItem' => [
        [
            'itemCode' => 'SW-PKG-01',
            'quantity' => 200,
            'unitPrice' => 100000
        ]
    ],
    'supplierName' => 'Software Distributor Uganda',
    'supplierTin' => '1017196396'
]);
```

---

## Technical Support & Reference

*   **API Base URL (Default):** `https://weafcompany.com/api`
*   **Documentation Portal:** [https://weafcompany.com/api/documentation](https://weafcompany.com/api/documentation)
*   **Technical Support:** services@weafcompany.com
