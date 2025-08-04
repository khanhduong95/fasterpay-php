<?php

require_once('../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay Contact API Examples\n";
echo "===============================\n\n";

// Example 1: Create a new contact
echo "1. Creating a new contact\n";
echo "-------------------------\n";

$contactData = [
    'type' => 'individual',
    'first_name' => 'Jane',
    'last_name' => 'Smith',
    'email' => 'jane.smith@example.com',
    'phone' => '+1-555-234-5678',
    'company' => 'Tech Solutions Inc',
    'job_title' => 'Product Manager',
    'status' => 'active',
    'tags' => ['customer', 'premium'],
    'notes' => 'High-value customer, prefers email communication',
    'custom_fields' => [
        'customer_since' => '2023-01-15',
        'preferred_contact_method' => 'email'
    ]
];

try {
    $contactResponse = $businessGateway->contactService()->createContact($contactData);

    if ($contactResponse->isSuccessful()) {
        echo "✓ Contact created successfully\n";
        $responseData = $contactResponse->getDecodeResponse();
        $contactId = $responseData['contact_id'] ?? 'contact_' . time();
        echo "  Contact ID: $contactId\n";
        echo "  Name: {$contactData['first_name']} {$contactData['last_name']}\n";
        echo "  Email: {$contactData['email']}\n";
        echo "  Phone: {$contactData['phone']}\n";
    } else {
        echo "✗ Error: " . $contactResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a business contact
echo "2. Creating a business contact\n";
echo "------------------------------\n";

$businessContactData = [
    'type' => 'business',
    'name' => 'Global Enterprise Solutions',
    'email' => 'contact@globalenterprise.com',
    'phone' => '+1-555-987-6543',
    'website' => 'https://globalenterprise.com',
    'industry' => 'Technology',
    'employee_count' => '500-1000',
    'annual_revenue' => '50000000',
    'status' => 'active',
    'primary_contact' => [
        'first_name' => 'Robert',
        'last_name' => 'Johnson',
        'email' => 'robert.johnson@globalenterprise.com',
        'job_title' => 'CFO'
    ]
];

try {
    $businessContactResponse = $businessGateway->contactService()->createContact($businessContactData);

    if ($businessContactResponse->isSuccessful()) {
        echo "✓ Business contact created successfully\n";
        $responseData = $businessContactResponse->getDecodeResponse();
        $businessContactId = $responseData['contact_id'] ?? 'business_contact_' . time();
        echo "  Contact ID: $businessContactId\n";
        echo "  Company: {$businessContactData['name']}\n";
        echo "  Industry: {$businessContactData['industry']}\n";
        echo "  Primary Contact: {$businessContactData['primary_contact']['first_name']} {$businessContactData['primary_contact']['last_name']}\n";
    } else {
        echo "✗ Error: " . $businessContactResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get contact by email
echo "3. Finding contact by email\n";
echo "---------------------------\n";

try {
    $emailResponse = $businessGateway->contactService()->getContactByEmail('jane.smith@example.com');

    if ($emailResponse->isSuccessful()) {
        echo "✓ Contact found by email\n";
        $contactData = $emailResponse->getDecodeResponse();
        echo "  Email: jane.smith@example.com\n";
        echo "  Name: " . ($contactData['first_name'] ?? 'Jane') . " " . ($contactData['last_name'] ?? 'Smith') . "\n";
        echo "  Status: " . ($contactData['status'] ?? 'active') . "\n";
    } else {
        echo "✗ Error: " . $emailResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Add address to contact
echo "4. Adding address to contact\n";
echo "----------------------------\n";

$contactId = 'contact_' . time(); // In real scenario, use actual contact ID
$addressData = [
    'type' => 'home',
    'street' => '456 Oak Avenue',
    'city' => 'San Francisco',
    'state' => 'CA',
    'postal_code' => '94102',
    'country_code' => 'US',
    'is_primary' => true
];

try {
    $addressResponse = $businessGateway->contactService()->addContactAddress($contactId, $addressData);

    if ($addressResponse->isSuccessful()) {
        echo "✓ Address added to contact successfully\n";
        echo "  Contact ID: $contactId\n";
        echo "  Address: {$addressData['street']}, {$addressData['city']}, {$addressData['state']}\n";
        echo "  Type: {$addressData['type']}\n";
    } else {
        echo "✗ Error: " . $addressResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Search contacts
echo "5. Searching contacts\n";
echo "---------------------\n";

$searchParams = [
    'query' => 'smith',
    'type' => 'individual',
    'status' => 'active',
    'tags' => ['customer'],
    'limit' => 10
];

try {
    $searchResponse = $businessGateway->contactService()->searchContacts($searchParams);

    if ($searchResponse->isSuccessful()) {
        echo "✓ Contact search completed\n";
        echo "  Query: 'smith'\n";
        echo "  Filter: Active individual customers\n";
        echo "  (In a real scenario, this would return matching contacts)\n";
    } else {
        echo "✗ Error: " . $searchResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Update contact
echo "6. Updating contact information\n";
echo "-------------------------------\n";

$updateData = [
    'job_title' => 'Senior Product Manager',
    'phone' => '+1-555-234-9999',
    'notes' => 'Promoted to Senior PM. Updated contact preferences.',
    'custom_fields' => [
        'last_interaction' => date('Y-m-d'),
        'account_value' => '15000'
    ]
];

try {
    $updateResponse = $businessGateway->contactService()->updateContact($contactId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Contact updated successfully\n";
        echo "  Contact ID: $contactId\n";
        echo "  New job title: {$updateData['job_title']}\n";
        echo "  Updated phone: {$updateData['phone']}\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: List contacts with filters
echo "7. Listing contacts with filters\n";
echo "--------------------------------\n";

$filters = [
    'type' => 'individual',
    'status' => 'active',
    'has_email' => true,
    'created_after' => '2023-01-01',
    'limit' => 25,
    'offset' => 0,
    'sort' => 'last_name',
    'order' => 'asc'
];

try {
    $listResponse = $businessGateway->contactService()->listContacts($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Contact list retrieved successfully\n";
        echo "  Filter: Active individuals with email addresses\n";
        echo "  Created after: 2023-01-01\n";
        echo "  Sorted by: Last name (ascending)\n";
        echo "  Limit: 25 contacts\n";
        echo "  (In a real scenario, this would show actual contact data)\n";
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Get contact addresses
echo "8. Getting contact addresses\n";
echo "----------------------------\n";

try {
    $addressesResponse = $businessGateway->contactService()->getContactAddresses($contactId);

    if ($addressesResponse->isSuccessful()) {
        echo "✓ Contact addresses retrieved successfully\n";
        echo "  Contact ID: $contactId\n";
        echo "  (In a real scenario, this would show all addresses for the contact)\n";
    } else {
        echo "✗ Error: " . $addressesResponse->getErrors()->getMessage() . "\n";
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