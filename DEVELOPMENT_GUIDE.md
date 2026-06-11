# WEAF EFRIS PHP SDK - Developer & Integration Guide

This guide provides step-by-step instructions for:
1. **Integrating** the SDK into a PHP or Laravel application (for developers using the package).
2. **Developing, Tagging, and Releasing** updates to the SDK codebase (for developers maintaining the package).

---

## PART 1: Integration Guide (For Package Users)

To integrate this SDK into a PHP or Laravel project, follow these standard steps:

### 1. Install the Package
Run Composer inside your project directory to download and install the package from Packagist:
```bash
composer require weaf/efris-sdk-php
```

### 2. Basic Client Initialization
Initialize the client using your WEAF API credentials or a pre-generated token:

```php
require 'vendor/autoload.php';

use Weaf\Efris\Client;

$client = new Client([
    'username' => 'your_weaf_account_username', // e.g. services@weafcompany.com
    'password' => 'your_weaf_account_password',
    'defaultTin' => 'your_company_tin_number',
    'environment' => 'sandbox' // Use 'production' for live environment
]);
```

### 3. Usage Examples

#### Scenario A: Search Taxpayer Details
```php
try {
    $taxpayer = $client->taxpayer->search('1000251605');
    print_r($taxpayer['data']);
} catch (\Weaf\Efris\Exceptions\WeafException $e) {
    echo "Error: " . $e->getMessage();
}
```

#### Scenario B: Register a Product
```php
try {
    $response = $client->products->register([
        'products' => [
            [
                'goodsName' => 'Software Coding Service',
                'goodsCode' => 'SRV-SW-01',
                'measureUnit' => 'PCE',
                'unitPrice' => '150000',
                'currency' => '101',
                'commodityCategoryId' => '81111810',
                'haveExciseTax' => '102',
                'description' => 'Custom software coding services per hour',
                'stockPrewarning' => '1',
                'havePieceUnit' => '102', // Single unit of measure
                'pieceMeasureUnit' => '',
                'pieceUnitPrice' => '',
                'packageScaledValue' => '',
                'pieceScaledValue' => '',
                'exciseDutyCode' => '',
                'operationType' => '101'
            ]
        ]
    ]);
    print_r($response);
} catch (\Weaf\Efris\Exceptions\WeafException $e) {
    echo "Error registering product: " . $e->getMessage();
}
```

---

## PART 2: Development & Release Guide (For SDK Maintainers)

To make changes to the SDK code and release them to Packagist:

### Step 1: Clone the Repository & Make Changes
Clone the SDK repository and make your code or documentation adjustments:
```bash
git clone https://github.com/emmanuelwandera8/weaf-efris-sdk-php.git
cd weaf-efris-sdk-php
```

### Step 2: Stage and Commit Changes
Verify your modified files using `git status` and commit them with a descriptive message:
```bash
git add .
git commit -m "feat: add descriptive message here"
```

### Step 3: Push Changes to Main
Push your local commits up to the remote GitHub repository:
```bash
git push origin main
```

### Step 4: Tag a New Version
Composer and Packagist resolve versions based on Git tags. Determine the next SemVer version number (e.g. `v1.1.3` after `v1.1.2`), tag it locally, and push the tag:
```bash
git tag v1.1.3
git push origin --tags
```

### Step 5: Create a GitHub Release
Create a formal GitHub release for the tag. This generates zip files and notifies webhook receivers (like Packagist):
```bash
gh release create v1.1.3 --title "v1.1.3 - Release Title" --notes "Describe the new features or bug fixes in this release."
```

### Step 6: Automating Packagist Updates
Once you push a new tag, Packagist will automatically update the package page if webhooks are set up:
- Go to [Packagist Profile](https://packagist.org/profile/) and verify that the account `weafcompany` is linked to GitHub.
- If it's linked, the webhook is automatic. If not, follow the webhook setup instructions in `PACKAGIST_AUTO_UPDATE_GUIDE.md` to trigger automatic synchronization.
