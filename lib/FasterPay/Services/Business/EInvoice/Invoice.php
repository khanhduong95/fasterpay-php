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
     * @throws Exception
     */
    public function createInvoice(array $params = [])
    {
        if (empty($params['contact_id'])) {
            throw new Exception('Contact ID is required');
        }

        if (empty($params['currency'])) {
            throw new Exception('Currency is required');
        }

        if (empty($params['items']) || !is_array($params['items'])) {
            throw new Exception('Invoice items are required');
        }

        // Validate currency format
        if (strlen($params['currency']) !== 3) {
            throw new Exception('Currency must be 3 characters (ISO 4217)');
        }

        // Validate items
        foreach ($params['items'] as $item) {
            if (!isset($item['price']) || !is_numeric($item['price'])) {
                throw new Exception('Each item must have a numeric price');
            }
            if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
                throw new Exception('Each item must have a positive quantity');
            }
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

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

        $this->validateInvoiceParams($params, false);

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $params);

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
     * @param array $filters Optional filters (limit, offset, status, date_from, date_to, etc.)
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
     * @param array $sendParams Send parameters (email, method, etc.)
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
     * @param array $options Download options (format, template, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function downloadInvoice($invoiceId = '', array $options = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/download');

        $response = $this->httpService->getHttpClient()->get($endpoint, $options);

        return new GeneralResponse($response);
    }

    /**
     * Get invoice status
     *
     * @param string $invoiceId Invoice ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getInvoiceStatus($invoiceId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/status');

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Cancel e-invoice
     *
     * @param string $invoiceId Invoice ID
     * @param array $cancelParams Cancellation parameters (reason, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function cancelInvoice($invoiceId = '', array $cancelParams = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/cancel');

        $response = $this->httpService->getHttpClient()->post($endpoint, $cancelParams);

        return new GeneralResponse($response);
    }

    /**
     * Mark invoice as paid
     *
     * @param string $invoiceId Invoice ID
     * @param array $paymentData Payment data (amount, date, method, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function markAsPaid($invoiceId = '', array $paymentData = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        if (empty($paymentData['amount'])) {
            throw new Exception('Payment amount is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/paid');

        $response = $this->httpService->getHttpClient()->post($endpoint, $paymentData);

        return new GeneralResponse($response);
    }

    /**
     * Add item to invoice
     *
     * @param string $invoiceId Invoice ID
     * @param array $itemData Item data
     * @return GeneralResponse
     * @throws Exception
     */
    public function addInvoiceItem($invoiceId = '', array $itemData = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        $this->validateInvoiceItem($itemData);

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/items');

        $response = $this->httpService->getHttpClient()->post($endpoint, $itemData);

        return new GeneralResponse($response);
    }

    /**
     * Update invoice item
     *
     * @param string $invoiceId Invoice ID
     * @param string $itemId Item ID
     * @param array $itemData Updated item data
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateInvoiceItem($invoiceId = '', $itemId = '', array $itemData = [])
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        if (empty($itemId)) {
            throw new Exception('Item ID is required');
        }

        $this->validateInvoiceItem($itemData, false);

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/items/' . $itemId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $itemData);

        return new GeneralResponse($response);
    }

    /**
     * Remove item from invoice
     *
     * @param string $invoiceId Invoice ID
     * @param string $itemId Item ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function removeInvoiceItem($invoiceId = '', $itemId = '')
    {
        if (empty($invoiceId)) {
            throw new Exception('Invoice ID is required');
        }

        if (empty($itemId)) {
            throw new Exception('Item ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $invoiceId . '/items/' . $itemId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Search invoices
     *
     * @param array $searchParams Search parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function searchInvoices(array $searchParams = [])
    {
        if (empty($searchParams)) {
            throw new Exception('Search parameters are required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/search');

        $response = $this->httpService->getHttpClient()->post($endpoint, $searchParams);

        return new GeneralResponse($response);
    }

    /**
     * Validate invoice parameters
     *
     * @param array $params Invoice parameters
     * @param bool $requireAll Whether all fields are required (for create vs update)
     * @throws Exception
     */
    private function validateInvoiceParams(array $params, $requireAll = true)
    {
        if ($requireAll) {
            if (empty($params['customer_id']) && empty($params['customer'])) {
                throw new Exception('Customer ID or customer details are required');
            }

            if (empty($params['items']) || !is_array($params['items'])) {
                throw new Exception('Invoice items are required');
            }

            if (empty($params['due_date'])) {
                throw new Exception('Due date is required');
            }
        }

        // Validate due date format if provided
        if (!empty($params['due_date'])) {
            $date = \DateTime::createFromFormat('Y-m-d', $params['due_date']);
            if (!$date || $date->format('Y-m-d') !== $params['due_date']) {
                throw new Exception('Invalid due date format. Use YYYY-MM-DD');
            }
        }

        // Validate currency if provided
        if (!empty($params['currency']) && strlen($params['currency']) !== 3) {
            throw new Exception('Currency must be 3 characters (ISO 4217)');
        }

        // Validate status if provided
        if (!empty($params['status'])) {
            $validStatuses = ['draft', 'sent', 'paid', 'cancelled', 'overdue'];
            if (!in_array($params['status'], $validStatuses)) {
                throw new Exception('Invalid status. Must be one of: ' . implode(', ', $validStatuses));
            }
        }

        // Validate items if provided
        if (!empty($params['items']) && is_array($params['items'])) {
            foreach ($params['items'] as $item) {
                $this->validateInvoiceItem($item);
            }
        }
    }

    /**
     * Validate invoice item
     *
     * @param array $item Item data
     * @param bool $requireAll Whether all fields are required
     * @throws Exception
     */
    private function validateInvoiceItem(array $item, $requireAll = true)
    {
        if ($requireAll) {
            if (empty($item['description'])) {
                throw new Exception('Item description is required');
            }

            if (empty($item['quantity']) || !is_numeric($item['quantity'])) {
                throw new Exception('Item quantity is required and must be numeric');
            }

            if (empty($item['unit_price']) || !is_numeric($item['unit_price'])) {
                throw new Exception('Item unit price is required and must be numeric');
            }
        }

        // Validate quantity if provided
        if (!empty($item['quantity']) && (!is_numeric($item['quantity']) || $item['quantity'] <= 0)) {
            throw new Exception('Item quantity must be a positive number');
        }

        // Validate unit price if provided
        if (!empty($item['unit_price']) && (!is_numeric($item['unit_price']) || $item['unit_price'] < 0)) {
            throw new Exception('Item unit price must be a non-negative number');
        }

        // Validate tax rate if provided
        if (!empty($item['tax_rate']) && (!is_numeric($item['tax_rate']) || $item['tax_rate'] < 0 || $item['tax_rate'] > 100)) {
            throw new Exception('Item tax rate must be between 0 and 100');
        }

        // Validate discount if provided
        if (!empty($item['discount']) && (!is_numeric($item['discount']) || $item['discount'] < 0)) {
            throw new Exception('Item discount must be a non-negative number');
        }
    }
}