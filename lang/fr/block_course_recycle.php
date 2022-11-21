<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package   block_course_recycle
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['course_recycle:addinstance'] = 'Ajouter un bloc Recyclage de Cours';
$string['course_recycle:myaddinstance'] = 'Ajouter un bloc Recyclage de Cours sur la page My';
$string['course_recycle:view'] = 'Voir le bloc Recyclage du cours';
$string['course_recycle:admin'] = 'Administrer le recyclage des cours';
$string['course_recycle:archive'] = 'Peut archiver les cours';
$string['course_recycle:student'] = 'Marque les rôles étudiants pour le recyclage';

// Privacy
$string['privacy:metadata'] = 'Bien que l\'enseignant puisse modifier l\'option de recyclage, cette donnée est dans la portée du cours et non de l\'utilisateur.';

$string['active'] = 'Visible';
$string['addeverywhere'] = 'Ajouter une instance à tous les cours';
$string['archive'] = 'Archiver';
$string['archiversettings'] = 'Paramètres de la plate-forme d\'archives';
$string['atendofsession'] = 'En fin de période';
$string['backtocourse'] = 'Revenir au cours';
$string['backtodashboard'] = 'Revenir au tableau de bord';
$string['backtoreport'] = 'Revenir au rapport';
$string['blockname'] = 'Recyclage du cours';
$string['byaccess'] = 'Par fréquentation';
$string['bydate'] = 'Par date de fin';
$string['bydate'] = 'Par date de fin';
$string['byenrols'] = 'Par inscriptions';
$string['choicelocked'] = 'Le choix du mode de recyclage est fermé. Contactez vos administrateurs.';
$string['configactiondelay'] = 'Délai d\'action';
$string['configactiondelay_desc'] = 'Le délai (en jours) avant que le recyclage n\'applique l\'action par défaut';
$string['configarchivefactory'] = 'Plate-forme d\'archive';
$string['configarchivesbackupdir'] = 'Répertoire de stockage des archives';
$string['configarchivestrategy'] = 'Stratégie d\'archivage';
$string['configdefaultiafcourses'] = 'Action par défaut';
$string['configdefaultiafcourses_desc'] = '';
$string['configaskowner'] = 'Demander au propriétaire';
$string['configaskowner_desc'] = 'Demander au propriétaire du cours avant d\'exécuter l\'action de recyclage';
$string['configblockstate'] = 'Etat du bloc';
$string['configchoicedone'] = 'Choix enregistré';
$string['configdecisiondelay'] = 'Délai de décision max';
$string['configdecisiondelay_desc'] = 'Le délai (en jours) pour exécuter l\'action de recyclage choisie';
$string['configdefaultaction'] = 'Action par défaut ';
$string['configdefaultactionfinishedcourses'] = 'Action par défaut pour les cours détectés comme terminés';
$string['configdefaultactionfinishedcourses_desc'] = 'L\'action impérative qui sera prise lorsque la condition explicite de fin de cours (par date de fin) est détectée.';
$string['configdefaultiafcourses'] = 'Action proposée par défaut (interactif)';
$string['configdefaultiafcourses_desc'] = 'L\'action par défaut proposée aux enseignants en mode interactif dans le bloc de décision de recyclage dans les cours.';
$string['configinstancesperrun'] = 'Nombre de cours par cron.';
$string['configlogfile'] = 'Fichier journal du recyclage';
$string['configminactiveaccesstomaintain'] = 'Nombre minimum d\'actifs';
$string['configminactiveaccesstomaintain_desc'] = 'Le nombre minimal d\'actifs en dessous duquel un cours est considéré comme abandonné.';
$string['configminhitstomaintain'] = 'Nombre minimal de hits pour maintenir';
$string['configminhitstomaintain_desc'] = 'If there is more than this amount of hits in the course within the inactive delay examination period, than maintain the course alive.';
$string['configmininactivedaystofinish'] = 'Jours inactifs à partir de la date courante';
$string['configmininactivedaystofinish_desc'] = 'The amount of days to backscan for user access to check if a course is potentially finished.';
$string['confignumberofnotifications'] = 'Notifications ';
$string['configrecycleaction'] = 'Action de recyclage ';
$string['configrequestforarchivenotification'] = 'Notifications de demandes d\'archivage';
$string['configrequestforarchivenotification_desc'] = 'Si actif, des notifications sont envoyées lorsqu\'un cours demande son archivage.';
$string['configretirecategory'] = 'Catégorie des cours retirés';
$string['configretirecategory_desc'] = 'Une catégorie (à cacher) de cours retirés de l\'exploitation avant archivage.';
$string['configsourcewstoken'] = 'Token de la source';
$string['configsourcewstoken_desc'] = 'Token de web services de la plate-forme source des cours à archiver';
$string['configsourcewwwroot'] = 'Racine web de la source';
$string['configsourcewwwroot_desc'] = 'Racine web de la plate-forme source des cours à archiver';
$string['configstopnotify'] = 'Ne plus envoyer de rappels';
$string['confirmboard'] = 'Relevé des cours à recycler';
$string['confirmmycourses'] = 'Confirmer les actions de clôture du cours';
$string['datachanged'] = 'Les changements ont été enregistrés';
$string['detectcourses'] = 'Détecter manuellement les cours terminés';
$string['directeditorsnum'] = 'Utilisateurs éditeurs';
$string['dorecycle'] = 'Lancer la tâche d\'archivage';
$string['finishedcoursessettings'] = 'Détection des cours terminés';
$string['inactive'] = 'Caché';
$string['interactivesettings'] = 'Réglages du mode interactif';
$string['interactivesettingshelp'] = 'Les paramètres qui suivent impactent l\'usage interactif de la fonction de recyclage lorsqu\'elle est utilisée directement dans les cours.';
$string['keep'] = 'Conserver sans aucune modification';
$string['locked'] = 'Verrouilé';
$string['nextactions'] = 'Prochaines actions';
$string['nextdate'] = 'Le';
$string['nocourses'] = 'Aucun cours à archiver';
$string['nonotifications'] = 'Pas de notifications';
$string['nopeer'] = 'Il n\'y aura personne pour décider !';
$string['notificationstopped'] = 'Notifications désactivées';
$string['opentill'] = 'Vous pouvez encore changer l\'opération de fin d\'année jusqu\'au {$a}.';
$string['pluginname'] = 'Recyclage du cours';
$string['reason'] = 'Raison';
$string['recycle'] = 'Gérer le recyclage';
$string['recycleaction'] = 'Actions';
$string['reminded1'] = 'Premier rappel émis';
$string['reminded2'] = 'Deuxième rappel émis';
$string['reminded3'] = 'Troisième rappel émis';
$string['reset'] = 'Réinitialiser';
$string['retire'] = 'Remiser';
$string['retire'] = 'Retirer le cours';
$string['rfanallets'] = 'A tous les enseignants éditeurs';
$string['rfannone'] = 'Pas de notification';
$string['rfanoldestet'] = 'A l\'enseignant éditeur le plus ancien';
$string['selectall'] = 'Tout sélectionner';
$string['stateyourcourses'] = 'Programmer la fermeture des cours';
$string['stopallnotifications'] = 'Ne plus m\'envoyer de rappel pour aucun cours';
$string['stopnotifications'] = 'Ne plus me rappeler pour ce cours';
$string['taskstate'] = 'Statut des tâches : ';
$string['task_discover_finished'] = 'Détection des cours terminés ';
$string['task_interactive_lock'] = 'Recyclage des cours : Verrouillage des choix';
$string['task_pull_and_archive'] = 'Import et archivage des cours';
$string['task_recycle_courses'] = 'Exécution des actions en attente ';
$string['task_interactive_recycle'] = 'Recyclage des cours : nettoyage';
$string['task_interactive_reset'] = 'Recyclage des cours : Réinitialisation';
$string['task_interactive_show'] = 'Recyclage des cours : Activation des choix';
$string['throw'] = 'Supprimer';
$string['unselectall'] = 'Tout désélectionner';
$string['updateaction'] = 'Mettre à jour l\'action de recyclage';
$string['withselection'] = 'Avec la sélection';

