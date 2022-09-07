# Loraux docker

Un remake de okcoco avec tout un tas de features en plus

1. Virtualisation avec docker pour éviter tout un tas de conflits de dépendances en fonction des machines
2. Reprise complete du système d'autenthification, avec connection via appli tierse fb, google etc..
3. harmonisation des différents environnements
4. Ajout de tâches automatiques pour tirer un maximum profit des limites des différentes API

## Getting Started

A faire pour installer depuis une autre machine.

1. Installer docker desktop https://docs.docker.com/desktop/install/ubuntu/ ne pas oublier (https://docs.docker.com/engine/install/ubuntu/#set-up-the-repository), ne pas oublier non plus https://docs.docker.com/engine/install/linux-postinstall/ pour ajouter notre user au docker group, pour éviter les problèmes de droits lors d'executions de commandes
sudo dpkg -i au lieu de sudo apt install dans la doc pour bien installer le deb file et non pas le package apt
2. ```git clone https://docs.docker.com/engine/install/ubuntu/#set-up-the-repository```
Enlever les auto scripts dans composer.json on les remettra après le build
3. ```docker compose build --pull --no-cache```
Problèmes possibles : problème d'authorisation (dans in ~/.docker/config.json remplacer credsStore par credStore)
4. ```docker compose up -d``` 
5. ```docker exec -it nom_du_conteneur_ou_id bash``` voir /bin/sh si il trouve pas bash dans les variables d'environement


# TODO

1. Reprendre processus d'authentification
2. gérer les differents codes d'erreurs pour toutes les request curl/guzzle (si pb connexion ac api)
 a. Pour google et youtube data api gérer les maximums de request
 b. mettre en place cron tous les matins pour avoir un minimum de contenu
3. Faire les formulaires et validateurs
4. Reprendre les migrations pour la bdd postgresql de docker
5. Reprendre les anciennes routes en annotation et les changer en attributs
6. chercher API pour les livres
7. ajouter sections favorites