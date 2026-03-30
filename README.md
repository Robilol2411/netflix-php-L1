CINEMAX

Bienvenue sur notre site, qui vous donne accès à l'ensemble des films du moment. Vous y trouverez des informations détaillées sur vos films préférés, la possibilité de les acheter, ainsi que la découverte de nouveaux films.

--------------------------------------------

Fonctionnalités :

- Création de compte et connexion (avec possibilité de changer le mot de passe).

- Barre de recherche permettant de rechercher tous les films disponibles via l’API.

- Panier pour acheter les films sélectionnés.

- Profil utilisateur permettant de modifier le mot de passe et d’afficher les films achetés.

- Détails des films accessibles en cliquant sur un film qui vous intéresse. 

--------------------------------------------

Installation :

1) Installation de Docker ici -->  https://www.docker.com/
   - Suivre les étapes d'installation
   - Redémarrer votre PC si nécessaire
   - Une fois installé, lancer Docker Desktop

2) Installer Visual Studio Code (si ce n’est pas déjà fait) --> https://code.visualstudio.com/download 
   - Suivre les étapes d'installation
   - Une fois installé, ouvrir VS Code 
   - 

3) Installation du projet sur votre VS Code 
   - Ouvrir un terminal
   - git clone https://github.com/Robilol2411/netflix-php-L1
   - Ensuite : cd php-crud

4) Configuration du projet dans VS Code
   - Dans le dossier baseDD : 
        - Créer un fichier env.php et y copier les données de env-exemple.php, en remplaçant par vos informations
        - Créer un fichier envapi.php et y copier les données de envapi-exemple.php, en y ajoutant votre propre clé API TMDB (obtenue sur https://www.themoviedb.org/)
        - Dans script.js mettre votre clé api

5) Installation de la Base de donnée
   - Ouvrir votre navigateur et aller sur : http://localhost:8080
   - Créer une nouvelle base de données
   - Donner un nom à la base et la créer
   - Aller dans la base puis cliquer sur Importer
   - Sélectionner le fichier monsite.sql situé dans le dossier sql de votre projet
   - Lancer l’importation

Votre site est maintenant fonctionnel.

--------------------------------------------

Compte avec des films : 
email : test@gmail.com
mot de passe : test

--------------------------------------------

Licence

Ce projet est sous licence MIT. 
