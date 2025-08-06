<?php

require_once('../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1
]);

echo "=== FasterPay Mass Payout API Examples ===\n\n";

// Example 1: Create mass payout following API documentation
echo "1. Creating mass payout\n";
echo "-----------------------\n";

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
            'receiver_full_name' => 'Jane Smith Corporation',
            'receiver_email' => 'jane@example.com',
            'receiver_type' => 'business'
        ]
    ]
];

$createdPayoutIds = [];

try {
    $massPayoutResponse = $businessGateway->payoutService()->createPayout($massPayoutParams);

    if ($massPayoutResponse->isSuccessful()) {
        echo "Mass payout created successfully\n";
        $responseData = $massPayoutResponse->getDecodeResponse();
        
        // Extract payout IDs from response for further operations
        if (isset($responseData['data']['payouts']) && is_array($responseData['data']['payouts'])) {
            foreach ($responseData['data']['payouts'] as $payoutItem) {
                $createdPayoutIds[] = $payoutItem['id'];
                echo "Payout ID: " . $payoutItem['id'] . " - Status: " . $payoutItem['status'] . "\n";
            }
        }
    } else {
        echo "Error: " . $massPayoutResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Get payout details using ID from create response
echo "2. Getting payout details\n";
echo "-------------------------\n";

if (!empty($createdPayoutIds)) {
    $payoutId = $createdPayoutIds[0]; // Use first created payout ID
    echo "Using payout ID from create response: " . $payoutId . "\n";
    
    try {
        $payoutDetails = $businessGateway->payoutService()->getPayoutDetails($payoutId);

        if ($payoutDetails->isSuccessful()) {
            echo "Payout details retrieved successfully\n";
            $responseData = $payoutDetails->getDecodeResponse();
            
            if (isset($responseData['data'])) {
                $data = $responseData['data'];
                echo "Payout ID: " . $data['id'] . "\n";
                echo "Status: " . $data['status'] . "\n";
                echo "Receiver: " . $data['receiver_full_name'] . " (" . $data['receiver_email'] . ")\n";
                echo "Amount: " . $data['amounts']['target_amount'] . " " . $data['amounts']['target_currency'] . "\n";
            }
        } else {
            echo "Error: " . $payoutDetails->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "Exception: " . $e->getMessage() . "\n";
    }
} else {
    echo "No payout ID available from previous create operation\n";
}

echo "\n";

// Example 3: Get payout list with filters
echo "3. Getting payout list with filters\n";
echo "-----------------------------------\n";

$filters = [
    'limit' => 20,
    'offset' => 0,
    'status' => 'submitted',
    'from_date' => '2024-01-01',
    'to_date' => '2024-12-31'
];

try {
    $payoutList = $businessGateway->payoutService()->getPayoutList($filters);

    if ($payoutList->isSuccessful()) {
        echo "Payout list retrieved successfully\n";
        $responseData = $payoutList->getDecodeResponse();
        
        if (isset($responseData['data']['payouts']) && is_array($responseData['data']['payouts'])) {
            echo "Found " . count($responseData['data']['payouts']) . " payouts\n";
            foreach ($responseData['data']['payouts'] as $payout) {
                echo "- " . $payout['id'] . " (" . $payout['status'] . ") - " . 
                     $payout['amounts']['target_amount'] . " " . $payout['amounts']['target_currency'] . "\n";
            }
        }
    } else {
        echo "Error: " . $payoutList->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}