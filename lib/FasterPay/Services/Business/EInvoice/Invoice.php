<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Invoice extends GeneralService
{
    protected $endpoint = 'api/external/invoices';

    /**
     * Create a new e-invoice
     *
     * @param array $params E-invoice parameters
     * @return GeneralResponse
     */
    public function createInvoice(array $params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        // For multipart/form-data requests
        $response = $this->httpService->getHttpClient()->postMultipart($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get e-invoice details
     *
     * @param string $invoiceId Invoice ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getInvoice($invoiceId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update e-invoice
     *
     * @param string $invoiceId Invoice ID
     * @param array $params Updated invoice parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateInvoice($invoiceId = '', array $params = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId);

        // Add _method=PUT for update via POST
        $params['_method'] = 'PUT';

        // For multipart/form-data requests
        $response = $this->httpService->getHttpClient()->postMultipart($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Delete e-invoice
     *
     * @param string $invoiceId Invoice ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteInvoice($invoiceId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List e-invoices
     *
     * @param array $filters Optional filters
     * @return GeneralResponse
     */
    public function listInvoices(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Send e-invoice
     *
     * @param string $invoiceId Invoice ID
     * @param array $sendParams Send parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function sendInvoice($invoiceId = '', array $sendParams = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/send');

        $response = $this->httpService->getHttpClient()->post($endpoint, $sendParams);

        return new GeneralResponse($response);
    }

    /**
     * Download e-invoice PDF
     *
     * @param string $invoiceId Invoice ID
     * @param array $options Download options
     * @return array Raw file response array with 'response' and 'httpCode'
     * @throws Exception
     */
    public function downloadInvoicePdf($invoiceId = '', array $options = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/pdf');

        // Return raw response for file download
        return $this->httpService->getHttpClient()->get($endpoint, $options);
    }
}