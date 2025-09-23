<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\FetchTopNews;

class NewsController extends AbstractController
{
    public function __construct(private FetchTopNews $fetchTopNews) {}

    #[Route('/api/news', name: 'api_news', methods: ['GET'])]
    public function index(): JsonResponse
    {
        try {
            $newsList = ($this->fetchTopNews)();

            $newsArray = array_map(function($news) {
                return [
                    'id' => $news->getId(),
                    'title' => $news->getTitle(),
                    'url' => $news->getUrl(),
                    'summary' => $news->getSummary(),
                    'source' => $news->getSource(),
                    'created_at' => $news->getCreatedAt()?->format('Y-m-d H:i:s'),
                    'updated_at' => $news->getUpdatedAt()?->format('Y-m-d H:i:s')
                ];
            }, $newsList);

            return new JsonResponse([
                'status' => 'success',
                'total' => count($newsList),
                'news' => $newsArray
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
