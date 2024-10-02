<?php

namespace App\Http\Controllers;

use App\Services\PrestashopService;
use Illuminate\Http\Request;

class PrestashopController extends Controller
{
    protected $prestashopService;

    public function __construct(PrestashopService $prestashopService)
    {
        $this->prestashopService = $prestashopService;
    }

    public function getProduct($id)
    {
        $product = $this->prestashopService->getProduct($id);

        return response()->json($product);
    }

}
