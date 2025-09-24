<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCUpdateNew;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CTRUpdateNew extends AbstractController{

    private USCUpdateNew $USCUpdateNew;

    public function __construct(USCUpdateNew $USCUpdateNew){
        $this->USCUpdateNew = $USCUpdateNew;
    }

    #[Route('/feeds/{id}', name: 'update_new', methods: ['PUT'])]
    public function __invoke(string $id, Request $request): JsonResponse
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

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Payload is mandatory'
                ], 400);
            }

            $result = $this->USCUpdateNew->__invoke($id, $data);

            if (!$result) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'New not found'
                ], 404);
            }

            return new JsonResponse([
                'status' => 'success',
                'data' => $result,
                'message' => 'New updated successfully'
            ], 200);

        }catch(\InvalidArgumentException $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }catch(\Throwable $e){
            return new JsonResponse([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
