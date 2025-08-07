<?php

namespace FasterPay\Services\Business;

use FasterPay\Exception;
use FasterPay\Response\JsonResponse;
use FasterPay\Services\GeneralService;

class Payout extends GeneralService
{
    protected $endpoint = 'api/external/payouts';

    /**
     * Create a mass payout
     *
     * @param array $params Payout parameters
     * @return JsonResponse
     */
    public function createPayout(array $params = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->post($endpoint, $params);

        return new JsonResponse($response);
    }

    /**
     * Get payout details
     *
     * @param string $payoutId Payout ID
     * @return JsonResponse
     * @throws Exception
     */
    public function getPayoutDetails($payoutId = '')
    {
        if (empty($payoutId)) {
            throw new Exception('Payout ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $payoutId);

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new JsonResponse($response);
    }

    /**
     * Get payout list
     *
     * @param array $filters Optional filters
     * @return JsonResponse
     */
    public function getPayoutList(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new JsonResponse($response);
    }
}