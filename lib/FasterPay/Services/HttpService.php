<?php

namespace FasterPay\Services;

use FasterPay\BusinessGateway;
use FasterPay\Gateway;

class HttpService implements HttpServiceInterface
{
    /**
     * @var Gateway|BusinessGateway
     */
    protected $client;

    /**
     * @param Gateway|BusinessGateway $client
     */
    public function __construct($client)
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
