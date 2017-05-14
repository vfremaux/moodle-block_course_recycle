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

$string['active'] = 'Visible';
$string['addeverywhere'] = 'Ajouter une instance à tous les cours';
$string['archive'] = 'En fin de période, archiver';
$string['atendofsession'] = 'En fin de période';
$string['blockname'] = 'Recyclage du cours';
$string['choicelocked'] = 'Le choix du mode de recyclage est fermé. Contactez vos administrateurs.';
$string['configblockstate'] = 'Etat du bloc';
$string['configdefaultaction'] = 'Action par défaut ';
$string['confignumberofnotifications'] = 'Notifications ';
$string['configrecycleaction'] = 'Action de recyclage ';
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
