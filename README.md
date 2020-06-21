# Todo & Co - P8
 
 Faire des améliorations sur un projet en cour. Todo & Co est une application qui permet de créer des tâches.
 
 ## Environnement de développement
 
 - Linux
 - Composer 1.6.3
 - PHP 7.2.24
 - Apache 2.4.29
 - MySQL 5.7,28
 - git 2.17.1
 
 ## Instalation
 
 Clonez le repository Github
 
 ```bash
 git clone https://github.com/ampueropierre/todolist-symfony-4.git
 ```
 
 Installer les dépendances
 
 ```
 composer install
 ```
 
 Créer la BDD
 
 ```
 php bin/console doctrine:database:create
 ```
 
 Créer les tables
 
 ```
 php bin/console doctrine:schema:create
 ```
 
 Installer la Fixture (démo de données fictives)
 
 ```
 php bin/console doctrine:fixture:load
 ```
 
 Lancer un serveur web avec la commande symfony
 
  ```
  symfony server:start
  ```
 
 Tester l'application avec un compte Admin
 > login: admin@domain.fr
 >
 > password: 0000
 
 <br>
 Enjoy !
 
 
