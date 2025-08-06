<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Tax API Examples\n";
echo "===================================\n\n";

// Example 1: Create a basic percentage tax
echo "1. Creating a basic percentage tax\n";
echo "----------------------------------\n";

$taxData = [
    'name' => 'Sales Tax',
    'type' => 'percentage',
    'value' => 8.5,
    'description' => '8.5% sales tax for California'
];

try {
    $taxResponse = $businessGateway->invoiceTaxService()->createTax($taxData);

    if ($taxResponse->isSuccessful()) {
        echo "Tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-' . time();
        echo "  Tax ID: " . $taxId . "\n";
        echo "  Name: " . $taxData['name'] . "\n";
        echo "  Type: " . $taxData['type'] . "\n";
        echo "  Rate: " . $taxData['value'] . "%\n";
        echo "  Description: " . $taxData['description'] . "\n";
    } else {
        echo "Error: " . $taxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a flat fee tax
echo "2. Creating a flat fee tax\n";
echo "---------------------------\n";

$flatTaxData = [
    'name' => 'Processing Fee',
    'type' => 'flat',
    'value' => 2.50,
    'currency' => 'USD',
    'description' => '$2.50 flat processing fee per transaction'
];

try {
    $flatTaxResponse = $businessGateway->invoiceTaxService()->createTax($flatTaxData);

    if ($flatTaxResponse->isSuccessful()) {
        echo "Flat tax created successfully\n";
        $responseData = $flatTaxResponse->getDecodeResponse();
        $flatTaxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-FLAT-' . time();
        echo "  Tax ID: " . $flatTaxId . "\n";
        echo "  Name: " . $flatTaxData['name'] . "\n";
        echo "  Type: " . $flatTaxData['type'] . "\n";
        echo "  Value: $" . $flatTaxData['value'] . " " . $flatTaxData['currency'] . "\n";
    } else {
        echo "Error: " . $flatTaxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create VAT tax
echo "3. Creating VAT tax\n";
echo "-------------------\n";

$vatTaxData = [
    'name' => 'VAT (UK)',
    'type' => 'percentage',
    'value' => 20.0,
    'description' => '20% VAT for United Kingdom transactions'
];

try {
    $vatResponse = $businessGateway->invoiceTaxService()->createTax($vatTaxData);

    if ($vatResponse->isSuccessful()) {
        echo "VAT tax created successfully\n";
        $responseData = $vatResponse->getDecodeResponse();
        $vatTaxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-VAT-' . time();
        echo "  Tax ID: " . $vatTaxId . "\n";
        echo "  Name: " . $vatTaxData['name'] . "\n";
        echo "  Rate: " . $vatTaxData['value'] . "%\n";
        echo "  Region: United Kingdom\n";
    } else {
        echo "Error: " . $vatResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get tax details
echo "4. Getting tax details\n";
echo "----------------------\n";

$taxId = isset($taxId) ? $taxId : 'TX-' . time();

try {
    $detailsResponse = $businessGateway->invoiceTaxService()->getTax($taxId);

    if ($detailsResponse->isSuccessful()) {
        echo "Tax details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $tax = isset($details['data']) ? $details['data'] : [];

        echo "  ID: " . (isset($tax['id']) ? $tax['id'] : $taxId) . "\n";
        echo "  Name: " . (isset($tax['name']) ? $tax['name'] : 'N/A') . "\n";
        echo "  Type: " . (isset($tax['type']) ? $tax['type'] : 'N/A') . "\n";
        echo "  Value: " . (isset($tax['value']) ? $tax['value'] : '0') . "\n";
        echo "  Currency: " . (isset($tax['currency']) ? $tax['currency'] : 'N/A') . "\n";
        echo "  Description: " . (isset($tax['description']) ? $tax['description'] : 'N/A') . "\n";
    } else {
        echo "Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update tax
echo "5. Updating tax\n";
echo "---------------\n";

$updateData = [
    'name' => 'Updated Sales Tax',
    'value' => 9.0,
    'description' => 'Updated 9% sales tax rate'
];

try {
    $updateResponse = $businessGateway->invoiceTaxService()->updateTax($taxId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "Tax updated successfully\n";
        echo "  Updated rate to: " . $updateData['value'] . "%\n";
        echo "  Updated description\n";
    } else {
        echo "Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List all taxes
echo "6. Listing all taxes\n";
echo "--------------------\n";

$listFilters = [
    'page' => 1,
    'per_page' => 10
];

try {
    $listResponse = $businessGateway->invoiceTaxService()->listTaxes($listFilters);

    if ($listResponse->isSuccessful()) {
        echo "Tax list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $taxes = isset($listData['data']['data']) ? $listData['data']['data'] : [];

        echo "  Found " . count($taxes) . " taxes\n";
        foreach ($taxes as $tax) {
            $id = isset($tax['id']) ? $tax['id'] : 'Unknown';
            $name = isset($tax['name']) ? $tax['name'] : 'Unnamed';
            $type = isset($tax['type']) ? $tax['type'] : 'unknown';
            $value = isset($tax['value']) ? $tax['value'] : '0';
            echo "    - $name ($type: $value) - ID: $id\n";
        }
    } else {
        echo "Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Create multiple regional taxes
echo "7. Creating multiple regional taxes\n";
echo "-----------------------------------\n";

$regionalTaxes = [
    [
        'name' => 'New York State Tax',
        'type' => 'percentage',
        'value' => 8.25,
        'description' => '8.25% sales tax for New York State'
    ],
    [
        'name' => 'Texas State Tax',
        'type' => 'percentage',
        'value' => 6.25,
        'description' => '6.25% sales tax for Texas'
    ],
    [
        'name' => 'European VAT',
        'type' => 'percentage',
        'value' => 19.0,
        'description' => '19% VAT for European Union'
    ]
];

foreach ($regionalTaxes as $taxData) {
    try {
        $response = $businessGateway->invoiceTaxService()->createTax($taxData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $id = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-REGION-' . time();
            echo "  Created: {$taxData['name']} ({$taxData['value']}%) - ID: $id\n";
        } else {
            echo "  Error creating {$taxData['name']}: " . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  Exception creating {$taxData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Example 8: Delete tax
echo "8. Deleting tax\n";
echo "----------------\n";

$deleteTaxId = isset($vatTaxId) ? $vatTaxId : 'TX-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->invoiceTaxService()->deleteTax($deleteTaxId);

    if ($deleteResponse->isSuccessful()) {
        echo "Tax deleted successfully\n";
        echo "  Deleted Tax ID: " . $deleteTaxId . "\n";
    } else {
        echo "Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nInvoice Tax API examples completed!\n";
echo "Use cases:\n";
echo "• Sales tax calculation and management\n";
echo "• VAT handling for international transactions\n";
echo "• Fixed processing fees\n";
echo "• State and local tax compliance\n";
echo "• Custom tax rates per region\n";
echo "• Tax exemption handling\n";
echo "• Multi-jurisdiction tax support\n";
echo "• Automated tax calculation in invoices\n";
echo "• Flat fee vs percentage tax options\n";
echo "• Regional tax configuration\n";

echo "\nTax Types:\n";
echo "• percentage: Tax calculated as percentage of amount\n";
echo "• flat: Fixed tax amount regardless of invoice total\n";

echo "\nValidation Notes:\n";
echo "• Only ID field validation implemented (as requested)\n";
echo "• API handles all parameter validation server-side\n";
echo "• Tax values for percentage type should be decimal (8.5 for 8.5%)\n";
echo "• Tax values for flat type should be monetary amount\n";
echo "• Currency required for flat type taxes\n";
echo "• Tax names limited to 191 characters\n";