##Module Bufferspace
###Rôle du module
Ce module gère des informations d'injections et des patients en provenance de la source de donnée extérieur.

Le module à 3 rôles :
 - Rôle 1 : Récupérer les informations patients et injections en provenance du fichier CSV lors de l'import pour alimenter les tables `tmp_patient` et `tmp_injection`
 - Rôle 2 : Lister / modifier / Supprimer les informations en provenance des tables  `tmp_patient` et `tmp_injection`. Administrer les vues correspondantes.
 - Rôle 3 : Agréger les informations des tables  `tmp_patient` et `tmp_injection` dans le but de générer le fichier d'export lors de la fin d'un cycle d'injections.

### Persistance des données
Les données contenues dans les tables `tmp_patient` et `tmp_injection` sont temporaires.
Elles sont purgées avant chaque import et après chaque export.  Ce module n'a pas vocation à historiser l'ensemble des patients et des injections réalisées par le système S.A.M.I. 
Le module `Logger` se charge de l'historisation des données. 