$string['archiversettings'] = 'Réglage du Moodle d\'archive';
$string['configsourcewwwroot'] = 'Url racine des sources';
$string['configsourcewwwroot_desc'] = 'URL Racine du moodle à archiver';
$string['configsourcetoken'] = 'Token de la source';
$string['configsourcetoken_desc'] = 'Token de web service du service de recyclage des cours';
$string['configaskowner'] = 'Demander au propriétaire';
$string['configaskowner_desc'] = 'Si activé, lors de la détection d\'un cours candidat au recyclage, on demande au propriétaire l\'action à effectuer
sur le cours (pendant un certain temps) avant d\'appliquer l\action par défaut';
$string['configpolicyenddate'] = 'Détecter par date de fin';
$string['configpolicyenddate_desc'] = 'Détecte les cours terminés par leur date de fin';
$string['configpolicyenrols'] = 'Detecter par les inscriptions';
$string['configpolicyenrols_desc'] = 'Détecte les cours terminés lorsqu\'ils n\'ont plus d\'inscriptions actives.';
$string['configpolicylastaccess'] = 'Detecter sur dernier access';
$string['configpolicylastaccess_desc'] = 'Déctecte les cours finis sur la base des enregsitrements de "dernier accès au cours"';
$string['configpreservesourcecategory'] = 'Préserver la catégorie d\'origine';
$string['configpreservesourcecategory_desc'] = 'Si activé, les archives seront stockées dans une catégorie similaire à celle
de leur plate-forme d\'origine. Les catégories non existantes seront ajoutées au premier archivage. Sinon, c\'est la politique du plugin de transport de cours 
associé à la fonction d\'archivage qui est respectée.';

$string['nocoursestoarchive'] = 'Aucun cours à archiver';

