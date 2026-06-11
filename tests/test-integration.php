<?php
/**
 * Standalone Integration and Verification Test for the WEAF EFRIS PHP SDK.
 */

// Register a manual PSR-4 autoloader for Weaf\Efris\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'Weaf\\Efris\\';
    $base_dir = dirname(__DIR__) . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

use Weaf\Efris\Client;
use Weaf\Efris\Exceptions\WeafValidationException;
use Weaf\Efris\Exceptions\WeafApiException;

echo "=== WEAF EFRIS PHP SDK Auto-Verification ===\n";

try {
    echo "1. Initializing client...\n";
    $client = new Client([
        'token' => 'sample_token_123',
        'defaultTin' => '1000251604',
        'environment' => 'sandbox'
    ]);
    
    echo "   - Config Default TIN: " . $client->config()->getDefaultTin() . "\n";
    echo "   - Config Env: " . $client->config()->getEnvironment() . "\n";
    echo "   - Config Base URL: " . $client->config()->getBaseUrl() . "\n";
    
    echo "\n2. Verifying resources initialization...\n";
    if (isset($client->invoices) && $client->invoices instanceof \Weaf\Efris\Resources\Invoices) {
        echo "   [OK] Invoices Resource loaded.\n";
    } else {
        echo "   [FAIL] Invoices Resource failed to load.\n";
    }
    if (isset($client->products) && $client->products instanceof \Weaf\Efris\Resources\Products) {
        echo "   [OK] Products Resource loaded.\n";
    } else {
        echo "   [FAIL] Products Resource failed to load.\n";
    }
    if (isset($client->stock) && $client->stock instanceof \Weaf\Efris\Resources\Stock) {
        echo "   [OK] Stock Resource loaded.\n";
    } else {
        echo "   [FAIL] Stock Resource failed to load.\n";
    }
    if (isset($client->taxpayer) && $client->taxpayer instanceof \Weaf\Efris\Resources\Taxpayer) {
        echo "   [OK] Taxpayer Resource loaded.\n";
    } else {
        echo "   [FAIL] Taxpayer Resource failed to load.\n";
    }

    echo "\n3. Testing API Exception hierarchy instantiation...\n";
    $validationException = new WeafValidationException("Validation Error Message", ['buyerTin' => ['Format is invalid']]);
    if ($validationException->getReturnCode() === '01' && isset($validationException->getErrors()['buyerTin'])) {
        echo "   [OK] Exception mappings work as expected.\n";
    } else {
        echo "   [FAIL] Exception hierarchy mapping failed.\n";
    }

    echo "\nSDK Class Structure verification finished successfully!\n";
    echo "=============================================\n";
    
} catch (\Exception $e) {
    echo "An unexpected error occurred: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
