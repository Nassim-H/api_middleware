# Test technique : middleware entre Prestashop et Odoo

## Présentation

Ce projet est un middleware permettant de synchroniser les données entre Prestashop et Odoo.

### Prestashop

Prestashop est une solution e-commerce open-source. Il permet de créer une boutique en ligne et de gérer les commandes, les clients, les produits, etc.

### Odoo

Odoo est une suite d'applications open-source qui couvre tous les besoins d'une entreprise : CRM, e-commerce, comptabilité, inventaire, point de vente, gestion de projet, etc.

### Middleware

Le middleware est une application qui permet de synchroniser les données entre Prestashop et Odoo. Il permet de récupérer les commandes de Prestashop et de les créer dans Odoo. Il permet également de récupérer les produits de Prestashop et de les créer dans Odoo.

## Objectifs 

L'objectif de ce test technique est de créer un middleware permettant de synchroniser les données entre Prestashop et Odoo.

Tout est en local, mais il faut qu'au moins une instance soit dans un conteneur Docker. 

## Installation

### Prestashop

Pour faciliter la mise en place de Prestashop, j'ai décidé d'utiliser Docker. Pour cela, il suffit de créer un fichier `docker-compose.yml` contenant le code suivant :

```yaml
version: '3'
services:
  mysql:
    container_name: some-mysql
    image: mysql:5.7
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: admin
      MYSQL_DATABASE: prestashop
    networks:
      - prestashop_network
  prestashop:
    container_name: prestashop
    image: prestashop/prestashop:latest
    restart: unless-stopped
    depends_on:
      - mysql
    ports:
      - 8080:80
    environment:
      DB_SERVER: some-mysql
      DB_NAME: prestashop
      DB_USER: root
      DB_PASSWD: admin
      PS_INSTALL_AUTO: 1
      PS_DOMAIN: localhost:8080
    networks:
      - prestashop_network
networks:
    prestashop_network:

```

Puis exécuter la commande suivante :

```bash
docker-compose up -d
```

L'application Prestashop est accessible à l'adresse `http://localhost:8080`.

### Rappel de Docker

Le fichier `docker-compose.yml` et la commande `docker-compose up -d` permettent de définir les services nécessaires pour l'application, les images Docker à utiliser, créer les conteneurs et les configurer. Il y a donc ici 2 conteneurs qui communiquent entre eux avec le réseau `presatshop-network`  

Il faut ensuite compléter l'installation dans l'adresse `http://localhost:8080` en suivant les étapes d'installation, notamment pour la base de données.

Pour finir, il faut renommer le dossier `admin` pour des raisons de sécurité. Il suffit d'entrer dans le conteneur Prestashop et de renommer le dossier :

```bash
docker exec -it prestashop bash
mv admin admin213z
```

Puis pour se connecter à l'administration de Prestashop, il suffit d'aller à l'adresse `http://localhost:8080/admin213z` et de se connecter avec les identifiants mis dans le fichier `docker-compose.yml`.

### Odoo

Il y a plusieurs manières d'installer Odoo, un ERP open-source. Un ERP, c'est un système d'information qui permet de gérer l'ensemble des processus d'une entreprise.

Pour ce projet, j'ai installé Odoo en local. Pour cela, j'ai installé le programme d'installation puis lancé le serveur Odoo.

### Middleware

J'ai décidé de faire le middleware en PHP Laravel. Pour cela, j'ai créé un projet Laravel et j'ai installé le package `prestashop-webservice-lib` pour communiquer avec l'API Prestashop.

Pour commencer le projet Laravel, il suffit de lancer la commande suivante :

```bash
composer create-project --prefer-dist laravel/laravel middleware
```





