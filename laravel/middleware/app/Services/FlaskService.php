<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class FlaskService
{
    protected $flaskUrl;

    public function __construct()
    {
        $this->flaskUrl = env('FLASK_API_URL'); // URL de l'API Flask
    }

    public function createProductInOdoo($product)
    {
        // Créer l'URL complète pour l'API Flask
        $url = $this->flaskUrl . '/create_product';

        // Données à envoyer à Flask
        $data = [
            'name' => $product['name'],
            'list_price' => $product['price'],
            'description' => $product['description'],
        ];

        try {
            // Envoyer la requête POST à Flask
            $response = Http::post($url, $data);

            // Vérifier si la requête est réussie
            if ($response->successful()) {
                return $response->json(); // Retourner la réponse JSON
            } else {
                throw new Exception('Erreur lors de la création du produit dans Odoo via Flask.');
            }
        } catch (Exception $e) {
            throw new Exception('Erreur lors de la connexion avec Flask : ' . $e->getMessage());
        }
    }
}
