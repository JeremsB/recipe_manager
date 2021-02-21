# MyDigitalSchool - Recipe Manager

Projet réalisé à MyDigitalSchool utilisant un Framework de persistance (Symfony 4).

Accès en ligne : https://mds-recipe-manager.herokuapp.com/

## En cas de problème

Si la chance n'est pas de notre côté (donc si le lien marche pas), il sera eventuellement possible d'éxecuter l'application en local.

1. Installer le CLI de Symfony (disponible depuis la v5) : https://symfony.com/download

2. Configurer l'accès à la base de données en modifiant le fichier *.env*
   - Configuration de la base : `DATABASE_URL=mysql://root:@127.0.0.1:3306/my-recipe-manager?serverVersion=5.7`

3. Générer la base de données en local
   - Création de la base : `symfony console doctrine:databse:create`
   - Migration des données : `symfony console doctrine:migrations:migrate` (saisir *Y*)
   - Génération des fixtures : `symfony console doctrine:fixtures:load` (saisir *Y*)

4. Lancer le serveur local : `symfony server:start`
