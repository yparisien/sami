##Module Logger
###Rôle du module
Ce module gère l'ensemble des entités qui permettent de reconstituer l'historique d'utilisation du système S.A.M.I. 

###Types de données
####InputAction
Ensemble des actions effectuées sur l'interface graphique (IHM) du S. A.M.I.
Une entité InputAction est caractérisée par les éléments suivants :
 - Une date/heure : Moment où l'action est effectuée
 - Id utilisateur : Identifiant de l'utilisateur à l'origine de l'action
 - Action réalisée : Chaîne de caractères décrivant l'action réalisée

####InputDrug
Un InputDrug correspond au chargement d'un médicament dans le système S.A.M.I.
Les informations contenues dans cette entité sont relatives à l'activité du médicament au moment de son chargement ainsi que les valeurs de contrôle qui peuvent être effectuée par le système.

####InputFile
Historique des fichiers importés et exportés. L'entité contient au format binaire la valeur d'entrée et la valeur de sortie de chaque fichier.

####PatientHistory
Entité récapitulative d'un injection sur un patient. Cette entité contient des informations redondantes par rapport à d'autres tables. Le but de cette entité et de pouvoir reconstituer facilement un journal des injections et d'obtenir la majorité des informations sur un seul enregistrement. La table `patient_history` est alimentée à la fin de chaque injection.

### Persistance des données
Les données contenues dans l'ensemble des tables de ce modules sont stockées sans limite de durée. 
