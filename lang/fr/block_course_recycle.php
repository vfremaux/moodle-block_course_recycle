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
 * @package     block_course_recycle
 * @category    blocks
 * @author      Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright   2013 onwards Valery Fremaux (http://www.mylearningfactory.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['course_recycle:addinstance'] = 'Ajouter un bloc Recyclage du Cours';
$string['course_recycle:view'] = 'Voir le bloc Recyclage du cours';
$string['course_recycle:admin'] = 'Administrer le recyclage des cours';

$string['atendofsession'] = 'En fin de période';
$string['throw'] = 'En fin de période, supprimer';
$string['reset'] = 'En fin de période, réinitialiser';
$string['keep'] = 'En fin de période, conserver sans aucune modification';
$string['blockname'] = 'Recyclage du cours';
$string['pluginname'] = 'Recyclage du cours';
$string['nonotifications'] = 'Pas de notifications';
$string['stopnotifications'] = 'Ne plus me rappeler pour ce cours';
$string['stopallnotifications'] = 'Ne plus m\'envoyer de rappel pour aucun cours';

$string['defaultnotification_title_tpl'] = 'Avis de fin de période %NOTIF% : %COURSE%';
$string['defaultnotification_tpl'] = 'Ce cours va devoir être recyclé le %TASKDATE%. Vous pouvez encore choisir quelle action sera
faite sur ce cours à cette date. Si vous ne faites aucun choix, votre cours sera %DEFAULTACTION%.

Rendez-vous <a href="%COURSEURL%">dans votre cours</a> pour faire votre choix ou pour désactiver ces notifications.
';

$string['task_recycle'] = 'Recyclage des cours en fin de session';

$string['configresetdate'] = 'Date de réinitialisation';
$string['configresetdate_desc'] = 'La date à laquelle le système de passage d\'année se réinitialise et les blocs se cachent';

$string['configshowdate'] = 'Date d\'apparition';
$string['configshowdate_desc'] = 'La date à laquelle les blocs apparaissent dans le cours s\'ils y sont.
Le bloc restera visible pour les enseignants jusqu\'à la date de réinitialisation';

$string['configrecycleaction'] = 'Action de recyclage ';

$string['configdefaultaction'] = 'Action par défaut ';
$string['configdefaultaction_desc'] = 'ce qu\'il adviendra du cours lorsque la tâche de fin d\'année est exécutée.';

$string['confignumberofnotifications'] = 'Notifications ';
$string['confignumberofnotifications_desc'] = 'Le nombre de notifications à envoyer avant la date d\'éxécution.
Les notifications seront envoyées à 15 jours d\'intervalle.';

$string['confignotificationtext'] = 'Texte de notification ';
$string['confignotificationtext_desc'] = 'Le texte à envoyer. Ce texte peut utiliser les emplacements %NOTIF% pour
le numéro de notification, %REMAININGNOTIFS% pour le nombre de notifications restantes et %TASKDATE% pour indiquer
la date de traitement.';
