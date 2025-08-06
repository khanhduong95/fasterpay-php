<?php

namespace FasterPay;

use FasterPay\Services\Business\Address;
use FasterPay\Services\Business\Contact;
use FasterPay\Services\Business\EInvoice\Discount;
use FasterPay\Services\Business\EInvoice\Invoice;
use FasterPay\Services\Business\EInvoice\Product;
use FasterPay\Services\Business\EInvoice\Tax;
use FasterPay\Services\Business\EInvoice\Template;
use FasterPay\Services\Business\Payout;
use FasterPay\Services\GenericApiService;
use FasterPay\Services\HttpService;
use FasterPay\Services\Signature;
use FasterPay\Services\Pingback;

class BusinessGateway implements GatewayInterface
{
    const BUSINESS_API_BASE_URL = 'https://business.fasterpay.com';

    protected $config;
    protected $http;
    protected $baseUrl = '';

    public function __construct($config = [])
    {
        if (is_array($config)) {
            $config = new Config($config);
        }

        $this->config = $config;
        $this->config->setBaseUrl(self::BUSINESS_API_BASE_URL);

        $header = [
            'X-ApiKey: ' . $this->config->getPrivateKey(),
            'Content-Type: application/json'
        ];

        $this->http = new HttpClient($header);
    }

    protected function getBaseUrl()
    {
        if (!$url = $this->config->getApiBaseUrl()) {
            $url = $this->baseUrl;
        }

        return $url . '/';
    }

    public function getEndPoint($endpoint)
    {
        return $this->getBaseUrl() . $endpoint;
    }

    public function getHttpClient()
    {
        return $this->http;
    }

    public function signature()
    {
        return new Signature($this);
    }

    public function pingback()
    {
        return new Pingback($this);
    }

    /**
     * Mass Payout Service
     *
     * @return Payout
     */
    public function payoutService()
    {
        return new Payout(new HttpService($this));
    }

    /**
     * Address Service
     *
     * @return Address
     */
    public function addressService()
    {
        return new Address(new HttpService($this));
    }

    /**
     * Contact Service
     *
     * @return Contact
     */
    public function contactService()
    {
        return new Contact(new HttpService($this));
    }

    /**
     * Invoice Service
     *
     * @return Invoice
     */
    public function invoiceService()
    {
        return new Invoice(new HttpService($this));
    }

    /**
     * Invoice Template Service
     *
     * @return Template
     */
    public function invoiceTemplateService()
    {
        return new Template(new HttpService($this));
    }

    /**
     * Invoice Tax Service
     *
     * @return Tax
     */
    public function invoiceTaxService()
    {
        return new Tax(new HttpService($this));
    }

    /**
     * Invoice Discount Service
     *
     * @return Discount
     */
    public function invoiceDiscountService()
    {
        return new Discount(new HttpService($this));
    }

    /**
     * Invoice Product Service
     *
     * @return Product
     */
    public function invoiceProductService()
    {
        return new Product(new HttpService($this));
    }

    public function getConfig()
    {
        return $this->config;
    }
}