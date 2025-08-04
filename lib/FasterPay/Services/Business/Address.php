<?php

namespace FasterPay\Services\Business;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Address extends GeneralService
{
    protected $endpoint = 'api/external/address';

    /**
     * Get address fields and subdivisions for a country
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @return GeneralResponse
     * @throws Exception
     */
    public function getAddressFields($countryCode = '')
    {
        if (empty($countryCode)) {
            throw new Exception('Country code is required');
        }

        if (strlen($countryCode) !== 2) {
            throw new Exception('Country code must be 2 characters (ISO 3166-1 alpha-2)');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/fields/' . strtoupper($countryCode));

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Get supported address fields for multiple countries
     *
     * @param array $countryCodes Array of country codes
     * @return array Array of GeneralResponse objects keyed by country code
     * @throws Exception
     */
    public function getMultipleAddressFields(array $countryCodes = [])
    {
        if (empty($countryCodes)) {
            throw new Exception('At least one country code is required');
        }

        $responses = [];

        foreach ($countryCodes as $countryCode) {
            try {
                $responses[$countryCode] = $this->getAddressFields($countryCode);
            } catch (Exception $e) {
                // Log error but continue with other countries
                error_log("Error getting address fields for $countryCode: " . $e->getMessage());
                $responses[$countryCode] = null;
            }
        }

        return $responses;
    }

    /**
     * Get subdivisions (states/provinces) for a country
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @return array Array of subdivisions
     * @throws Exception
     */
    public function getSubdivisions($countryCode = '')
    {
        $fieldsResponse = $this->getAddressFields($countryCode);

        if ($fieldsResponse->isSuccessful()) {
            $data = $fieldsResponse->getDecodeResponse();
            return $data['data']['subdivisions'] ?? [];
        }

        return [];
    }

    /**
     * Get required fields for a country
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @return array Array of required field definitions
     * @throws Exception
     */
    public function getRequiredFields($countryCode = '')
    {
        $fieldsResponse = $this->getAddressFields($countryCode);

        if ($fieldsResponse->isSuccessful()) {
            $data = $fieldsResponse->getDecodeResponse();
            return $data['data']['fields'] ?? [];
        }

        return [];
    }

    /**
     * Validate address data against country requirements
     *
     * @param array $addressData Address data to validate
     * @param string $countryCode Country code for validation rules
     * @return array Validation results
     * @throws Exception
     */
    public function validateAddressData(array $addressData = [], $countryCode = '')
    {
        if (empty($countryCode)) {
            throw new Exception('Country code is required for validation');
        }

        $requiredFields = $this->getRequiredFields($countryCode);
        $validationErrors = [];
        $validationWarnings = [];

        foreach ($requiredFields as $field) {
            $fieldName = $field['name'] ?? '';
            $fieldLabel = $field['label'] ?? $fieldName;
            $fieldType = $field['type'] ?? 'text';

            // Check if required field is present
            if (empty($addressData[$fieldName])) {
                $validationErrors[] = "$fieldLabel is required for $countryCode";
                continue;
            }

            // Validate field type
            switch ($fieldType) {
                case 'select':
                    // For select fields, validate against subdivisions
                    if ($fieldName === 'administrative_area') {
                        $subdivisions = $this->getSubdivisions($countryCode);
                        $validCodes = array_column($subdivisions, 'code');

                        if (!in_array($addressData[$fieldName], $validCodes)) {
                            $validationErrors[] = "Invalid $fieldLabel for $countryCode";
                        }
                    }
                    break;

                case 'text':
                    // Basic text validation
                    if (strlen($addressData[$fieldName]) < 2) {
                        $validationWarnings[] = "$fieldLabel seems too short";
                    }
                    break;
            }
        }

        return [
            'valid' => empty($validationErrors),
            'errors' => $validationErrors,
            'warnings' => $validationWarnings,
            'country_code' => $countryCode
        ];
    }

    /**
     * Get address format template for a country
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @return array Address format information
     * @throws Exception
     */
    public function getAddressFormat($countryCode = '')
    {
        $fieldsResponse = $this->getAddressFields($countryCode);

        if ($fieldsResponse->isSuccessful()) {
            $data = $fieldsResponse->getDecodeResponse();
            $fields = $data['data']['fields'] ?? [];

            return [
                'country_code' => $countryCode,
                'fields' => $fields,
                'field_order' => array_column($fields, 'name'),
                'required_fields' => array_column($fields, 'name'),
                'subdivisions_available' => !empty($data['data']['subdivisions'])
            ];
        }

        throw new Exception("Could not retrieve address format for $countryCode");
    }

    /**
     * Check if a country supports subdivisions (states/provinces)
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @return bool True if country has subdivisions
     * @throws Exception
     */
    public function hasSubdivisions($countryCode = '')
    {
        $subdivisions = $this->getSubdivisions($countryCode);
        return !empty($subdivisions);
    }

    /**
     * Get subdivision by code
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @param string $subdivisionCode Subdivision code
     * @return array|null Subdivision information or null if not found
     * @throws Exception
     */
    public function getSubdivisionByCode($countryCode = '', $subdivisionCode = '')
    {
        if (empty($subdivisionCode)) {
            throw new Exception('Subdivision code is required');
        }

        $subdivisions = $this->getSubdivisions($countryCode);

        foreach ($subdivisions as $subdivision) {
            if ($subdivision['code'] === $subdivisionCode) {
                return $subdivision;
            }
        }

        return null;
    }

    /**
     * Search subdivisions by name
     *
     * @param string $countryCode Country code (ISO 2-letter)
     * @param string $searchTerm Search term
     * @return array Array of matching subdivisions
     * @throws Exception
     */
    public function searchSubdivisions($countryCode = '', $searchTerm = '')
    {
        if (empty($searchTerm)) {
            throw new Exception('Search term is required');
        }

        $subdivisions = $this->getSubdivisions($countryCode);
        $matches = [];

        foreach ($subdivisions as $subdivision) {
            if (stripos($subdivision['name'], $searchTerm) !== false ||
                stripos($subdivision['code'], $searchTerm) !== false) {
                $matches[] = $subdivision;
            }
        }

        return $matches;
    }
}