$string['Ask'] = 'Demander au propriétaire';
$string['RequestForArchive'] = 'Archivage envisagé';
$string['Done'] = 'Traitement terminé';
$string['Failed'] = 'Echec de l\'archivage';
$string['Stay'] = 'Ne rien faire';
$string['Reset'] = 'Réinitialiser';
$string['Clone'] = 'Cloner';
$string['CloneAndReset'] = 'Cloner et réinitialiser la copie';
$string['Archive'] = 'Archiver et conserver';
$string['ArchiveAndDelete'] = 'Archiver et supprimer';
$string['ArchiveAndReset'] = 'Archiver puis réinitialiser';
$string['ArchiveAndRetire'] = 'Archiver et remiser';
$string['ArchiveCloneAndReset'] = 'Archiver l\'original, cloner et réinitialiser la copie';
$string['Delete'] = 'Supprimer totalement le cours (définitif)';
$string['Retire'] = 'Déplacer le cours dans la catégorie {$a}';

$string['throwhdr'] = 'Supprimer';
$string['resethdr'] = 'Réinitialiser';
$string['archivehdr'] = 'Archiver';
$string['keephdr'] = 'Conserver tel quel';
$string['unsethdr'] = 'Non déterminé';

$string['defaultnotification_title_tpl'] = 'Avis de fin de période %NOTIF% : %COURSE%';
$string['defaultnotification_tpl'] = 'Ce cours va devoir être recyclé le %TASKDATE%. Vous pouvez encore choisir quelle action sera
faite sur ce cours à cette date. Si vous ne faites aucun choix, votre cours sera %DEFAULTACTION%.

Rendez-vous <a href="%COURSEURL%">dans votre cours</a> pour faire votre choix ou pour désactiver ces notifications.
';

$string['requestforarchive_title_tpl'] = '[%SITENAME%] : Des nouvelles demandes d\'archivage sont en attente';
$string['requestforarchive_tpl'] = 'Des nouveaux cours dont vous êtes éditeur semblent être terminés. Vous pouvez
choisir l\'action d\'archivage qui sera conduite sur ces cours.

Visitez l\'url <a href="<%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%>"><%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%></a> pour faire part de votre décision.
';

$string['defaultaction_title_tpl'] = '[<%%SITENAME%%>] : Un de vos cours va être recyclé';
$string['defaultaction_tpl'] = '
<p>Le cours [<%%SHORTNAME%%>] <%%FULLNAME%%> que vous détenez va être recyclé avec une action <%%ACTION%%>.</p>

<p>Vous pouvez encore visiter l\'URL <a href="<%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%>"><%%WWWROOT%%>/login/index.php?ticket=<%%TICKET%%></a> pour changer la destination du cours.</p>
';
$string['configdefaultaction_desc'] = 'ce qu\'il adviendra du cours lorsque la tâche de fin d\'année est exécutée.';

$string['confignumberofnotifications_desc'] = 'Le nombre de notifications à envoyer avant la date d\'éxécution. Les notifications
seront envoyées à 15 jours d\'intervalle.';

$string['confignotificationtext'] = 'Texte de notification ';
$string['confignotificationtext_desc'] = 'Le texte à envoyer. Ce texte peut utiliser les emplacements %NOTIF% pour le numéro
de notification, %REMAININGNOTIFS% pour le nombre de notifications restantes et %TASKDATE% pour indiquer la date de traitement.';

$string['configblockstate_desc'] = 'Cette variable d\'état contrôle le cycle de fonctionnement du recyclage de cours quelque soit le cours.
Le composant a un automate unique valable pour tout le site qui affecte la phase de fonctionnement de toutes les instances de ce bloc.
Les administrateurs peuvent changer l\'état pour une mise en place. Les états sont ensuite modifiés par la programmation de tâches
programmées associées au module.
';

$string['configinstancesperrun_desc'] = 'Nombre de cours recyclés par exécution de cron.';

$string['configarchivestrategy_desc'] = 'Stratégie d\'archivage des cours. La stratégie par défaut est la génération d\'une sauvegarde
particulière dans un répertoire dédié aux archives. D\'autres plugins tiers peuvent proposer des méthodes d\'archivage alternatives
(C.f. bloc publisflow)';

$string['configarchivefactory_desc'] = 'Si le bloc Publishflow est installé, une des "fabriques" disponibles servant de plate-forme d\'archives.';

$string['configarchivesbackupdir_desc'] = 'Un répertoire système où stocker les sauvegardes d\'archive pour la stratégie standard. Il doit être
indiqué par son chemin absolu dans le système eet doit être inscriptible par le propriétaire de la tâche de recyclage.';

$string['configlogfile_desc'] = 'L\'emplacement d\'un éventuel fichier journal pour logger le résultat du recyclage. Si il est vide, 
aucune journalisation ne sera faite. Le chemin peut être déterminé relativement au répertoire de fichiers de Moodle en commençant le chemin
par %DATAROOT%';
