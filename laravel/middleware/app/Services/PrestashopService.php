<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

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

}
