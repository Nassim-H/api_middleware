<?php
while (true) {
    echo "Lancement de la commande sync:prestashop-products...\n";
    passthru('php artisan sync:prestashop-products');
    echo "Attente de 60 secondes avant la prochaine synchronisation...\n";
    sleep(60); 
}
