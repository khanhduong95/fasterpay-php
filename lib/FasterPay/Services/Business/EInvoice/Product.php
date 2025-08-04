<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Product extends GeneralService
{
    protected $endpoint = 'api/external/invoices/products';

    /**
     * Create a new invoice product
     *
     * @param array $params Product parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function createProduct(array $params = [])
    {
        if (empty($params['name'])) {
            throw new Exception('Product name is required');
        }

        if (empty($params['sku'])) {
            throw new Exception('Product SKU is required');
        }

        if (empty($params['type'])) {
            throw new Exception('Product type is required');
        }

        // Validate product type
        $validTypes = ['digital', 'physical', 'service'];
        if (!in_array($params['type'], $validTypes)) {
            throw new Exception('Product type must be one of: ' . implode(', ', $validTypes));
        }

        // Validate prices if provided
        if (!empty($params['prices']) && is_array($params['prices'])) {
            foreach ($params['prices'] as $price) {
                if (!isset($price['price']) || !is_numeric($price['price'])) {
                    throw new Exception('Each price must have a numeric price value');
                }
                if (empty($price['currency']) || strlen($price['currency']) !== 3) {
                    throw new Exception('Each price must have a valid 3-character currency code');
                }
                if ($price['price'] < 0) {
                    throw new Exception('Price must be non-negative');
                }
            }
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get product details
     *
     * @param string $productId Product ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getProduct($productId = '')
    {
        if (empty($productId)) {
            throw new Exception('Product ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $productId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update product
     *
     * @param string $productId Product ID
     * @param array $params Updated product parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateProduct($productId = '', array $params = [])
    {
        if (empty($productId)) {
            throw new Exception('Product ID is required');
        }

        // Validate product type if provided
        if (!empty($params['type'])) {
            $validTypes = ['digital', 'physical', 'service'];
            if (!in_array($params['type'], $validTypes)) {
                throw new Exception('Product type must be one of: ' . implode(', ', $validTypes));
            }
        }

        // Validate prices if provided
        if (!empty($params['prices']) && is_array($params['prices'])) {
            foreach ($params['prices'] as $price) {
                if (!isset($price['price']) || !is_numeric($price['price'])) {
                    throw new Exception('Each price must have a numeric price value');
                }
                if (empty($price['currency']) || strlen($price['currency']) !== 3) {
                    throw new Exception('Each price must have a valid 3-character currency code');
                }
                if ($price['price'] < 0) {
                    throw new Exception('Price must be non-negative');
                }
            }
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $productId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Delete product
     *
     * @param string $productId Product ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteProduct($productId = '')
    {
        if (empty($productId)) {
            throw new Exception('Product ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $productId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List products
     *
     * @param array $filters Optional filters
     * @return GeneralResponse
     */
    public function listProducts(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Search products by SKU
     *
     * @param string $sku Product SKU
     * @return GeneralResponse
     * @throws Exception
     */
    public function getProductBySku($sku = '')
    {
        if (empty($sku)) {
            throw new Exception('Product SKU is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $params = ['sku' => $sku];

        $response = $this->httpService->getHttpClient()->get($endpoint, $params);

        return new GeneralResponse($response);
    }
}