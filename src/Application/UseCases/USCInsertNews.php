<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCNews;

final class USCInsertNews{

    private IFCNews $IFCNews;
    public function __construct(IFCNews $IFCNews){

        $this->IFCNews = $IFCNews;

    }

    public function __invoke(array $news): array
    {
        return $this->IFCNews->insertNews($news);
    }

}
