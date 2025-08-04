<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice API Examples\n";
echo "=================================\n\n";

// Example 1: Create a new e-invoice
echo "1. Creating a new e-invoice\n";
echo "---------------------------\n";

$invoiceData = [
    'invoice_number' => 'INV-' . date('Y') . '-' . sprintf('%06d', rand(1, 999999)),
    'issue_date' => date('Y-m-d'),
    'due_date' => date('Y-m-d', strtotime('+30 days')),
    'currency' => 'USD',
    'status' => 'draft',
    'customer' => [
        'id' => 'customer_123',
        'name' => 'Acme Corporation',
        'email' => 'billing@acme.com',
        'phone' => '+1-555-123-4567',
        'tax_id' => '12-3456789',
        'billing_address' => [
            'street' => '123 Business Blvd',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country_code' => 'US'
        ]
    ],
    'items' => [
        [
            'description' => 'Website Development Services',
            'quantity' => 1,
            'unit_price' => 5000.00,
            'tax_rate' => 8.25,
            'category' => 'services'
        ],
        [
            'description' => 'Monthly Hosting Package',
            'quantity' => 12,
            'unit_price' => 99.99,
            'tax_rate' => 8.25,
            'category' => 'services'
        ],
        [
            'description' => 'SSL Certificate',
            'quantity' => 1,
            'unit_price' => 150.00,
            'tax_rate' => 8.25,
            'category' => 'products'
        ]
    ],
    'payment_terms' => 'Net 30',
    'notes' => 'Thank you for your business. Payment is due within 30 days.',
    'template' => 'standard',
    'send_automatically' => false
];

