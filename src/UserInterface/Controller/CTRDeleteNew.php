<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCDeleteNew;
use App\Infrastructure\Services\SVCValidateIdParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class CTRDeleteNew extends AbstractController{

    private USCDeleteNew $USCDeleteNew;
    private SVCValidateIdParam $SVCValidateIdParam;

    public function __construct(
        USCDeleteNew $USCDeleteNew,
        SVCValidateIdParam $SVCValidateIdParam
        ){
        $this->USCDeleteNew = $USCDeleteNew;
        $this->SVCValidateIdParam = $SVCValidateIdParam;
    }

    #[Route('/feeds/{id}', name: 'delete_new', methods: ['DELETE'])]
    public function __invoke(string $id): JsonResponse
    {
        try{

            $validatedId = $this->SVCValidateIdParam->validate($id);

            $result = $this->USCDeleteNew->__invoke($validatedId);

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
