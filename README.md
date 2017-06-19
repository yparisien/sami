Interface graphique S.A.M.I.
=======================
Introduction
------------------
L'interface graphique (IHM) S.A.M.I permet de piloter le système S.A.M.I. de DelphInnove.
L'IHM est une application web PHP basée sur le framework Zend Framework 2. Couplée à une base de donnée MySQL, elle permet de paramétrer l'accès à l'IHM et d'archiver l'ensemble des informations relatives aux injections. L'envoi et la réception de données en provenance de l'automate se fait par webservices en mode texte.

Installation
----------------
###Pré-requis
####Pour Windows
 1. Serveur IIS
 2. Web Plateform Installer
 3. PHP 5.6 ou supérieur
 4. Serveur MySQL Community 5.6 ou supérieur
 5. GIT
 
###Procédure d'installation
####Pour Windows 
 6. Ouvrir l'invite de commande Git Bash dans à la racine du disque système (C:)
 7. Lancer la commande git suivante : 
`git clone https://bitbucket.org/delphinnove/sami.git`
 8. Lancer la commande suivante :
`php public/index.php install` 