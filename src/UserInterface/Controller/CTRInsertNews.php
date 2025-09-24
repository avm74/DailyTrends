<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCInsertNews;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRInsertNews extends AbstractController{

    private USCInsertNews $USCInsertNews;

    public function __construct(USCInsertNews $USCInsertNews){

        $this->USCInsertNews = $USCInsertNews;

    }

    #[Route('/api/feeds', name: 'insert_news', methods: ['POST'])]
    public function __invoke(Request $request){

        try{

            $data = json_decode($request->getContent(), true);

            if(!$data){
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Payload is mandatory'
                ], 400);
            }

            $result = $this->USCInsertNews->__invoke($data);

            return new JsonResponse([
                'status' => 'success',
                'result' => $result
            ], 200);

        }catch(\Throwable $e){

            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);

        }

    }
}
