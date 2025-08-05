<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice API Examples\n";
echo "=================================\n\n";

// Example 1: Create a new e-invoice with embedded components
echo "1. Creating a new e-invoice with embedded components\n";
echo "----------------------------------------------------\n";

$invoiceData = [
    'currency' => 'USD',
    'summary' => 'Website development project invoice',
    'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'contact_id' => 'CT-250527-AZARCIJE',
    'template' => [
        'name' => 'Project Template',
        'footer' => 'Thank you for your business!',
        'colors' => [
            'primary' => '#2563eb',
            'secondary' => '#f8fafc'
        ],
        'localized_address' => [
            'address_line1' => '123 Business Ave',
            'locality' => 'San Francisco',
            'administrative_area' => 'CA',
            'postal_code' => '94105'
        ],
        'country_code' => 'US'
    ],
    'tax' => [
        'name' => 'Sales Tax',
        'type' => 'flat',
        'value' => 0.08,
        'description' => '8% sales tax'
    ],
    'discount' => [
        'name' => 'Early Payment',
        'type' => 'flat',
        'value' => 50.0,
        'currency' => 'USD',
        'description' => '$50 early payment discount'
    ],
    'items' => [
        [
            'price' => 2500.00,
            'quantity' => 1,
            'product' => [
                'sku' => 'WEB-DEV-PROJ',
                'type' => 'digital',
                'name' => 'Website Development',
                'description' => 'Complete website development project',
                'prices' => [
                    [
                        'price' => 2500.00,
                        'currency' => 'USD'
                    ]
                ]
            ],
            'tax' => [
                'name' => 'Item Tax',
                'type' => 'flat',
                'value' => 0.08,
                'description' => '8% tax on this item'
            ],
            'discount' => [
                'name' => 'Item Discount',
                'type' => 'percentage',
                'value' => 5,
                'description' => '5% discount on this item'
            ]
        ]
    ]
];

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ E-invoice with embedded components created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'FPBIV-' . time();
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $invoiceData['number'] . "\n";
        echo "  Currency: " . $invoiceData['currency'] . "\n";
        echo "  Items: " . count($invoiceData['items']) . "\n";
        echo "  Template: " . $invoiceData['template']['name'] . "\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create template with logo file (if you have a logo file)
echo "2. Creating invoice template with logo\n";
echo "--------------------------------------\n";

$templateData = [
    'name' => 'Professional Template with Logo',
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
    // Note: For file upload, you would add: 'logo' => '@/path/to/logo.png'
];

