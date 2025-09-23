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
            return new JsonResponse([
                'status' => 'success',
                'total' => count($newsList),
                'news' => $newsList
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
