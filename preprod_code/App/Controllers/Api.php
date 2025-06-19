<?php

namespace App\Controllers;

use App\Models\Articles;
use App\Models\Cities;
use Core\View;
use Exception;
use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Mon API",
    version: "1.0.0",
    description: "Documentation de l'API pour les articles et villes",
    contact: new OA\Contact(email: "support@monapi.com")
)]
#[OA\Server(url: "http://localhost", description: "Environnement local")]
#[OA\Tag(name: "Articles", description: "Opérations sur les articles")]
#[OA\Tag(name: "Villes", description: "Recherche de villes")]
#[OA\Schema(
    schema: 'Product',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'title', type: 'string', example: 'Article exemple'),
        new OA\Property(property: 'description', type: 'string'),
        new OA\Property(property: 'price', type: 'number', format: 'float'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time')
    ]
)]
#[OA\Schema(
    schema: 'City',
    properties: [
        new OA\Property(property: 'id', type: 'integer'),
        new OA\Property(property: 'name', type: 'string'),
        new OA\Property(property: 'country', type: 'string'),
        new OA\Property(property: 'population', type: 'integer')
    ]
)]
class Api extends \Core\Controller
{
    #[OA\Get(
        path: "/products",
        summary: "Récupère la liste des articles",
        description: "Retourne tous les articles avec possibilité de tri",
        tags: ["Articles"],
        parameters: [
            new OA\Parameter(
                name: "sort",
                in: "query",
                description: "Critère de tri (name, date, price)",
                required: false,
                schema: new OA\Schema(type: "string", enum: ["name", "date", "price"])
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des articles",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Product")
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erreur serveur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function ProductsAction()
    {
        try {
            $sortOptions = ['name', 'date', 'price'];
            $query = $_GET['sort'] ?? null;

            if ($query && !in_array($query, $sortOptions)) {
                throw new Exception('Paramètre de tri invalide');
            }

            $articles = Articles::getAll($query);

            header('Content-Type: application/json');
            echo json_encode($articles);
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    #[OA\Get(
        path: "/cities",
        summary: "Recherche de villes",
        description: "Recherche des villes par nom",
        tags: ["Villes"],
        parameters: [
            new OA\Parameter(
                name: "query",
                in: "query",
                description: "Terme de recherche (min 2 caractères)",
                required: true,
                schema: new OA\Schema(type: "string", minLength: 2)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Résultats de recherche",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/City")
                )
            ),
            new OA\Response(
                response: 400,
                description: "Requête invalide",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            ),
            new OA\Response(
                response: 500,
                description: "Erreur serveur",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "string")
                    ]
                )
            )
        ]
    )]
    public function CitiesAction()
    {
        try {
            if (!isset($_GET['query']) || empty($_GET['query'])) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Paramètre \'query\' requis']);
                return;
            }

            $cities = Cities::search($_GET['query']);

            header('Content-Type: application/json');
            echo json_encode($cities);
        } catch (Exception $e) {
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Erreur interne du serveur']);
        }
    }
}