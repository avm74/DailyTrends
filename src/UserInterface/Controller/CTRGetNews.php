<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCGetNews;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRGetNews extends AbstractController{

    private USCGetNews $USCGetNews;

    public function __construct(USCGetNews $USCGetNews){
        $this->USCGetNews = $USCGetNews;
    }

    #[Route('/feeds', name: 'get_news', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try{

            $news = $this->USCGetNews->__invoke();

            return new JsonResponse([
                'status' => 'success',
                'data' => $news,
                'count' => count($news)
            ], 200);

        }catch(\Throwable $e){

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);

        }
    }
}
