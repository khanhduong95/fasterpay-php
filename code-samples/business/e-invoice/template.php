<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Template API Examples\n";
echo "==========================================\n\n";

// Example 1: Create a new invoice template
echo "1. Creating a new invoice template\n";
echo "----------------------------------\n";

$templateData = [
    'name' => 'Professional Template',
    'address' => null,
    'footer' => 'Thank you for your business! Payment terms: Net 30 days.',
    'colors' => [
        'primary' => '#2563eb',
        'secondary' => '#f8fafc'
    ],
    'localized_address' => [
        'address_line1' => '123 Business Avenue',
        'locality' => 'San Francisco',
        'administrative_area' => 'CA',
        'postal_code' => '94105'
    ],
    'country_code' => 'US'
];

try {
    $templateResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($templateData);

    if ($templateResponse->isSuccessful()) {
        echo "✓ Invoice template created successfully\n";
        $responseData = $templateResponse->getDecodeResponse();
        $templateId = $responseData['data']['id'] ?? 'IT-' . time();
        echo "  Template ID: $templateId\n";
        echo "  Name: {$templateData['name']}\n";
        echo "  Primary Color: {$templateData['colors']['primary']}\n";
        echo "  Address: {$templateData['localized_address']['address_line1']}, {$templateData['localized_address']['locality']}\n";
        echo "  Full Address: " . ($responseData['data']['full_address'] ?? 'Generated automatically') . "\n";
    } else {
        echo "✗ Error: " . $templateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a template with custom branding
echo "2. Creating a branded template\n";
echo "------------------------------\n";

$brandedTemplateData = [
    'name' => 'Company Branded Template',
    'footer' => 'Powered by YourCompany © 2024 | www.yourcompany.com',
    'colors' => [
        'primary' => '#ff6b35',
        'secondary' => '#f7f3f0'
    ],
    'localized_address' => [
        'address_line1' => '456 Corporate Blvd',
        'address_line2' => 'Suite 200',
        'locality' => 'New York',
        'administrative_area' => 'NY',
        'postal_code' => '10001'
    ],
    'country_code' => 'US'
];

try {
    $brandedResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($brandedTemplateData);

    if ($brandedResponse->isSuccessful()) {
        echo "✓ Branded template created successfully\n";
        $responseData = $brandedResponse->getDecodeResponse();
        $brandedTemplateId = $responseData['data']['id'] ?? 'IT-BRAND-' . time();
        echo "  Template ID: $brandedTemplateId\n";
        echo "  Brand Colors: {$brandedTemplateData['colors']['primary']} / {$brandedTemplateData['colors']['secondary']}\n";
        echo "  Corporate Address: {$brandedTemplateData['localized_address']['address_line1']}\n";
    } else {
        echo "✗ Error: " . $brandedResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get template details
echo "3. Getting template details\n";
echo "---------------------------\n";

$templateId = $templateId ?? 'IT-' . time();

try {
    $detailsResponse = $businessGateway->eInvoiceTemplateService()->getTemplate($templateId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Template details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $template = $details['data'] ?? [];

        echo "  ID: " . ($template['id'] ?? $templateId) . "\n";
        echo "  Name: " . ($template['name'] ?? 'N/A') . "\n";
        echo "  Country: " . ($template['country_code'] ?? 'N/A') . "\n";
        echo "  Footer: " . ($template['footer'] ?? 'N/A') . "\n";
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Update template
echo "4. Updating template\n";
echo "--------------------\n";

$updateData = [
    'footer' => 'Updated footer - Thank you for choosing our services!',
    'colors' => [
        'primary' => '#059669',
        'secondary' => '#ecfdf5'
    ]
];

try {
    $updateResponse = $businessGateway->eInvoiceTemplateService()->updateTemplate($templateId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Template updated successfully\n";
        echo "  Updated primary color to: {$updateData['colors']['primary']}\n";
        echo "  Updated footer\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: List all templates
echo "5. Listing all templates\n";
echo "------------------------\n";

try {
    $listResponse = $businessGateway->eInvoiceTemplateService()->listTemplates();

    if ($listResponse->isSuccessful()) {
        echo "✓ Templates list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $templates = $listData['data']['data'] ?? [];

        echo "  Total templates: " . count($templates) . "\n";

        if (!empty($templates)) {
            echo "  Templates:\n";
            foreach ($templates as $template) {
                $id = $template['id'] ?? 'Unknown';
                $name = $template['name'] ?? 'Unnamed';
                $country = $template['country_code'] ?? 'N/A';
                echo "    - $name ($id) - Country: $country\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Create template for different country
echo "6. Creating template for UK\n";
echo "---------------------------\n";

$ukTemplateData = [
    'name' => 'UK Template',
    'footer' => 'Registered in England and Wales',
    'colors' => [
        'primary' => '#1e40af',
        'secondary' => '#eff6ff'
    ],
    'localized_address' => [
        'address_line1' => '10 Downing Street',
        'locality' => 'London',
        'postal_code' => 'SW1A 2AA'
    ],
    'country_code' => 'GB'
];

try {
    $ukResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($ukTemplateData);

    if ($ukResponse->isSuccessful()) {
        echo "✓ UK template created successfully\n";
        $responseData = $ukResponse->getDecodeResponse();
        $ukTemplateId = $responseData['data']['id'] ?? 'IT-UK-' . time();
        echo "  Template ID: $ukTemplateId\n";
        echo "  Country: GB (United Kingdom)\n";
        echo "  Address format: UK postal code format\n";
    } else {
        echo "✗ Error: " . $ukResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\nE-Invoice Template API examples completed!\n";
echo "Use cases:\n";
echo "• Brand-consistent invoice design\n";
echo "• Multi-country invoice templates\n";
echo "• Corporate identity management\n";
echo "• Professional invoice presentation\n";
echo "• Template reuse across invoices\n";
echo "• Localized address formatting\n";