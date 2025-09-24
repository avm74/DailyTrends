<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\IFCScraper;
use App\Domain\Entities\News;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class REPElPaisScraper implements IFCScraper
{
    private const EL_PAIS_URL = 'https://www.elpais.com/';

    public function __construct(private HttpClientInterface $client) {}

    public function scrapNews(): array
    {
        try {
            $response = $this->client->request('GET', self::EL_PAIS_URL);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception("El País not available");
            }

            $crawler = new Crawler($response->getContent());
            $news = [];

            $crawler->filter('article')->each(function (Crawler $node) use (&$news) {
                if (count($news) >= 5) {
                    return;
                }

                $title = $node->filter('h2 a, h3 a, .c_t a')->text('No title');
                $url = $node->filter('h2 a, h3 a, .c_t a')->attr('href') ?? '';
                $summary = $node->filter('.c_d, .c_e, p')->text('No summary');

                if (!empty($title) && $title !== 'No title') {
                    $news[] = [
                        'title' => trim($title),
                        'url' => $this->completeUrl($url),
                        'summary' => trim($summary),
                        'source' => 'El País'
                    ];
                }
            });

            return $news;
        } catch (\Throwable $e) {
            throw new \Exception("Error in El País scraper: ".$e->getMessage());
        }
    }

    private function completeUrl(string $url): string
    {
        if (empty($url)) return '';
        if (str_starts_with($url, 'http')) return $url;
        if (str_starts_with($url, '/')) return rtrim(self::EL_PAIS_URL, '/').$url;
        return self::EL_PAIS_URL.$url;
    }
}
