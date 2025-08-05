<?php

require_once('../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay Contact API Examples\n";
echo "===============================\n\n";

// Example 1: Create a new individual contact
echo "1. Creating a new individual contact\n";
echo "------------------------------------\n";

$contactData = [
    'email' => 'john.smith@example.com',
    'phone' => '2015550124',
    'phone_country_code' => 'US',
    'first_name' => 'John',
    'last_name' => 'Smith',
    'country' => 'US',
    'favorite' => true
];

try {
    $contactResponse = $businessGateway->contactService()->createContact($contactData);

    if ($contactResponse->isSuccessful()) {
        echo "✓ Contact created successfully\n";
        $responseData = $contactResponse->getDecodeResponse();
        $contactId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'CT-' . time();
        echo "  Contact ID: " . $contactId . "\n";
        echo "  Name: " . $contactData['first_name'] . " " . $contactData['last_name'] . "\n";
        echo "  Email: " . $contactData['email'] . "\n";
        echo "  Phone: " . $contactData['phone'] . "\n";
        echo "  Country: " . $contactData['country'] . "\n";
    } else {
        echo "✗ Error: " . $contactResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create another contact
echo "2. Creating another contact\n";
echo "---------------------------\n";

$secondContactData = [
    'email' => 'jane.doe@example.com',
    'phone' => '2015550987',
    'phone_country_code' => 'US',
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'country' => 'US',
    'favorite' => false
];

try {
    $secondContactResponse = $businessGateway->contactService()->createContact($secondContactData);

    if ($secondContactResponse->isSuccessful()) {
        echo "✓ Second contact created successfully\n";
        $responseData = $secondContactResponse->getDecodeResponse();
        $secondContactId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'CT-2-' . time();
        echo "  Contact ID: " . $secondContactId . "\n";
        echo "  Name: " . $secondContactData['first_name'] . " " . $secondContactData['last_name'] . "\n";
        echo "  Email: " . $secondContactData['email'] . "\n";
        echo "  Phone: " . $secondContactData['phone'] . "\n";
    } else {
        echo "✗ Error: " . $secondContactResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get contact details
echo "3. Getting contact details\n";
echo "--------------------------\n";

try {
    $detailsResponse = $businessGateway->contactService()->getContact($contactId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Contact details retrieved successfully\n";
        $details = $detailsResponse->getDecodeResponse();
        
        if (isset($details['data'])) {
            $contact = $details['data'];
            echo "  ID: " . (isset($contact['id']) ? $contact['id'] : 'N/A') . "\n";
            echo "  Name: " . (isset($contact['first_name']) ? $contact['first_name'] : '') . " " . (isset($contact['last_name']) ? $contact['last_name'] : '') . "\n";
            echo "  Email: " . (isset($contact['email']) ? $contact['email'] : 'N/A') . "\n";
            echo "  Phone: " . (isset($contact['phone_full_number']) ? $contact['phone_full_number'] : 'N/A') . "\n";
            echo "  Country: " . (isset($contact['country']) ? $contact['country'] : 'N/A') . "\n";
        }
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Update contact
echo "4. Updating contact information\n";
echo "-------------------------------\n";

$updateData = [
    'phone' => '2015559999',
    'favorite' => false,
    'first_name' => 'Jonathan'
];

try {
    $updateResponse = $businessGateway->contactService()->updateContact($contactId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Contact updated successfully\n";
        echo "  Contact ID: " . $contactId . "\n";
        echo "  New phone: " . $updateData['phone'] . "\n";
        echo "  Updated name: " . $updateData['first_name'] . "\n";
        echo "  Favorite: " . ($updateData['favorite'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Create another contact for listing
echo "5. Creating a third contact for demonstration\n";
echo "---------------------------------------------\n";

$thirdContactData = [
    'email' => 'mike.wilson@example.com',
    'phone' => '2015550555',
    'phone_country_code' => 'US',
    'first_name' => 'Mike',
    'last_name' => 'Wilson',
    'country' => 'US',
    'favorite' => true
];

try {
    $thirdContactResponse = $businessGateway->contactService()->createContact($thirdContactData);

    if ($thirdContactResponse->isSuccessful()) {
        echo "✓ Third contact created successfully\n";
        $responseData = $thirdContactResponse->getDecodeResponse();
        $thirdContactId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'CT-3-' . time();
        echo "  Contact ID: " . $thirdContactId . "\n";
        echo "  Name: " . $thirdContactData['first_name'] . " " . $thirdContactData['last_name'] . "\n";
        echo "  Email: " . $thirdContactData['email'] . "\n";
        echo "  Favorite: " . ($thirdContactData['favorite'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Error: " . $thirdContactResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List contacts with filters
echo "6. Listing contacts with filters\n";
echo "--------------------------------\n";

$filters = [
    'limit' => 25,
    'offset' => 0
];

try {
    $listResponse = $businessGateway->contactService()->listContacts($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Contact list retrieved successfully\n";
        echo "  Limit: 25 contacts\n";
        
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " contacts\n";
            
            // Display first few contacts
            $contacts = array_slice($listData['data'], 0, 3);
            foreach ($contacts as $contact) {
                $name = (isset($contact['first_name']) ? $contact['first_name'] : '') . ' ' . (isset($contact['last_name']) ? $contact['last_name'] : '');
                $email = isset($contact['email']) ? $contact['email'] : 'No email';
                echo "    - " . trim($name) . " (" . $email . ")\n";
            }
            
            if (count($listData['data']) > 3) {
                echo "    ... and " . (count($listData['data']) - 3) . " more\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Delete a contact
echo "7. Deleting a contact\n";
echo "---------------------\n";

$contactToDelete = isset($thirdContactId) ? $thirdContactId : 'CT-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->contactService()->deleteContact($contactToDelete);

    if ($deleteResponse->isSuccessful()) {
        echo "✓ Contact deleted successfully\n";
        echo "  Deleted Contact ID: " . $contactToDelete . "\n";
    } else {
        echo "✗ Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\nContact API examples completed!\n";
echo "Use cases:\n";
echo "• Customer relationship management (CRM)\n";
echo "• Contact database management\n";
echo "• Lead tracking and nurturing\n";
echo "• Customer support integration\n";
echo "• Marketing campaign management\n";
echo "• Sales pipeline management\n";