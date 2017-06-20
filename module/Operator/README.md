##Module Operator
###Rôle du module
Ce module gère l'ensemble des écrans qui permettent de :
 - Préparer l'automate pour un cycle d'injection (chargement de la source, import du fichier patients)
 - Réaliser les injections (accompagnement dans le déroulé du worflow d'une injection sur un patient)
 - Terminer le cycle d'injection (déchargement de la source, export du fichier patient)

###Entités gérées
####Kit patient (table patientkit)
A chaque injection d'un patient un nouveau kit patient doit être utilisé. Le module Operator a pour rôle de contrôler que le kit est bien connecté avant chaque injection et qu'il n'a pas été déjà utilisé lors d'une précédente injection. 

####Kit source (table sourcekit)
A chaque chargement d'un médicament un nouveau kit source doit être utilisé. Le module Operator a pour rôle de contrôler que le kit est bien connecté après chaque chargement et qu'il n'a pas été déjà utilisé lors de précédentes injections. 

###Saisies utilisateurs
Le cycle d'injection est un enchaînement d'écrans nécessitant des saisies utilisateurs. Le module Operator a pour but de transmettre les saisies utilisateurs à l'automate mais aussi de les logguer.
Les saisies utilisateurs doivent être considérées comme potentiellement erronées. Pour éviter de transmettre à l'automate des données erronées, le module Operator contient plusieurs mécaniques de contrôle des saisies utilisateurs : 
 1. Limitation des valeurs saisies : Les interfaces de ce module contiennent des champs de saisie qui ne sont pas libres et borne l'utilisateur dans sa saisie. Si une donnée doit être positive alors l'interface ne proposera qu'un champ de saisie capable de recevoir une valeur positive.
 2. Contrôle des valeurs saisies avant soumission : Certains champs peuvent être contrôlés au moment de leur saisie et avant que le formulaire qui les contient ne soit définitivement soumis par l'utilisateur. Ce mécanisme permet d'informer en temps réels les saisies erronées de l'utilisateur.
 3. Contrôle des valeurs saisies après soumission : Un dernier mécanisme de vérification est mis en place avant l'envoi des données à l'automate. Les tests réalisés sur les saisies ont pour but d'anticiper une éventuelle défaillance des mécanismes de vérifications précédents.

> Il peut arriver que des valeurs erronées parviennent à l'automate. Celui-ci est en mesure de le détecter et les signaler. Si tel est le cas l'erreur est remontée sous forme de message popup sur l'écran utilisateur.

###Echanges avec l'automate 
Les échanges avec l'automate sont généralement déclenchés à la suite de la saisie d'un ensemble d'informations dans les interfaces de gestion des injections. Le module Operator contient des fonctions capables de faire remonter ces informations à l'automate de manière non bloquante pour l'interface (appels AJAX). Le module Operator contient un ensemble de fonction faisant appel au service générique `RobotService` du module `Manager`.