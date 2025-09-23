<?php

namespace App\Infrastructure\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;

class SymfonyHttpScraper
{
    public function __construct(private HttpClientInterface $client) {}

    public function get(string $url, array $options = []): string
    {
        try {
            $response = $this->client->request('GET', $url, $options);
            return $response->getContent();
        } catch (TransportExceptionInterface|ClientExceptionInterface|ServerExceptionInterface|RedirectionExceptionInterface $e) {
            throw new \RuntimeException("Error fetching URL {$url}: ".$e->getMessage());
        }
    }
}
