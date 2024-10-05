<?php

namespace App\Services;

use Exception;

class OdooService
{
    protected $url;
    protected $db;
    protected $username;
    protected $password;
    protected $uid;

    public function __construct()
    {
        $this->url = env('ODOO_URL');
        $this->db = env('ODOO_DB');
        $this->username = env('ODOO_USERNAME');
        $this->password = env('ODOO_PASSWORD');
        $this->uid = $this->authenticate();
    }

    // Méthode pour envoyer une requête XML-RPC via cURL
    private function sendRpcRequest($endpoint, $xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url . "/xmlrpc/2/" . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: text/xml',
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);

        return $response; // Le XML-RPC renvoyé sera en XML, à parser ensuite
    }

    // Authentification auprès d'Odoo
    public function authenticate()
    {
        // Construire le XML manuellement pour la requête d'authentification
        $xml = "<?xml version='1.0'?>
        <methodCall>
            <methodName>authenticate</methodName>
            <params>
                <param><value><string>{$this->db}</string></value></param>
                <param><value><string>{$this->username}</string></value></param>
                <param><value><string>{$this->password}</string></value></param>
                <param><value><array><data></data></array></value></param>
            </params>
        </methodCall>";

        $response = $this->sendRpcRequest("common", $xml);

        // Parser la réponse XML-RPC
        $parsedResponse = $this->parseXmlResponseAuth($response);

        if (!is_int($parsedResponse)) {
            throw new Exception("Authentication failed with Odoo");
        }

        return $parsedResponse; // Retourne l'UID utilisateur
    }

    // Méthode pour interagir avec les objets Odoo via l'API
    public function call($model, $method, $params = [], $kwargs = [])
    {
        // Construire le XML manuellement pour les appels de méthodes
        $xml = "<?xml version='1.0'?>
        <methodCall>
            <methodName>execute_kw</methodName>
            <params>
                <param><value><string>{$this->db}</string></value></param>
                <param><value><int>{$this->uid}</int></value></param>
                <param><value><string>{$this->password}</string></value></param>
                <param><value><string>{$model}</string></value></param>
                <param><value><string>{$method}</string></value></param>
                <param><value><array><data>";

        // Ajouter les paramètres de la méthode
        foreach ($params as $param) {
            if (is_array($param)) {
                $xml .= "<value><array><data>";
                foreach ($param as $p) {
                    $xml .= "<value><string>{$p}</string></value>";
                }
                $xml .= "</data></array></value>";
            } else {
                $xml .= "<value><string>{$param}</string></value>";
            }
        }

        $xml .= "</data></array></value></param>";
        $xml .= "</params></methodCall>";

        $response = $this->sendRpcRequest("object", $xml);
        return $this->parseXmlResponse($response);
    }

    // Méthode pour parser la réponse XML-RPC
    private function parseXmlResponseAuth($response)
    {
        // Convertir le XML en objet PHP
        $xml = simplexml_load_string($response);
        if ($xml === false) {
            throw new Exception("Failed to parse XML-RPC response");
        }
    
        // Extraire la valeur de la réponse XML-RPC (peut être un int dans le cas d'une authentification)
        return (int) $xml->params->param->value->int; // Assure-toi que cela renvoie un entier
    }
    

    private function parseXmlResponse($response)
{
    // Convertir le XML en objet PHP
    $xml = simplexml_load_string($response);
    if ($xml === false) {
        throw new Exception("Failed to parse XML-RPC response");
    }

    // Si la réponse contient des produits, elle sera sous forme de tableau, pas un entier
    $result = [];

    // Vérifie si c'est un tableau de résultats
    if (isset($xml->params->param->value->array->data->value)) {
        foreach ($xml->params->param->value->array->data->value as $item) {
            $product = [];
            foreach ($item->struct->member as $member) {
                $name = (string) $member->name;
                $value = (string) $member->value->string ?? (string) $member->value;
                
                // Si la valeur est un int ou un double, les récupérer correctement
                if (isset($member->value->int)) {
                    $value = (int) $member->value->int;
                } elseif (isset($member->value->double)) {
                    $value = (float) $member->value->double;
                }
                
                $product[$name] = $value;
            }
            $result[] = $product;
        }
    }

    return $result;
}


    // Exemple de méthode pour récupérer les produits dans Odoo
    public function getProducts()
    {
        return $this->call('product.product', 'search_read', [[]], ['fields' => ['id', 'name', 'list_price']]);
    }
}
