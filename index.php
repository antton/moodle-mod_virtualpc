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
 * View all Virtual PC instances in this course.
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT);   // Course id.

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

add_to_log($course->id, 'virtualpc', 'view all', 'index.php?id='.$course->id, '');

$coursecontext = context_course::instance($course->id);

$PAGE->set_url('/mod/virtualpc/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

if (! $virtualpcs = get_all_instances_in_course('virtualpc', $course)) {
    notice(get_string('novirtualpcs', 'virtualpc'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
if ($course->format == 'weeks') {
    $table->head  = array(get_string('week'), get_string('name'));
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array(get_string('topic'), get_string('name'));
    $table->align = array('center', 'left', 'left', 'left');
} else {
    $table->head  = array(get_string('name'));
    $table->align = array('left', 'left', 'left');
}

foreach ($virtualpcs as $virtualpc) {
    if (!$virtualpc->visible) {

        $link = html_writer::link(
            new moodle_url('/mod/virtualpc/view.php', array('id' => $virtualpc->coursemodule)),
            format_string($virtualpc->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/virtualpc/view.php', array('id' => $virtualpc->coursemodule)),
            format_string($virtualpc->name, true));
    }

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($virtualpc->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo $OUTPUT->heading(get_string('modulenameplural', 'virtualpc'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
