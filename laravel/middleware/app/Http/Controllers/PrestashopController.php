<?php

namespace App\Http\Controllers;

use App\Services\PrestashopService;
use App\Services\FlaskService; // Importer le FlaskService pour communiquer avec Flask
use Illuminate\Http\Request;
use Exception;

class PrestashopController extends Controller
{
    protected $prestashopService;
    protected $flaskService;

    public function __construct(PrestashopService $prestashopService, FlaskService $flaskService)
    {
        $this->prestashopService = $prestashopService;
        $this->flaskService = $flaskService; // Initialisation du service Flask
    }

    public function getProduct($id)
    {
        $product = $this->prestashopService->getProduct($id);

        return response()->json($product);
    }

    public function createProduct(Request $request)
    {
        $data = $request->validate([
            'state' => 'required|integer',
            'price' => 'required|numeric',
            'name' => 'required|string',
            'description' => 'required|string',
        ]);

        $product = $this->prestashopService->createProduct($data);

        return response()->json($product);
    }

    /**
     * Synchroniser un produit de Prestashop vers Odoo via Flask
     */
    public function syncProductToOdoo($productId)
    {
        try {
            // Récupérer le produit depuis Prestashop
            $productarray = $this->prestashopService->getProduct($productId);
            $product = $productarray['product'];


        
            // Vérifier si le produit a bien été récupéré
            if (!$product) {
                throw new Exception('Produit introuvable dans Prestashop.');
            }

            // Préparer les données pour l'API Flask (adapter les champs selon ce que Flask/Odoo attend)
            $productData = [
                'name' => $product['name'],               // Nom du produit
                'price' => $product['price'],        // Prix de vente (ou utiliser un autre champ de Prestashop)
                'description' => $product['description'], // Description du produit
                // Ajouter d'autres champs ici si nécessaire
            ];

            // Envoyer les données à Flask pour créer le produit dans Odoo
            $response = $this->flaskService->createProductInOdoo($productData);

            // Retourner la réponse à l'utilisateur
            return response()->json([
                'success' => true,
                'message' => 'Produit synchronisé avec succès dans Odoo.',
                'product_in_odoo' => $response
            ], 200);

        } catch (Exception $e) {
            // En cas d'erreur, renvoyer une réponse avec un message d'erreur
            return response()->json([
                'error' => 'Erreur lors de la synchronisation du produit.',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }
}
