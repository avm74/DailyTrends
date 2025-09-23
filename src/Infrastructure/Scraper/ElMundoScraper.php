<?php

namespace App\Infrastructure\Scraper;

use App\Domain\Scraper\ScraperInterface;
use App\Domain\Entities\News;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class ElMundoScraper implements ScraperInterface
{
    private const EL_MUNDO_URL = 'https://www.elmundo.es/';
    public function __construct(private HttpClientInterface $client) {}

    public function fetchTopNews(int $limit = 5): array
    {
        try {
            $response = $this->client->request('GET', self::EL_MUNDO_URL);
            if ($response->getStatusCode() !== 200) {
                throw new \Exception("El Mundo no disponible");
            }

            $crawler = new Crawler($response->getContent());
            $news = [];

            $crawler->filter('article')->each(function (Crawler $node) use (&$news, $limit) {
                if (count($news) >= $limit) return;


                $title = $node->filter('.ue-c-cover-content__headline')->text('Sin título');
                $url = $node->filter('.ue-c-cover-content__link')->attr('href') ?? '';
                $summary = $node->filter('.ue-c-cover-content__kicker')->text('Sin resumen');

                if (!empty($title) && $title !== 'Sin título') {
                    $news[] = new News(
                        trim($title),
                        $this->completeUrl($url),
                        trim($summary),
                        'El Mundo'
                    );
                }
            });

            return $news;
        } catch (\Throwable $e) {
            throw new \Exception("Error en scraper El Mundo: ".$e->getMessage());
        }
    }

    public function getSource(): string
    {
        return 'El Mundo';
    }

    private function completeUrl(string $url): string
    {
        if (empty($url)) return '';
        if (str_starts_with($url, 'http')) return $url;
        if (str_starts_with($url, '/')) return rtrim(self::EL_MUNDO_URL, '/').$url;
        return self::EL_MUNDO_URL.$url;
    }
}
