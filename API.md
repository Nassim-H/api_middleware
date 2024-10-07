# API Middleware : Synchronisation Prestashop et Odoo

## Présentation de l'API

Cette API permet de synchroniser des produits entre Prestashop et Odoo. Les différentes routes permettent d'ajouter des produits dans chaque plateforme, de les récupérer et de les synchroniser d'un système à l'autre.

## Sécurité et Authentification

Pour sécuriser l'API, une clé API statique est utilisée. En dehors des requête GET pour lister les produits, chaque requête doit inclure cette clé dans les en-têtes pour être autorisée. La clé API est définie dans le fichier `.env` de l'application Laravel sous la variable `API_KEY`.

### En-tête requis pour l'authentification

Les requêtes de création de produit doivent inclure l'en-tête suivant :

- **X-API-KEY** : *string* (La clé API définie dans le fichier `.env`)

### Exemple d'en-tête pour Insomnia ou Postman

```http
X-API-KEY: your_secret_api_key_here
```

## Routes

### 1. Récupérer un produit depuis Prestashop

**Description** : Récupère les détails d'un produit à partir de Prestashop en utilisant son ID.

- **Méthode** : GET
- **URL** : `/api/prestashop/products/{id}`
- **Paramètres** :
  - `id` : *integer* (ID du produit à récupérer)
- **En-tête** :
  - `X-API-KEY` : *string* (Clé API pour l'authentification)
- **Réponse** :
  - `200 OK` : Retourne les détails du produit.
  - `404 Not Found` : Produit non trouvé.

### 2. Créer un produit dans Prestashop

**Description** : Ajoute un nouveau produit dans Prestashop avec les données fournies.

- **Méthode** : POST
- **URL** : `/api/prestashop/products`
- **Paramètres** :
  - **Body** :
    - `state` : *integer* (État du produit)
    - `price` : *float* (Prix du produit)
    - `name` : *string* (Nom du produit)
    - `description` : *string* (Description du produit)
  - **En-tête** :
    - `X-API-KEY` : *string* (Clé API pour l'authentification)
- **Réponse** :
  - `201 Created` : Produit créé avec succès, retourne les détails du produit.
  - `400 Bad Request` : Erreur de validation des données.

### 3. Créer un produit dans Odoo depuis Flask

**Description** : Ajoute un produit dans Odoo via l'API Flask.

- **Méthode** : POST
- **URL** : `/api/odoo/products`
- **Paramètres** :
  - **Body** :
    - `name` : *string* (Nom du produit)
    - `list_price` : *float* (Prix de vente)
    - `description` : *string* (Description du produit)
  - **En-tête** :
    - `X-API-KEY` : *string* (Clé API pour l'authentification)  
- **Réponse** :
  - `201 Created` : Produit créé avec succès dans Odoo, retourne l'ID du produit.
  - `400 Bad Request` : Erreur de validation des données.
  - `500 Internal Server Error` : Erreur lors de la création du produit dans Odoo.

### 4. Synchroniser un produit de Prestashop vers Odoo

**Description** : Synchronise un produit spécifique de Prestashop vers Odoo en passant par l'API Flask.

- **Méthode** : GET
- **URL** : `/api/products/sync-to-odoo/{id}`
- **Paramètres** :
  - `id` : *integer* (ID du produit à synchroniser depuis Prestashop)
- **En-tête** :
  - `X-API-KEY` : *string* (Clé API pour l'authentification)
- **Réponse** :
  - `200 OK` : Produit synchronisé avec succès vers Odoo.
  - `404 Not Found` : Produit non trouvé dans Prestashop.
  - `500 Internal Server Error` : Erreur lors de la synchronisation vers Odoo.

### 5. Synchroniser un produit de Odoo vers Prestashop

**Description** : Synchronise un produit spécifique d'Odoo vers Prestashop.

- **Méthode** : GET
- **URL** : `/api/products/sync-to-prestashop/{id}`
- **Paramètres** :
  - `id` : *integer* (ID du produit à synchroniser depuis Odoo)
- **En-tête** :
  - `X-API-KEY` : *string* (Clé API pour l'authentification)
- **Réponse** :
  - `200 OK` : Produit synchronisé avec succès vers Prestashop.
  - `404 Not Found` : Produit non trouvé dans Odoo.
  - `500 Internal Server Error` : Erreur lors de la synchronisation vers Prestashop.

### 6. Lister tous les produits depuis Prestashop

**Description** : Récupère la liste de tous les produits disponibles dans Prestashop.

- **Méthode** : GET
- **URL** : `/api/prestashop/products`
- **Réponse** :
  - `200 OK` : Retourne la liste des produits sous forme de tableau.
  - `500 Internal Server Error` : Erreur lors de la récupération des produits.

## Instructions supplémentaires


### Exemple de requête pour la création d'un produit dans Prestashop (Insomnia ou Postman)

```http
POST /api/prestashop/product HTTP/1.1
Host: localhost:8000
Content-Type: application/json
X-API-KEY: your_secret_api_key_here

{
    "state": 1,
    "price": 29.99,
    "name": "Mon produit",
    "description": "Description de mon produit"
}

```

### Exemple de synchronisation de produit depuis Prestashop vers Odoo
```http

GET /api/products/sync-to-odoo/1 HTTP/1.1
Host: localhost:8000
X-API-KEY: your_secret_api_key_here

```

