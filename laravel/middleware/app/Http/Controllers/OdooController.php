<?php

namespace App\Http\Controllers;

use App\Services\OdooService;
use Illuminate\Http\Request;

class OdooController extends Controller
{
    protected $odooService;

    public function __construct(OdooService $odooService)
    {
        $this->odooService = $odooService;
    }

    public function testAuth()
{
    try {
        $uid = $this->odooService->authenticate();
        return response()->json(['UID' => $uid]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
        ], 500);
    }
}



    public function getProducts()
    {
        try {
            $products = $this->odooService->getProducts();
            return response()->json($products);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