try {
    $invoiceResponse = $businessGateway->eInvoiceService()->createInvoice($invoiceData);

    if ($invoiceResponse->isSuccessful()) {
        echo "✓ E-invoice created successfully\n";
        $responseData = $invoiceResponse->getDecodeResponse();
        $invoiceId = $responseData['invoice_id'] ?? 'inv_' . time();
        echo "  Invoice ID: $invoiceId\n";
        echo "  Invoice Number: {$invoiceData['invoice_number']}\n";
        echo "  Customer: {$invoiceData['customer']['name']}\n";

        // Calculate totals
        $subtotal = 0;
        foreach ($invoiceData['items'] as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $tax = $subtotal * 0.0825; // 8.25% tax
        $total = $subtotal + $tax;

        echo "  Subtotal: $" . number_format($subtotal, 2) . "\n";
        echo "  Tax: $" . number_format($tax, 2) . "\n";
        echo "  Total: $" . number_format($total, 2) . "\n";
    } else {
        echo "✗ Error: " . $invoiceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Mark invoice as paid
echo "5. Marking invoice as paid\n";
echo "--------------------------\n";

$paymentData = [
    'amount' => 6558.87, // Total amount from invoice
    'payment_date' => date('Y-m-d'),
    'payment_method' => 'bank_transfer',
    'reference' => 'TXN-' . time(),
    'notes' => 'Payment received via bank transfer'
];

try {
    $paidResponse = $businessGateway->eInvoiceService()->markAsPaid($invoiceId, $paymentData);

    if ($paidResponse->isSuccessful()) {
        echo "✓ Invoice marked as paid successfully\n";
        echo "  Invoice ID: $invoiceId\n";
        echo "  Amount: $" . number_format($paymentData['amount'], 2) . "\n";
        echo "  Payment Date: {$paymentData['payment_date']}\n";
        echo "  Method: {$paymentData['payment_method']}\n";
        echo "  Reference: {$paymentData['reference']}\n";
    } else {
        echo "✗ Error: " . $paidResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Search invoices
echo "6. Searching invoices\n";
echo "---------------------\n";

$searchParams = [
    'customer_name' => 'Acme',
    'status' => 'paid',
    'amount_min' => 1000,
    'amount_max' => 10000,
    'date_from' => '2024-01-01',
    'date_to' => '2024-12-31',
    'limit' => 20
];

try {
    $searchResponse = $businessGateway->eInvoiceService()->searchInvoices($searchParams);

    if ($searchResponse->isSuccessful()) {
        echo "✓ Invoice search completed\n";
        echo "  Customer: Contains 'Acme'\n";
        echo "  Status: Paid\n";
        echo "  Amount range: $1,000 - $10,000\n";
        echo "  Date range: 2024\n";
        echo "  (In a real scenario, this would return matching invoices)\n";
    } else {
        echo "✗ Error: " . $searchResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: List invoices with filters
echo "7. Listing invoices with filters\n";
echo "--------------------------------\n";

$filters = [
    'status' => 'sent',
    'overdue' => true,
    'currency' => 'USD',
    'limit' => 50,
    'offset' => 0,
    'sort' => 'due_date',
    'order' => 'asc'
];

try {
    $listResponse = $businessGateway->eInvoiceService()->listInvoices($filters);

    if ($listResponse->isSuccessful()) {
        echo "✓ Invoice list retrieved successfully\n";
        echo "  Filter: Overdue sent invoices in USD\n";
        echo "  Sorted by: Due date (ascending)\n";
        echo "  Limit: 50 invoices\n";
        echo "  (In a real scenario, this would show actual invoice data)\n";
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Get invoice status
echo "8. Getting invoice status\n";
echo "-------------------------\n";

try {
    $statusResponse = $businessGateway->eInvoiceService()->getInvoiceStatus($invoiceId);

    if ($statusResponse->isSuccessful()) {
        echo "✓ Invoice status retrieved successfully\n";
        $statusData = $statusResponse->getDecodeResponse();
        echo "  Invoice ID: $invoiceId\n";
        echo "  Status: " . ($statusData['status'] ?? 'paid') . "\n";
        echo "  Last updated: " . ($statusData['updated_at'] ?? date('Y-m-d H:i:s')) . "\n";
        echo "  Payment status: " . ($statusData['payment_status'] ?? 'completed') . "\n";
    } else {
        echo "✗ Error: " . $statusResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 9: Update invoice item
echo "9. Updating invoice item\n";
echo "------------------------\n";

$itemId = 'item_' . time(); // In real scenario, use actual item ID
$itemUpdateData = [
    'description' => 'Premium Domain Registration (Updated)',
    'unit_price' => 39.99,
    'notes' => 'Upgraded to premium domain package'
];

try {
    $itemUpdateResponse = $businessGateway->eInvoiceService()->updateInvoiceItem($invoiceId, $itemId, $itemUpdateData);

    if ($itemUpdateResponse->isSuccessful()) {
        echo "✓ Invoice item updated successfully\n";
        echo "  Invoice ID: $invoiceId\n";
        echo "  Item ID: $itemId\n";
        echo "  New description: {$itemUpdateData['description']}\n";
        echo "  New price: $" . number_format($itemUpdateData['unit_price'], 2) . "\n";
    } else {
        echo "✗ Error: " . $itemUpdateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 10: Create recurring invoice template
echo "10. Creating recurring invoice\n";
echo "------------------------------\n";

$recurringInvoiceData = [
    'template_name' => 'Monthly Hosting Service',
    'invoice_number_pattern' => 'HOST-{YYYY}-{MM}-{####}',
    'recurrence' => 'monthly',
    'start_date' => date('Y-m-d'),
    'end_date' => date('Y-m-d', strtotime('+1 year')),
    'customer' => [
        'id' => 'customer_456',
        'name' => 'Tech Startup LLC',
        'email' => 'accounts@techstartup.com'
    ],
    'items' => [
        [
            'description' => 'Premium Hosting Package',
            'quantity' => 1,
            'unit_price' => 299.99,
            'tax_rate' => 8.25
        ],
        [
            'description' => 'SSL Certificate Renewal',
            'quantity' => 1,
            'unit_price' => 99.99,
            'tax_rate' => 8.25
        ]
    ],
    'auto_send' => true,
    'payment_terms' => 'Net 15'
];

try {
    $recurringResponse = $businessGateway->eInvoiceService()->createInvoice($recurringInvoiceData);

    if ($recurringResponse->isSuccessful()) {
        echo "✓ Recurring invoice template created successfully\n";
        $responseData = $recurringResponse->getDecodeResponse();
        $templateId = $responseData['template_id'] ?? 'template_' . time();
        echo "  Template ID: $templateId\n";
        echo "  Template Name: {$recurringInvoiceData['template_name']}\n";
        echo "  Recurrence: {$recurringInvoiceData['recurrence']}\n";
        echo "  Customer: {$recurringInvoiceData['customer']['name']}\n";
        echo "  Monthly Amount: $" . number_format(399.98 * 1.0825, 2) . " (including tax)\n";
    } else {
        echo "✗ Error: " . $recurringResponse->getErrors()->getMessage() . "\n";
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

echo "\n";

// Example 2: Add item to existing invoice
echo "2. Adding item to invoice\n";
echo "-------------------------\n";

$invoiceId = 'inv_' . time(); // In real scenario, use actual invoice ID
$newItem = [
    'description' => 'Domain Registration',
    'quantity' => 1,
    'unit_price' => 29.99,
    'tax_rate' => 8.25,
    'category' => 'services'
];

try {
    $itemResponse = $businessGateway->eInvoiceService()->addInvoiceItem($invoiceId, $newItem);

    if ($itemResponse->isSuccessful()) {
        echo "✓ Item added to invoice successfully\n";
        echo "  Invoice ID: $invoiceId\n";
        echo "  Item: {$newItem['description']}\n";
        echo "  Price: $" . number_format($newItem['unit_price'], 2) . "\n";
    } else {
        echo "✗ Error: " . $itemResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Send invoice to customer
echo "3. Sending invoice to customer\n";
echo "------------------------------\n";

$sendParams = [
    'method' => 'email',
    'email' => 'billing@acme.com',
    'subject' => 'Invoice ' . ($invoiceData['invoice_number'] ?? 'INV-2024-001234') . ' from Your Company',
    'message' => 'Dear Customer, please find attached your invoice. Payment is due within 30 days.',
    'copy_sender' => true,
    'attach_pdf' => true
];

try {
    $sendResponse = $businessGateway->eInvoiceService()->sendInvoice($invoiceId, $sendParams);

    if ($sendResponse->isSuccessful()) {
        echo "✓ Invoice sent successfully\n";
        echo "  Invoice ID: $invoiceId\n";
        echo "  Sent to: {$sendParams['email']}\n";
        echo "  Method: {$sendParams['method']}\n";
    } else {
        echo "✗ Error: " . $sendResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Download invoice PDF
echo "4. Downloading invoice PDF\n";
echo "--------------------------\n";

$downloadOptions = [
    'format' => 'pdf',
    'template' => 'professional',
    'include_payments' => true
];

try {
    $downloadResponse = $businessGateway->eInvoiceService()->downloadInvoice($invoiceId, $downloadOptions);

    if ($downloadResponse->isSuccessful()) {
        echo "✓ Invoice PDF ready for download\n";
        echo "  Invoice ID: $invoiceId\n";
        echo "  Format: PDF\n";
        echo "  Template: Professional\n";
        echo "  (In a real scenario, this would return the PDF data or download URL)\n";
    } else {
        echo "✗ Error: " . $downloadResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}