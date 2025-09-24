<?php

namespace App\Infrastructure\Repositories\Services;

use App\Domain\Contracts\IFCNews;
use App\Domain\Entities\News;
use Doctrine\ORM\EntityManagerInterface;

class REPNews implements IFCNews{

    private EntityManagerInterface $entityManagerInterface;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function insertNews(array $news): array
    {
        $toPersist = [];
        $notToPersist = [];

        foreach($news as $new){

            $allowedFields = ['title', 'url', 'summary', 'source'];
            $invalidFields = [];

            foreach ($new as $field => $value) {
                if (!in_array($field, $allowedFields)) {
                    $invalidFields[] = $field;
                }
            }

            if (!empty($invalidFields)) {
                $notToPersist[] = [
                    "new" => $new,
                    "reason" => "Invalid field(s): " . implode(', ', $invalidFields) . " don't exist and can't be inserted"
                ];
                continue;
            }

            if (!isset($new['url']) || !isset($new['title']) || !isset($new['summary']) || !isset($new['source'])) {
                $notToPersist[] = [
                    "new" => $new,
                    "reason" => "Missing required fields"
                ];
                continue;
            }

            $url = trim($new['url']);
            $title = trim($new['title']);
            $summary = trim($new['summary']);
            $source = trim($new['source']);

            if(!$url || !$title || !$summary || !$source){
                $notToPersist[] = [
                    "new" => $new,
                    "reason" => "Empty fields"
                ];
                continue;
            }

            $existingNew = $this->entityManagerInterface->getRepository(News::class)->findOneBy(['url' => $url]);

            if ($existingNew) {
                $notToPersist[] = [
                    "new" => $new,
                    "reason" => "Duplicated new"
                ];
                continue;
            }

            $newToPersist = new News($title, $url, $summary, $source);

            $this->entityManagerInterface->persist($newToPersist);
            $toPersist[] = $new;

        }

        if($toPersist){
            $this->entityManagerInterface->flush();
        }

        return [
            "insertedNews" => $toPersist,
            "notInsertedNews" => $notToPersist
        ];

    }

    public function getAllNews(): array
    {
        $news = $this->entityManagerInterface->getRepository(News::class)->findAll();

        return array_map(function($newsItem) {
            return $newsItem->toArray();
        }, $news);
    }

    public function getNewById(int $id): ?array
    {
        $news = $this->entityManagerInterface->getRepository(News::class)->find($id);

        if (!$news) {
            return null;
        }

        return $news->toArray();
    }

    public function updateNew(int $id, array $data): ?array
    {
        $news = $this->entityManagerInterface->getRepository(News::class)->find($id);

        if (!$news) {
            return null;
        }

        $allowedFields = ['title', 'url', 'summary', 'source'];
        $invalidFields = [];

        foreach ($data as $field => $value) {
            if (!in_array($field, $allowedFields)) {
                $invalidFields[] = $field;
            }
        }

        if (!empty($invalidFields)) {
            throw new \InvalidArgumentException('The field(s) ' . implode(', ', $invalidFields) . ' don\'t exist and can\'t be updated');
        }

        $hasValidField = false;

        foreach ($allowedFields as $field) {
            if (isset($data[$field]) && !empty(trim($data[$field]))) {
                $hasValidField = true;
                break;
            }
        }

        if (!$hasValidField) {
            throw new \InvalidArgumentException('At least one field (title, url, summary, source) must be provided for update');
        }

        if (isset($data['title'])) {
            $news->setTitle(trim($data['title']));
        }
        if (isset($data['url'])) {
            $news->setUrl(trim($data['url']));
        }
        if (isset($data['summary'])) {
            $news->setSummary(trim($data['summary']));
        }
        if (isset($data['source'])) {
            $news->setSource(trim($data['source']));
        }

        $this->entityManagerInterface->flush();

        return $news->toArray();
    }

    public function deleteNew(int $id): bool
    {
        $news = $this->entityManagerInterface->getRepository(News::class)->find($id);

        if (!$news) {
            return false;
        }

        $this->entityManagerInterface->remove($news);
        $this->entityManagerInterface->flush();

        return true;
    }

}
