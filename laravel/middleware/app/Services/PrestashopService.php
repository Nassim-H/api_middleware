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
}
