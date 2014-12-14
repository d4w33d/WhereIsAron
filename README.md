
# Where Is Aron

Application web basée sur Symfony.

## Avant-propos

Ceci est une démo faite en ~ 1,5 jour. L'application n'est donc pas finalisée ni
optimisée, mais a pour but de montrer un exemple fonctionnel sous Symfony2.

La crontache est développée (cf. src/AppBundle/Command/ActivitiesCommand.php)
mais n'envoie pas d'emails ni de SMS.

Les endroit comportant du code métier sont :

* app/Resources : layout HTML et assets
* src/AppBundle : bundle de l'application (a terme, peut être divisé en
    plusieurs bundles - notamment Front, User et Activity pour les
    fonctionnalités présentes)
    * Contrôleurs
    * Ressources spécifiques à l'application
    * Entités doctrine (User - étendu de FOS, Activity)

## Notes techniques

* **Symfony 2.6**
* **PostgreSQL**
* **FOSUserBundle** pour la gestion des utilisateurs
* **Apache**

## Fonctionnalités

L'idée de cette application est de proposer un service de "ping out IRL :)",
pour éviter un plan à la Aron Ralston (*127 heures*).

* Rando improvisée, petite session VTT, sortie en mer, etc. ;
* Il peut être bien de prévenir ses proches d'où on est si jamais il nous
    arrive un problème ;
* Le service permet de renseigner :
    * L'activité que l'on compte faire et l'itinéraire suivi ;
    * Les personnes à prévenir en cas de problème (téléphone ou email) ;
    * Un délai au bout duquel, si nous n'avons pas donné signe de vie,
        les proches sont prévenus automatiquement, avec jointes les
        informations sur l'activité renseignées préalablement, afin qu'ils
        puissent tenter de nous joindre ou, au besoin, prévenir les secours.

### Actuel

* **Login**, **Signup**, **Gestion de compte** basique ;
* Ajout et paramétrage d'une **activité** ;
* **Ping** depuis le web ;
* **Envoi d'un email** aux adresses renseignées, passé le délai imparti.

### Évolution

* Permettre de renseigner des numéros de téléphone pour envoi d'un **SMS** ;
* Version **responsive** ;
* Application mobile (**Cordova**) ;
* Possibilité de **pinger par l'envoi d'un SMS** (Internet n'étant pas toujours
    disponible) a un numéro spécifique, en renseignant un token défini par
    utilisateur ;

## Notes d'installation

### Crontache

La crontache doit être idéalement exécutée toutes les minutes, et la commande
est la suivante :

    $ php app/console app:activities

## Licence

*MIT Licensed* (voir fichier **LICENSE**).
