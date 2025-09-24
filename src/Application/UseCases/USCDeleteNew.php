<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCNews;

final class USCDeleteNew{

    private IFCNews $IFCNews;

    public function __construct(IFCNews $IFCNews){
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(int $id): bool
    {
        return $this->IFCNews->deleteNew($id);
    }

}
