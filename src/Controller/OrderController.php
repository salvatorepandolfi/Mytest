<?php

namespace App\Controller;

use App\DTO\OrderRequest;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class OrderController extends AbstractController
{
    public function __construct(
        private OrderService $orderService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/api/orders', name: 'create_order', methods: ['POST'])]
    public function createOrder(Request $request): JsonResponse
    {
        try {
            // Deserialize the request body to DTO
            $orderRequest = $this->serializer->deserialize(
                $request->getContent(),
                OrderRequest::class,
                'json'
            );

            // Validate the request
            $violations = $this->validator->validate($orderRequest);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = [
                        'field' => $violation->getPropertyPath(),
                        'message' => $violation->getMessage()
                    ];
                }
                
                return new JsonResponse([
                    'error' => 'Validation failed',
                    'details' => $errors
                ], Response::HTTP_BAD_REQUEST);
            }

            // Process the order
            $orderResponse = $this->orderService->createOrder($orderRequest);

            // Serialize and return the response
            $responseData = $this->serializer->serialize($orderResponse, 'json');
            
            return new JsonResponse(
                json_decode($responseData, true),
                Response::HTTP_CREATED
            );

        } catch (\Symfony\Component\Serializer\Exception\NotEncodableValueException $e) {
            return new JsonResponse([
                'error' => 'Invalid JSON format',
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
            
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
            return new JsonResponse([
                'error' => 'Product not found',
                'message' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/health', name: 'health_check', methods: ['GET'])]
    public function healthCheck(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'healthy',
            'timestamp' => (new \DateTimeImmutable())->format('c')
        ]);
    }
}