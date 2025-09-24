<?php

namespace App\Application\UseCases;

use App\Infrastructure\Repositories\REPElPaisScraper;
use App\Infrastructure\Repositories\REPElMundoScraper;
use App\Domain\Contracts\IFCNews;

final class USCScrapAllNews{

    private REPElPaisScraper $REPElPaisScraper;
    private REPElMundoScraper $REPElMundoScraper;
    private IFCNews $IFCNews;

    public function __construct(REPElPaisScraper $REPElPaisScraper, REPElMundoScraper $REPElMundoScraper, IFCNews $IFCNews){
        $this->REPElPaisScraper = $REPElPaisScraper;
        $this->REPElMundoScraper = $REPElMundoScraper;
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(): array
    {
        $allScrapedNews = [];

        $paisNews = $this->REPElPaisScraper->scrapNews();
        $mundoNews = $this->REPElMundoScraper->scrapNews();

        $allScrapedNews = array_merge($allScrapedNews, $paisNews, $mundoNews);

        if (empty($allScrapedNews)) {
            return [];
        }

        $result = $this->IFCNews->insertNews($allScrapedNews);

        return $result['insertedNews'];
    }

}
