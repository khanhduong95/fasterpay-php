<?php

/**
 * FasterPay E-Invoice API - Complete Component Creation & Invoice Examples
 * Creates invoice components first to get their IDs, then uses those IDs for invoices
 */

require_once('../../../lib/autoload.php');

// Initialize BusinessGateway
$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => 'your_public_key',
    'privateKey' => 'your_private_key',
]);

// Store component IDs for later use
$createdTemplateId = null;
$createdTaxId = null;
$createdDiscountId = null;
$createdProductId = null;
$createdContactId = null;

// ===================================================================
// CREATE CONTACT FIRST
// ===================================================================

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
    $response = $businessGateway->contactService()->createContact($contactData);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $createdContactId = $responseData['data']['id'];
        echo 'Contact created: ' . $createdContactId . "\n";
    } else {
        echo 'Error creating contact: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception creating contact: ' . $e->getMessage() . "\n";
}

// ===================================================================
// CREATE TEMPLATE COMPONENT
// ===================================================================

$templateData = [
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
    // 'logo' => new SplFileInfo('/path/to/logo.jpg')
    // 'logo' => '/path/to/logo.jpg'
];

try {
    $response = $businessGateway->invoiceTemplateService()->createTemplate($templateData);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $createdTemplateId = $responseData['data']['id'];
        echo 'Template created: ' . $createdTemplateId . "\n";
    } else {
        echo 'Error creating template: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception creating template: ' . $e->getMessage() . "\n";
}

// ===================================================================
// CREATE TAX COMPONENT
// ===================================================================

$taxData = [
    'name' => 'Sales Tax',
    'type' => 'percentage',
    'value' => 8.5,
    'description' => '8.5% sales tax for California'
];

try {
    $response = $businessGateway->invoiceTaxService()->createTax($taxData);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $createdTaxId = $responseData['data']['id'];
        echo 'Tax created: ' . $createdTaxId . "\n";
    } else {
        echo 'Error creating tax: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception creating tax: ' . $e->getMessage() . "\n";
}

// ===================================================================
// CREATE DISCOUNT COMPONENT
// ===================================================================

$discountData = [
    'name' => 'Early Payment Discount',
    'type' => 'percentage',
    'value' => 10.0,
    'description' => '10% discount for early payment'
];

try {
    $response = $businessGateway->invoiceDiscountService()->createDiscount($discountData);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $createdDiscountId = $responseData['data']['id'];
        echo 'Discount created: ' . $createdDiscountId . "\n";
    } else {
        echo 'Error creating discount: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception creating discount: ' . $e->getMessage() . "\n";
}

// ===================================================================
// CREATE PRODUCT COMPONENT
// ===================================================================

$productData = [
    'name' => 'Website Development Package',
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'description' => 'Complete website development with responsive design.',
    'prices' => [
        [
            'price' => 2500.00,
            'currency' => 'USD'
        ]
    ]
    // 'image' => new SplFileInfo('/path/to/image.jpg')
    // 'image' => '/path/to/image.jpg'
];

try {
    $response = $businessGateway->invoiceProductService()->createProduct($productData);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $createdProductId = $responseData['data']['id'];
        echo 'Product created: ' . $createdProductId . "\n";
    } else {
        echo 'Error creating product: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception creating product: ' . $e->getMessage() . "\n";
}

// ===================================================================
// CREATE INVOICE - Using Created Component IDs
// ===================================================================

$createdInvoiceId = null;

