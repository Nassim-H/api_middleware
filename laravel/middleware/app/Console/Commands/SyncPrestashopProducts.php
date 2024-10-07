<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PrestashopService;
use App\Http\Controllers\PrestashopController;
use Exception;

class SyncPrestashopProducts extends Command
{
    // Nom et description de la commande
    protected $signature = 'sync:prestashop-products';
    protected $description = 'Synchronise les nouveaux produits de Prestashop vers Odoo';

    protected $prestashopService;
    protected $odooController;

    public function __construct(PrestashopService $prestashopService, PrestashopController $odooController)
    {
        parent::__construct();
        $this->prestashopService = $prestashopService;
        $this->odooController = $odooController;
    }

    public function handle()
    {
        try {

            $products = $this->prestashopService->getAllProducts();

            foreach ($products as $product) {
                $productId = $product['id'];


                if ($this->isNewProduct($productId)) {
                    $this->info("Produit ID {$productId} est nouveau et n'est pas encore synchronisé.");
                    $this->odooController->syncProductToOdoo($productId);
                    $this->info("Produit ID {$productId} synchronisé avec succès.");
                    $this->markAsSynchronized($productId);
                } else {
                    $this->info("Produit ID {$productId} est déjà synchronisé.");
                }
            }

            $this->info('Synchronisation terminée.');
        } catch (Exception $e) {
            $this->error('Erreur lors de la synchronisation des produits : ' . $e->getMessage());
        }
    }

    protected function isNewProduct($productId)
    {
        $syncedData = $this->getSyncedProductIds();
        return !in_array($productId, $syncedData['prestashop_synced']);
    }

    protected function markAsSynchronized($productId)
    {
        $syncedData = $this->getSyncedProductIds();
        $syncedData['prestashop_synced'][] = $productId;

        $this->saveSyncedProductIds($syncedData);
    }

    protected function getSyncedProductIds()
    {
        $filePath = storage_path('app/synced_products.json');

        if (!file_exists($filePath)) {
            return ['prestashop_synced' => [], 'odoo_synced' => []];
        }

        $content = file_get_contents($filePath);
        return json_decode($content, true);
    }

    protected function saveSyncedProductIds(array $syncedData)
    {
        $filePath = storage_path('app/synced_products.json');
        file_put_contents($filePath, json_encode($syncedData, JSON_PRETTY_PRINT));
    }
}
