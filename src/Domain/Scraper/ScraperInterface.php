<?php

namespace App\Domain\Scraper;

interface ScraperInterface
{

    public function fetchTopNews(int $limit = 5): array;
    public function getSource(): string;
}
