<?php

require_once('../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Address API Examples\n";
echo "==============================\n\n";

// Example 1: Get address fields for United States
echo "1. Getting address fields for United States\n";
echo "--------------------------------------------\n";

try {
    $addressFieldsResponse = $businessGateway->addressService()->getAddressFields('US');

    if ($addressFieldsResponse->isSuccessful()) {
        echo "Address fields retrieved successfully\n";
        $data = $addressFieldsResponse->getDecodeResponse();
        $countryCode = isset($data['data']['country_code']) ? $data['data']['country_code'] : 'US';
        $fields = isset($data['data']['fields']) ? $data['data']['fields'] : [];
        $subdivisions = isset($data['data']['subdivisions']) ? $data['data']['subdivisions'] : [];

        echo "  Country: $countryCode\n";
        echo "  Required fields:\n";

        foreach ($fields as $field) {
            $type = isset($field['type']) ? $field['type'] : 'text';
            $label = isset($field['label']) ? $field['label'] : '';
            $name = isset($field['name']) ? $field['name'] : '';
            echo "    - $label ($name): $type\n";
        }

        echo "  States/subdivisions: " . count($subdivisions) . " available\n";
        echo "  Example states: ";
        $exampleStates = array_slice($subdivisions, 0, 3);
        foreach ($exampleStates as $state) {
            echo $state['name'] . ' (' . $state['code'] . ') ';
        }
        echo "\n";
    } else {
        echo "Error: " . $addressFieldsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Get address fields for Canada
echo "2. Getting address fields for Canada\n";
echo "------------------------------------\n";

try {
    $canadaFieldsResponse = $businessGateway->addressService()->getAddressFields('CA');

    if ($canadaFieldsResponse->isSuccessful()) {
        echo "Canadian address fields retrieved\n";
        $data = $canadaFieldsResponse->getDecodeResponse();
        $fields = isset($data['data']['fields']) ? $data['data']['fields'] : [];

        echo "  Canada requires " . count($fields) . " fields:\n";
        foreach ($fields as $field) {
            echo "    - " . (isset($field['label']) ? $field['label'] : 'Unknown') . "\n";
        }
    } else {
        echo "Error: " . $canadaFieldsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nAddress API examples completed!\n";
