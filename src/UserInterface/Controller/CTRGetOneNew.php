<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCGetOneNew;
use App\Infrastructure\Repositories\Services\SVCValidateIdParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRGetOneNew extends AbstractController{

    private USCGetOneNew $USCGetOneNew;
    private SVCValidateIdParam $SVCValidateIdParam;

    public function __construct(
        USCGetOneNew $USCGetOneNew,
        SVCValidateIdParam $SVCValidateIdParam
    ){
        $this->USCGetOneNew = $USCGetOneNew;
        $this->SVCValidateIdParam = $SVCValidateIdParam;
    }

    #[Route('/feeds/{id}', name: 'get_one_new', methods: ['GET'])]
    public function __invoke(string $id): JsonResponse
    {
        try{

            $validatedId = $this->SVCValidateIdParam->validate($id);

            $result = $this->USCGetOneNew->__invoke($validatedId);

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
