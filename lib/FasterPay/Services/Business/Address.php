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
}