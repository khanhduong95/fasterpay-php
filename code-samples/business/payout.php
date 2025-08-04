<?php

require_once('../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

// Example 1: Create mass payout
$massPayoutParams = [
    'source_currency' => 'USD',
    'template' => 'wallet',
    'payouts' => [
        [
            'reference_id' => 'payout_001',
            'amount' => '150.00',
            'amount_currency' => 'USD',
            'target_currency' => 'USD',
            'receiver_full_name' => 'John Doe',
            'receiver_email' => 'john@example.com',
            'receiver_type' => 'private'
        ],
        [
            'reference_id' => 'payout_002',
            'amount' => '75.50',
            'amount_currency' => 'USD',
            'target_currency' => 'USD',
            'receiver_full_name' => 'Jane Smith',
            'receiver_email' => 'jane@example.com',
            'receiver_type' => 'private'
        ],
        [
            'reference_id' => 'payout_003',
            'amount' => '200.25',
            'amount_currency' => 'USD',
            'target_currency' => 'USD',
            'receiver_full_name' => 'Bob Johnson',
            'receiver_email' => 'bob@example.com',
            'receiver_type' => 'business'
        ]
    ]
];

try {
    $massPayoutResponse = $businessGateway->massPayoutService()->createPayout($massPayoutParams);

    if ($massPayoutResponse->isSuccessful()) {
        echo "Mass payout created successfully\n";
        echo "Response: " . $massPayoutResponse->getRawResponse() . "\n";

        // Extract payout ID from response for further operations
        $responseData = $massPayoutResponse->getDecodeResponse();
        $payoutId = $responseData['payout_id'] ?? null;

        if ($payoutId) {
            echo "Payout ID: " . $payoutId . "\n";
        }
    } else {
        echo "Error: " . $massPayoutResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Example 2: Get payout details
$payoutId = '<your-payout-id>';

try {
    $payoutDetails = $businessGateway->massPayoutService()->getPayoutDetails($payoutId);

    if ($payoutDetails->isSuccessful()) {
        echo "Payout details retrieved successfully\n";
        echo "Details: " . $payoutDetails->getRawResponse() . "\n";
    } else {
        echo "Error: " . $payoutDetails->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Example 3: Get payout list with filters
$filters = [
    'limit' => 20,
    'offset' => 0,
    'status' => 'pending',
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
];

try {
    $payoutList = $businessGateway->massPayoutService()->getPayoutList($filters);

    if ($payoutList->isSuccessful()) {
        echo "Payout list retrieved successfully\n";
        echo "List: " . $payoutList->getRawResponse() . "\n";
    } else {
        echo "Error: " . $payoutList->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Example 4: Get payout status
try {
    $payoutStatus = $businessGateway->massPayoutService()->getPayoutStatus($payoutId);

    if ($payoutStatus->isSuccessful()) {
        echo "Payout status retrieved successfully\n";
        echo "Status: " . $payoutStatus->getRawResponse() . "\n";
    } else {
        echo "Error: " . $payoutStatus->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Example 5: Cancel payout (if still pending)
try {
    $cancelResponse = $businessGateway->massPayoutService()->cancelPayout($payoutId);

    if ($cancelResponse->isSuccessful()) {
        echo "Payout cancelled successfully\n";
        echo "Response: " . $cancelResponse->getRawResponse() . "\n";
    } else {
        echo "Error: " . $cancelResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

// Example 6: Retry failed payout
try {
    $retryResponse = $businessGateway->massPayoutService()->retryPayout($payoutId);

    if ($retryResponse->isSuccessful()) {
        echo "Payout retry initiated successfully\n";
        echo "Response: " . $retryResponse->getRawResponse() . "\n";
    } else {
        echo "Error: " . $retryResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}