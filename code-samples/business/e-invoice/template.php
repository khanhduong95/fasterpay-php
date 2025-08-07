<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Template API Examples\n";
echo "========================================\n\n";

// Example 1: Create template
echo "1. Creating template\n";
echo "--------------------\n";

$templateData = [
    'name' => 'Professional Template',
    'footer' => 'Thank you for your business!',
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
    'country_code' => 'US',
    // 'logo' => '/path/to/logo.png' // Uncomment for logo upload (file path)
    // 'logo' => new SplFileInfo('/path/to/logo.png') // Or use SplFileInfo object
];

try {
    $response = $businessGateway->invoiceTemplateService()->createTemplate($templateData);

    if ($response->isSuccessful()) {
        echo "Template created successfully\n";
        $data = $response->getDecodeResponse();
        $templateId = $data['data']['id'] ?: 'IT-' . time();
        echo "  Template ID: $templateId\n";
        echo "  Name: {$templateData['name']}\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Update template
echo "2. Updating template\n";
echo "--------------------\n";

$updateData = [
    'footer' => 'Updated footer text',
    'colors' => [
        'primary' => '#059669',
        'secondary' => '#ecfdf5'
    ],
    // 'logo' => '/path/to/new-logo.png' // Uncomment for logo upload (file path)
    // 'logo' => new SplFileInfo('/path/to/new-logo.png') // Or use SplFileInfo object
];

try {
    $response = $businessGateway->invoiceTemplateService()->updateTemplate($templateId, $updateData);

    if ($response->isSuccessful()) {
        echo "Template updated successfully\n";
        echo "  Updated colors and footer\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get template details
echo "3. Getting template details\n";
echo "---------------------------\n";

try {
    $response = $businessGateway->invoiceTemplateService()->getTemplate($templateId);

    if ($response->isSuccessful()) {
        echo "Template details retrieved\n";
        $data = $response->getDecodeResponse();
        $template = $data['data'] ?: [];

        echo "  ID: " . ($template['id'] ?: $templateId) . "\n";
        echo "  Name: " . ($template['name'] ?: 'N/A') . "\n";
        echo "  Country: " . ($template['country_code'] ?: 'N/A') . "\n";
        echo "  Footer: " . ($template['footer'] ?: 'N/A') . "\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: List templates
echo "4. Listing templates\n";
echo "--------------------\n";

try {
    $response = $businessGateway->invoiceTemplateService()->listTemplates(['page' => 1, 'per_page' => 10]);

    if ($response->isSuccessful()) {
        echo "Templates retrieved successfully\n";
        $data = $response->getDecodeResponse();
        $templates = $data['data']['data'] ?: [];
        echo "  Found " . count($templates) . " templates\n";

        foreach ($templates as $template) {
            $id = $template['id'] ?: 'Unknown';
            $name = $template['name'] ?: 'Unnamed';
            echo "    - $name ($id)\n";
        }
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Delete template
echo "5. Deleting template\n";
echo "--------------------\n";

try {
    $response = $businessGateway->invoiceTemplateService()->deleteTemplate($templateId);

    if ($response->isSuccessful()) {
        echo "Template deleted successfully\n";
        echo "  Deleted Template ID: $templateId\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nInvoice Template API examples completed!\n";