if ($createdTemplateId && $createdTaxId && $createdDiscountId && $createdProductId && $createdContactId) {
    $invoiceDataWithIds = [
        'invoice_template_id' => $createdTemplateId,
        'tax_id' => $createdTaxId,
        'discount_id' => $createdDiscountId,
        'currency' => 'USD',
        'summary' => 'Website development project using created components',
        'number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
        'contact_id' => $createdContactId,
        'due_date' => date('Y-m-d', strtotime('+30 days')),
        'items' => [
            [
                'price' => 2500.00,
                'product_id' => $createdProductId,
                'tax_id' => $createdTaxId,
                'discount_id' => $createdDiscountId,
                'quantity' => 1
            ]
        ]
    ];

    try {
        $response = $businessGateway->invoiceService()->createInvoice($invoiceDataWithIds);
        
        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $createdInvoiceId = $responseData['data']['id'];
            echo 'Invoice created with component IDs: ' . $createdInvoiceId . "\n";
        } else {
            echo 'Error creating invoice: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception creating invoice: ' . $e->getMessage() . "\n";
    }
} else {
    echo "Cannot create invoice - missing required components\n";
}

// ===================================================================
// CREATE INVOICE - With Embedded Components (Alternative approach)
// ===================================================================

$embeddedInvoiceId = null;

if ($createdContactId) {
    $invoiceDataWithEmbedded = [
        'template' => [
            'name' => 'Embedded Template',
            'footer' => 'Thank you for choosing our services!',
            'colors' => [
                'primary' => '#059669',
                'secondary' => '#f0fdf4'
            ],
            'localized_address' => [
                'address_line1' => '456 Commerce St',
                'locality' => 'Los Angeles',
                'administrative_area' => 'CA',
                'postal_code' => '90210'
            ],
            'country_code' => 'US'
            // 'logo' => new SplFileInfo('/path/to/embedded-logo.jpg')
            // 'logo' => '/path/to/embedded-logo.jpg'
        ],
        'tax' => [
            'name' => 'State Sales Tax',
            'type' => 'percentage',
            'value' => 7.25,
            'description' => '7.25% California state sales tax'
        ],
        'discount' => [
            'name' => 'First Time Customer Discount',
            'type' => 'flat',
            'value' => 100.00,
            'currency' => 'USD',
            'description' => '$100 discount for first-time customers'
        ],
        'currency' => 'USD',
        'summary' => 'Website development with embedded components',
        'number' => 'INV-EMB-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
        'contact_id' => $createdContactId,
        'due_date' => date('Y-m-d', strtotime('+45 days')),
        'items' => [
            [
                'price' => 3000.00,
                'product' => [
                    'sku' => 'WEB-DEV-PREMIUM',
                    'type' => 'digital',
                    'name' => 'Premium Website Development',
                    'description' => 'Premium website development with advanced features',
                    // 'image' => new SplFileInfo('/path/to/product-image.jpg')
                    // 'image' => '/path/to/product-image.jpg'
                    'prices' => [
                        [
                            'price' => 3000.00,
                            'currency' => 'USD'
                        ]
                    ]
                ],
                'tax' => [
                    'name' => 'Service Tax',
                    'type' => 'percentage',
                    'value' => 5.0,
                    'description' => '5% service tax'
                ],
                'discount' => [
                    'name' => 'Bulk Service Discount',
                    'type' => 'percentage',
                    'value' => 5.0,
                    'description' => '5% discount for premium services'
                ],
                'quantity' => 1
            ]
        ]
    ];

    try {
        $response = $businessGateway->invoiceService()->createInvoice($invoiceDataWithEmbedded);
        
        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $embeddedInvoiceId = $responseData['data']['id'];
            echo 'Invoice created with embedded components: ' . $embeddedInvoiceId . "\n";
        } else {
            echo 'Error creating embedded invoice: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception creating embedded invoice: ' . $e->getMessage() . "\n";
    }
}

// ===================================================================
// UPDATE INVOICE - Use Created Invoice ID
// ===================================================================

