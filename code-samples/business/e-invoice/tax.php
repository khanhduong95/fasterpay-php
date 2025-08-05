<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Tax API Examples\n";
echo "====================================\n\n";

// Example 1: Create a flat tax
echo "1. Creating a flat tax\n";
echo "----------------------\n";

$flatTaxData = [
    'name' => 'Processing Fee',
    'type' => 'flat',
    'value' => 2.50,
    'description' => 'Fixed processing fee per transaction'
];

try {
    $taxResponse = $businessGateway->eInvoiceTaxService()->createTax($flatTaxData);

    if ($taxResponse->isSuccessful()) {
        echo "✓ Flat tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-' . time();
        echo "  Tax ID: " . $taxId . "\n";
        echo "  Name: " . $flatTaxData['name'] . "\n";
        echo "  Type: " . $flatTaxData['type'] . "\n";
        echo "  Value: $" . number_format($flatTaxData['value'], 2) . "\n";
        echo "  Description: " . $flatTaxData['description'] . "\n";
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
    'name' => 'Sales Tax',
    'type' => 'percentage',
    'value' => 8.25,
    'description' => '8.25% state sales tax'
];

try {
    $salesTaxResponse = $businessGateway->eInvoiceTaxService()->createTax($percentageTaxData);

    if ($salesTaxResponse->isSuccessful()) {
        echo "✓ Percentage tax created successfully\n";
        $responseData = $salesTaxResponse->getDecodeResponse();
        $salesTaxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-SALES-' . time();
        echo "  Tax ID: " . $salesTaxId . "\n";
        echo "  Name: " . $percentageTaxData['name'] . "\n";
        echo "  Type: " . $percentageTaxData['type'] . "\n";
        echo "  Value: " . $percentageTaxData['value'] . "%\n";
        echo "  Description: " . $percentageTaxData['description'] . "\n";
    } else {
        echo "✗ Error: " . $salesTaxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create VAT tax
echo "3. Creating VAT tax\n";
echo "-------------------\n";

$vatTaxData = [
    'name' => 'VAT',
    'type' => 'percentage',
    'value' => 20,
    'description' => '20% Value Added Tax'
];

try {
    $vatResponse = $businessGateway->eInvoiceTaxService()->createTax($vatTaxData);

    if ($vatResponse->isSuccessful()) {
        echo "✓ VAT tax created successfully\n";
        $responseData = $vatResponse->getDecodeResponse();
        $vatTaxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-VAT-' . time();
        echo "  Tax ID: " . $vatTaxId . "\n";
        echo "  Name: " . $vatTaxData['name'] . "\n";
        echo "  Type: " . $vatTaxData['type'] . "\n";
        echo "  Value: " . $vatTaxData['value'] . "%\n";
        echo "  Description: " . $vatTaxData['description'] . "\n";
    } else {
        echo "✗ Error: " . $vatResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get tax details
echo "4. Retrieving tax details\n";
echo "-------------------------\n";

$testTaxId = isset($taxId) ? $taxId : 'TX-250527-2E9N';

try {
    $getTaxResponse = $businessGateway->eInvoiceTaxService()->getTax($testTaxId);

    if ($getTaxResponse->isSuccessful()) {
        echo "✓ Tax details retrieved successfully\n";
        $taxData = $getTaxResponse->getDecodeResponse();
        if (isset($taxData['data'])) {
            $tax = $taxData['data'];
            echo "  Tax ID: " . (isset($tax['id']) ? $tax['id'] : $testTaxId) . "\n";
            echo "  Name: " . (isset($tax['name']) ? $tax['name'] : 'N/A') . "\n";
            echo "  Type: " . (isset($tax['type']) ? $tax['type'] : 'N/A') . "\n";
            echo "  Value: " . (isset($tax['value']) ? $tax['value'] : 'N/A') . "\n";
            echo "  Description: " . (isset($tax['description']) ? $tax['description'] : 'N/A') . "\n";
        }
    } else {
        echo "✗ Error: " . $getTaxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update tax
echo "5. Updating tax\n";
echo "---------------\n";

$updateTaxData = [
    'name' => 'Updated Processing Fee',
    'description' => 'Updated processing fee with new rate',
    'value' => 3.00
];

try {
    $updateResponse = $businessGateway->eInvoiceTaxService()->updateTax($testTaxId, $updateTaxData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Tax updated successfully\n";
        echo "  Tax ID: " . $testTaxId . "\n";
        echo "  Updated Name: " . $updateTaxData['name'] . "\n";
        echo "  Updated Value: $" . number_format($updateTaxData['value'], 2) . "\n";
        echo "  Updated Description: " . $updateTaxData['description'] . "\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List taxes
echo "6. Listing taxes\n";
echo "----------------\n";

$filters = [
    'limit' => 10,
    'offset' => 0
];

try {
    $listResponse = $businessGateway->eInvoiceTaxService()->listTaxes($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Tax list retrieved successfully\n";
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " taxes:\n";
            foreach ($listData['data'] as $tax) {
                $id = isset($tax['id']) ? $tax['id'] : 'Unknown';
                $name = isset($tax['name']) ? $tax['name'] : 'Unnamed';
                $type = isset($tax['type']) ? $tax['type'] : 'Unknown';
                $value = isset($tax['value']) ? $tax['value'] : '0';
                echo "    - " . $name . " (" . $id . ") - " . $type . ": " . $value . "\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Delete tax
echo "7. Deleting tax\n";
echo "---------------\n";

$deleteTestTaxId = isset($vatTaxId) ? $vatTaxId : 'TX-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->eInvoiceTaxService()->deleteTax($deleteTestTaxId);

    if ($deleteResponse->isSuccessful()) {
        echo "✓ Tax deleted successfully\n";
        echo "  Deleted Tax ID: " . $deleteTestTaxId . "\n";
    } else {
        echo "✗ Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Using taxes in invoices (embedded tax object)
echo "8. Using taxes in invoices\n";
echo "--------------------------\n";

$invoiceWithTaxData = [
    'currency' => 'USD',
    'summary' => 'Invoice with embedded tax',
    'number' => 'INV-TAX-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'contact_id' => 'CT-250527-AZARCIJE',
    'tax' => [
        'name' => 'Service Tax',
        'type' => 'percentage',
        'value' => 5,
        'description' => '5% service tax'
    ],
    'items' => [
        [
            'price' => 100.00,
            'quantity' => 1,
            'name' => 'Consulting Service',
            'description' => 'Professional consulting service'
        ]
    ]
];

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceWithTaxData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ Invoice with embedded tax created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'FPBIV-' . time();
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $invoiceWithTaxData['number'] . "\n";
        echo "  Tax: " . $invoiceWithTaxData['tax']['name'] . " (" . $invoiceWithTaxData['tax']['value'] . "%)\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
} => 0
);

try {
    $listResponse = $businessGateway->eInvoiceTaxService()->listTaxes($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Tax list retrieved successfully\n";
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " taxes:\n";
            foreach ($listData['data'] as $tax) {
                $id = isset($tax['id']) ? $tax['id'] : 'Unknown';
                $name = isset($tax['name']) ? $tax['name'] : 'Unnamed';
                $type = isset($tax['type']) ? $tax['type'] : 'Unknown';
                $value = isset($tax['value']) ? $tax['value'] : '0';
                echo "    - " . $name . " (" . $id . ") - " . $type . ": " . $value . "\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Delete tax
echo "7. Deleting tax\n";
echo "---------------\n";

$deleteTestTaxId = isset($vatTaxId) ? $vatTaxId : 'TX-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->eInvoiceTaxService()->deleteTax($deleteTestTaxId);

    if ($deleteResponse->isSuccessful()) {
        echo "✓ Tax deleted successfully\n";
        echo "  Deleted Tax ID: " . $deleteTestTaxId . "\n";
    } else {
        echo "✗ Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Using taxes in invoices (embedded tax object)
echo "8. Using taxes in invoices\n";
echo "--------------------------\n";

$invoiceWithTaxData = array(
    'currency' => 'USD',
    'summary' => 'Invoice with embedded tax',
    'number' => 'INV-TAX-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'contact_id' => 'CT-250527-AZARCIJE',
    'tax' => array(
        'name' => 'Service Tax',
        'type' => 'percentage',
        'value' => 5,
        'description' => '5% service tax'
    ),
    'items' => array(
        array(
            'price' => 100.00,
            'quantity' => 1,
            'name' => 'Consulting Service',
            'description' => 'Professional consulting service'
        )
    )
);

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceWithTaxData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ Invoice with embedded tax created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'FPBIV-' . time();
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $invoiceWithTaxData['number'] . "\n";
        echo "  Tax: " . $invoiceWithTaxData['tax']['name'] . " (" . $invoiceWithTaxData['tax']['value'] . "%)\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\nE-Invoice Tax API examples completed!\n";
echo "Use cases:\n";
echo "• Sales tax calculation and management\n";
echo "• VAT handling for international transactions\n";
echo "• Fixed processing fees\n";
echo "• State and local tax compliance\n";
echo "• Custom tax rates per region\n";
echo "• Tax exemption handling\n";
echo "• Multi-jurisdiction tax support\n";
echo "• Automated tax calculation in invoices\n";