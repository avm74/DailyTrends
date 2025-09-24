<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCNews;

final class USCGetNews{

    private IFCNews $IFCNews;

    public function __construct(IFCNews $IFCNews){
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(): array
    {
        return $this->IFCNews->getAllNews();
    }

}
