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
        $validated = $request->validate([
            'name' => 'required|string',
            'list_price' => 'required|numeric',
            'description' => 'required|string',
        ]);

        try {
            $product = $this->flaskService->createProductInOdoo(
                [
                    'name' => $validated['name'],
                    'price' => $validated['list_price'],
                    'description' => $validated['description'],
                ]
            );

            return response()->json(['success' => true, 'product' => $product], 201);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProduct($id)
    {
        $product = $this->flaskService->getProduct($id);

        return response()->json($product);
    }

    public function listAllProducts()
    {
        $products = $this->flaskService->getProducts();

        return response()->json($products);
    }
}
