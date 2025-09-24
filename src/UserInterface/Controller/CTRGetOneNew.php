<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCGetOneNew;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRGetOneNew extends AbstractController{

    private USCGetOneNew $USCGetOneNew;

    public function __construct(USCGetOneNew $USCGetOneNew){
        $this->USCGetOneNew = $USCGetOneNew;
    }

    #[Route('/feeds/{id}', name: 'get_one_new', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        try{

            if (!is_numeric($id) || !ctype_digit($id)) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Invalid ID. ID must be a positive integer'
                ], 400);
            }

            $id = (int) $id;

            if ($id <= 0) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Invalid ID. ID must be a positive number'
                ], 400);
            }

            $result = $this->USCGetOneNew->__invoke($id);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'New not found'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'data' => $result
            ], 200);

        }catch(\Throwable $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
