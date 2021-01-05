<?php

namespace App\Service\Source;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

abstract class AbstractHttpSource implements SourceInterface
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws GuzzleException
     *
     * @return string
     */
    public function getRaw(): string
    {
        return (string) $this->client->get($this->getUri())->getBody();
    }

    abstract protected function getUri(): string;
}
