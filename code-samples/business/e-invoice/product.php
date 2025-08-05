<?php

require_once('../../../lib/autoload.php');

$businessGateway = new FasterPay\BusinessGateway([
    'publicKey' => '<your-public-key>',
    'privateKey' => '<your-private-key>',
    'isTest' => 1,
]);

echo "FasterPay E-Invoice Product API Examples\n";
echo "=========================================\n\n";

// Example 1: Create a digital product with multiple currencies
echo "1. Creating a digital product with multiple currencies\n";
echo "------------------------------------------------------\n";

$digitalProductData = [
    'name' => 'Website Development Package',
    'sku' => 'WEB-DEV-001',
    'type' => 'digital',
    'description' => 'Complete website development with modern responsive design, SEO optimization, and content management system.',
    'prices' => [
        [
            'price' => 2500.00,
            'currency' => 'USD'
        ],
        [
            'price' => 2100.00,
            'currency' => 'EUR'
        ],
        [
            'price' => 1850.00,
            'currency' => 'GBP'
        ]
    ]
];

try {
    $digitalProductResponse = $businessGateway->eInvoiceProductService()->createProduct($digitalProductData);

    if ($digitalProductResponse->isSuccessful()) {
        echo "Digital product created successfully\n";
        $responseData = $digitalProductResponse->getDecodeResponse();
        $digitalProductId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-DIGITAL-' . time();
        echo "  Product ID: " . $digitalProductId . "\n";
        echo "  SKU: " . $digitalProductData['sku'] . "\n";
        echo "  Name: " . $digitalProductData['name'] . "\n";
        echo "  Type: " . $digitalProductData['type'] . "\n";
        echo "  Prices: USD 2500.00 / EUR 2100.00 / GBP 1850.00\n";
        echo "  Image URL: " . (isset($responseData['data']['image_url']) ? $responseData['data']['image_url'] : 'No image uploaded') . "\n";
    } else {
        echo "Error: " . $digitalProductResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 2: Create a physical product with image upload (multipart)
echo "2. Creating a physical product with image upload\n";
echo "------------------------------------------------\n";

$physicalProductData = [
    'name' => 'Professional Laptop 15 inch',
    'sku' => 'LAPTOP-PRO-15',
    'type' => 'physical',
    'description' => 'High-performance laptop with 16GB RAM, 512GB SSD, and professional graphics card. Perfect for developers and designers.',
    'image' => '/path/to/laptop-image.jpg', // This will trigger multipart upload
    'prices' => [
        [
            'price' => 1899.99,
            'currency' => 'USD'
        ],
        [
            'price' => 1649.99,
            'currency' => 'EUR'
        ]
    ]
];

try {
    $physicalProductResponse = $businessGateway->eInvoiceProductService()->createProduct($physicalProductData);

    if ($physicalProductResponse->isSuccessful()) {
        echo "Physical product created successfully\n";
        $responseData = $physicalProductResponse->getDecodeResponse();
        $physicalProductId = isset($responseData['data']['id']) ? $responseData['data']['id'] : 'PD-PHYSICAL-' . time();
        echo "  Product ID: " . $physicalProductId . "\n";
        echo "  SKU: " . $physicalProductData['sku'] . "\n";
        echo "  Name: " . $physicalProductData['name'] . "\n";
        echo "  Type: " . $physicalProductData['type'] . "\n";
        echo "  Prices: USD 1899.99 / EUR 1649.99\n";
        echo "  Image URL: " . (isset($responseData['data']['image_url']) ? $responseData['data']['image_url'] : 'No image uploaded') . "\n";
    } else {
        echo "Error: " . $physicalProductResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 3: Get product details
echo "3. Getting product details\n";
echo "--------------------------\n";

$productIdToGet = isset($digitalProductId) ? $digitalProductId : 'PD-250528-L5CC';

try {
    $productDetailsResponse = $businessGateway->eInvoiceProductService()->getProduct($productIdToGet);

    if ($productDetailsResponse->isSuccessful()) {
        echo "Product details retrieved successfully\n";
        $productData = $productDetailsResponse->getDecodeResponse();
        
        if (isset($productData['data'])) {
            $product = $productData['data'];
            echo "  Product ID: " . (isset($product['id']) ? $product['id'] : 'N/A') . "\n";
            echo "  SKU: " . (isset($product['sku']) ? $product['sku'] : 'N/A') . "\n";
            echo "  Name: " . (isset($product['name']) ? $product['name'] : 'N/A') . "\n";
            echo "  Type: " . (isset($product['type']) ? $product['type'] : 'N/A') . "\n";
            echo "  Description: " . (isset($product['description']) ? substr($product['description'], 0, 100) . '...' : 'N/A') . "\n";
            
            if (isset($product['prices']) && is_array($product['prices'])) {
                echo "  Prices:\n";
                foreach ($product['prices'] as $price) {
                    $priceAmount = isset($price['price']) ? $price['price'] : '0.00';
                    $currency = isset($price['currency']) ? $price['currency'] : 'N/A';
                    echo "    - " . $priceAmount . " " . $currency . "\n";
                }
            }
        }
    } else {
        echo "Error: " . $productDetailsResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 4: Update product with image upload (multipart)
echo "4. Updating product with image upload\n";
echo "-------------------------------------\n";

$productIdToUpdate = isset($physicalProductId) ? $physicalProductId : 'PD-UPDATE-' . time();

$updateData = [
    'name' => 'Professional Laptop 15 inch - Updated Edition',
    'description' => 'Updated high-performance laptop with enhanced features: 32GB RAM, 1TB SSD, and latest graphics card.',
    'image' => '/path/to/updated-laptop-image.jpg', // This will trigger multipart upload
    'prices' => [
        [
            'price' => 2199.99,
            'currency' => 'USD'
        ],
        [
            'price' => 1899.99,
            'currency' => 'EUR'
        ],
        [
            'price' => 1699.99,
            'currency' => 'GBP'
        ]
    ]
];

try {
    $updateResponse = $businessGateway->eInvoiceProductService()->updateProduct($productIdToUpdate, $updateData);

    if ($updateResponse->isSuccessful()) {
        echo "Product updated successfully\n";
        $responseData = $updateResponse->getDecodeResponse();
        echo "  Product ID: " . $productIdToUpdate . "\n";
        echo "  Updated Name: " . $updateData['name'] . "\n";
        echo "  Updated Prices: USD 2199.99 / EUR 1899.99 / GBP 1699.99\n";
        echo "  Image URL: " . (isset($responseData['data']['image_url']) ? $responseData['data']['image_url'] : 'No image uploaded') . "\n";
    } else {
        echo "Error: " . $updateResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 5: List products with filters
echo "5. Listing products with filters\n";
echo "--------------------------------\n";

$filters = [
    'limit' => 20,
    'offset' => 0,
    'type' => 'digital'
];

try {
    $listResponse = $businessGateway->eInvoiceProductService()->listProducts($filters);

    if ($listResponse->isSuccessful()) {
        echo "Product list retrieved successfully\n";
        $listData = $listResponse->getDecodeResponse();
        
        if (isset($listData['data']) && is_array($listData['data'])) {
            echo "  Found " . count($listData['data']) . " digital products\n";
            
            // Display first few products
            $products = array_slice($listData['data'], 0, 3);
            foreach ($products as $product) {
                $id = isset($product['id']) ? $product['id'] : 'Unknown';
                $name = isset($product['name']) ? $product['name'] : 'Unnamed';
                $sku = isset($product['sku']) ? $product['sku'] : 'No SKU';
                $type = isset($product['type']) ? $product['type'] : 'Unknown';
                
                echo "    - " . $name . " (" . $id . ", SKU: " . $sku . ", Type: " . $type . ")\n";
            }
            
            if (count($listData['data']) > 3) {
                echo "    ... and " . (count($listData['data']) - 3) . " more\n";
            }
        }
    } else {
        echo "Error: " . $listResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 6: Delete product price
echo "6. Deleting product price\n";
echo "-------------------------\n";

$productIdForPriceDeletion = isset($digitalProductId) ? $digitalProductId : 'PD-PRICE-DELETE-' . time();
$currencyToDelete = 'GBP';

try {
    $deletePriceResponse = $businessGateway->eInvoiceProductService()->deleteProductPrice($productIdForPriceDeletion, $currencyToDelete);

    if ($deletePriceResponse->isSuccessful()) {
        echo "Product price deleted successfully\n";
        echo "  Product ID: " . $productIdForPriceDeletion . "\n";
        echo "  Deleted Currency: " . $currencyToDelete . "\n";
    } else {
        echo "Error: " . $deletePriceResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n";

// Example 7: Create service products
echo "7. Creating service products\n";
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
        $serviceResponse = $businessGateway->eInvoiceProductService()->createProduct($serviceData);

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

// Example 8: Delete product
echo "8. Deleting product\n";
echo "-------------------\n";

$productToDelete = 'PD-DELETE-' . time();

try {
    $deleteResponse = $businessGateway->eInvoiceProductService()->deleteProduct($productToDelete);

    if ($deleteResponse->isSuccessful()) {
        echo "Product deleted successfully\n";
        echo "  Deleted Product ID: " . $productToDelete . "\n";
    } else {
        echo "Error: " . $deleteResponse->getErrors()->getMessage() . "\n";
    }
} catch (FasterPay\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\nE-Invoice Product API examples completed!\n";
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