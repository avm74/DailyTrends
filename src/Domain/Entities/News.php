<?php

namespace App\Domain\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'news')]
class News
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $url;

    #[ORM\Column(type: 'string', length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $summary;

    #[ORM\Column(type: 'string', length: 100)]
    private string $source;

    public function __construct(string $title, string $url, string $summary, string $source)
    {
        $this->title = $title;
        $this->url = $url;
        $this->summary = $summary;
        $this->source = $source;
    }

    public function getId(): ?int { return $this->id; }
    public function getTitle(): string { return $this->title; }
    public function getUrl(): string { return $this->url; }
    public function getSummary(): string { return $this->summary; }
    public function getSource(): string { return $this->source; }
}
