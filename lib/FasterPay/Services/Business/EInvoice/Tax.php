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
     */
    public function createTax(array $params = [])
    {
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