<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller;

use App\Layers\Presentation\Http\Controller\Response\HealthResponse;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HealthController extends AbstractController
{
    #[Route('/api/health', name: 'api_health', methods: ['GET'])]
    #[OA\Get(summary: 'Health check', description: 'Returns a basic backend health payload.')]
    #[OA\Response(
        response: 200,
        description: 'Health payload',
        content: new OA\JsonContent(ref: new Model(type: HealthResponse::class))
    )]
    #[OA\Tag(name: 'Health')]
    public function health(): JsonResponse
    {
        return $this->json([
            'status' => 'ok',
            'service' => 'bookingSystem-clean-architecture',
            'php' => PHP_VERSION,
        ]);
    }
}
