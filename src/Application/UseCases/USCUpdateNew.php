<?php

namespace App\Application\UseCases;

use App\Domain\Contracts\IFCNews;

final class USCUpdateNew{

    private IFCNews $IFCNews;

    public function __construct(IFCNews $IFCNews){
        $this->IFCNews = $IFCNews;
    }

    public function __invoke(int $id, array $data): ?array
    {
        return $this->IFCNews->updateNew($id, $data);
    }

}
