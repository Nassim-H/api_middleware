<?php

namespace App\Http\Controllers;

use App\Services\PrestashopService;
use Illuminate\Http\Request;
use App\Services\OdooService;

class PrestashopController extends Controller
{
    protected $prestashopService;
    protected $odooService;

    public function __construct(PrestashopService $prestashopService, OdooService $odooService)
    {
        $this->prestashopService = $prestashopService;
        $this->odooService = $odooService;
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
    public function syncProductToOdoo($prestashopProductId)
    {
        // Récupérer le produit depuis Prestashop
        $product = $this->prestashopService->getProduct($prestashopProductId);
        
        // Préparer les données pour Odoo
        $odooProductData = [
            'name' => $product['name'],
            'list_price' => $product['price'],
            'description' => $product['description'],
        ];
    
        // Appeler le service Odoo pour créer le produit dans Odoo
        $productId = $this->odooService->createProduct($odooProductData);
    
        return response()->json(['message' => 'Produit synchronisé avec succès de Prestashop à Odoo.', 'product_id' => $productId]);
    }
    

}
