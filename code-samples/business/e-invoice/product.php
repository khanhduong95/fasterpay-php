<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
]);

echo "FasterPay Invoice Product API Examples\n";
echo "=======================================\n\n";

// Example 1: Create a new product
echo "1. Creating a new product\n";
echo "-------------------------\n";

$productData = [
    'name' => 'Professional Website Package',
    'sku' => 'WEB-PRO-001',
    'type' => 'digital',
    'description' => 'Complete professional website package with responsive design and SEO optimization',
    'prices' => [
        [
            'price' => 2500.00,
            'currency' => 'USD'
        ],
        [
            'price' => 2100.00,
            'currency' => 'EUR'
        ]
    ]
];

try {
    $productResponse = $businessGateway->invoiceProductService()->createProduct($productData);

    if ($productResponse->isSuccessful()) {
        echo "Product created successfully\n";
        $responseData = $productResponse->getDecodeResponse();
        $productId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-' . time();
        echo "  Product ID: " . $productId . "\n";
        echo "  SKU: " . $productData['sku'] . "\n";
        echo "  Name: " . $productData['name'] . "\n";
        echo "  Type: " . $productData['type'] . "\n";
        echo "  Prices: " . count($productData['prices']) . " currencies\n";
    } else {
        echo "Error: " . $productResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create product with image
echo "2. Creating product with image\n";
echo "------------------------------\n";

$productWithImageData = [
    'name' => 'Premium Software License',
    'sku' => 'SW-PREM-001',
    'type' => 'digital',
    'description' => 'Premium software license with full features and 1-year support',
    'prices' => [
        [
            'price' => 299.99,
            'currency' => 'USD'
        ]
    ],
    'image' => '/path/to/product-image.jpg' // File path provided directly in params
];

try {
    // Note: In real implementation, provide actual image file path in params['image']
    // The service automatically detects file fields and uses multipart upload

    // For demo purposes, create without image
    $demoData = $productWithImageData;
    unset($demoData['image']); // Remove for demo
    $imageResponse = $businessGateway->invoiceProductService()->createProduct($demoData);

    if ($imageResponse->isSuccessful()) {
        echo "Product with image created successfully\n";
        $responseData = $imageResponse->getDecodeResponse();
        $imageProductId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-IMG-' . time();
        echo "  Product ID: " . $imageProductId . "\n";
        echo "  Name: " . $productWithImageData['name'] . "\n";
        echo "  Image: File would be uploaded automatically if 'image' field contains file path\n";
        echo "  Usage: \$params['image'] = '/path/to/image.jpg'\n";
    } else {
        echo "Error: " . $imageResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get product details
echo "3. Getting product details\n";
echo "--------------------------\n";

$productId = isset($productId) ? $productId : 'PD-' . time();

try {
    $detailsResponse = $businessGateway->invoiceProductService()->getProduct($productId);

    if ($detailsResponse->isSuccessful()) {
        echo "Product details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $product = isset($details['data']) ? $details['data'] : [];

        echo "  ID: " . (isset($product['id']) ? $product['id'] : $productId) . "\n";
        echo "  Name: " . (isset($product['name']) ? $product['name'] : 'N/A') . "\n";
        echo "  SKU: " . (isset($product['sku']) ? $product['sku'] : 'N/A') . "\n";
        echo "  Type: " . (isset($product['type']) ? $product['type'] : 'N/A') . "\n";
        echo "  Description: " . substr(isset($product['description']) ? $product['description'] : 'N/A', 0, 50) . "...\n";

        if (isset($product['prices']) && is_array($product['prices'])) {
            echo "  Prices:\n";
            foreach ($product['prices'] as $price) {
                echo "    - " . (isset($price['currency']) ? $price['currency'] : 'N/A') . ": " . (isset($price['price']) ? $price['price'] : '0.00') . "\n";
            }
        }
    } else {
        echo "Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Update product
echo "4. Updating product\n";
echo "-------------------\n";

$updateData = [
    'name' => 'Updated Professional Website Package',
    'description' => 'Enhanced professional website package with advanced SEO optimization and mobile app integration',
    'prices' => [
        [
            'price' => 2750.00,
            'currency' => 'USD'
        ],
        [
            'price' => 2300.00,
            'currency' => 'EUR'
        ]
    ]
];

try {
    $updateResponse = $businessGateway->invoiceProductService()->updateProduct($productId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "Product updated successfully\n";
        echo "  Updated name and description\n";
        echo "  Updated prices for USD and EUR\n";
    } else {
        echo "Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: List all products
echo "5. Listing all products\n";
echo "-----------------------\n";

$listFilters = [
    'page' => 1,
    'per_page' => 10
];

try {
    $listResponse = $businessGateway->invoiceProductService()->listProducts($listFilters);

    if ($listResponse->isSuccessful()) {
        echo "Products list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $products = isset($listData['data']['data']) ? $listData['data']['data'] : [];

        echo "  Found " . count($products) . " products\n";
        foreach ($products as $product) {
            $id = isset($product['id']) ? $product['id'] : 'Unknown';
            $name = isset($product['name']) ? $product['name'] : 'Unnamed';
            $sku = isset($product['sku']) ? $product['sku'] : 'No SKU';
            $type = isset($product['type']) ? $product['type'] : 'unknown';
            echo "    - $name ($sku) - Type: $type - ID: $id\n";
        }
    } else {
        echo "Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Create multiple products (bulk operation)
echo "6. Creating multiple products (bulk operation)\n";
echo "----------------------------------------------\n";

$bulkProducts = [
    [
        'name' => 'Basic Hosting Plan',
        'sku' => 'HOST-BASIC-001',
        'type' => 'digital',
        'description' => 'Basic web hosting plan with 10GB storage',
        'prices' => [['price' => 9.99, 'currency' => 'USD']]
    ],
    [
        'name' => 'Premium Hosting Plan',
        'sku' => 'HOST-PREM-001',
        'type' => 'digital',
        'description' => 'Premium web hosting plan with 100GB storage and SSL',
        'prices' => [['price' => 29.99, 'currency' => 'USD']]
    ],
    [
        'name' => 'Physical Marketing Kit',
        'sku' => 'MKT-KIT-001',
        'type' => 'physical',
        'description' => 'Complete marketing kit with brochures and business cards',
        'prices' => [['price' => 149.99, 'currency' => 'USD']]
    ]
];

foreach ($bulkProducts as $productData) {
    try {
        $response = $businessGateway->invoiceProductService()->createProduct($productData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $id = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-BULK-' . time();
            $price = $productData['prices'][0]['price'];
            $currency = $productData['prices'][0]['currency'];
            echo "  Created: {$productData['name']} (SKU: {$productData['sku']}, Price: $price $currency)\n";
        } else {
            echo "  Error creating {$productData['name']}: " . $response->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  Exception creating {$productData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Example 7: Delete price from product
echo "7. Deleting price from product\n";
echo "------------------------------\n";

$productIdForPriceDeletion = isset($productId) ? $productId : 'PD-' . time();
$currencyToDelete = 'EUR';

try {
    $deletePriceResponse = $businessGateway->invoiceProductService()->deleteProductPrice($productIdForPriceDeletion, $currencyToDelete);

    if ($deletePriceResponse->isSuccessful()) {
        echo "Price deleted successfully\n";
        echo "  Product ID: " . $productIdForPriceDeletion . "\n";
        echo "  Deleted Currency: " . $currencyToDelete . "\n";
    } else {
        echo "Error: " . $deletePriceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Create service products
echo "8. Creating service products\n";
echo "----------------------------\n";

$serviceProducts = [
    [
        'name' => 'Monthly Consultation Services',
        'sku' => 'CONSULT-MONTHLY',
        'type' => 'digital',
        'description' => 'Professional monthly consultation services including strategy planning and business analysis.',
        'prices' => [
            [
                'price' => 500.00,
                'currency' => 'USD'
            ]
        ]
    ],
    [
        'name' => 'Premium Support Package',
        'sku' => 'SUPPORT-PREMIUM',
        'type' => 'digital',
        'description' => '24/7 premium support with dedicated account manager and priority response.',
        'prices' => [
            [
                'price' => 299.99,
                'currency' => 'USD'
            ],
            [
                'price' => 249.99,
                'currency' => 'EUR'
            ]
        ]
    ]
];

foreach ($serviceProducts as $serviceData) {
    try {
        $serviceResponse = $businessGateway->invoiceProductService()->createProduct($serviceData);

        if ($serviceResponse->isSuccessful()) {
            $responseData = $serviceResponse->getDecodeResponse();
            $serviceId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-SERVICE-' . time();
            $price = $serviceData['prices'][0]['price'];
            $currency = $serviceData['prices'][0]['currency'];

            echo "  Service product created: " . $serviceData['name'] . " (ID: " . $serviceId . ", Price: " . $price . " " . $currency . ")\n";
        } else {
            echo "  Error creating " . $serviceData['name'] . ": " . $serviceResponse->getErrors()->getMessage() . "\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  Exception creating " . $serviceData['name'] . ": " . $e->getMessage() . "\n";
    }
}

echo "\n";

// Example 10: Delete product
echo "10. Deleting product\n";
echo "--------------------\n";

$productToDelete = 'PD-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->invoiceProductService()->deleteProduct($productToDelete);

    if ($deleteResponse->isSuccessful()) {
        echo "Product deleted successfully\n";
        echo "  Deleted Product ID: " . $productToDelete . "\n";
    } else {
        echo "Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nInvoice Product API examples completed!\n";
echo "Use cases:\n";
echo "• Product catalog management\n";
echo "• Multi-currency pricing\n";
echo "• Digital product distribution\n";
echo "• Physical inventory integration\n";
echo "• Service offering management\n";
echo "• SKU-based product lookup\n";
echo "• Image upload and management\n";
echo "• Price management by currency\n";
echo "• Product type categorization\n";
echo "• Bulk product operations\n";

echo "\nMultipart Support:\n";
echo "• Automatic file detection in HttpClient\n";
echo "• Image uploads supported for create and update\n";
echo "• Uses multipart/form-data when image files present\n";
echo "• POST method for create with automatic multipart\n";
echo "• PUT method for update (converts to POST + _method=PUT for multipart)\n";
echo "• Maximum image size: 1MB\n";
echo "• Supported formats: JPG, PNG, GIF\n";

echo "\nProduct Types:\n";
echo "• digital: Software, licenses, digital downloads\n";
echo "• physical: Hardware, merchandise, shipped items\n";

echo "\nValidation Notes:\n";
echo "• Only ID field validation implemented (as requested)\n";
echo "• API handles all parameter validation server-side\n";
echo "• Currency codes must be ISO-4217 format (3 characters)\n";
echo "• Price amounts must be numeric and non-negative\n";
echo "• Product names limited to 191 characters\n";
echo "• SKU limited to 50 characters\n";