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
     * @throws Exception
     */
    public function createPayout(array $params = [])
    {
        if (empty($params['payouts']) || !is_array($params['payouts'])) {
            throw new Exception('Payouts array is required');
        }

        if (empty($params['source_currency'])) {
            throw new Exception('Source currency is required');
        }

        if (empty($params['template'])) {
            throw new Exception('Template is required');
        }

        // Validate each payout
        foreach ($params['payouts'] as $payout) {
            if (empty($payout['receiver_email'])) {
                throw new Exception('Each payout must have a receiver_email');
            }
            if (empty($payout['amount'])) {
                throw new Exception('Each payout must have an amount');
            }
            if (empty($payout['amount_currency'])) {
                throw new Exception('Each payout must have an amount_currency');
            }
            if (empty($payout['receiver_full_name'])) {
                throw new Exception('Each payout must have a receiver_full_name');
            }
            if (empty($payout['receiver_type'])) {
                throw new Exception('Each payout must have a receiver_type');
            }

            // Validate receiver_type
            if (!in_array($payout['receiver_type'], ['private', 'business'])) {
                throw new Exception('Receiver type must be either "private" or "business"');
            }
        }

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
    public function getPayoutList(array $filters = [])
    {
        $endpoint = $this->httpService->getEndPoint($this->endpoint);

        $response = $this->httpService->getHttpClient()->get($endpoint, $filters);

        return new GeneralResponse($response);
    }

    /**
     * Cancel a payout
     *
     * @param string $payoutId Payout ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function cancelPayout($payoutId = '')
    {
        if (empty($payoutId)) {
            throw new Exception('Payout ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $payoutId . '/cancel');

        $response = $this->httpService->getHttpClient()->post($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Get payout status
     *
     * @param string $payoutId Payout ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function getPayoutStatus($payoutId = '')
    {
        if (empty($payoutId)) {
            throw new Exception('Payout ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $payoutId . '/status');

        $response = $this->httpService->getHttpClient()->get($endpoint);

        return new GeneralResponse($response);
    }

    /**
     * Retry a failed payout
     *
     * @param string $payoutId Payout ID
     * @return GeneralResponse
     * @throws Exception
     */
    public function retryPayout($payoutId = '')
    {
        if (empty($payoutId)) {
            throw new Exception('Payout ID is required');
        }

        $endpoint = $this->httpService->getEndPoint($this->endpoint . '/' . $payoutId . '/retry');

        $response = $this->httpService->getHttpClient()->post($endpoint);

        return new GeneralResponse($response);
    }
}