<?php

namespace App\Domain\Contracts;

interface IFCNews{
    public function insertNews(array $news): array;
    public function getAllNews(): array;
}
