<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCNews;

final class USCGetOneNew{

    private IFCNews $IFCNews;

    public function __construct(IFCNews $IFCNews){
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(int $id): ?array
    {
        return $this->IFCNews->getNewById($id);
    }

}
