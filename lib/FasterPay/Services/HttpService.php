<?php

namespace FasterPay\Services;

use FasterPay\GatewayInterface;

class HttpService implements HttpServiceInterface
{
    /**
     * @var GatewayInterface
     */
    protected $client;

    public function __construct(GatewayInterface $client)
    {
        $this->client = $client;
    }

    public function getHttpClient()
    {
        return $this->client->getHttpClient();
    }

    public function getEndPoint($endpoint = '')
    {
        return $this->client->getEndPoint($endpoint);
    }

    public function __call($function, $params)
    {
        if (method_exists($this, $function)) {
            return call_user_func_array([$this, $function], $params);
        }
    }
}
