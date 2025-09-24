<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\IFCScraper;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class REPElMundoScraper implements IFCScraper
{
    private const EL_MUNDO_URL = 'https://www.elmundo.es/';

    public function __construct(private HttpClientInterface $client) {}

    public function scrapNews(): array
    {
        try {
            $response = $this->client->request('GET', self::EL_MUNDO_URL);

            if ($response->getStatusCode() !== 200) {
                throw new \Exception("El Mundo not available");
            }

            $crawler = new Crawler($response->getContent());
            $news = [];

            $crawler->filter('article, .ue-c-cover-content, .ue-c-article, .article-item')->each(function (Crawler $node) use (&$news) {
                if (count($news) >= 5) {
                    return;
                }

                $title = '';
                $url = '';
                $summary = '';

                $titleSelectors = [
                    '.ue-c-cover-content__headline',
                    '.ue-c-article__headline',
                    'h2 a',
                    'h3 a',
                    '.headline',
                    'a[data-testid="headline"]'
                ];

                $urlSelectors = [
                    '.ue-c-cover-content__link',
                    '.ue-c-article__link',
                    'h2 a',
                    'h3 a',
                    'a[data-testid="headline"]'
                ];

                $summarySelectors = [
                    '.ue-c-cover-content__kicker',
                    '.ue-c-article__kicker',
                    '.summary',
                    'p',
                    '.description'
                ];

                foreach ($titleSelectors as $selector) {
                    try {
                        $titleElement = $node->filter($selector);
                        if ($titleElement->count() > 0) {
                            $title = $titleElement->text();
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                foreach ($urlSelectors as $selector) {
                    try {
                        $urlElement = $node->filter($selector);
                        if ($urlElement->count() > 0) {
                            $url = $urlElement->attr('href') ?? '';
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                foreach ($summarySelectors as $selector) {
                    try {
                        $summaryElement = $node->filter($selector);
                        if ($summaryElement->count() > 0) {
                            $summary = $summaryElement->text();
                            break;
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                if (!empty($title) && $title !== 'No title' && !empty($url)) {
                    $news[] = [
                        'title' => trim($title),
                        'url' => $this->completeUrl($url),
                        'summary' => trim($summary),
                        'source' => 'El Mundo'
                    ];
                }
            });

            return $news;
        } catch (\Throwable $e) {
            throw new \Exception("Error in El Mundo scraper: ".$e->getMessage());
        }
    }

    private function completeUrl(string $url): string
    {
        if (empty($url)) return '';
        if (str_starts_with($url, 'http')) return $url;
        if (str_starts_with($url, '/')) return rtrim(self::EL_MUNDO_URL, '/').$url;
        return self::EL_MUNDO_URL.$url;
    }
}
