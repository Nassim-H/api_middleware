<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('sync:products', function () {
    $this->comment('Synchronisation des produits venant de Prestashop...');
    $this->call('sync:prestashop-products');
    $this->info('Produits synchronisés avec succès !');
})->purpose('Synchroniser les produits de Prestashop vers Odoo');
