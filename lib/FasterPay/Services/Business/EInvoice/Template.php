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
     * @param array $params Template parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function createTemplate(array $params = [])
    {
        if (empty($params['name'])) {
            throw new Exception('Template name is required');
        }

        if (empty($params['country_code'])) {
            throw new Exception('Country code is required');
        }

        // Validate country code format
        if (strlen($params['country_code']) !== 2) {
            throw new Exception('Country code must be 2 characters (ISO 3166-1 alpha-2)');
        }

        // Validate colors if provided
        if (!empty($params['colors'])) {
            if (!empty($params['colors']['primary']) && !$this->isValidHexColor($params['colors']['primary'])) {
                throw new Exception('Primary color must be a valid hex color');
            }
            if (!empty($params['colors']['secondary']) && !$this->isValidHexColor($params['colors']['secondary'])) {
                throw new Exception('Secondary color must be a valid hex color');
            }
        }

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
     * @param array $params Updated template parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateTemplate($templateId = '', array $params = [])
    {
        if (empty($templateId)) {
            throw new Exception('Template ID is required');
        }

        // Validate colors if provided
        if (!empty($params['colors'])) {
            if (!empty($params['colors']['primary']) && !$this->isValidHexColor($params['colors']['primary'])) {
                throw new Exception('Primary color must be a valid hex color');
            }
            if (!empty($params['colors']['secondary']) && !$this->isValidHexColor($params['colors']['secondary'])) {
                throw new Exception('Secondary color must be a valid hex color');
            }
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
     * @param array $filters Optional filters
     * @return GeneralResponse
     */
    public function listTemplates(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Validate hex color format
     *
     * @param string $color Hex color string
     * @return bool
     */
    private function isValidHexColor($color)
    {
        return preg_match('/^#[a-f0-9]{6}$/i', $color);
    }
}