<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FlaskService; 
use Exception;

class FlaskController extends Controller
{
    protected $flaskService;

    public function __construct(FlaskService $flaskService)
    {
        $this->flaskService = $flaskService;
    }

    public function createProductInOdoo(Request $request)
    {
        // Valider les données d'entrée
        $validated = $request->validate([
            'name' => 'required|string',
            'list_price' => 'required|numeric',
            'description' => 'required|string',
        ]);

        try {
            // Appel du service Flask pour créer le produit dans Odoo
            $product = $this->flaskService->createProductInOdoo(
                [
                    'name' => $validated['name'],
                    'price' => $validated['list_price'],
                    'description' => $validated['description'],
                ]
            );

            // Retourner une réponse réussie
            return response()->json(['success' => true, 'product' => $product], 201);

        } catch (Exception $e) {
            // Gérer les erreurs
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
