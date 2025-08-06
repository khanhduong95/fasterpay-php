<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Discount API Examples\n";
echo "==========================================\n\n";

// Example 1: Create a flat discount
echo "1. Creating a flat discount\n";
echo "---------------------------\n";

$flatDiscountData = [
    'name' => 'Early Bird Discount',
    'type' => 'flat',
    'value' => 10.00,
    'currency' => 'USD',
    'description' => '$10 discount for early payment'
];

try {
    $discountResponse = $businessGateway->invoiceDiscountService()->createDiscount($flatDiscountData);

    if ($discountResponse->isSuccessful()) {
        echo "✓ Flat discount created successfully\n";
        $responseData = $discountResponse->getDecodeResponse();
        $discountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-' . time();
        echo "  Discount ID: " . $discountId . "\n";
        echo "  Name: " . $flatDiscountData['name'] . "\n";
        echo "  Type: " . $flatDiscountData['type'] . "\n";
        echo "  Value: $" . number_format($flatDiscountData['value'], 2) . " " . $flatDiscountData['currency'] . "\n";
        echo "  Description: " . $flatDiscountData['description'] . "\n";
    } else {
        echo "✗ Error: " . $discountResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a percentage discount
echo "2. Creating a percentage discount\n";
echo "---------------------------------\n";

$percentageDiscountData = [
    'name' => 'Volume Discount',
    'type' => 'percentage',
    'value' => 15,
    'description' => '15% discount for bulk orders'
];

try {
    $volumeResponse = $businessGateway->invoiceDiscountService()->createDiscount($percentageDiscountData);

    if ($volumeResponse->isSuccessful()) {
        echo "✓ Percentage discount created successfully\n";
        $responseData = $volumeResponse->getDecodeResponse();
        $volumeDiscountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-VOLUME-' . time();
        echo "  Discount ID: " . $volumeDiscountId . "\n";
        echo "  Name: " . $percentageDiscountData['name'] . "\n";
        echo "  Type: " . $percentageDiscountData['type'] . "\n";
        echo "  Value: " . $percentageDiscountData['value'] . "%\n";
        echo "  Description: " . $percentageDiscountData['description'] . "\n";
    } else {
        echo "✗ Error: " . $volumeResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create loyalty discount
echo "3. Creating loyalty discount\n";
echo "----------------------------\n";

$loyaltyDiscountData = [
    'name' => 'Loyalty Customer Discount',
    'type' => 'percentage',
    'value' => 25,
    'description' => '25% discount for loyal customers'
];

try {
    $loyaltyResponse = $businessGateway->invoiceDiscountService()->createDiscount($loyaltyDiscountData);

    if ($loyaltyResponse->isSuccessful()) {
        echo "✓ Loyalty discount created successfully\n";
        $responseData = $loyaltyResponse->getDecodeResponse();
        $loyaltyDiscountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-LOYALTY-' . time();
        echo "  Discount ID: " . $loyaltyDiscountId . "\n";
        echo "  Name: " . $loyaltyDiscountData['name'] . "\n";
        echo "  Type: " . $loyaltyDiscountData['type'] . "\n";
        echo "  Value: " . $loyaltyDiscountData['value'] . "%\n";
        echo "  Description: " . $loyaltyDiscountData['description'] . "\n";
    } else {
        echo "✗ Error: " . $loyaltyResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get discount details
echo "4. Retrieving discount details\n";
echo "------------------------------\n";

$testDiscountId = isset($discountId) ? $discountId : 'DC-250527-WZX0';

try {
    $getDiscountResponse = $businessGateway->invoiceDiscountService()->getDiscount($testDiscountId);

    if ($getDiscountResponse->isSuccessful()) {
        echo "✓ Discount details retrieved successfully\n";
        $discountData = $getDiscountResponse->getDecodeResponse();
        if (isset($discountData['data'])) {
            $discount = $discountData['data'];
            echo "  Discount ID: " . (isset($discount['id']) ? $discount['id'] : $testDiscountId) . "\n";
            echo "  Name: " . (isset($discount['name']) ? $discount['name'] : 'N/A') . "\n";
            echo "  Type: " . (isset($discount['type']) ? $discount['type'] : 'N/A') . "\n";
            echo "  Value: " . (isset($discount['value']) ? $discount['value'] : 'N/A') . "\n";
            if (isset($discount['currency'])) {
                echo "  Currency: " . $discount['currency'] . "\n";
            }
            echo "  Description: " . (isset($discount['description']) ? $discount['description'] : 'N/A') . "\n";
        }
    } else {
        echo "✗ Error: " . $getDiscountResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update discount
echo "5. Updating discount\n";
echo "--------------------\n";

$updateDiscountData = [
    'name' => 'Updated Early Bird Discount',
    'description' => 'Updated early payment discount with better terms',
    'value' => 15.00
];

try {
    $updateResponse = $businessGateway->invoiceDiscountService()->updateDiscount($testDiscountId, $updateDiscountData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Discount updated successfully\n";
        echo "  Discount ID: " . $testDiscountId . "\n";
        echo "  Updated Name: " . $updateDiscountData['name'] . "\n";
        echo "  Updated Value: $" . number_format($updateDiscountData['value'], 2) . "\n";
        echo "  Updated Description: " . $updateDiscountData['description'] . "\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List discounts
echo "6. Listing discounts\n";
echo "--------------------\n";

$filters = [
    'limit' => 10,
    'offset' => 0
];

try {
    $listResponse = $businessGateway->invoiceDiscountService()->listDiscounts($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Discount list retrieved successfully\n";
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']['data']) && is_array($listData['data']['data'])) {
            echo "  Found " . count($listData['data']['data']) . " discounts:\n";
            foreach ($listData['data']['data'] as $discount) {
                $id = isset($discount['id']) ? $discount['id'] : 'Unknown';
                $name = isset($discount['name']) ? $discount['name'] : 'Unnamed';
                $type = isset($discount['type']) ? $discount['type'] : 'Unknown';
                $value = isset($discount['value']) ? $discount['value'] : '0';
                $currency = isset($discount['currency']) ? ' ' . $discount['currency'] : '';
                echo "    - " . $name . " (" . $id . ") - " . $type . ": " . $value . $currency . "\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Delete discount
echo "7. Deleting discount\n";
echo "--------------------\n";

$deleteTestDiscountId = isset($loyaltyDiscountId) ? $loyaltyDiscountId : 'DC-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->invoiceDiscountService()->deleteDiscount($deleteTestDiscountId);

    if ($deleteResponse->isSuccessful()) {
        echo "✓ Discount deleted successfully\n";
        echo "  Deleted Discount ID: " . $deleteTestDiscountId . "\n";
    } else {
        echo "✗ Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

echo "\nInvoice Discount API examples completed!\n";
echo "Use cases:\n";
echo "• Early payment incentives\n";
echo "• Volume purchase discounts\n";
echo "• Loyalty customer rewards\n";
echo "• Seasonal promotional offers\n";
echo "• First-time customer discounts\n";
echo "• Bulk order price reductions\n";
echo "• Partner and affiliate discounts\n";
echo "• Promotional campaign management\n";