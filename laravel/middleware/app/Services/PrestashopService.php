<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class PrestashopService
{
    protected $url;
    protected $apiKey;

    public function __construct()
    {
        $this->url = env('PRESTASHOP_URL');
        $this->apiKey = env('PRESTASHOP_API_KEY');
    }

    public function getProduct($id)
    {
        $response = Http::withBasicAuth($this->apiKey, '')
            ->get("{$this->url}/products/{$id}?output_format=JSON");

        if ($response->failed()) {
            return [
                'error' => 'Failed to fetch product',
                'status' => $response->status()
            ];
        }

        return $response->json();
    }

    public function createProduct($data)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
            <prestashop xmlns:xlink="http://www.w3.org/1999/xlink">
                <product>
                    <state><![CDATA[' . $data['state'] . ']]></state>
                    <price><![CDATA[' . $data['price'] . ']]></price>
                    <name>
                        <language id="1"><![CDATA[' . $data['name'] . ']]></language>
                    </name>
                    <description>
                        <language id="1"><![CDATA[' . $data['description'] . ']]></language>
                    </description>
                </product>
            </prestashop>';

        $response = Http::withBasicAuth($this->apiKey, '')
            ->withHeaders([
                'Content-Type' => 'application/xml',
            ])
            ->withBody($xml, 'application/xml')  
            ->post("{$this->url}/products");

        if ($response->failed()) {
            return [
                'error' => 'Failed to create product in Prestashop',
                'status' => $response->status(),
                'body' => $response->body(),
            ];
        }

        return $response->json();  
    }

    public function getAllProducts()
{
    try {
        $response = Http::withBasicAuth($this->apiKey, '')
        ->get("{$this->url}/products?output_format=JSON");

        if ($response->failed()) {
            throw new Exception('Erreur lors de la récupération des produits Prestashop.');
        }

        $products = $response->json();

        if (isset($products['products'])) {
            return $products['products'];
        }

        throw new Exception('Format de réponse inattendu de l\'API Prestashop.');
    } catch (Exception $e) {
        Log::error('Erreur lors de la récupération des produits Prestashop: ' . $e->getMessage());

        throw new Exception('Impossible de récupérer les produits Prestashop.');
    }
}

    public function getNewProducts()
{
    // Obtenir la liste des produits
    $products = $this->getAllProducts();

    // Filtrer les produits récents (par exemple en fonction de la date de création)
    $newProducts = collect($products)->filter(function ($product) {
        // Comparer la date d'ajout du produit avec une heure ou une date spécifique
        $productDate = \Carbon\Carbon::parse($product['date_add']);
        return $productDate->greaterThanOrEqualTo(now()->subMinute());
    });

    return $newProducts;
}

}
