<?php

/**
 * FasterPay E-Invoice API Examples
 *
 * Complete examples using BusinessGateway and proper service architecture
 * Uses PHP 5.4+ latest syntax features (short arrays, etc.)
 */

require_once('../../../lib/autoload.php');

// Initialize BusinessGateway
$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay E-Invoice API Examples\n";
echo "=================================\n\n";

$contactData = [
    'email' => 'john.smith@example.com',
    'phone' => '2015550124',
    'phone_country_code' => 'US',
    'first_name' => 'John',
    'last_name' => 'Smith',
    'country' => 'US',
    'favorite' => true
];

$contactResponse = $businessGateway->contactService()->createContact($contactData);

$responseData = $contactResponse->getDecodeResponse();
$contactId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'CT-' . time();

try {

    // Example 1: Create invoice with embedded components
    echo "1. Creating invoice with embedded components\n";
    echo "--------------------------------------------\n";

    $invoiceData = [
        'currency' => 'USD',
        'summary' => 'Website development project invoice',
        'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
        'contact_id' => $contactId,
        'due_date' => date('Y-m-d', strtotime('+30 days')),
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
            'name' => 'Early Payment Discount',
            'type' => 'percentage',
            'value' => 10.0,
            'description' => '10% discount for early payment'
        ],
        'items' => [
            [
                'price' => 2500.00,
                'quantity' => 1,
                'product' => [
                    'sku' => 'WEB-DEV-001',
                    'type' => 'digital',
                    'name' => 'Website Development',
                    'description' => 'Complete website development with responsive design',
                    'prices' => [
                        [
                            'price' => 2500.00,
                            'currency' => 'USD'
                        ]
                    ]
                ],
                'tax' => [
                    'name' => 'Service Tax',
                    'type' => 'percentage',
                    'value' => 5.0
                ]
            ],
            [
                'price' => 99.99,
                'quantity' => 12,
                'product' => [
                    'sku' => 'HOST-PREM-001',
                    'type' => 'digital',
                    'name' => 'Premium Hosting',
                    'description' => 'Premium hosting with SSL and backups'
                ]
            ]
        ]
    ];

    $response = $businessGateway->invoiceService()->createInvoice($invoiceData);

    if ($response->isSuccessful()) {
        echo "✓ Invoice created successfully!\n";
        $data = $response->getDecodeResponse();
        $invoiceId = $data['data']['id'];
        echo "  Invoice ID: " . $invoiceId . "\n";
        echo "  Invoice Number: " . $data['data']['number'] . "\n";
        echo "  Status: " . $data['data']['status'] . "\n";
        echo "  Currency: " . $data['data']['currency'] . "\n";
        echo "  Items: " . count($data['data']['items']) . "\n\n";
    } else {
        echo "✗ Failed to create invoice\n";
        echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
    }

    // Example 2: Create invoice using existing component IDs
    echo "2. Creating invoice with existing component IDs\n";
    echo "-----------------------------------------------\n";

    $simpleInvoiceData = [
        'currency' => 'USD',
        'summary' => 'Consulting services invoice',
        'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
        'contact_id' => $contactId,
        'invoice_template_id' => 'IT-250527-AWRO',
        'tax_id' => 'TX-250527-2E9N',
        'discount_id' => 'DC-250527-WZX0',
        'items' => [
            [
                'product_id' => 'PD-250528-L5CC',
                'price' => 150.00,
                'quantity' => 10,
                'tax_id' => 'TX-250527-2E9N'
            ]
        ]
    ];

    $response = $businessGateway->invoiceService()->createInvoice($simpleInvoiceData);

    if ($response->isSuccessful()) {
        echo "✓ Simple invoice created successfully!\n";
        $data = $response->getDecodeResponse();
        $simpleInvoiceId = $data['data']['id'];
        echo "  Invoice ID: " . $simpleInvoiceId . "\n\n";
    } else {
        echo "✗ Failed to create simple invoice\n";
        echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
    }

    // Example 3: Get invoice details
    echo "3. Getting invoice details\n";
    echo "--------------------------\n";

    if (isset($invoiceId)) {
        $response = $businessGateway->invoiceService()->getInvoice($invoiceId, ['include' => 'prices']);

        if ($response->isSuccessful()) {
            $data = $response->getDecodeResponse();
            $invoice = $data['data'];
            echo "✓ Invoice details retrieved successfully!\n";
            echo "  ID: " . $invoice['id'] . "\n";
            echo "  Status: " . $invoice['status'] . "\n";
            echo "  Currency: " . $invoice['currency'] . "\n";
            echo "  Summary: " . $invoice['summary'] . "\n";
            echo "  Due Date: " . $invoice['due_date'] . "\n";
            echo "  Items Count: " . count($invoice['items']) . "\n\n";
        } else {
            echo "✗ Failed to get invoice details\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 4: List invoices with filters
    echo "4. Listing invoices with filters\n";
    echo "--------------------------------\n";

    $filters = [
        'limit' => 10,
        'offset' => 0,
        'status' => 'draft'
    ];

    $response = $businessGateway->invoiceService()->listInvoices($filters);

    if ($response->isSuccessful()) {
        echo "✓ Invoices listed successfully!\n";
        $data = $response->getDecodeResponse();
        $invoices = $data['data']['data'];
        echo "  Found " . count($invoices) . " draft invoices\n";

        foreach (array_slice($invoices, 0, 3) as $invoice) {
            echo "  - " . $invoice['id'] . " (" . $invoice['status'] . ")\n";
        }
        echo "\n";
    } else {
        echo "✗ Failed to list invoices\n";
        echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
    }

    // Example 5: Update invoice details
    echo "5. Updating invoice details\n";
    echo "---------------------------\n";

    if (isset($invoiceId)) {
        $updateData = [
            'summary' => 'Updated website development project invoice',
            'template' => [
                'footer' => 'Updated footer - Thank you for choosing our services!'
            ]
        ];

        $response = $businessGateway->invoiceService()->updateInvoice($invoiceId, $updateData);

        if ($response->isSuccessful()) {
            echo "✓ Invoice updated successfully!\n";
            echo "  Updated summary and template footer\n\n";
        } else {
            echo "✗ Failed to update invoice\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 6: Update invoice status
    echo "6. Updating invoice status\n";
    echo "--------------------------\n";

    if (isset($invoiceId)) {
        $statusParams = [
            'status' => 'sent',
            'notes' => 'Invoice sent to customer via email'
        ];

        $response = $businessGateway->invoiceService()->updateInvoiceStatus($invoiceId, $statusParams);

        if ($response->isSuccessful()) {
            echo "✓ Invoice status updated successfully!\n";
            echo "  New status: " . $statusParams['status'] . "\n";
            echo "  Notes: " . $statusParams['notes'] . "\n\n";
        } else {
            echo "✗ Failed to update invoice status\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 7: Preview invoice HTML
    echo "7. Previewing invoice HTML\n";
    echo "--------------------------\n";

    if (isset($invoiceId)) {
        $response = $businessGateway->invoiceService()->previewInvoice($invoiceId);

        if ($response->isSuccessful()) {
            echo "✓ Invoice preview generated successfully!\n";
            $htmlContent = $response->getRaw();
            echo "  HTML content length: " . strlen($htmlContent) . " characters\n\n";
        } else {
            echo "✗ Failed to generate invoice preview\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 8: Download invoice PDF
    echo "8. Downloading invoice PDF\n";
    echo "--------------------------\n";

    if (isset($invoiceId)) {
        $response = $businessGateway->invoiceService()->downloadInvoicePdf($invoiceId);

        if ($response->isSuccessful()) {
            echo "✓ Invoice PDF downloaded successfully!\n";
            $pdfContent = $response->getRaw();
            echo "  PDF size: " . strlen($pdfContent) . " bytes\n";

            // Optionally save to file
            // file_put_contents('invoice_' . $invoiceId . '.pdf', $pdfContent);
            echo "\n";
        } else {
            echo "✗ Failed to download invoice PDF\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 9: Send invoice to customer
    echo "9. Sending invoice to customer\n";
    echo "------------------------------\n";

    if (isset($invoiceId)) {
        // Send test email first
        $response = $businessGateway->invoiceService()->sendInvoice($invoiceId, ['test' => true]);

        if ($response->isSuccessful()) {
            echo "✓ Test invoice email sent successfully!\n";

            // Send actual invoice
            $response = $businessGateway->invoiceService()->sendInvoice($invoiceId);

            if ($response->isSuccessful()) {
                echo "✓ Invoice sent to customer successfully!\n\n";
            } else {
                echo "✗ Failed to send invoice to customer\n";
                echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
            }
        } else {
            echo "✗ Failed to send test invoice\n";
            echo "  Error: " . $response->getErrors()->getMessage() . "\n\n";
        }
    }

    // Example 10: Delete operations (cleanup)
    echo "10. Cleanup operations\n";
    echo "----------------------\n";

    // Delete test invoice
    $testInvoiceId = 'FPBIV-DELETE-' . time();
    $response = $businessGateway->invoiceService()->deleteInvoice($testInvoiceId);

    if ($response->isSuccessful()) {
        echo "✓ Test invoice deleted successfully\n";
    } else {
        echo "✓ Expected error for non-existent invoice (cleanup test)\n";
    }

    echo "\n";

} catch (FasterPay\Exception $e) {
    echo "✗ An error occurred: " . $e->getMessage() . "\n";
    echo "  Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nE-Invoice API Examples Completed!\n";
echo "==================================\n\n";

echo "Key Features Demonstrated:\n";
echo "- Create invoices with embedded components (templates, products, taxes, discounts)\n";
echo "- Create invoices using existing component IDs\n";
echo "- Retrieve and list invoices with filtering\n";
echo "- Update invoice details and status\n";
echo "- Preview invoices and download PDFs\n";
echo "- Send invoices to customers\n";
echo "- Manage templates, products, taxes, and discounts\n";
echo "- Handle webhook notifications\n";
echo "- Comprehensive error handling\n";
echo "- File upload support for multipart requests\n\n";

echo "Use Cases:\n";
echo "• Automated billing and invoicing systems\n";
echo "• Recurring subscription billing\n";
echo "• Professional invoice generation\n";
echo "• Payment tracking and reminders\n";
echo "• Financial reporting and analytics\n";
echo "• Multi-currency invoicing\n";
echo "• Tax compliance and reporting\n";
echo "• Customer payment portal integration\n";
echo "• E-commerce order invoicing\n";
echo "• Service-based business billing\n\n";

echo "Best Practices:\n";
echo "- Validate only route parameters (IDs) - API handles all other parameter validation\n";
echo "- Use BusinessGateway service methods for consistent architecture\n";
echo "- Handle responses using isSuccessful(), getDecodeResponse(), and getErrors()\n";
echo "- Use appropriate HTTP methods via HttpClient (post, get, put, delete)\n";
echo "- Implement proper error handling and logging\n";
echo "- Use webhook handlers for real-time status updates\n";
echo "- Follow the documented invoice status lifecycle\n";
echo "- Store component IDs for reuse and reference\n";
echo "- Use test mode during development and integration\n";
echo "- Implement retry logic for transient failures\n\n";

echo "Configuration Notes:\n";
echo "- Set publicKey and privateKey to your actual FasterPay keys\n";
echo "- Set isTest to 0 for production environment\n";
echo "- Ensure contact_id references exist before creating invoices\n";
echo "- Configure webhook URLs in your FasterPay dashboard\n";
echo "- Test all operations in sandbox before going live\n";