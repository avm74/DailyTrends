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

            $url = trim($new['url']);
            $title = trim($new['source']);
            $summary = trim($new['summary']);
            $source = trim($new['title']);

            if(!$url || !$title || !$summary || !$source){
                $notToPersist[] = [
                    "new" => $new,
                    "reason" => "Missing fields"
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

            // Crear nueva entidad News
            $newToPersist = new News($title, $url, $summary, $source);

            // Guardar en la base de datos
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

}
