<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Product extends GeneralService
{
    protected $endpoint = 'api/external/invoices/products';

    /**
     * Create a new product
     * Supports multipart/form-data for image uploads
     *
     * @param array $params Product parameters
     * @return GeneralResponse
     */
    public function createProduct(array $params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        // Use POST method - HttpClient will auto-detect files and use multipart if needed
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
     * Supports multipart/form-data for image uploads
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

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $productId);

        // Use PUT method - HttpClient will auto-detect files and use POST + _method=PUT if needed
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
     * List products with optional filters
     *
     * @param array $filters Optional filters (limit, offset, sku, type, etc.)
     * @return GeneralResponse
     */
    public function listProducts(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Delete product price by currency
     *
     * @param string $productId Product ID
     * @param string $currency Currency code (ISO-4217 format)
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteProductPrice($productId = '', $currency = '')
    {
        if (empty($productId)) {
            throw new Exception('Product ID is required');
        }

        if (empty($currency)) {
            throw new Exception('Currency is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $productId . '/prices/' . $currency);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }
}