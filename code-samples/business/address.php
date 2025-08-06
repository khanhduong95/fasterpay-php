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
        echo "✓ Address fields retrieved successfully\n";
        $data = $addressFieldsResponse->getDecodeResponse();
        $countryCode = $data['data']['country_code'] ?? 'US';
        $fields = $data['data']['fields'] ?? [];
        $subdivisions = $data['data']['subdivisions'] ?? [];

        echo "  Country: $countryCode\n";
        echo "  Required fields:\n";

        foreach ($fields as $field) {
            $type = $field['type'] ?? 'text';
            $label = $field['label'] ?? '';
            $name = $field['name'] ?? '';
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
        echo "✗ Error: " . $addressFieldsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Get address fields for Canada
echo "2. Getting address fields for Canada\n";
echo "------------------------------------\n";

try {
    $canadaFieldsResponse = $businessGateway->addressService()->getAddressFields('CA');

    if ($canadaFieldsResponse->isSuccessful()) {
        echo "✓ Canadian address fields retrieved\n";
        $data = $canadaFieldsResponse->getDecodeResponse();
        $fields = $data['data']['fields'] ?? [];

        echo "  Canada requires " . count($fields) . " fields:\n";
        foreach ($fields as $field) {
            echo "    - " . ($field['label'] ?? 'Unknown') . "\n";
        }
    } else {
        echo "✗ Error: " . $canadaFieldsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get subdivisions for a country
echo "3. Getting subdivisions (states) for US\n";
echo "---------------------------------------\n";

try {
    $subdivisions = $businessGateway->addressService()->getSubdivisions('US');

    if (!empty($subdivisions)) {
        echo "✓ Found " . count($subdivisions) . " US states/territories\n";
        echo "  Sample states:\n";

        // Show first 10 states
        $sampleStates = array_slice($subdivisions, 0, 10);
        foreach ($sampleStates as $state) {
            echo "    - {$state['name']} ({$state['code']})\n";
        }
        echo "  ... and " . (count($subdivisions) - 10) . " more\n";
    } else {
        echo "✗ No subdivisions found for US\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Validate address data
echo "4. Validating address data\n";
echo "--------------------------\n";

$sampleAddressData = [
    'address_line1' => '123 Main Street',
    'locality' => 'New York',
    'administrative_area' => 'NY',
    'postal_code' => '10001'
];

try {
    $validationResult = $businessGateway->addressService()->validateAddressData($sampleAddressData, 'US');

    echo "✓ Address validation completed\n";
    echo "  Valid: " . ($validationResult['valid'] ? 'Yes' : 'No') . "\n";
    echo "  Country: {$validationResult['country_code']}\n";

    if (!empty($validationResult['errors'])) {
        echo "  Errors:\n";
        foreach ($validationResult['errors'] as $error) {
            echo "    - $error\n";
        }
    }

    if (!empty($validationResult['warnings'])) {
        echo "  Warnings:\n";
        foreach ($validationResult['warnings'] as $warning) {
            echo "    - $warning\n";
        }
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Get address format for multiple countries
echo "5. Getting address formats for multiple countries\n";
echo "-------------------------------------------------\n";

$countries = ['US', 'CA', 'GB', 'DE', 'FR'];

try {
    $addressFields = $businessGateway->addressService()->getMultipleAddressFields($countries);

    echo "✓ Retrieved address fields for multiple countries\n";

    foreach ($countries as $country) {
        if (isset($addressFields[$country])) {
            if ($addressFields[$country]->isSuccessful()) {
                $data = $addressFields[$country]->getDecodeResponse();
                $fieldCount = count($data['data']['fields'] ?? []);
                $subdivisionCount = count($data['data']['subdivisions'] ?? []);

                echo "  $country: $fieldCount fields";
                if ($subdivisionCount > 0) {
                    echo ", $subdivisionCount subdivisions";
                }
                echo "\n";
            } else {
                echo "  $country: Error retrieving data\n";
            }
        } else {
            echo "  $country: Failed to retrieve\n";
        }
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Search for specific subdivision
echo "6. Searching for California\n";
echo "---------------------------\n";

try {
    $searchResults = $businessGateway->addressService()->searchSubdivisions('US', 'California');

    if (!empty($searchResults)) {
        echo "✓ Found " . count($searchResults) . " match(es) for 'California'\n";
        foreach ($searchResults as $result) {
            echo "  - {$result['name']} ({$result['code']})\n";
        }
    } else {
        echo "✗ No matches found for 'California'\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Get specific subdivision by code
echo "7. Getting subdivision details for 'CA' (California)\n";
echo "----------------------------------------------------\n";

try {
    $subdivision = $businessGateway->addressService()->getSubdivisionByCode('US', 'CA');

    if ($subdivision) {
        echo "✓ Found subdivision details\n";
        echo "  Code: {$subdivision['code']}\n";
        echo "  Name: {$subdivision['name']}\n";
        echo "  Has children: " . (empty($subdivision['children']) ? 'No' : 'Yes') . "\n";
    } else {
        echo "✗ Subdivision 'CA' not found\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Check if country has subdivisions
echo "8. Checking subdivision support\n";
echo "-------------------------------\n";

$testCountries = ['US', 'GB', 'MC']; // US, UK, Monaco

foreach ($testCountries as $country) {
    try {
        $hasSubdivisions = $businessGateway->addressService()->hasSubdivisions($country);
        echo "  $country: " . ($hasSubdivisions ? 'Has subdivisions' : 'No subdivisions') . "\n";
    } catch (FasterPay\Exception $e) {
        echo "  $country: Error checking - " . $e->getMessage() . "\n";
    }
}

echo "\nAddress API examples completed!\n";
echo "Use cases:\n";
echo "• Dynamic address form generation\n";
echo "• Address validation for different countries\n";
echo "• State/province dropdown population\n";
echo "• International shipping address collection\n";
echo "• Address format compliance checking\n";
echo "• Subdivision code lookup and validation\n";
