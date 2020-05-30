# MyDigitalSchool - Recipe Manager

Projet réalisé à MyDigitalSchool utilisant un Framework de persistance (Symfony 4).

Accès en ligne: https://mds-recipe-manager.herokuapp.com/

# Si la chance n'est pas de notre côté (donc si le lien marche pas snif), on peut tester l'application en local

1. Installer le CLI de Symfony (disponible depuis la v5) : https://symfony.com/download
2. Configurer dans le .ENV la base de données en fonction du besoin. Config de base : 
DATABASE_URL=mysql://root:@127.0.0.1:3306/my-recipe-manager?serverVersion=5.7
3. Pour créer la base de de données en local
3.1 `symfony console doctrine:databse:create`
3.2 `symfony console doctrine:migrations:migrate`, click Y
3.3 `symfony console doctrine:fixtures:loaad`, click Y
And now you have a beautiful database with fixtures

4. You can run server with : `symfony server:start`
