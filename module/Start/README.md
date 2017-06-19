##Module Start
###Rôle du module
Ce module gère le (re)démarrage de l 'IHM. Ce module effectue plusieurs contrôles et remonte les erreurs en provenance de l'automate. Plusieurs systèmes de protections sont mis en place pour éviter l'appel non autorisé à certaines fonctionnalités.

###Contrôles du système
####Vérification des rôles ACL
3 rôles sont définis : 
- Guest : n'importe quel utilisateur non authentifié
- Operator : Utilisateur authentifié avec un privilège opérateur
- Supervisor : Utilisateur authentifié avec un privilège superviseur

L'accès aux routes (écrans) est limité suivant le type d'utilisateur qui tente d'y accéder.
Les règles pour chaque type d'utilisateur sont définis dans le fichier `config/module.acl.roles.php`

####Contrôle de l'état de l'interface
L'utilisateur ne peut accéder aux fonctionnalités de l'interface que si un ensemble de variables de contrôles sont vérifiés. Ces variables diffèrent selon l'étape de traitement ou se situe l'utilisateur. Si l'état de l'interface n'autorise pas l'utilisateur a effectuer une action, toute tentative d'accès à un écran effectuant cette action sera refusée.

####Contrôle de l'état de l'automate
L'automate peut à tout moment être victime d'une avarie ou renconter un problème. Le module `Start` a pour rôle de scruter l'état de l'automate et de faire remonter un éventuel code d'erreur sous forme graphique.

### Séquence de (re)démarrage de l'interface
Le démarrage de l'interface effectue un ensemble de vérification et de contrôles pour positionner l'utilisateur sur l'écran adéquat en fonction des informations en provenance de l'automate.
Le chargement de l'interface effectue les contrôles suivant à chaque démarrage ou redémarrage de l'interface en interrogeant l'automate : 
 1. Vérification de la mise sous tension de l'automate
 2. Chargement de l'unité de mesure utilisée dans les calculs (MBq ou MCi)
 3. Chargement de la base des radionucléides
 4. Vérification de la présence d'un médicament dans l'automate et chargement des informations
 5. Vérification de la connexion d'un kit source à la machine et récupération du numéro de série
 6. Récupération du point de démarrage de l'interface