<?php

namespace App\Domain\Repository;

use App\Domain\Entities\News;

interface NewsRepositoryInterface
{

    public function save(News $news): void;


    public function findAll(): array;

    public function findBySource(string $source): array;

    public function findByUrl(string $url): ?News;

    public function delete(News $news): void;
}
