<?php

namespace App\Controller;

use App\Entity\Point;
use App\Entity\Polygone;
use App\Repository\PolygoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/polygones', name: 'api_polygones_')]
final class PolygoneApiController extends AbstractController
{
    /**
     * 1. GET /api/polygones : Liste tous les polygones et leurs sommets
     */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(PolygoneRepository $repository): JsonResponse
    {
        $polygones = $repository->findBy([], ['id' => 'DESC']);

        return $this->json($polygones, Response::HTTP_OK, [], [
            'groups' => ['polygone:read']
        ]);
    }

    /**
     * 2. GET /api/polygones/{id} : Détail d'un polygone
     */
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(?Polygone $polygone): JsonResponse
    {
        if (!$polygone) {
            return $this->json(['error' => 'Polygone non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($polygone, Response::HTTP_OK, [], [
            'groups' => ['polygone:read']
        ]);
    }

    /**
     * 3. POST /api/polygones : Enregistrement d'un nouveau polygone avec ses points
     *
     * Format JSON attendu :
     * {
     *   "nom": "Triangle 3-4-5",
     *   "points": [
     *     { "x": 1, "y": 1 },
     *     { "x": 7, "y": 1 },
     *     { "x": 1, "y": 9 }
     *   ]
     * }
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data || !isset($data['nom']) || trim($data['nom']) === '') {
            return $this->json(['error' => 'Le nom du polygone est obligatoire.'], Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['points']) || !is_array($data['points']) || count($data['points']) < 3) {
            return $this->json(['error' => 'Un polygone doit contenir au moins 3 points.'], Response::HTTP_BAD_REQUEST);
        }

        $polygone = new Polygone();
        $polygone->setNom(trim($data['nom']));

        foreach ($data['points'] as $index => $pointData) {
            if (!isset($pointData['x']) || !isset($pointData['y']) || !is_numeric($pointData['x']) || !is_numeric($pointData['y'])) {
                return $this->json([
                    'error' => sprintf("Le point à l'index %d possède des coordonnées invalides (x et y doivent être des nombres).", $index)
                ], Response::HTTP_BAD_REQUEST);
            }

            $point = new Point();
            $point->setX((float) $pointData['x']);
            $point->setY((float) $pointData['y']);
            $polygone->addPoint($point);
        }

        $em->persist($polygone);
        $em->flush();

        return $this->json($polygone, Response::HTTP_CREATED, [], [
            'groups' => ['polygone:read']
        ]);
    }

    /**
     * 4. DELETE /api/polygones/{id} : Suppression d'un polygone (et de ses points en cascade)
     */
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(?Polygone $polygone, EntityManagerInterface $em): JsonResponse
    {
        if (!$polygone) {
            return $this->json(['error' => 'Polygone non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($polygone);
        $em->flush();

        return $this->json([
            'success' => true,
            'message' => sprintf('Polygone #%d supprimé avec succès.', $polygone->getId())
        ], Response::HTTP_OK);
    }
}
