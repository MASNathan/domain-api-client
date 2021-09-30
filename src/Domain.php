<?php

namespace MASNathan\DomainAPI;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;
use Psr\Http\Message\ResponseInterface;

final class Domain
{
    private string $host;
    private string $key;
    private Client $client;

    public function __construct(string $host, string $key, Client $client = null)
    {
        $this->host = $host;
        $this->key = $key;
        $this->client = $client ?? new Client();
    }

    private function buildEndpoint(string $section, array $params = []): string
    {
        $endpoint = sprintf("https://%s/%s", $this->host, $section);
        if (! $params) {
            return $endpoint;
        }

        return $endpoint . '?' . http_build_query($params);
    }

    private function get(string $section, array $params = []): ResponseInterface
    {
        return $this->client->get($this->buildEndpoint($section, $params), [
            'timeout' => 30,
            'headers' => [
                'x-rapidapi-host' => $this->host,
                'x-rapidapi-key' => $this->key,
            ],
        ]);
    }

    private function getBatch(string $section, array $requests = []): array
    {
        $batch = [];
        foreach ($requests as $params) {
            $batch[] = $this->client->getAsync($this->buildEndpoint($section, $params), [
                'timeout' => 30,
                'headers' => [
                    'x-rapidapi-host' => $this->host,
                    'x-rapidapi-key' => $this->key,
                ],
            ]);
        }

        return $batch;
    }

    private function handleResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    private function handleBatchResponse(array $result): array
    {
        if ($result['state'] != PromiseInterface::FULFILLED) {
            /** @var GuzzleException $exception */
            $exception = $result['reason'];

            if ($exception instanceof ClientException) {
                return $this->handleResponse($exception->getResponse());
            }

            return ['message' => $exception->getMessage()];
        }

        /** @var ResponseInterface $response */
        $response = $result['value'];

        return $this->handleResponse($response);
    }

    public function whois(string $domain): array
    {
        $response = $this->get('whois', compact('domain'));

        return $this->handleResponse($response);
    }

    public function whoisBatch(array $domains): array
    {
        if (count($domains) > 100) {
            throw new \InvalidArgumentException(sprintf("Batch limit is 100 domains, %d sent", count($domains)));
        }

        $batch = $this->getBatch('whois', array_map(function ($domain) {
            return compact('domain');
        }, $domains));

        return array_map(function (array $result) {
            return $this->handleBatchResponse($result);
        }, Utils::settle($batch)->wait());
    }

    public function dns(string $domain): array
    {
        $response = $this->get('dns', compact('domain'));

        return $this->handleResponse($response);
    }

    public function dnsBatch(array $domains): array
    {
        if (count($domains) > 100) {
            throw new \InvalidArgumentException(sprintf("Batch limit is 100 domains, %d sent", count($domains)));
        }

        $batch = $this->getBatch('dns', array_map(function ($domain) {
            return compact('domain');
        }, $domains));

        return array_map(function (array $result) {
            return $this->handleBatchResponse($result);
        }, Utils::settle($batch)->wait());
    }
}
