<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Discount API Examples\n";
echo "==========================================\n\n";

// Example 1: Create a flat discount
echo "1. Creating a flat discount\n";
echo "---------------------------\n";

$flatDiscountData = [
    'name' => 'Early Bird Discount',
    'type' => 'flat',
    'value' => 0.1,
    'currency' => 'USD',
    'description' => '$10 discount for early payment'
];

try {
    $discountResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($flatDiscountData);

    if ($discountResponse->isSuccessful()) {
        echo "✓ Flat discount created successfully\n";
        $responseData = $discountResponse->getDecodeResponse();
        $discountId = $responseData['data']['id'] ?? 'DC-' . time();
        echo "  Discount ID: $discountId\n";
        echo "  Name: {$flatDiscountData['name']}\n";
        echo "  Type: {$flatDiscountData['type']}\n";
        echo "  Value: $" . $flatDiscountData['value'] . " {$flatDiscountData['currency']}\n";
        echo "  Description: {$flatDiscountData['description']}\n";
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
    $volumeResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($percentageDiscountData);

    if ($volumeResponse->isSuccessful()) {
        echo "✓ Percentage discount created successfully\n";
        $responseData = $volumeResponse->getDecodeResponse();
        $volumeDiscountId = $responseData['data']['id'] ?? 'DC-VOLUME-' . time();
        echo "  Discount ID: $volumeDiscountId\n";
        echo "  Name: {$percentageDiscountData['name']}\n";
        echo "  Type: {$percentageDiscountData['type']}\n";
        echo "  Value: {$percentageDiscountData['value']}%\n";
        echo "  Description: {$percentageDiscountData['description']}\n";
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
    'name' => 'Loyal Customer Discount',
    'type' => 'percentage',
    'value' => 10,
    'description' => '10% discount for returning customers'
];

try {
    $loyaltyResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($loyaltyDiscountData);

    if ($loyaltyResponse->isSuccessful()) {
        echo "✓ Loyalty discount created successfully\n";
        $responseData = $loyaltyResponse->getDecodeResponse();
        $loyaltyDiscountId = $responseData['data']['id'] ?? 'DC-LOYALTY-' . time();
        echo "  Discount ID: $loyaltyDiscountId\n";
        echo "  Value: {$loyaltyDiscountData['value']}% for loyal customers\n";
    } else {
        echo "✗ Error: " . $loyaltyResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get discount details
echo "4. Getting discount details\n";
echo "---------------------------\n";

$discountId = $discountId ?? 'DC-' . time();

try {
    $detailsResponse = $businessGateway->eInvoiceDiscountService()->getDiscount($discountId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Discount details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $discount = $details['data'] ?? [];

        echo "  ID: " . ($discount['id'] ?? $discountId) . "\n";
        echo "  Name: " . ($discount['name'] ?? 'N/A') . "\n";
        echo "  Type: " . ($discount['type'] ?? 'N/A') . "\n";
        echo "  Value: " . ($discount['value'] ?? 'N/A') . "\n";
        echo "  Currency: " . ($discount['currency'] ?? 'N/A') . "\n";
        echo "  Description: " . ($discount['description'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update discount
echo "5. Updating discount\n";
echo "--------------------\n";

$updateData = [
    'value' => 0.15,
    'description' => 'Updated to $15 early payment discount'
];

try {
    $updateResponse = $businessGateway->eInvoiceDiscountService()->updateDiscount($discountId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Discount updated successfully\n";
        echo "  New value: $" . $updateData['value'] . "\n";
        echo "  Updated description: {$updateData['description']}\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List all discounts
echo "6. Listing all discounts\n";
echo "------------------------\n";

try {
    $listResponse = $businessGateway->eInvoiceDiscountService()->listDiscounts();

    if ($listResponse->isSuccessful()) {
        echo "✓ Discount list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $discounts = $listData['data']['data'] ?? [];

        echo "  Total discounts: " . count($discounts) . "\n";

        if (!empty($discounts)) {
            echo "  Available discounts:\n";
            foreach ($discounts as $discount) {
                $id = $discount['id'] ?? 'Unknown';
                $name = $discount['name'] ?? 'Unnamed';
                $type = $discount['type'] ?? 'N/A';
                $value = $discount['value'] ?? 0;
                $currency = $discount['currency'] ?? '';

                if ($type === 'percentage') {
                    echo "    - $name ($id): $value% off\n";
                } else {
                    echo "    - $name ($id): $" . $value . " $currency off\n";
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

// Example 7: Create seasonal discounts
echo "7. Creating seasonal discounts\n";
echo "------------------------------\n";

$seasonalDiscounts = [
    [
        'name' => 'Black Friday Special',
        'type' => 'percentage',
        'value' => 25,
        'description' => '25% off everything - Black Friday special'
    ],
    [
        'name' => 'New Year Discount',
        'type' => 'flat',
        'value' => 50,
        'currency' => 'USD',
        'description' => '$50 off to start the new year right'
    ],
    [
        'name' => 'Summer Sale',
        'type' => 'percentage',
        'value' => 20,
        'description' => '20% summer discount on all services'
    ]
];

$createdSeasonalDiscounts = [];

foreach ($seasonalDiscounts as $discountData) {
    try {
        $response = $businessGateway->eInvoiceDiscountService()->createDiscount($discountData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $discountId = $responseData['data']['id'] ?? 'DC-SEASONAL-' . time();
            $createdSeasonalDiscounts[] = $discountId;

            if ($discountData['type'] === 'percentage') {
                echo "✓ {$discountData['name']}: {$discountData['value']}% off (ID: $discountId)\n";
            } else {
                echo "✓ {$discountData['name']}: $" . $discountData['value'] . " off (ID: $discountId)\n";
            }
        } else {
            echo "✗ Failed to create {$discountData['name']}: " . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "✗ Exception creating {$discountData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "  Created " . count($createdSeasonalDiscounts) . " seasonal discount offers\n";

echo "\n";

// Example 8: Create tiered discounts for different customer levels
echo "8. Creating tiered customer discounts\n";
echo "-------------------------------------\n";

$tieredDiscounts = [
    [
        'name' => 'Bronze Member',
        'type' => 'percentage',
        'value' => 5,
        'description' => '5% discount for Bronze tier customers'
    ],
    [
        'name' => 'Silver Member',
        'type' => 'percentage',
        'value' => 10,
        'description' => '10% discount for Silver tier customers'
    ],
    [
        'name' => 'Gold Member',
        'type' => 'percentage',
        'value' => 15,
        'description' => '15% discount for Gold tier customers'
    ],
    [
        'name' => 'Platinum Member',
        'type' => 'percentage',
        'value' => 20,
        'description' => '20% discount for Platinum tier customers'
    ]
];

echo "Creating customer tier discounts:\n";

foreach ($tieredDiscounts as $tierData) {
    try {
        $response = $businessGateway->eInvoiceDiscountService()->createDiscount($tierData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $tierId = $responseData['data']['id'] ?? 'DC-TIER-' . time();
            echo "  ✓ {$tierData['name']}: {$tierData['value']}% (ID: $tierId)\n";
        } else {
            echo "  ✗ Failed to create {$tierData['name']}\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  ✗ Exception creating {$tierData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\nE-Invoice Discount API examples completed!\n";
echo "Use cases:\n";
echo "• Customer loyalty programs\n";
echo "• Seasonal and promotional campaigns\n";
echo "• Volume-based pricing strategies\n";
echo "• Early payment incentives\n";
echo "• Tiered customer benefits\n";
echo "• Dynamic pricing adjustments\n";
echo "• Bulk order discounts\n";
echo "• Customer retention strategies\n";