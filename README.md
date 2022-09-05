# Loraux docker

Un remake de okcoco avec tout un tas de features en plus

1. Virtualisation avec docker pour éviter tout un tas de conflits de dépendances en fonction des machines
2. Reprise complete du système d'autenthification, avec connection via appli tierse fb, google etc..
3. harmonisation des différents environnements
4. Ajout de tâches automatiques pour tirer un maximum profit des limites des différentes API

## Getting Started

A faire pour installer depuis une autre machine.

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