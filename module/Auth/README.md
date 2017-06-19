##Module Auth
###Rôle du module
Ce module gère les différents moyens d'authentification de l'utilisateur.

L'authentification ce fait en 2 temps :
 - Temps 1 : L'utilisateur doit saisir le login d'un compte existant.
 - Temps 2 : Si l'utilisateur existe, le système lui demande de saisir son mot de passe

> /!\ ATTENTION /!\ : Si 3 essais consécutifs ne mènent pas à une authentification correcte, l'utilisateur est déconnecté. Il est redirigé au début du système d'authentification.

Il y a 3 types d'authentification : 
 1. L'authentification au démarrage du système : Permet de connecter un opérateur ou superviseur avant de démarrer un cycle d'injection.
 2. L'authentification d'injection : Cette authentification se produit avant chaque injection pour vérifier que l'utilisateur qui va lancer l'injection est bien celui qui c'est authentifié au démarrage du système.
 3. L'authentification de validation superviseur : Cette authentification est demandée lorsque l'opérateur tente une opération qui utilise des valeurs en dehors des limites calculées par la système. Il faut alors qu'un utilisateur avec des privilèges supérieurs (superviseur) confirme les valeurs saisies par l'opérateur.

###Gestion de la session
Le module Auth est en charge de la gestion de la session utilisateur PHP. La durée de vie de la session est précisée dans le code. La valeur est fixée à 7200 secondes, soit 2 heures.