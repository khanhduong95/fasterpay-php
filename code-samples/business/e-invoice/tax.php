<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Tax API Examples\n";
echo "=====================================\n\n";

// Example 1: Create a flat tax
echo "1. Creating a flat tax\n";
echo "----------------------\n";

$flatTaxData = [
    'name' => 'Sales Tax',
    'type' => 'flat',
    'value' => 0.08,
    'description' => '8% flat sales tax rate'
];

try {
    $taxResponse = $businessGateway->eInvoiceTaxService()->createTax($flatTaxData);

    if ($taxResponse->isSuccessful()) {
        echo "✓ Flat tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = $responseData['data']['id'] ?? 'TX-' . time();
        echo "  Tax ID: $taxId\n";
        echo "  Name: {$flatTaxData['name']}\n";
        echo "  Type: {$flatTaxData['type']}\n";
        echo "  Rate: " . ($flatTaxData['value'] * 100) . "%\n";
        echo "  Description: {$flatTaxData['description']}\n";
    } else {
        echo "✗ Error: " . $taxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a percentage tax
echo "2. Creating a percentage tax\n";
echo "----------------------------\n";

$percentageTaxData = [
    'name' => 'VAT',
    'type' => 'percentage',
    'value' => 20,
    'description' => '20% Value Added Tax (UK)'
];

try {
    $vatResponse = $businessGateway->eInvoiceTaxService()->createTax($percentageTaxData);

    if ($vatResponse->isSuccessful()) {
        echo "✓ Percentage tax created successfully\n";
        $responseData = $vatResponse->getDecodeResponse();
        $vatId = $responseData['data']['id'] ?? 'TX-VAT-' . time();
        echo "  Tax ID: $vatId\n";
        echo "  Name: {$percentageTaxData['name']}\n";
        echo "  Type: {$percentageTaxData['type']}\n";
        echo "  Rate: {$percentageTaxData['value']}%\n";
        echo "  Description: {$percentageTaxData['description']}\n";
    } else {
        echo "✗ Error: " . $vatResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create GST tax
echo "3. Creating GST tax\n";
echo "-------------------\n";

$gstTaxData = [
    'name' => 'GST',
    'type' => 'flat',
    'value' => 0.10,
    'description' => '10% Goods and Services Tax'
];

try {
    $gstResponse = $businessGateway->eInvoiceTaxService()->createTax($gstTaxData);

    if ($gstResponse->isSuccessful()) {
        echo "✓ GST tax created successfully\n";
        $responseData = $gstResponse->getDecodeResponse();
        $gstId = $responseData['data']['id'] ?? 'TX-GST-' . time();
        echo "  Tax ID: $gstId\n";
        echo "  Rate: " . ($gstTaxData['value'] * 100) . "% GST\n";
    } else {
        echo "✗ Error: " . $gstResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get tax details
echo "4. Getting tax details\n";
echo "----------------------\n";

$taxId = $taxId ?? 'TX-' . time();

try {
    $detailsResponse = $businessGateway->eInvoiceTaxService()->getTax($taxId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Tax details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $tax = $details['data'] ?? [];

        echo "  ID: " . ($tax['id'] ?? $taxId) . "\n";
        echo "  Name: " . ($tax['name'] ?? 'N/A') . "\n";
        echo "  Type: " . ($tax['type'] ?? 'N/A') . "\n";
        echo "  Value: " . ($tax['value'] ?? 'N/A') . "\n";
        echo "  Description: " . ($tax['description'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update tax
echo "5. Updating tax\n";
echo "---------------\n";

$updateData = [
    'value' => 0.085,
    'description' => 'Updated to 8.5% sales tax rate'
];

try {
    $updateResponse = $businessGateway->eInvoiceTaxService()->updateTax($taxId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Tax updated successfully\n";
        echo "  New rate: " . ($updateData['value'] * 100) . "%\n";
        echo "  Updated description: {$updateData['description']}\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List all taxes
echo "6. Listing all taxes\n";
echo "--------------------\n";

try {
    $listResponse = $businessGateway->eInvoiceTaxService()->listTaxes();

    if ($listResponse->isSuccessful()) {
        echo "✓ Tax list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $taxes = $listData['data']['data'] ?? [];

        echo "  Total taxes: " . count($taxes) . "\n";

        if (!empty($taxes)) {
            echo "  Configured taxes:\n";
            foreach ($taxes as $tax) {
                $id = $tax['id'] ?? 'Unknown';
                $name = $tax['name'] ?? 'Unnamed';
                $type = $tax['type'] ?? 'N/A';
                $value = $tax['value'] ?? 0;

                if ($type === 'percentage') {
                    echo "    - $name ($id): $value% ($type)\n";
                } else {
                    echo "    - $name ($id): " . ($value * 100) . "% ($type)\n";
                }
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Create zero tax (tax-exempt)
echo "7. Creating tax-exempt option\n";
echo "-----------------------------\n";

$zeroTaxData = [
    'name' => 'Tax Exempt',
    'type' => 'flat',
    'value' => 0,
    'description' => 'No tax applied - tax exempt status'
];

try {
    $zeroTaxResponse = $businessGateway->eInvoiceTaxService()->createTax($zeroTaxData);

    if ($zeroTaxResponse->isSuccessful()) {
        echo "✓ Tax-exempt option created successfully\n";
        $responseData = $zeroTaxResponse->getDecodeResponse();
        $zeroTaxId = $responseData['data']['id'] ?? 'TX-EXEMPT-' . time();
        echo "  Tax ID: $zeroTaxId\n";
        echo "  Rate: 0% (tax exempt)\n";
    } else {
        echo "✗ Error: " . $zeroTaxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Create multiple regional taxes
echo "8. Creating regional tax variations\n";
echo "-----------------------------------\n";

$regionalTaxes = [
    [
        'name' => 'California Sales Tax',
        'type' => 'flat',
        'value' => 0.0725,
        'description' => '7.25% California state sales tax'
    ],
    [
        'name' => 'New York Sales Tax',
        'type' => 'flat',
        'value' => 0.08,
        'description' => '8% New York state sales tax'
    ],
    [
        'name' => 'Texas Sales Tax',
        'type' => 'flat',
        'value' => 0.0625,
        'description' => '6.25% Texas state sales tax'
    ]
];

$createdRegionalTaxes = [];

foreach ($regionalTaxes as $taxData) {
    try {
        $response = $businessGateway->eInvoiceTaxService()->createTax($taxData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $taxId = $responseData['data']['id'] ?? 'TX-REGIONAL-' . time();
            $createdRegionalTaxes[] = $taxId;

            echo "✓ {$taxData['name']}: " . ($taxData['value'] * 100) . "% (ID: $taxId)\n";
        } else {
            echo "✗ Failed to create {$taxData['name']}: " . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "✗ Exception creating {$taxData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "  Created " . count($createdRegionalTaxes) . " regional tax configurations\n";

echo "\nE-Invoice Tax API examples completed!\n";
echo "Use cases:\n";
echo "• Multi-jurisdictional tax compliance\n";
echo "• Automated tax calculation\n";
echo "• Regional tax rate management\n";
echo "• Tax-exempt transaction handling\n";
echo "• VAT/GST compliance for international sales\n";
echo "• Dynamic tax rate updates\n";