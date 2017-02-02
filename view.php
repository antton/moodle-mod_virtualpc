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
 * This page prints a particular instance of Virtual PC.
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once(dirname(__FILE__).'/uds_class.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n = optional_param('n', 0, PARAM_INT);  // Virtualpc instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('virtualpc', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $virtualpc  = $DB->get_record('virtualpc', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $virtualpc  = $DB->get_record('virtualpc', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $virtualpc->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('virtualpc', $virtualpc->id, $course->id, false, MUST_EXIST);
} else {
    print_error('courseorinstanceid', 'virtualpc');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

// Print the page header.
$url = new moodle_url ( '/mod/virtualpc/view.php', array ( 'id' => $cm->id ) );

$PAGE->set_url($url);
$PAGE->set_context($context);
$PAGE->set_title(format_string($virtualpc->name));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

// TODO Validate or comprobe the server are running.
$renderer = $PAGE->get_renderer('mod_virtualpc');

// Validate if the user is in a role allowed to VIEW.
if (has_capability('mod/virtualpc:view', $context)) {

    $printaccessbutton = false;

    $broker = uds_login();

    $pool = uds_servicespools_byname($broker, $virtualpc->poolname);

    if ($pool) {
        $printaccessbutton = true;
        $virtualpc->thumb = $pool->thumb;
    }

    uds_logout($broker);

    $bc = new block_contents();

    $bc->content = $renderer->display_virtualpc_detail($virtualpc, $id, $printaccessbutton);
    $bc->title = get_string('modulename', 'virtualpc');

    echo $OUTPUT->block($bc, BLOCK_POS_LEFT);

    $params = array(
        'context' => $context,
        'objectid' => $cm->id
    );
    $event = \mod_virtualpc\event\course_module_viewed::create($params);
    $event->add_record_snapshot('virtualpc', $virtualpc);
    $event->trigger();

} else {
    print_error('viewpermission', 'virtualpc');
}

echo $OUTPUT->footer();
