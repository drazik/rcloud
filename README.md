# RCloud

Plateforme collaborative de développement R.

## Choix des technos

D'abord un petit tour des différentes technologies que j'ai utilisées pour arriver là où on en est.

### Symfony

Le projet utilise le framework [Symfony](http://symfony.com/). C'est un framework PHP utilisant le design pattern MVC ainsi qu'une architecture orientée services. Ce choix est motivé par le fait que Symfony impose de très bien organiser son code et pousse à la mise en place de bonnes pratiques de POO. De plus, le framework embarque de nombreux composants pratiques et fiables tels que Twig pour la gestion des vues, Doctrine pour la gestion de la base de données ou Swiftmailer pour l'envoi de mails. Tous ces composants simplifient le développement et nous permet de nous concentrer sur ce qu'on veut développer plutôt que tout ce qu'il y a autour.

Le seul bundle utilisé est le [FOSUserBundle](https://github.com/FriendsOfSymfony/FOSUserBundle/). Il permet de gérer les utilisateurs : inscription, connexion, oubli du mot de passe...

Le projet a été fait avec la version 2.3 de Symfony. Voir un peu plus loin concernant une éventuelle mise à jour de celui-ci.

### Ace

[Ace](http://ace.c9.io/#nav=about) est un éditeur de code écrit en JavaScript. Il est utilisé pour créer la zone d'édition de scripts.

### jQuery

[jQuery](http://jquery.com/) est utilisé pour manipuler facilement le DOM et faire les quelques requêtes AJAX qui sont faites (envoi du script et réception du résultat après exécution de celui-ci sur le serveur).

### Mustache

[Mustache](https://github.com/janl/mustache.js) est un moteur de template en JavaScript. Il est actuellement utilisé dans le projet uniquement pour la partie onglets de l'éditeur de scripts. Ce n'est pas quelque chose de très important.

### KNACSS

C'est un framework CSS qui fournit des classes génériques permettant de créer rapidement un layout. Je pense que ça ne vaut pas la peine de continuer à l'utiliser. Il m'a plus freiné qu'autre chose.

## Ce qui existe

Actuellement, le site permet toutes les actions élémentaires pour un utilisateur, à savoir :

* Inscription
* Connexion
* Récupération d'un mot de passe oublié
* Modification de mot de passe

Au niveau de l'exécution de code R, un éditeur est disponible et fonctionnel. Il permet d'ouvrir (et fermer !) différents onglets, d'y taper des scripts R, d'exécuter tout ou partie (en fonction de la sélection de l'utilisateur) d'un script, de sauvegarder un script, d'en ouvrir un préalablement sauvegardé...

## Organisation du code existant

Le code est divisé en 2 bundles : RBundle et UserBundle.

### RBundle

C'est ce bundle qui, comme son nom l'indique, se charge de la partie R de la plateforme. Il contient 2 contrôleurs :

* `EditorController` : affiche l'éditeur de scripts. Il ne fait que récupérer l'utilisateur courant, les scripts qu'il a enregistrés, et les passer à la vue se trouvant dans `/src/RCloud/Bundle/RBundle/Resources/views/Editor/show.html.twig`. C'est ensuite cette vue qui, grâce au code Javascript spaggheti qui se trouve en bas du fichier, gère le système d'onglet, l'envoi de scripts à exécuter au serveur, le chargement d'un script déjà enregistrer, l'enregistrement d'un script...
* `ScriptController` : gère l'exécution d'un script (méthode `runAction`); l'enregistrement d'un script (méthodes `saveNewScript` et `saveExistingScript`); le listage des scripts de l'utilisateur courant (méthode `listAction`) et la suppression d'un script (méthode `removeAction`)

L'exécution d'un script est actuellement gérée de la manière suivante :

* Récupération du script à exécuter (paramètre de la requête HTTP)
* Ecriture du script à exécuter dans un fichier créé dans un dossier "personnel" (dont le nom correspond au nom d'utilisateur de l'utilisateur)
* Exécution du script via la commande "R CMD BATCH" qui écrit le résultat de l'exécution dans un fichier
* Lecture du contenu du fichier contenant le résultat
* Récupération d'éventuels graphes générés par l'exécution du script
* Envoi du résultat et des graphes (s'il y en a) au client

### UserBundle

Ce bundle hérite du FOSUserBundle (situé dans le dossier `/vendor/friendsofsymfony/user-bundle`). Toutes les fonctionnalités de base d'un espace utilisateur (inscription, connexion, etc) sont gérées par le FOSUserBundle. En héritant de celui-ci, on peut donc juste ajouter les fonctionnalités souhaitées.

Par exemple, l'entité `User` hérite de l'entité générique fournie par FOSUserBundle et lui ajoute uniquement une liste de scripts correspondants aux scripts que l'utilisateur aura sauvegardés, donc qui lui appartiennent.

L'unique contrôleur que contient ce bundle, `DashboardController`, permet d'afficher ce que j'ai appelé le dashboard, mais qui est juste une page d'accueil qui s'affiche à l'utilisateur lorsqu'il se connecte à son compte. Cette page lui permet d'accéder à l'éditeur de code, à la liste de ses scripts, à son profil, et à un lien pour se déconnecter. Le contrôleur ne fait rien si ce n'est afficher la vue `/src/RCloud/Bundle/UserBundle/Resources/views/Dashboard/show.html.twig`.