<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Discount extends GeneralService
{
    protected $endpoint = 'api/external/invoices/discounts';

    /**
     * Create a new invoice discount
     *
     * @param array $params Discount parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function createDiscount(array $params = [])
    {
        if (empty($params['name'])) {
            throw new Exception('Discount name is required');
        }

        if (empty($params['type'])) {
            throw new Exception('Discount type is required');
        }

        if (!isset($params['value']) || !is_numeric($params['value'])) {
            throw new Exception('Discount value is required and must be numeric');
        }

        // Validate discount type
        $validTypes = ['flat', 'percentage'];
        if (!in_array($params['type'], $validTypes)) {
            throw new Exception('Discount type must be either "flat" or "percentage"');
        }

        // Validate value based on type
        if ($params['type'] === 'percentage' && ($params['value'] < 0 || $params['value'] > 100)) {
            throw new Exception('Percentage discount value must be between 0 and 100');
        }

        if ($params['type'] === 'flat' && $params['value'] < 0) {
            throw new Exception('Flat discount value must be non-negative');
        }

        // Validate currency for flat discounts
        if ($params['type'] === 'flat' && empty($params['currency'])) {
            throw new Exception('Currency is required for flat discount type');
        }

        if (!empty($params['currency']) && strlen($params['currency']) !== 3) {
            throw new Exception('Currency must be 3 characters (ISO 4217)');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get discount details
     *
     * @param string $discountId Discount ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getDiscount($discountId = '')
    {
        if (empty($discountId)) {
            throw new Exception('Discount ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $discountId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update discount
     *
     * @param string $discountId Discount ID
     * @param array $params Updated discount parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateDiscount($discountId = '', array $params = [])
    {
        if (empty($discountId)) {
            throw new Exception('Discount ID is required');
        }

        // Validate discount type if provided
        if (!empty($params['type'])) {
            $validTypes = ['flat', 'percentage'];
            if (!in_array($params['type'], $validTypes)) {
                throw new Exception('Discount type must be either "flat" or "percentage"');
            }
        }

        // Validate value if provided
        if (isset($params['value'])) {
            if (!is_numeric($params['value'])) {
                throw new Exception('Discount value must be numeric');
            }

            if (!empty($params['type'])) {
                if ($params['type'] === 'percentage' && ($params['value'] < 0 || $params['value'] > 100)) {
                    throw new Exception('Percentage discount value must be between 0 and 100');
                }

                if ($params['type'] === 'flat' && $params['value'] < 0) {
                    throw new Exception('Flat discount value must be non-negative');
                }
            }
        }

        // Validate currency if provided
        if (!empty($params['currency']) && strlen($params['currency']) !== 3) {
            throw new Exception('Currency must be 3 characters (ISO 4217)');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $discountId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Delete discount
     *
     * @param string $discountId Discount ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteDiscount($discountId = '')
    {
        if (empty($discountId)) {
            throw new Exception('Discount ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $discountId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List discounts
     *
     * @param array $filters Optional filters
     * @return GeneralResponse
     */
    public function listDiscounts(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }
}