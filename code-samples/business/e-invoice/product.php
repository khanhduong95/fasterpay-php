<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Product API Examples\n";
echo "=========================================\n\n";

// Example 1: Create product
echo "1. Creating product\n";
echo "-------------------\n";

$createData = [
    'name' => 'Website Development Package',
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'description' => 'Complete website development with modern responsive design.',
    'prices' => [
        [
            'price' => 2500.00,
            'currency' => 'USD'
        ]
    ]
    // 'image' => '/path/to/product-image.jpg' // Uncomment for image upload (file path)
    // 'image' => new SplFileInfo('/path/to/product-image.jpg') // Or use SplFileInfo object
];

try {
    $response = $businessGateway->invoiceProductService()->createProduct($createData);

    if ($response->isSuccessful()) {
        echo "Product created successfully\n";
        $data = $response->getDecodeResponse();
        $productId = $data['data']['id'] ?: 'PD-' . time();
        echo "  Product ID: $productId\n";
        echo "  Name: {$createData['name']}\n";
        echo "  Type: {$createData['type']}\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Get product details
echo "2. Getting product details\n";
echo "--------------------------\n";

try {
    $response = $businessGateway->invoiceProductService()->getProduct($productId);

    if ($response->isSuccessful()) {
        echo "Product details retrieved\n";
        $data = $response->getDecodeResponse();
        $product = $data['data'] ?: [];

        echo "  ID: " . ($product['id'] ?: $productId) . "\n";
        echo "  SKU: " . ($product['sku'] ?: 'N/A') . "\n";
        echo "  Name: " . ($product['name'] ?: 'N/A') . "\n";
        echo "  Type: " . ($product['type'] ?: 'N/A') . "\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Update product
echo "3. Updating product\n";
echo "-------------------\n";

$updateData = [
    'name' => 'Website Development Package - Premium',
    'description' => 'Premium website development with enhanced features.',
    'prices' => [
        [
            'price' => 3500.00,
            'currency' => 'USD'
        ]
    ]
    // 'image' => '/path/to/updated-image.jpg' // Uncomment for image upload (file path)
    // 'image' => new SplFileInfo('/path/to/updated-image.jpg') // Or use SplFileInfo object
];

try {
    $response = $businessGateway->invoiceProductService()->updateProduct($productId, $updateData);

    if ($response->isSuccessful()) {
        echo "Product updated successfully\n";
        echo "  Updated name and pricing\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: List products
echo "4. Listing products\n";
echo "-------------------\n";

try {
    $response = $businessGateway->invoiceProductService()->listProducts(['limit' => 10, 'offset' => 0]);

    if ($response->isSuccessful()) {
        echo "Products retrieved successfully\n";
        $data = $response->getDecodeResponse();
        $products = $data['data']['data'] ?: [];
        echo "  Found " . count($products) . " products\n";

        foreach ($products as $product) {
            $id = $product['id'] ?: 'Unknown';
            $name = $product['name'] ?: 'Unnamed';
            echo "    - $name ($id)\n";
        }
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Delete product price
echo "5. Deleting product price\n";
echo "-------------------------\n";

try {
    $response = $businessGateway->invoiceProductService()->deleteProductPrice($productId, 'USD');

    if ($response->isSuccessful()) {
        echo "Product price deleted successfully\n";
        echo "  Deleted USD price for Product ID: $productId\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Delete product
echo "6. Deleting product\n";
echo "-------------------\n";

try {
    $response = $businessGateway->invoiceProductService()->deleteProduct($productId);

    if ($response->isSuccessful()) {
        echo "Product deleted successfully\n";
        echo "  Deleted Product ID: $productId\n";
    } else {
        echo "Error: " . $response->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nInvoice Product API examples completed!\n";