<?php

namespace FasterPay\Services\Business\EInvoice;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Template extends GeneralService
{
    protected $endpoint = 'api/external/invoices/templates';

    /**
     * Create a new invoice template
     *
     * @param array $params Template parameters (files auto-detected)
     * @return GeneralResponse
     * @throws Exception
     */
    public function createTemplate(array $params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $response = $this->httpService->getHttpClient()->post($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * Get template details
     *
     * @param string $templateId Template ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getTemplate($templateId = '')
    {
        if (empty($templateId)) {
            throw new Exception('Template ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $templateId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update template
     *
     * @param string $templateId Template ID
     * @param array $params Updated template parameters (files auto-detected)
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateTemplate($templateId = '', array $params = [])
    {
        if (empty($templateId)) {
            throw new Exception('Template ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $templateId);
        $response = $this->httpService->getHttpClient()->put($endpoint, $params);
        return new GeneralResponse($response);
    }

    /**
     * Delete template
     *
     * @param string $templateId Template ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteTemplate($templateId = '')
    {
        if (empty($templateId)) {
            throw new Exception('Template ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $templateId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List templates
     *
     * @param array $filters Optional filters (page, per_page, filter)
     * @return GeneralResponse
     */
    public function listTemplates(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);
        return new GeneralResponse($response);
    }
}