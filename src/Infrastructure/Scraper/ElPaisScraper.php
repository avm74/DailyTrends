<?php

namespace App\Infrastructure\Scraper;

use App\Domain\Scraper\ScraperInterface;
use App\Domain\Entities\News;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class ElPaisScraper implements ScraperInterface
{
    private const EL_PAIS_URL = 'https://www.elpais.com/';

    public function __construct(private HttpClientInterface $client) {}

    public function fetchTopNews(int $limit = 5): array
    {
        try {
            $response = $this->client->request('GET', self::EL_PAIS_URL);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception("El País no disponible");
            }

            $crawler = new Crawler($response->getContent());
            $news = [];

            $crawler->filter('article')->each(function (Crawler $node) use (&$news, $limit) {
                if (count($news) >= $limit) return;

                $title = $node->filter('h2 a, h3 a, .c_t a')->text('Sin título');
                $url = $node->filter('h2 a, h3 a, .c_t a')->attr('href') ?? '';
                $summary = $node->filter('.c_d, .c_e, p')->text('Sin resumen');

                if (!empty($title) && $title !== 'Sin título') {
                    $news[] = new News(
                        trim($title),
                        $this->completeUrl($url),
                        trim($summary),
                        'El País'
                    );
                }
            });

            return $news;
        } catch (\Throwable $e) {
            throw new \Exception("Error en scraper El País: ".$e->getMessage());
        }
    }

    public function getSource(): string
    {
        return 'El País';
    }

    private function completeUrl(string $url): string
    {
        if (empty($url)) return '';
        if (str_starts_with($url, 'http')) return $url;
        if (str_starts_with($url, '/')) return rtrim(self::EL_PAIS_URL, '/').$url;
        return self::EL_PAIS_URL.$url;
    }
}