try {
    $templateResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($templateData);

    if ($templateResponse->isSuccessful()) {
        echo "✓ Invoice template created successfully\n";
        $responseData = $templateResponse->getDecodeResponse();
        $templateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-' . time();
        echo "  Template ID: " . $templateId . "\n";
        echo "  Name: " . $templateData['name'] . "\n";
        echo "  Primary Color: " . $templateData['colors']['primary'] . "\n";
    } else {
        echo "✗ Error: " . $templateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create tax
echo "3. Creating tax configuration\n";
echo "-----------------------------\n";

$taxData = [
    'name' => 'Sales Tax',
    'type' => 'flat',
    'value' => 0.08,
    'description' => '8% sales tax rate'
];

try {
    $taxResponse = $businessGateway->eInvoiceTaxService()->createTax($taxData);

    if ($taxResponse->isSuccessful()) {
        echo "✓ Tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-' . time();
        echo "  Tax ID: " . $taxId . "\n";
        echo "  Name: " . $taxData['name'] . "\n";
        echo "  Type: " . $taxData['type'] . "\n";
        echo "  Rate: " . ($taxData['value'] * 100) . "%\n";
    } else {
        echo "✗ Error: " . $taxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Create discount
echo "4. Creating discount offer\n";
echo "--------------------------\n";

$discountData = [
    'name' => 'Early Bird Discount',
    'type' => 'flat',
    'value' => 50.0,
    'currency' => 'USD',
    'description' => '$50 discount for early payment'
];

try {
    $discountResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($discountData);

    if ($discountResponse->isSuccessful()) {
        echo "✓ Discount created successfully\n";
        $responseData = $discountResponse->getDecodeResponse();
        $discountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-' . time();
        echo "  Discount ID: " . $discountId . "\n";
        echo "  Name: " . $discountData['name'] . "\n";
        echo "  Value: $" . $discountData['value'] . " " . $discountData['currency'] . "\n";
    } else {
        echo "✗ Error: " . $discountResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Create product with image (if you have an image file)
echo "5. Creating product with image\n";
echo "------------------------------\n";

$productData = [
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'name' => 'Website Development Package',
    'description' => 'Complete website development with modern responsive design',
    'prices' => [
        [
            'price' => 2500.00,
            'currency' => 'USD'
        ]
    ]
    // Note: For file upload, you would add: 'image' => '@/path/to/product-image.jpg'
];

try {
    $productResponse = $businessGateway->eInvoiceProductService()->createProduct($productData);

    if ($productResponse->isSuccessful()) {
        echo "✓ Product created successfully\n";
        $responseData = $productResponse->getDecodeResponse();
        $productId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-' . time();
        echo "  Product ID: " . $productId . "\n";
        echo "  SKU: " . $productData['sku'] . "\n";
        echo "  Name: " . $productData['name'] . "\n";
        echo "  Type: " . $productData['type'] . "\n";
    } else {
        echo "✗ Error: " . $productResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Update invoice using POST with _method=PUT
echo "6. Updating invoice\n";
echo "-------------------\n";

$invoiceId = isset($invoiceId) ? $invoiceId : 'FPBIV-' . time();
$updateData = [
    'summary' => 'Updated website development project invoice',
    'template' => [
        'footer' => 'Updated footer - Thank you for choosing our services!'
    ]
];

try {
    $updateResponse = $businessGateway->eInvoiceService()->updateInvoice($invoiceId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Invoice updated successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Updated summary and template footer\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Update invoice status
echo "7. Updating invoice status\n";
echo "--------------------------\n";

$statusParams = [
    'status' => 'sent',
    'notes' => 'Invoice sent to customer via email'
];

try {
    $statusResponse = $businessGateway->eInvoiceService()->updateInvoiceStatus($invoiceId, $statusParams);

    if ($statusResponse->isSuccessful()) {
        echo "✓ Invoice status updated successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  New status: " . $statusParams['status'] . "\n";
        echo "  Notes: " . $statusParams['notes'] . "\n";
    } else {
        echo "✗ Error: " . $statusResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Preview invoice (HTML)
echo "8. Previewing invoice HTML\n";
echo "--------------------------\n";

try {
    $previewResponse = $businessGateway->eInvoiceService()->previewInvoice($invoiceId);

    if ($previewResponse->isSuccessful()) {
        echo "✓ Invoice preview generated successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Preview format: HTML\n";
        echo "  (HTML content received - can be displayed in browser)\n";
    } else {
        echo "✗ Error: " . $previewResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 9: Send invoice to customer
echo "9. Sending invoice to customer\n";
echo "------------------------------\n";

$sendParams = [
    'test' => false  // According to API docs, main parameter is 'test'
];

try {
    $sendResponse = $businessGateway->eInvoiceService()->sendInvoice($invoiceId, $sendParams);

    if ($sendResponse->isSuccessful()) {
        echo "✓ Invoice sent successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Sent to customer's email address\n";
    } else {
        echo "✗ Error: " . $sendResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 10: Download invoice PDF (file response)
echo "10. Downloading invoice PDF\n";
echo "---------------------------\n";

try {
    // downloadInvoicePdf returns raw file data, not GeneralResponse
    $downloadResponse = $businessGateway->eInvoiceService()->downloadInvoicePdf($invoiceId);

    if ($downloadResponse['httpCode'] == 200) {
        echo "✓ Invoice PDF downloaded successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Content-Length: " . strlen($downloadResponse['response']) . " bytes\n";
        echo "  Content-Type: PDF\n";
        
        // In a real application, you would save this to a file:
        // $filename = 'invoice_' . $invoiceId . '.pdf';
        // file_put_contents($filename, $downloadResponse['response']);
        // echo "  Saved as: " . $filename . "\n";
        
        echo "  (File content received - would be saved as PDF in real application)\n";
    } else {
        echo "✗ Error downloading PDF: HTTP " . $downloadResponse['httpCode'] . "\n";
        if (!empty($downloadResponse['response'])) {
            // Try to decode error response
            $errorData = json_decode($downloadResponse['response'], true);
            if ($errorData && isset($errorData['message'])) {
                echo "  Error message: " . $errorData['message'] . "\n";
            }
        }
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Invoice status handling via pingbacks (documentation example)
echo "Invoice status handling via pingbacks\n";
echo "-------------------------------------\n";

echo "✓ Invoice status changes are handled via pingbacks\n";
echo "  Pingback example from API documentation:\n";
echo "  {\n";
echo "    \"event\": \"invoice.status.updated\",\n";
echo "    \"invoice\": {\n";
echo "      \"status\": \"void\",\n";
echo "      \"id\": \"FPBIV-250616-3UHJ\"\n";
echo "    }\n";
echo "  }\n";
echo "  \n";
echo "  For complete list of available invoice statuses, please refer to:\n";
echo "  https://docs.fasterpay.com/api#section-einvoice-api-statuses\n";
echo "  \n";
echo "  Status changes are automatically communicated via pingbacks\n";
echo "  to your configured pingback URL. The system will send status\n";
echo "  updates when invoices transition between different states\n";
echo "  during their lifecycle.\n";

echo "\nE-Invoice API examples completed!\n";
echo "Use cases:\n";
echo "• Automated billing and invoicing\n";
echo "• Recurring subscription billing\n";
echo "• Professional invoice generation\n";
echo "• Payment tracking and reminders\n";
echo "• Financial reporting and analytics\n";
echo "• Multi-currency invoicing\n";
echo "• Tax compliance and reporting\n";
echo "• Customer payment portal integration\n"; "// Example 1: Create a new e-invoice with embedded components
echo "1. Creating a new e-invoice with embedded components\n";
echo "----------------------------------------------------\n";

$invoiceData = array(
    'currency' => 'USD',
    'summary' => 'Website development project invoice',
    'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'contact_id' => 'CT-250527-AZARCIJE',
    'template' => array(
        'name' => 'Project Template',
        'footer' => 'Thank you for your business!',
        'colors' => array(
            'primary' => '#2563eb',
            'secondary' => '#f8fafc'
        ),
        'localized_address' => array(
            'address_line1' => '123 Business Ave',
            'locality' => 'San Francisco',
            'administrative_area' => 'CA',
            'postal_code' => '94105'
        ),
        'country_code' => 'US'
    ),
    'tax' => array(
        'name' => 'Sales Tax',
        'type' => 'flat',
        'value' => 0.08,
        'description' => '8% sales tax'
    ),
    'discount' => array(
        'name' => 'Early Payment',
        'type' => 'flat',
        'value' => 50.0,
        'currency' => 'USD',
        'description' => '$50 early payment discount'
    ),
    'items' => array(
        array(
            'price' => 2500.00,
            'quantity' => 1,
            'product' => array(
                'sku' => 'WEB-DEV-PROJ',
                'type' => 'digital',
                'name' => 'Website Development',
                'description' => 'Complete website development project',
                'prices' => array(
                    array(
                        'price' => 2500.00,
                        'currency' => 'USD'
                    )
                )
            ),
            'tax' => array(
                'name' => 'Item Tax',
                'type' => 'flat',
                'value' => 0.08,
                'description' => '8% tax on this item'
            ),
            'discount' => array(
                'name' => 'Item Discount',
                'type' => 'percentage',
                'value' => 5,
                'description' => '5% discount on this item'
            )
        )
    )
);

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ E-invoice with embedded components created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'FPBIV-' . time();
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $invoiceData['number'] . "\n";
        echo "  Currency: " . $invoiceData['currency'] . "\n";
        echo "  Items: " . count($invoiceData['items']) . "\n";
        echo "  Template: " . $invoiceData['template']['name'] . "\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create template with logo file (if you have a logo file)
echo "2. Creating invoice template with logo\n";
echo "--------------------------------------\n";

$templateData = array(
    'name' => 'Professional Template with Logo',
    'footer' => 'Thank you for your business! Payment terms: Net 30 days.',
    'colors' => array(
        'primary' => '#2563eb',
        'secondary' => '#f8fafc'
    ),
    'localized_address' => array(
        'address_line1' => '123 Business Avenue',
        'locality' => 'San Francisco',
        'administrative_area' => 'CA',
        'postal_code' => '94105'
    ),
    'country_code' => 'US'
    // Note: For file upload, you would add: 'logo' => '@/path/to/logo.png'
);

try {
    $templateResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($templateData);

    if ($templateResponse->isSuccessful()) {
        echo "✓ Invoice template created successfully\n";
        $responseData = $templateResponse->getDecodeResponse();
        $templateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-' . time();
        echo "  Template ID: " . $templateId . "\n";
        echo "  Name: " . $templateData['name'] . "\n";
        echo "  Primary Color: " . $templateData['colors']['primary'] . "\n";
    } else {
        echo "✗ Error: " . $templateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create tax
echo "3. Creating tax configuration\n";
echo "-----------------------------\n";

$taxData = array(
    'name' => 'Sales Tax',
    'type' => 'flat',
    'value' => 0.08,
    'description' => '8% sales tax rate'
);

try {
    $taxResponse = $businessGateway->eInvoiceTaxService()->createTax($taxData);

    if ($taxResponse->isSuccessful()) {
        echo "✓ Tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-' . time();
        echo "  Tax ID: " . $taxId . "\n";
        echo "  Name: " . $taxData['name'] . "\n";
        echo "  Type: " . $taxData['type'] . "\n";
        echo "  Rate: " . ($taxData['value'] * 100) . "%\n";
    } else {
        echo "✗ Error: " . $taxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Create discount
echo "4. Creating discount offer\n";
echo "--------------------------\n";

$discountData = array(
    'name' => 'Early Bird Discount',
    'type' => 'flat',
    'value' => 50.0,
    'currency' => 'USD',
    'description' => '$50 discount for early payment'
);

try {
    $discountResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($discountData);

    if ($discountResponse->isSuccessful()) {
        echo "✓ Discount created successfully\n";
        $responseData = $discountResponse->getDecodeResponse();
        $discountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-' . time();
        echo "  Discount ID: " . $discountId . "\n";
        echo "  Name: " . $discountData['name'] . "\n";
        echo "  Value: $" . $discountData['value'] . " " . $discountData['currency'] . "\n";
    } else {
        echo "✗ Error: " . $discountResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Create product with image (if you have an image file)
echo "5. Creating product with image\n";
echo "------------------------------\n";

$productData = array(
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'name' => 'Website Development Package',
    'description' => 'Complete website development with modern responsive design',
    'prices' => array(
        array(
            'price' => 2500.00,
            'currency' => 'USD'
        )
    )
    // Note: For file upload, you would add: 'image' => '@/path/to/product-image.jpg'
);

try {
    $productResponse = $businessGateway->eInvoiceProductService()->createProduct($productData);

    if ($productResponse->isSuccessful()) {
        echo "✓ Product created successfully\n";
        $responseData = $productResponse->getDecodeResponse();
        $productId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-' . time();
        echo "  Product ID: " . $productId . "\n";
        echo "  SKU: " . $productData['sku'] . "\n";
        echo "  Name: " . $productData['name'] . "\n";
        echo "  Type: " . $productData['type'] . "\n";
    } else {
        echo "✗ Error: " . $productResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Update invoice using POST with _method=PUT
echo "6. Updating invoice\n";
echo "-------------------\n";

$invoiceId = isset($invoiceId) ? $invoiceId : 'FPBIV-' . time();
$updateData = array(
    'summary' => 'Updated website development project invoice',
    'template' => array(
        'footer' => 'Updated footer - Thank you for choosing our services!'
    )
);

try {
    $updateResponse = $businessGateway->eInvoiceService()->updateInvoice($invoiceId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Invoice updated successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Updated summary and template footer\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Send invoice to customer
echo "7. Sending invoice to customer\n";
echo "------------------------------\n";

$sendParams = array(
    'method' => 'email',
    'email' => 'customer@example.com',
    'subject' => 'Invoice ' . (isset($invoiceData['number']) ? $invoiceData['number'] : 'INV-001') . ' from Your Company',
    'message' => 'Dear Customer, please find attached your invoice. Payment is due within 30 days.',
    'copy_sender' => true,
    'attach_pdf' => true
);

try {
    $sendResponse = $businessGateway->eInvoiceService()->sendInvoice($invoiceId, $sendParams);

    if ($sendResponse->isSuccessful()) {
        echo "✓ Invoice sent successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Sent to: " . $sendParams['email'] . "\n";
        echo "  Method: " . $sendParams['method'] . "\n";
        echo "  PDF attached: " . ($sendParams['attach_pdf'] ? 'Yes' : 'No') . "\n";
    } else {
        echo "✗ Error: " . $sendResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Download invoice PDF (file response)
echo "8. Downloading invoice PDF\n";
echo "--------------------------\n";

$downloadOptions = array(
    'format' => 'pdf',
    'template' => 'professional',
    'include_payments' => true
);

try {
    // downloadInvoicePdf returns raw file data, not GeneralResponse
    $downloadResponse = $businessGateway->eInvoiceService()->downloadInvoicePdf($invoiceId, $downloadOptions);

    if ($downloadResponse['httpCode'] == 200) {
        echo "✓ Invoice PDF downloaded successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Content-Length: " . strlen($downloadResponse['response']) . " bytes\n";
        echo "  Content-Type: PDF\n";
        
        // In a real application, you would save this to a file:
        // $filename = 'invoice_' . $invoiceId . '.pdf';
        // file_put_contents($filename, $downloadResponse['response']);
        // echo "  Saved as: " . $filename . "\n";
        
        echo "  (File content received - would be saved as PDF in real application)\n";
    } else {
        echo "✗ Error downloading PDF: HTTP " . $downloadResponse['httpCode'] . "\n";
        if (!empty($downloadResponse['response'])) {
            // Try to decode error response
            $errorData = json_decode($downloadResponse['response'], true);
            if ($errorData && isset($errorData['message'])) {
                echo "  Error message: " . $errorData['message'] . "\n";
            }
        }
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 9: Get invoice details
echo "9. Getting invoice details\n";
echo "--------------------------\n";

try {
    $detailsResponse = $businessGateway->eInvoiceService()->getInvoice($invoiceId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Invoice details retrieved successfully\n";
        $details = $detailsResponse->getDecodeResponse();
        
        if (isset($details['data'])) {
            $invoice = $details['data'];
            echo "  Invoice ID: " . (isset($invoice['id']) ? $invoice['id'] : $invoiceId) . "\n";
            echo "  Status: " . (isset($invoice['status']) ? $invoice['status'] : 'N/A') . "\n";
            echo "  Currency: " . (isset($invoice['currency']) ? $invoice['currency'] : 'N/A') . "\n";
            echo "  Summary: " . (isset($invoice['summary']) ? $invoice['summary'] : 'N/A') . "\n";
            echo "  Created: " . (isset($invoice['created_at']) ? $invoice['created_at'] : 'N/A') . "\n";
        }
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: List invoices
echo "8. Listing invoices\n";
echo "-------------------\n";

$filters = array(
    'limit' => 20,
    'offset' => 0
);

try {
    $listResponse = $businessGateway->eInvoiceService()->listInvoices($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Invoice list retrieved successfully\n";
        echo "  Limit: 20 invoices\n";
        
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " invoices\n";
            
            // Display first few invoices
            $invoices = array_slice($listData['data'], 0, 3);
            foreach ($invoices as $invoice) {
                $id = isset($invoice['id']) ? $invoice['id'] : 'Unknown';
                $status = isset($invoice['status']) ? $invoice['status'] : 'Unknown';
                $currency = isset($invoice['currency']) ? $invoice['currency'] : 'Unknown';
                echo "    - " . $id . " (Status: " . $status . ", Currency: " . $currency . ")\n";
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

// Example 9: Delete invoice
echo "9. Deleting invoice\n";
echo "-------------------\n";

$invoiceToDelete = 'FPBIV-DELETE-' . time(); // Use a test invoice ID

try {
    $deleteResponse = $businessGateway->eInvoiceService()->deleteInvoice($invoiceToDelete);

    if ($deleteResponse->isSuccessful()) {
        echo "✓ Invoice deleted successfully\n";
        echo "  Deleted Invoice ID: " . $invoiceToDelete . "\n";
    } else {
        echo "✗ Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 10: Handle invoice status via pingbacks (documentation example)
echo "10. Invoice status handling via pingbacks\n";
echo "-----------------------------------------\n";

echo "✓ Invoice status changes are handled via pingbacks\n";
echo "  Pingback example from API documentation:\n";
echo "  {\n";
echo "    \"event\": \"invoice.status.updated\",\n";
echo "    \"invoice\": {\n";
echo "      \"status\": \"void\",\n";
echo "      \"id\": \"FPBIV-250616-3UHJ\"\n";
echo "    }\n";
echo "  }\n";
echo "  \n";
echo "  Available invoice statuses include:\n";
echo "  - draft: Invoice is in draft status\n";
echo "  - sent: Invoice has been sent to customer\n";
echo "  - paid: Invoice has been paid\n";
echo "  - void: Invoice has been voided/cancelled\n";
echo "  - overdue: Invoice payment is overdue\n";
echo "  \n";
echo "  Status changes are automatically communicated via pingbacks\n";
echo "  to your configured pingback URL.\n";<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway(array(
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
));

echo "FasterPay E-Invoice API Examples\n";
echo "=================================\n\n";

// Example 1: Create a new e-invoice
echo "1. Creating a new e-invoice\n";
echo "---------------------------\n";

$invoiceData = array(
    'currency' => 'USD',
    'summary' => 'Website development project invoice',
    'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'contact_id' => 'CT-250527-AZARCIJE',
    'items' => array(
        array(
            'price' => 2500.00,
            'quantity' => 1,
            'name' => 'Website Development Services',
            'description' => 'Complete website development with responsive design'
        ),
        array(
            'price' => 99.99,
            'quantity' => 12,
            'name' => 'Monthly Hosting Package',
            'description' => 'Premium hosting service for 12 months'
        )
    )
);

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ E-invoice created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'FPBIV-' . time();
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $invoiceData['number'] . "\n";
        echo "  Currency: " . $invoiceData['currency'] . "\n";
        echo "  Items: " . count($invoiceData['items']) . "\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create template
echo "2. Creating invoice template\n";
echo "----------------------------\n";

$templateData = array(
    'name' => 'Professional Template',
    'footer' => 'Thank you for your business! Payment terms: Net 30 days.',
    'colors' => array(
        'primary' => '#2563eb',
        'secondary' => '#f8fafc'
    ),
    'localized_address' => array(
        'address_line1' => '123 Business Avenue',
        'locality' => 'San Francisco',
        'administrative_area' => 'CA',
        'postal_code' => '94105'
    ),
    'country_code' => 'US'
);

try {
    $templateResponse = $businessGateway->eInvoiceTemplateService()->createTemplate($templateData);

    if ($templateResponse->isSuccessful()) {
        echo "✓ Invoice template created successfully\n";
        $responseData = $templateResponse->getDecodeResponse();
        $templateId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'IT-' . time();
        echo "  Template ID: " . $templateId . "\n";
        echo "  Name: " . $templateData['name'] . "\n";
        echo "  Primary Color: " . $templateData['colors']['primary'] . "\n";
    } else {
        echo "✗ Error: " . $templateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create tax
echo "3. Creating tax configuration\n";
echo "-----------------------------\n";

$taxData = array(
    'name' => 'Sales Tax',
    'type' => 'flat',
    'value' => 0.08,
    'description' => '8% sales tax rate'
);

try {
    $taxResponse = $businessGateway->eInvoiceTaxService()->createTax($taxData);

    if ($taxResponse->isSuccessful()) {
        echo "✓ Tax created successfully\n";
        $responseData = $taxResponse->getDecodeResponse();
        $taxId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'TX-' . time();
        echo "  Tax ID: " . $taxId . "\n";
        echo "  Name: " . $taxData['name'] . "\n";
        echo "  Type: " . $taxData['type'] . "\n";
        echo "  Rate: " . ($taxData['value'] * 100) . "%\n";
    } else {
        echo "✗ Error: " . $taxResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Create discount
echo "4. Creating discount offer\n";
echo "--------------------------\n";

$discountData = array(
    'name' => 'Early Bird Discount',
    'type' => 'flat',
    'value' => 50.0,
    'currency' => 'USD',
    'description' => '$50 discount for early payment'
);

try {
    $discountResponse = $businessGateway->eInvoiceDiscountService()->createDiscount($discountData);

    if ($discountResponse->isSuccessful()) {
        echo "✓ Discount created successfully\n";
        $responseData = $discountResponse->getDecodeResponse();
        $discountId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'DC-' . time();
        echo "  Discount ID: " . $discountId . "\n";
        echo "  Name: " . $discountData['name'] . "\n";
        echo "  Value: $" . $discountData['value'] . " " . $discountData['currency'] . "\n";
    } else {
        echo "✗ Error: " . $discountResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Create product
echo "5. Creating product\n";
echo "-------------------\n";

$productData = array(
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'name' => 'Website Development Package',
    'description' => 'Complete website development with modern responsive design',
    'prices' => array(
        array(
            'price' => 2500.00,
            'currency' => 'USD'
        )
    )
);

try {
    $productResponse = $businessGateway->eInvoiceProductService()->createProduct($productData);

    if ($productResponse->isSuccessful()) {
        echo "✓ Product created successfully\n";
        $responseData = $productResponse->getDecodeResponse();
        $productId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-' . time();
        echo "  Product ID: " . $productId . "\n";
        echo "  SKU: " . $productData['sku'] . "\n";
        echo "  Name: " . $productData['name'] . "\n";
        echo "  Type: " . $productData['type'] . "\n";
    } else {
        echo "✗ Error: " . $productResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Send invoice
echo "6. Sending invoice to customer\n";
echo "------------------------------\n";

$invoiceId = isset($invoiceId) ? $invoiceId : 'FPBIV-' . time();
$sendParams = array(
    'method' => 'email',
    'email' => 'customer@example.com',
    'subject' => 'Invoice ' . (isset($invoiceData['number']) ? $invoiceData['number'] : 'INV-001') . ' from Your Company',
    'message' => 'Dear Customer, please find attached your invoice. Payment is due within 30 days.',
    'copy_sender' => true
);

try {
    $sendResponse = $businessGateway->eInvoiceService()->sendInvoice($invoiceId, $sendParams);

    if ($sendResponse->isSuccessful()) {
        echo "✓ Invoice sent successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Sent to: " . $sendParams['email'] . "\n";
        echo "  Method: " . $sendParams['method'] . "\n";
    } else {
        echo "✗ Error: " . $sendResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Mark invoice as paid
echo "7. Marking invoice as paid\n";
echo "--------------------------\n";

$paymentData = array(
    'amount' => 2599.99,
    'payment_date' => date('Y-m-d'),
    'payment_method' => 'bank_transfer',
    'reference' => 'TXN-' . time(),
    'notes' => 'Payment received via bank transfer'
);

try {
    $paidResponse = $businessGateway->eInvoiceService()->markAsPaid($invoiceId, $paymentData);

    if ($paidResponse->isSuccessful()) {
        echo "✓ Invoice marked as paid successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Amount: $" . number_format($paymentData['amount'], 2) . "\n";
        echo "  Payment Date: " . $paymentData['payment_date'] . "\n";
        echo "  Method: " . $paymentData['payment_method'] . "\n";
    } else {
        echo "✗ Error: " . $paidResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: List invoices
echo "8. Listing invoices\n";
echo "-------------------\n";

$filters = array(
    'limit' => 20,
    'offset' => 0
);

try {
    $listResponse = $businessGateway->eInvoiceService()->listInvoices($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Invoice list retrieved successfully\n";
        echo "  Limit: 20 invoices\n";
        
        $listData = $listResponse->getDecodeResponse();
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " invoices\n";
            
            // Display first few invoices
            $invoices = array_slice($listData['data'], 0, 3);
            foreach ($invoices as $invoice) {
                $id = isset($invoice['id']) ? $invoice['id'] : 'Unknown';
                $status = isset($invoice['status']) ? $invoice['status'] : 'Unknown';
                $currency = isset($invoice['currency']) ? $invoice['currency'] : 'Unknown';
                echo "    - " . $id . " (Status: " . $status . ", Currency: " . $currency . ")\n";
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

// Example 9: Get invoice status
echo "9. Getting invoice status\n";
echo "-------------------------\n";

try {
    $statusResponse = $businessGateway->eInvoiceService()->getInvoiceStatus($invoiceId);

    if ($statusResponse->isSuccessful()) {
        echo "✓ Invoice status retrieved successfully\n";
        $statusData = $statusResponse->getDecodeResponse();
        echo "  Invoice ID: " . $invoiceId . "\n";
        
        if (isset($statusData['data'])) {
            $status = $statusData['data'];
            echo "  Status: " . (isset($status['status']) ? $status['status'] : 'N/A') . "\n";
            echo "  Last updated: " . (isset($status['updated_at']) ? $status['updated_at'] : date('Y-m-d H:i:s')) . "\n";
        }
    } else {
        echo "✗ Error: " . $statusResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 10: Cancel invoice
echo "10. Cancelling invoice\n";
echo "----------------------\n";

$cancelParams = array(
    'reason' => 'Customer requested cancellation'
);

try {
    $cancelResponse = $businessGateway->eInvoiceService()->cancelInvoice($invoiceId, $cancelParams);

    if ($cancelResponse->isSuccessful()) {
        echo "✓ Invoice cancelled successfully\n";
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Reason: " . $cancelParams['reason'] . "\n";
    } else {
        echo "✗ Error: " . $cancelResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\nE-Invoice API examples completed!\n";
echo "Use cases:\n";
echo "• Automated billing and invoicing\n";
echo "• Recurring subscription billing\n";
echo "• Professional invoice generation\n";
echo "• Payment tracking and reminders\n";
echo "• Financial reporting and analytics\n";
echo "• Multi-currency invoicing\n";
echo "• Tax compliance and reporting\n";
echo "• Customer payment portal integration\n";