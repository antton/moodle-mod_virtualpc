<?php
// This file is part of Virtual PC module.
//
// Virtual PC  is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Virtual PC is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 *  Join page to a virtual pc machine
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/uds_class.php');
require_once(dirname(__FILE__).'/locallib.php');

$id = required_param('id', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_ALPHANUM);

if (!$cm = get_coursemodule_from_id('virtualpc', $id)) {
    error('Course Module ID was incorrect');
}

$cond = array('id' => $cm->course);
if (!$course = $DB->get_record('course', $cond)) {
    error('Course is misconfigured');
}

$cond = array('id' => $cm->instance);
if (!$virtualpc = $DB->get_record('virtualpc', $cond)) {
    error('Course module is incorrect');
}

require_login ($course, true, $cm);

$context = context_module::instance($cm->id);

// Print the page header.
$url = new moodle_url('/course/join.php', array ('id' => $course->id));

$PAGE->set_url($url);

$PAGE->set_title(format_string($virtualpc->name));
$PAGE->set_heading($course->fullname);

if (has_capability('mod/virtualpc:join', $context) && confirm_sesskey($sesskey)) {

    $broker = uds_login();

    $pool = uds_servicespools_byname($broker, $virtualpc->poolname);

    $ticketid = uds_tickets_create($broker, $USER->username, $pool->id,
                      $USER->firstname . " " . $USER->lastname);

    uds_logout($broker);

    if ($pool->id or $ticketid > 0) {

        add_to_log($course->id, 'virtualpc', "join", "view.php?id={$cm->id}",
        'The user with id \''.$USER->id.'\' joined the virtualpc activity with course module id \'' .
            $cm->id .'\'', $cm->id, $USER->id );

        if (preg_match('/^https/i', get_config('virtualpc', 'serverurl'))) {
            $target = get_config('virtualpc', 'serverurl') . '/tkauth/' . $ticketid;
        } else {
            $target = get_config('virtualpc', 'serverurl') . ':' .
                      get_config('virtualpc', 'serverport') . '/tkauth/'.$ticketid;
        }

        redirect($target);

    } else {
        $msg = get_string('idpoolnotfound', 'virtualpc', $pool->id);
    }

} else {
        $msg = get_string('usernotenrolled', 'virtualpc');
}

echo $OUTPUT->header();

notice ($msg, new moodle_url('/course/view.php', array('id' => $course->id)));

// Finish the page.
echo $OUTPUT->footer();