##Module Manager
###Rôle du module
Ce module gère l'ensemble des entités qui permettent paramétrer les utilisateurs, médicaments et examens dans le système.
L'accès aux interfaces de ce module n'est autorisé que pour les profils d'utilisateur de type Superviseur. Ce mdoule contient également la couche de service permettant de dialoguer avec l'automate

###Entités gérées
####Médicament (table drug)
Interfaces CRUD permettant la gestion des médicaments qui pourront être chargé dans le S.A.M.I
Les injections ne peuvent pas fonctionner sans qu'au moins un médicament ne soit paramétré dans le système.
Il faut également que le médicament soit associé à un radionucléides présent dans la base de donnée.

####Examen (table examination)
Interfaces CRUD permettant la gestion des examens qui pourront être lancé sur un patient. 
Aucune injection ne peut avoir lieu sans que la base examens soit remplie.
L'examen doit être associé à un médicament.

####Radionucléides (table radionuclide)
L'interface des radionucléides n'est que purement consultative. Elle ne peut être editée qu'en intervenant sur le code de l'automate. les radionucléides sont alimentés en base de donnée lors du chargement de l'interface. Le module `Start` est à l'origine de l'alimentation de la table `radionuclide`.

####Système (table système)
Interfaces CRUD permettant de régler quelques paramètres de l'IHM tel que la langue, l'activité maximale autorisée pour injection. Elle permet également de consulter les numéros de série et version de l'automate.

####Utilisateurs (table user)
Interfaces CRUD permettant de gérer la base utilisateur de l'IHM. 

### Webservices automate
Les webservices interagissants avec l'automate sont contenu dans la classe `module/Manager/src/Manager/Robot/RobotService.php`.
Les échanges se font par le protocole HTTP. La librairie Curl de PHP est utilisé pour établir la connexion avec l'automate. 
Les méthodes utilisées sont :
 - receive : pour récupérer des informations en provenance de l'automate
 - send : pour envoyer des instructions à l'automate
Si l'IHM est configurée en mode simulation, tous les appels de type `receive` sont simulés et les valeurs renvoyées hard-codées.