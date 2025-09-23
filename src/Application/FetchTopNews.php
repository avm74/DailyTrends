<?php

namespace App\Application;

use App\Domain\Repository\NewsRepositoryInterface;
use Psr\Log\LoggerInterface;

class FetchTopNews
{
    public function __construct(
        private iterable $scrapers,
        private NewsRepositoryInterface $newsRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(int $limit = 5): array
    {
        $allNews = [];

        foreach ($this->scrapers as $scraper) {
            $scraperNews = $scraper->fetchTopNews($limit);

            foreach ($scraperNews as $news) {

                $existingNews = $this->newsRepository->findByUrl($news->getUrl());

                if (!$existingNews) {

                    try {
                        $this->newsRepository->save($news);
                        $this->logger->info("Noticia guardada exitosamente", [
                            'news_title' => $news->getTitle(),
                            'news_url' => $news->getUrl(),
                            'news_source' => $news->getSource()
                        ]);
                    } catch (\Exception $e) {
                        $this->logger->error("Error guardando noticia en BD", [
                            'error' => $e->getMessage(),
                            'news_title' => $news->getTitle(),
                            'news_url' => $news->getUrl(),
                            'news_source' => $news->getSource()
                        ]);
                    }
                }


                $allNews[] = $news;
            }
        }

        return $allNews;
    }
}
