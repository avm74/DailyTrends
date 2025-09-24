<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCScraper;
use App\Domain\Contracts\IFCNews;

final class USCScrapNews{

    private IFCScraper $IFCScraper;
    private IFCNews $IFCNews;

    public function __construct(IFCScraper $IFCScraper, IFCNews $IFCNews){
        $this->IFCScraper = $IFCScraper;
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(): array
    {
        $scrapedNews = $this->IFCScraper->scrapNews();

        if (empty($scrapedNews)) {
            return [];
        }

        $result = $this->IFCNews->insertNews($scrapedNews);

        return $result['insertedNews'];
    }

}
