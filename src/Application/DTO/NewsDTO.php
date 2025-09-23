<?php

namespace App\Application\DTO;

class NewsDTO
{
    public function __construct(
        public string $title,
        public string $url,
        public string $summary,
        public string $source
    ) {}
}
