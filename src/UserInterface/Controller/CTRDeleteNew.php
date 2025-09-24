<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCDeleteNew;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRDeleteNew extends AbstractController{

    private USCDeleteNew $USCDeleteNew;

    public function __construct(USCDeleteNew $USCDeleteNew){
        $this->USCDeleteNew = $USCDeleteNew;
    }

    #[Route('/feeds/{id}', name: 'delete_new', methods: ['DELETE'])]
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

            $result = $this->USCDeleteNew->__invoke($id);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'New not found'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'message' => 'New deleted successfully'
            ], 200);

        }catch(\Throwable $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
