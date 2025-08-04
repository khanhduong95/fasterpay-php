<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Product API Examples\n";
echo "=========================================\n\n";

// Example 1: Create a digital product
echo "1. Creating a digital product\n";
echo "-----------------------------\n";

$digitalProductData = [
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'name' => 'Website Development Package',
    'description' => 'Complete website development with modern responsive design, SEO optimization, and content management system.',
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
    $productResponse = $businessGateway->eInvoiceProductService()->createProduct($digitalProductData);

    if ($productResponse->isSuccessful()) {
        echo "✓ Digital product created successfully\n";
        $responseData = $productResponse->getDecodeResponse();
        $productId = $responseData['data']['id'] ?? 'PD-' . time();
        echo "  Product ID: $productId\n";
        echo "  SKU: {$digitalProductData['sku']}\n";
        echo "  Name: {$digitalProductData['name']}\n";
        echo "  Type: {$digitalProductData['type']}\n";
        echo "  Prices: \$2,500 USD / €2,100 EUR\n";
        echo "  Image URL: " . ($responseData['data']['image_url'] ?? 'No image uploaded') . "\n";
    } else {
        echo "✗ Error: " . $productResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a physical product
echo "2. Creating a physical product\n";
echo "------------------------------\n";

$physicalProductData = [
    'sku' => 'LAPTOP-PRO-15',
    'type' => 'physical',
    'name' => 'Professional Laptop 15"',
    'description' => 'High-performance laptop with 16GB RAM, 512GB SSD, and professional graphics card. Perfect for developers and designers.',
    'prices' => [
        [
            'price' => 1899.99,
            'currency' => 'USD'
        ],
        [
            'price' => 1649.99,
            'currency' => 'EUR'
        ],
        [
            'price' => 1499.99,
            'currency' => 'GBP'
        ]
    ]
];

try {
    $laptopResponse = $businessGateway->eInvoiceProductService()->createProduct($physicalProductData);

    if ($laptopResponse->isSuccessful()) {
        echo "✓ Physical product created successfully\n";
        $responseData = $laptopResponse->getDecodeResponse();
        $laptopId = $responseData['data']['id'] ?? 'PD-LAPTOP-' . time();
        echo "  Product ID: $laptopId\n";
        echo "  SKU: {$physicalProductData['sku']}\n";
        echo "  Name: {$physicalProductData['name']}\n";
        echo "  Type: {$physicalProductData['type']}\n";
        echo "  Multi-currency pricing: USD/EUR/GBP\n";
    } else {
        echo "✗ Error: " . $laptopResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Create a service product
echo "3. Creating a service product\n";
echo "-----------------------------\n";

$serviceProductData = [
    'sku' => 'CONSULT-HOUR',
    'type' => 'service',
    'name' => 'Technical Consulting Hour',
    'description' => 'One hour of expert technical consulting for software architecture, system design, and performance optimization.',
    'prices' => [
        [
            'price' => 150.00,
            'currency' => 'USD'
        ]
    ]
];

try {
    $consultingResponse = $businessGateway->eInvoiceProductService()->createProduct($serviceProductData);

    if ($consultingResponse->isSuccessful()) {
        echo "✓ Service product created successfully\n";
        $responseData = $consultingResponse->getDecodeResponse();
        $consultingId = $responseData['data']['id'] ?? 'PD-CONSULT-' . time();
        echo "  Product ID: $consultingId\n";
        echo "  SKU: {$serviceProductData['sku']}\n";
        echo "  Name: {$serviceProductData['name']}\n";
        echo "  Type: {$serviceProductData['type']}\n";
        echo "  Rate: \$150/hour\n";
    } else {
        echo "✗ Error: " . $consultingResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Get product details
echo "4. Getting product details\n";
echo "--------------------------\n";

$productId = $productId ?? 'PD-' . time();

try {
    $detailsResponse = $businessGateway->eInvoiceProductService()->getProduct($productId);

    if ($detailsResponse->isSuccessful()) {
        echo "✓ Product details retrieved\n";
        $details = $detailsResponse->getDecodeResponse();
        $product = $details['data'] ?? [];

        echo "  ID: " . ($product['id'] ?? $productId) . "\n";
        echo "  SKU: " . ($product['sku'] ?? 'N/A') . "\n";
        echo "  Name: " . ($product['name'] ?? 'N/A') . "\n";
        echo "  Type: " . ($product['type'] ?? 'N/A') . "\n";
        echo "  Description: " . (substr($product['description'] ?? '', 0, 50) . '...') . "\n";

        $prices = $product['prices'] ?? [];
        if (!empty($prices)) {
            echo "  Prices: ";
            foreach ($prices as $price) {
                echo $price['currency'] . ' ' . $price['price'] . ' ';
            }
            echo "\n";
        }
    } else {
        echo "✗ Error: " . $detailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: Update product
echo "5. Updating product\n";
echo "-------------------\n";

$updateData = [
    'description' => 'Updated: Premium website development package with advanced SEO, e-commerce integration, and 6 months support.',
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
    $updateResponse = $businessGateway->eInvoiceProductService()->updateProduct($productId, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "✓ Product updated successfully\n";
        echo "  Updated pricing: \$2,750 USD / €2,300 EUR\n";
        echo "  Updated description with enhanced features\n";
    } else {
        echo "✗ Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: List all products
echo "6. Listing all products\n";
echo "-----------------------\n";

try {
    $listResponse = $businessGateway->eInvoiceProductService()->listProducts();

    if ($listResponse->isSuccessful()) {
        echo "✓ Product list retrieved\n";
        $listData = $listResponse->getDecodeResponse();
        $products = $listData['data']['data'] ?? [];

        echo "  Total products: " . count($products) . "\n";

        if (!empty($products)) {
            echo "  Product catalog:\n";
            foreach ($products as $product) {
                $id = $product['id'] ?? 'Unknown';
                $sku = $product['sku'] ?? 'N/A';
                $name = $product['name'] ?? 'Unnamed';
                $type = $product['type'] ?? 'N/A';

                echo "    - $name (SKU: $sku, Type: $type, ID: $id)\n";
            }
        }
    } else {
        echo "✗ Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Search product by SKU
echo "7. Searching product by SKU\n";
echo "----------------------------\n";

try {
    $skuResponse = $businessGateway->eInvoiceProductService()->getProductBySku('WEB-DEV-001');

    if ($skuResponse->isSuccessful()) {
        echo "✓ Product found by SKU\n";
        $skuData = $skuResponse->getDecodeResponse();
        $products = $skuData['data']['data'] ?? [];

        if (!empty($products)) {
            $product = $products[0];
            echo "  Found: " . ($product['name'] ?? 'Unknown') . "\n";
            echo "  Type: " . ($product['type'] ?? 'N/A') . "\n";
        }
    } else {
        echo "✗ Error: " . $skuResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "✗ Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 8: Create a product bundle
echo "8. Creating product bundles\n";
echo "---------------------------\n";

$bundleProducts = [
    [
        'sku' => 'STARTUP-BUNDLE',
        'type' => 'digital',
        'name' => 'Startup Launch Bundle',
        'description' => 'Everything you need to launch your startup: website, logo design, business cards, and marketing materials.',
        'prices' => [
            [
                'price' => 3999.00,
                'currency' => 'USD'
            ]
        ]
    ],
    [
        'sku' => 'ENTERPRISE-PACKAGE',
        'type' => 'service',
        'name' => 'Enterprise Solution Package',
        'description' => 'Complete enterprise solution with custom development, integration, training, and 1-year support.',
        'prices' => [
            [
                'price' => 25000.00,
                'currency' => 'USD'
            ]
        ]
    ]
];

echo "Creating product bundles:\n";

foreach ($bundleProducts as $bundleData) {
    try {
        $response = $businessGateway->eInvoiceProductService()->createProduct($bundleData);

        if ($response->isSuccessful()) {
            $responseData = $response->getDecodeResponse();
            $bundleId = $responseData['data']['id'] ?? 'PD-BUNDLE-' . time();
            $price = $bundleData['prices'][0]['price'];
            $currency = $bundleData['prices'][0]['currency'];

            echo "  ✓ {$bundleData['name']}: $" . number_format($price, 2) . " $currency (ID: $bundleId)\n";
        } else {
            echo "  ✗ Failed to create {$bundleData['name']}\n";
        }
    } catch (FasterPay\Exception $e) {
        echo "  ✗ Exception creating {$bundleData['name']}: " . $e->getMessage() . "\n";
    }
}

echo "\nE-Invoice Product API examples completed!\n";
echo "Use cases:\n";
echo "• Product catalog management\n";
echo "• Multi-currency pricing\n";
echo "• Service offering management\n";
echo "• Digital product distribution\n";
echo "• Physical inventory integration\n";
echo "• Bundle and package creation\n";
echo "• SKU-based product lookup\n";
echo "• Dynamic pricing updates\n";