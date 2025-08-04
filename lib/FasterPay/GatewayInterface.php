<?php

namespace FasterPay;

interface GatewayInterface
{
    public function getEndPoint($endpoint);

    public function getHttpClient();

    public function signature();

    public function pingback();
}