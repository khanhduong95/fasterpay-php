<?php

namespace FasterPay\Services\Business;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Contact extends GeneralService
{
    protected $endpoint = 'api/external/contacts';

    /**
     * Create a new contact
     *
     * @param array $params Contact parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function createContact(array $params = [])
    {
        $this->validateContactParams($params);

        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get contact details
     *
     * @param string $contactId Contact ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getContact($contactId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Update contact
     *
     * @param string $contactId Contact ID
     * @param array $params Updated contact parameters
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateContact($contactId = '', array $params = [])
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $this->validateContactParams($params, false);

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Delete contact
     *
     * @param string $contactId Contact ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function deleteContact($contactId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * List contacts
     *
     * @param array $filters Optional filters (limit, offset, type, status, etc.)
     * @return GeneralResponse
     */
    public function listContacts(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Search contacts
     *
     * @param array $searchParams Search parameters (name, email, phone, etc.)
     * @return GeneralResponse
     * @throws Exception
     */
    public function searchContacts(array $searchParams = [])
    {
        if (empty($searchParams)) {
            throw new Exception('Search parameters are required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/search');

        $response = $this->httpService->getHttpClient()->post($endpoint, $searchParams);

        return new GeneralResponse($response);
    }

    /**
     * Get contact by email
     *
     * @param string $email Email address
     * @return GeneralResponse
     * @throws Exception
     */
    public function getContactByEmail($email = '')
    {
        if (empty($email)) {
            throw new Exception('Email address is required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/email/' . urlencode($email));

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Get contact by phone
     *
     * @param string $phone Phone number
     * @return GeneralResponse
     * @throws Exception
     */
    public function getContactByPhone($phone = '')
    {
        if (empty($phone)) {
            throw new Exception('Phone number is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/phone/' . urlencode($phone));

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Add address to contact
     *
     * @param string $contactId Contact ID
     * @param array $addressData Address data
     * @return GeneralResponse
     * @throws Exception
     */
    public function addContactAddress($contactId = '', array $addressData = [])
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        if (empty($addressData)) {
            throw new Exception('Address data is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId . '/address');

        $response = $this->httpService->getHttpClient()->post($endpoint, $addressData);

        return new GeneralResponse($response);
    }

    /**
     * Update contact address
     *
     * @param string $contactId Contact ID
     * @param string $addressId Address ID
     * @param array $addressData Updated address data
     * @return GeneralResponse
     * @throws Exception
     */
    public function updateContactAddress($contactId = '', $addressId = '', array $addressData = [])
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        if (empty($addressId)) {
            throw new Exception('Address ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId . '/address/' . $addressId);

        $response = $this->httpService->getHttpClient()->put($endpoint, $addressData);

        return new GeneralResponse($response);
    }

    /**
     * Remove address from contact
     *
     * @param string $contactId Contact ID
     * @param string $addressId Address ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function removeContactAddress($contactId = '', $addressId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        if (empty($addressId)) {
            throw new Exception('Address ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId . '/address/' . $addressId);

        $response = $this->httpService->getHttpClient()->delete($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Get contact addresses
     *
     * @param string $contactId Contact ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getContactAddresses($contactId = '')
    {
        if (empty($contactId)) {
            throw new Exception('Contact ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $contactId . '/addresses');

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Validate contact parameters
     *
     * @param array $params Contact parameters
     * @param bool $requireAll Whether all fields are required (for create vs update)
     * @throws Exception
     */
    private function validateContactParams(array $params, $requireAll = true)
    {
        if ($requireAll) {
            if (empty($params['first_name']) && empty($params['name'])) {
                throw new Exception('First name or name is required');
            }

            if (empty($params['email']) && empty($params['phone'])) {
                throw new Exception('Email or phone number is required');
            }
        }

        // Validate email format if provided
        if (!empty($params['email']) && !filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Validate phone format if provided (basic validation)
        if (!empty($params['phone'])) {
            $phone = preg_replace('/[^\d+]/', '', $params['phone']);
            if (strlen($phone) < 7 || strlen($phone) > 15) {
                throw new Exception('Invalid phone number format');
            }
        }

        // Validate phone_country_code if provided
        if (!empty($params['phone_country_code']) && strlen($params['phone_country_code']) !== 2) {
            throw new Exception('Phone country code must be 2 characters (ISO 3166-1 alpha-2)');
        }

        // Validate country if provided
        if (!empty($params['country']) && strlen($params['country']) !== 2) {
            throw new Exception('Country must be 2 characters (ISO 3166-1 alpha-2)');
        }
    }
}