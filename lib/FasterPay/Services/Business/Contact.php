<?php

namespace FasterPay\Services\Business;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Contact extends GeneralService
{
    protected $endpoint = 'api/external/contacts';

    public function createContact(array $params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $response = $this->httpService->getHttpClient()->post($endpoint, $params);
        return new GeneralResponse($response);
    }

    public function getContact($contactId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }
        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);
        $response = $this->httpService->getHttpClient()->get($endpoint);
        return new GeneralResponse($response);
    }

    public function updateContact($contactId = '', array $params = [])
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }
        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);
        $response = $this->httpService->getHttpClient()->put($endpoint, $params);
        return new GeneralResponse($response);
    }

    public function deleteContact($contactId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }
        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);
        $response = $this->httpService->getHttpClient()->delete($endpoint);
        return new GeneralResponse($response);
    }

    public function listContacts(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);
        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);
        return new GeneralResponse($response);
    }
}
