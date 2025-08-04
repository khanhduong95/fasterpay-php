<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Tax extends GeneralService
{
    protected $endpoint = 'api/external/invoices/taxes';

    /**
     * Create a new invoice tax
     *
     * @param array $params Tax parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function createTax(array $params = [])
    {
        if (empty($params['name'])) {
            throw new Exception('Tax name is required');
        }

        if (empty($params['type'])) {
            throw new Exception('Tax type is required');
        }

        if (!isset($params['value']) || !is_numeric($params['value'])) {
            throw new Exception('Tax value is required and must be numeric');
        }

        // Validate tax type
        $validTypes = ['flat', 'percentage'];
        if (!in_array($params['type'], $validTypes)) {
            throw new Exception('Tax type must be either "flat" or "percentage"');
        }

        // Validate value based on type
        if ($params['type'] === 'percentage' && ($params['value'] < 0 || $params['value'] > 100)) {
            throw new Exception('Percentage tax value must be between 0 and 100');
        }

        if ($params['type'] === 'flat' && $params['value'] < 0) {
            throw new Exception('Flat tax value must be non-negative');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get tax details
     *
     * @param string $taxId Tax ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getTax($taxId = '')
    {
        if (empty($taxId)) {
            throw new Exception('Tax ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $taxId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update tax
     *
     * @param string $taxId Tax ID
     * @param array $params Updated tax parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateTax($taxId = '', array $params = [])
    {
        if (empty($taxId)) {
            throw new Exception('Tax ID is required');
        }

        // Validate tax type if provided
        if (!empty($params['type'])) {
            $validTypes = ['flat', 'percentage'];
            if (!in_array($params['type'], $validTypes)) {
                throw new Exception('Tax type must be either "flat" or "percentage"');
            }
        }

        // Validate value if provided
        if (isset($params['value'])) {
            if (!is_numeric($params['value'])) {
                throw new Exception('Tax value must be numeric');
            }

            if (!empty($params['type'])) {
                if ($params['type'] === 'percentage' && ($params['value'] < 0 || $params['value'] > 100)) {
                    throw new Exception('Percentage tax value must be between 0 and 100');
                }

                if ($params['type'] === 'flat' && $params['value'] < 0) {
                    throw new Exception('Flat tax value must be non-negative');
                }
            }
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $taxId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Delete tax
     *
     * @param string $taxId Tax ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteTax($taxId = '')
    {
        if (empty($taxId)) {
            throw new Exception('Tax ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $taxId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List taxes
     *
     * @param array $filters Optional filters
     * @return GeneralResponse
     */
    public function listTaxes(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }
}