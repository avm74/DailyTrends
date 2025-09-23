<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Entities\News;
use App\Domain\Repository\NewsRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class DoctrineNewsRepository implements NewsRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(News $news): void
    {
        $this->em->persist($news);
        $this->em->flush();
    }

    public function findAll(): array
    {
        return $this->em->createQueryBuilder()
            ->select('n')
            ->from(News::class, 'n')
            ->getQuery()
            ->getResult();
    }

    public function findBySource(string $source): array
    {
        return $this->em->createQueryBuilder()
            ->select('n')
            ->from(News::class, 'n')
            ->where('n.source = :source')
            ->setParameter('source', $source)
            ->getQuery()
            ->getResult();
    }

    public function findByUrl(string $url): ?News
    {
        return $this->em->createQueryBuilder()
            ->select('n')
            ->from(News::class, 'n')
            ->where('n.url = :url')
            ->setParameter('url', $url)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function delete(News $news): void
    {
        $this->em->remove($news);
        $this->em->flush();
    }
}
