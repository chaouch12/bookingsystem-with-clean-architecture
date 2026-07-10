<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Internal\Health\Controller;

use App\Layers\Presentation\Internal\Health\Models\Response\HealthResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[OA\Tag(name: 'Health')]
final class HealthController extends AbstractController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    #[OA\Get(description: 'Returns a basic backend health payload.', summary: 'Health check')]
    #[OA\Response(response: 200, description: 'Health payload', content: new OA\JsonContent(ref: new Model(type: HealthResponse::class)))]
    public function __invoke(): JsonResponse
    {
        return $this->json(
            new HealthResponse(
                'ok',
                'bookingSystem-clean-architecture',
                PHP_VERSION,
            ),
        );
    }
}
