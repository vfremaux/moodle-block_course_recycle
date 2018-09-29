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

$string['course_recycle:addinstance'] = 'Ajouter un bloc Recyclage du Cours';
$string['course_recycle:view'] = 'Voir le bloc Recyclage du cours';
$string['course_recycle:admin'] = 'Administrer le recyclage des cours';

// Privacy
$string['privacy:metadata'] = 'Bien que l\'enseignant puisse modifier l\'option de recyclage, cette donnée est dans la portée du cours et non de l\'utilisateur.';

$string['active'] = 'Visible';
$string['addeverywhere'] = 'Ajouter une instance à tous les cours';
$string['archive'] = 'En fin de période, archiver';
$string['backtocourse'] = 'Revenir au cours';
$string['atendofsession'] = 'En fin de période';
$string['blockname'] = 'Recyclage du cours';
$string['choicelocked'] = 'Le choix du mode de recyclage est fermé. Contactez vos administrateurs.';
$string['configblockstate'] = 'Etat du bloc';
$string['configdefaultaction'] = 'Action par défaut ';
$string['confignumberofnotifications'] = 'Notifications ';
$string['configrecycleaction'] = 'Action de recyclage ';
$string['configchoicedone'] = 'Choix enregistré';
$string['configstopnotify'] = 'Ne plus envoyer de rappels';
$string['configinstancesperrun'] = 'Nombre de cours par cron.';
$string['configarchivestrategy'] = 'Stratégie d\'archivage';
$string['configarchivefactory'] = 'Plate-forme d\'archive';
$string['configarchivesbackupdir'] = 'Répertoire de stockage des archives';
$string['configlogfile'] = 'Fichier journal du recyclage';
$string['inactive'] = 'Caché';
$string['keep'] = 'En fin de période, conserver sans aucune modification';
$string['locked'] = 'Verrouilé';
$string['nonotifications'] = 'Pas de notifications';
$string['opentill'] = 'Vous pouvez encore changer l\'opération de fin d\'année jusqu\'au {$a}.';
$string['pluginname'] = 'Recyclage du cours';
$string['recycle'] = 'Gérer le recyclage';
$string['reminded1'] = 'Premier rappel émis';
$string['reminded2'] = 'Deuxième rappel émis';
$string['reminded3'] = 'Troisième rappel émis';
$string['reset'] = 'En fin de période, réinitialiser';
$string['stopallnotifications'] = 'Ne plus m\'envoyer de rappel pour aucun cours';
$string['stopnotifications'] = 'Ne plus me rappeler pour ce cours';
$string['task_recycle'] = 'Recyclage des cours : nettoyage';
$string['task_show'] = 'Recyclage des cours : Activation des choix';
$string['task_reset'] = 'Recyclage des cours : Réinitialisation';
$string['task_lock'] = 'Recyclage des cours : Verrouillage des choix';
$string['throw'] = 'En fin de période, supprimer';

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
