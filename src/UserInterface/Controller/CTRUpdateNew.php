<?php

namespace App\UserInterface\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Application\UseCases\USCUpdateNew;
use App\Infrastructure\Repositories\Services\SVCValidateIdParam;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CTRUpdateNew extends AbstractController{

    private USCUpdateNew $USCUpdateNew;
    private SVCValidateIdParam $SVCValidateIdParam;

    public function __construct(
        USCUpdateNew $USCUpdateNew,
        SVCValidateIdParam $SVCValidateIdParam
    ){
        $this->USCUpdateNew = $USCUpdateNew;
        $this->SVCValidateIdParam = $SVCValidateIdParam;
    }

    #[Route('/feeds/{id}', name: 'update_new', methods: ['PUT'])]
    public function __invoke(string $id, Request $request): JsonResponse
    {
        try{

            $validatedId = $this->SVCValidateIdParam->validate($id);

            $data = json_decode($request->getContent(), true);

            if (!$data) {
                return new JsonResponse([
                    'status' => 'error',
                    'message' => 'Payload is mandatory'
                ], 400);
            }

            $result = $this->USCUpdateNew->__invoke($validatedId, $data);

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
