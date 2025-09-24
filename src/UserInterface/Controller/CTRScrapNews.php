<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCScrapAllNews;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRScrapNews extends AbstractController{

    private USCScrapAllNews $USCScrapAllNews;

    public function __construct(USCScrapAllNews $USCScrapAllNews){
        $this->USCScrapAllNews = $USCScrapAllNews;
    }

    #[Route('/api/scrap', name: 'news_scraping', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try{
            $result = $this->USCScrapAllNews->__invoke();

            return new JsonResponse([
                'status' => 'success',
                'data' => $result,
                'message' => 'News scraped successfully'
            ], 200);

        }catch(\Throwable $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
