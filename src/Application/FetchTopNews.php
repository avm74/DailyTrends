<?php

namespace App\Application;

use App\Application\DTO\NewsDTO;

class FetchTopNews
{
    public function __construct(private iterable $scrapers) {}

    public function __invoke(int $limit = 5): array
    {
        $allNews = [];
        foreach ($this->scrapers as $scraper) {
            foreach ($scraper->fetchTopNews($limit) as $news) {
                $allNews[] = new NewsDTO(
                    $news->getTitle(),
                    $news->getUrl(),
                    $news->getSummary(),
                    $news->getSource()
                );
            }
        }

        return $allNews;
    }
}
