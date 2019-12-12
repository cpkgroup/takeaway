<?php

namespace App\Service\Email;

use Symfony\Contracts\HttpClient\HttpClientInterface;

trait ProviderParamsTrait
{
    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $data;

    public function __construct(HttpClientInterface $httpClient, array $data)
    {
        $this->httpClient = $httpClient;
        $this->data = $data;
    }
}
