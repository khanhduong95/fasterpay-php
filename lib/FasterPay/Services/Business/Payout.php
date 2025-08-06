<?php

namespace FasterPay\Services\Business;

use FasterPay\Exception;
use FasterPay\Response\GeneralResponse;
use FasterPay\Services\GeneralService;

class Payout extends GeneralService
{
    protected $endpoint = 'api/external/payouts';

    /**
     * Create a mass payout
     *
     * @param array $params Payout parameters
     * @return GeneralResponse
     */
    public function createPayout($params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new GeneralResponse($response);
    }

    /**
     * Get payout details
     *
     * @param string $payoutId Payout ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getPayoutDetails($payoutId = '')
    {
        if (empty($payoutId)) {
            throw new Exception('Payout ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $payoutId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Get payout list
     *
     * @param array $filters Optional filters (limit, offset, status, etc.)
     * @return GeneralResponse
     */
    public function getPayoutList($filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }
}