<?php

namespace App\Domain\Entities;

class News
{
    public function __construct(
        private string $title,
        private string $url,
        private string $summary,
        private string $source
    ) {}

    public function getTitle(): string { return $this->title; }
    public function getUrl(): string { return $this->url; }
    public function getSummary(): string { return $this->summary; }
    public function getSource(): string { return $this->source; }
}