if ($createdInvoiceId) {
    $updateData = [
        'summary' => 'Updated website development project',
        'items' => [
            [
                'price' => 2750.00, // Updated price
                'product_id' => $createdProductId,
                'tax_id' => $createdTaxId,
                'discount_id' => $createdDiscountId,
                'quantity' => 1
            ]
        ]
    ];

    try {
        $response = $businessGateway->invoiceService()->updateInvoice($createdInvoiceId, $updateData);
        
        if ($response->isSuccessful()) {
            echo 'Invoice updated successfully: ' . $createdInvoiceId . "\n";
        } else {
            echo 'Error updating invoice: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception updating invoice: ' . $e->getMessage() . "\n";
    }
}

// ===================================================================
// LIST INVOICES
// ===================================================================

$filters = [
    'limit' => 10,
    'offset' => 0,
    'status' => 'draft',
    'include' => 'items,items.product.prices'
];

try {
    $response = $businessGateway->invoiceService()->listInvoices($filters);
    
    if ($response->isSuccessful()) {
        $responseData = $response->getDecodeResponse();
        $invoices = $responseData['data']['data'];
        echo 'Found ' . count($invoices) . ' draft invoices' . "\n";
        
        foreach ($invoices as $invoice) {
            echo '  - Invoice ID: ' . $invoice['id'] . ' - Status: ' . $invoice['status'] . "\n";
        }
    } else {
        echo 'Error listing invoices: ' . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo 'Exception listing invoices: ' . $e->getMessage() . "\n";
}

// ===================================================================
// GET INVOICE DETAILS - Use Created Invoice ID
// ===================================================================

if ($createdInvoiceId) {
    try {
        $response = $businessGateway->invoiceService()->getInvoice($createdInvoiceId);
        
        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $invoice = $responseData['data'];
            echo 'Retrieved invoice details:' . "\n";
            echo '  ID: ' . $invoice['id'] . "\n";
            echo '  Status: ' . $invoice['status'] . "\n";
            echo '  Currency: ' . $invoice['currency'] . "\n";
            echo '  Summary: ' . $invoice['summary'] . "\n";
        } else {
            echo 'Error getting invoice details: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception getting invoice details: ' . $e->getMessage() . "\n";
    }
}

// ===================================================================
// UPDATE INVOICE STATUS - Use Created Invoice ID
// ===================================================================

if ($createdInvoiceId) {
    $statusData = [
        'status' => 'sent'
    ];

    try {
        $response = $businessGateway->invoiceService()->updateInvoiceStatus($createdInvoiceId, $statusData);
        
        if ($response->isSuccessful()) {
            echo 'Invoice status updated to: ' . $statusData['status'] . ' for invoice: ' . $createdInvoiceId . "\n";
        } else {
            echo 'Error updating invoice status: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception updating invoice status: ' . $e->getMessage() . "\n";
    }
}

// ===================================================================
// SEND INVOICE - Use Embedded Invoice ID
// ===================================================================

if ($embeddedInvoiceId) {
    $sendParams = [
        'email' => 'customer@example.com'
    ];

    try {
        $response = $businessGateway->invoiceService()->sendInvoice($embeddedInvoiceId, $sendParams);
        
        if ($response->isSuccessful()) {
            echo 'Invoice sent successfully to: ' . $sendParams['email'] . ' for invoice: ' . $embeddedInvoiceId . "\n";
        } else {
            echo 'Error sending invoice: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception sending invoice: ' . $e->getMessage() . "\n";
    }
}

// ===================================================================
// DOWNLOAD INVOICE PDF - Use Created Invoice ID
// ===================================================================

if ($createdInvoiceId) {
    try {
        $response = $businessGateway->invoiceService()->downloadInvoicePdf($createdInvoiceId);
        
        if ($response->isSuccessful()) {
            $pdfContent = $response->getRaw();
            echo 'PDF downloaded successfully for: ' . $createdInvoiceId . "\n";
            echo 'PDF size: ' . strlen($pdfContent) . ' bytes' . "\n";
            
            // Save to file if needed
            // file_put_contents('invoice_' . $createdInvoiceId . '.pdf', $pdfContent);
        } else {
            echo 'Error downloading PDF: ' . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo 'Exception downloading PDF: ' . $e->getMessage() . "\n";
    }
}