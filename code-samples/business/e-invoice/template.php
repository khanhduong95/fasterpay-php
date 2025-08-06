<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Template API Examples\n";
echo "========================================\n\n";

// Example 1: Create a new invoice template
echo "1. Creating a new invoice template\n";
echo "----------------------------------\n";

$templateData = [
    'name' => 'Professional Template',
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
    $templateResponse = $businessGateway->invoiceTemplateService()->createTemplate($templateData);

    if ($templateResponse->isSuccessful()) {
        echo "Template created successfully\n";
        $responseData = $templateResponse->getDecodeResponse();
        $templateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-' . time();
        echo "  Template ID: $templateId\n";
        echo "  Name: {$templateData['name']}\n";
        echo "  Primary Color: {$templateData['colors']['primary']}\n";
        echo "  Address: {$templateData['localized_address']['address_line1']}, {$templateData['localized_address']['locality']}\n";
        echo "  Full Address: " . (isset($responseData['data']['full_address']) ? $responseData['data']['full_address'] : 'Generated automatically') . "\n";
    } else {
        echo "Error: " . $templateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 1.5: Create template with logo upload
echo "1.5 Creating template with logo\n";
echo "--------------------------------\n";

$templateWithLogoData = [
    'name' => 'Branded Logo Template',
    'footer' => 'Professional template with company logo',
    'colors' => [
        'primary' => '#e11d48',
        'secondary' => '#fdf2f8'
    ],
    'localized_address' => [
        'address_line1' => '789 Logo Street',
        'locality' => 'Design City',
        'administrative_area' => 'CA',
        'postal_code' => '90210'
    ],
    'country_code' => 'US',
    'logo' => '/path/to/company-logo.png' // File path provided directly in params
];

try {
    // Note: In real implementation, provide actual logo file path in params['logo']
    // The service automatically detects file fields and uses multipart upload

    // For demo purposes, create without logo
    $demoData = $templateWithLogoData;
    unset($demoData['logo']); // Remove for demo
    $logoResponse = $businessGateway->invoiceTemplateService()->createTemplate($demoData);

    if ($logoResponse->isSuccessful()) {
        echo "Template with logo created successfully\n";
        $responseData = $logoResponse->getDecodeResponse();
        $logoTemplateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-LOGO-' . time();
        echo "  Template ID: $logoTemplateId\n";
        echo "  Name: {$templateWithLogoData['name']}\n";
        echo "  Logo: File would be uploaded automatically if 'logo' field contains file path\n";
        echo "  Usage: \$params['logo'] = '/path/to/file.png'\n";
    } else {
        echo "Error: " . $logoResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a template with custom branding
echo "2. Creating a branded template\n";
echo "------------------------------\n";

$brandedTemplateData = [
    'name' => 'Company Branded Template',
    'footer' => 'Powered by YourCompany 2024 | www.yourcompany.com',
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
    $brandedResponse = $businessGateway->invoiceTemplateService()->createTemplate($brandedTemplateData);

    if ($brandedResponse->isSuccessful()) {
        echo "Branded template created successfully\n";
        $responseData = $brandedResponse->getDecodeResponse();
        $brandedTemplateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-BRAND-' . time();
        echo "  Template ID: $brandedTemplateId\n";
        echo "  Brand Colors: {$brandedTemplateData['colors']['primary']} / {$brandedTemplateData['colors']['secondary']}\n";
        echo "  Corporate Address: {$brandedTemplateData['localized_address']['address_line1']}\n";
    } else {
        echo "Error: " . $brandedResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get template details
echo "3. Getting template details\n";
echo "---------------------------\n";

$templateId = isset($templateId) ? $templateId : 'IT-' . time();

try {
    $detailsResponse = $businessGateway->invoiceTemplateService()->getTemplate($templateId);

    if ($detailsResponse->isSuccessful()) {
        echo "Template details retrieved\n";
        $details = isset($detailsResponse->getDecodeResponse()['data']) ? $detailsResponse->getDecodeResponse()['data'] : [];
        $template = $details;

        echo "  ID: " . (isset($template['id']) ? $template['id'] : $templateId) . "\n";
        echo "  Name: " . (isset($template['name']) ? $template['name'] : 'N/A') . "\n";
        echo "  Country: " . (isset($template['country_code']) ? $template['country_code'] : 'N/A') . "\n";
        echo "  Footer: " . (isset($template['footer']) ? $template['footer'] : 'N/A') . "\n";

        if (isset($template['colors'])) {
            echo "  Primary Color: " . (isset($template['colors']['primary']) ? $template['colors']['primary'] : 'N/A') . "\n";
            echo "  Secondary Color: " . (isset($template['colors']['secondary']) ? $template['colors']['secondary'] : 'N/A') . "\n";
        }

        if (isset($template['full_address'])) {
            echo "  Full Address: " . $template['full_address'] . "\n";
        }
    } else {
        echo "Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
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
    $updateResponse = $businessGateway->invoiceTemplateService()->updateTemplate($templateId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "Template updated successfully\n";
        echo "  Updated primary color to: {$updateData['colors']['primary']}\n";
        echo "  Updated footer\n";
    } else {
        echo "Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4.5: Update template with logo
echo "4.5 Updating template with logo\n";
echo "--------------------------------\n";

$updateWithLogoData = [
    'footer' => 'Updated template with new branding and logo!',
    'colors' => [
        'primary' => '#7c2d12',
        'secondary' => '#fef7ed'
    ],
    'logo' => '/path/to/updated-logo.jpg' // File path provided directly in params
];

try {
    // Note: In real implementation, provide actual logo file path in params['logo']
    // The service automatically detects file fields and uses multipart upload with _method=PUT

    // For demo purposes, update without logo
    $demoUpdateData = $updateWithLogoData;
    unset($demoUpdateData['logo']); // Remove for demo
    $updateLogoResponse = $businessGateway->invoiceTemplateService()->updateTemplate($templateId, $demoUpdateData);

    if ($updateLogoResponse->isSuccessful()) {
        echo "Template with logo updated successfully\n";
        echo "  Updated footer and branding colors\n";
        echo "  Logo: File would be uploaded automatically if 'logo' field contains file path\n";
        echo "  Usage: \$params['logo'] = '/path/to/new-file.jpg'\n";
        echo "  Method: Automatically uses POST + _method=PUT for file uploads\n";
    } else {
        echo "Error: " . $updateLogoResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: List all templates
echo "5. Listing all templates\n";
echo "------------------------\n";

$listFilters = [
    'page' => 1,
    'per_page' => 10
];

try {
    $listResponse = $businessGateway->invoiceTemplateService()->listTemplates($listFilters);

    if ($listResponse->isSuccessful()) {
        echo "Templates list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $templates = isset($listData['data']['data']) ? $listData['data']['data'] : [];

        echo "  Found " . count($templates) . " templates\n";
        foreach ($templates as $template) {
            $id = isset($template['id']) ? $template['id'] : 'Unknown';
            $name = isset($template['name']) ? $template['name'] : 'Unnamed';
            echo "    - $name ($id)\n";
        }
    } else {
        echo "Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Create UK-specific template
echo "6. Creating UK-specific template\n";
echo "--------------------------------\n";

$ukTemplateData = [
    'name' => 'UK Business Template',
    'footer' => 'Registered in England and Wales. Company No: 12345678. VAT No: GB123456789',
    'colors' => [
        'primary' => '#1e40af',
        'secondary' => '#f1f5f9'
    ],
    'localized_address' => [
        'address_line1' => '10 Downing Street',
        'locality' => 'London',
        'postal_code' => 'SW1A 2AA'
    ],
    'country_code' => 'GB'
];

try {
    $ukResponse = $businessGateway->invoiceTemplateService()->createTemplate($ukTemplateData);

    if ($ukResponse->isSuccessful()) {
        echo "UK template created successfully\n";
        $responseData = $ukResponse->getDecodeResponse();
        $ukTemplateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-UK-' . time();
        echo "  Template ID: $ukTemplateId\n";
        echo "  UK Address: {$ukTemplateData['localized_address']['address_line1']}, {$ukTemplateData['localized_address']['locality']}\n";
        echo "  Postal Code: {$ukTemplateData['localized_address']['postal_code']}\n";
    } else {
        echo "Error: " . $ukResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Search templates by name
echo "7. Searching templates by name\n";
echo "------------------------------\n";

$searchFilters = [
    'filter' => 'Professional',
    'per_page' => 5
];

try {
    $searchResponse = $businessGateway->invoiceTemplateService()->listTemplates($searchFilters);

    if ($searchResponse->isSuccessful()) {
        echo "Template search completed\n";
        $searchData = $searchResponse->getDecodeResponse();
        $templates = isset($searchData['data']['data']) ? $searchData['data']['data'] : [];

        if (count($templates) > 0) {
            echo "  Found " . count($templates) . " matching templates:\n";
            foreach ($templates as $template) {
                $id = isset($template['id']) ? $template['id'] : 'Unknown';
                $name = isset($template['name']) ? $template['name'] : 'Unnamed';
                echo "    - $name ($id)\n";
            }
        } else {
            echo "  No templates found matching 'Professional'\n";
        }
    } else {
        echo "Error: " . $searchResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Bulk template operations
echo "8. Bulk template operations\n";
echo "---------------------------\n";

$bulkTemplates = [
    [
        'name' => 'Minimal Template',
        'footer' => 'Simple and clean invoice design',
        'colors' => ['primary' => '#374151', 'secondary' => '#f9fafb']
    ],
    [
        'name' => 'Corporate Template',
        'footer' => 'Professional corporate invoice template',
        'colors' => ['primary' => '#1f2937', 'secondary' => '#f3f4f6']
    ]
];

foreach ($bulkTemplates as $templateData) {
    $templateData['country_code'] = 'US';
    $templateData['localized_address'] = [
        'address_line1' => '123 Business St',
        'locality' => 'Business City',
        'administrative_area' => 'CA',
        'postal_code' => '90210'
    ];

    try {
        $response = $businessGateway->invoiceTemplateService()->createTemplate($templateData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $id = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-BULK-' . time();
            echo "  Created: {$templateData['name']} (ID: $id)\n";
        } else {
            echo "  Error creating {$templateData['name']}: " . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  Exception creating {$templateData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Example 9: Delete a template
echo "9. Deleting a template\n";
echo "----------------------\n";

$deleteTemplateId = isset($ukTemplateId) ? $ukTemplateId : 'IT-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->invoiceTemplateService()->deleteTemplate($deleteTemplateId);

    if ($deleteResponse->isSuccessful()) {
        echo "Template deleted successfully\n";
        echo "  Deleted Template ID: $deleteTemplateId\n";
    } else {
        echo "Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nInvoice Template API examples completed!\n";
echo "Use cases:\n";
echo "- Brand-consistent invoice design\n";
echo "- Multi-country invoice templates\n";
echo "- Corporate identity management\n";
echo "- Professional invoice presentation\n";
echo "- Template reuse across invoices\n";
echo "- Localized address formatting\n";
echo "- Custom color schemes and branding\n";
echo "- Template management and organization\n";
echo "- Logo upload and branding (multipart/form-data)\n";
echo "- Server-side validation and processing\n";

echo "\nMultipart Support Notes:\n";
echo "- File auto-detection built into HttpClient - works across all services\n";
echo "- Any field with existing file path automatically uploaded via multipart\n";
echo "- Works with any field name (logo, image, document, attachment, etc.)\n";
echo "- POST: Automatically uses multipart/form-data when files detected\n";
echo "- PUT: Automatically uses POST + _method=PUT when files detected\n";
echo "- No client-side validation - API handles all parameter validation\n";
echo "- File detection: Non-URL strings that point to existing files\n";
echo "- Universal: Same logic applies to all API endpoints with file support\n";