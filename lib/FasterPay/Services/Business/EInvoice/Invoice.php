<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

/**
 * FasterPay E-Invoice Service
 *
 * Main component service for handling FasterPay e-invoice operations
 * Follows the same architecture as other business services
 * Uses PHP 5.4+ latest syntax
 */
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
        $response = $this->httpService->getHttpClient()->post($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * Get e-invoice details
     *
     * @param string $invoiceId Invoice ID
     * @param array $params Optional query parameters (include, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function getInvoice($invoiceId = '', array $params = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId);
        $response = $this->httpService->getHttpClient()->get($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * List invoices with optional filters
     *
     * @param array $filters Optional filters (limit, offset, status, etc.)
     * @return GeneralResponse
     */
    public function listInvoices(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);
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
        $response = $this->httpService->getHttpClient()->put($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * Update invoice status
     *
     * @param string $invoiceId Invoice ID
     * @param array $params Status parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateInvoiceStatus($invoiceId = '', array $params = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/status');
        $response = $this->httpService->getHttpClient()->put($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * Preview invoice (get HTML)
     *
     * @param string $invoiceId Invoice ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function previewInvoice($invoiceId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/preview');
        $response = $this->httpService->getHttpClient()->get($endpoint);
        return new GeneralResponse($response);
    }

    /**
     * Download invoice PDF
     *
     * @param string $invoiceId Invoice ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function downloadInvoicePdf($invoiceId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/pdf');
        $response = $this->httpService->getHttpClient()->get($endpoint);
        return new GeneralResponse($response);
    }

    /**
     * Send invoice to customer
     *
     * @param string $invoiceId Invoice ID
     * @param array $params Send parameters (test, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function sendInvoice($invoiceId = '', array $params = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/send');
        $response = $this->httpService->getHttpClient()->post($endpoint, $params);
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
}