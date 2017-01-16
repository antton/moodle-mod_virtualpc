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
 * All the core Moodle functions neeeded to allow the module to work
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('uds_class.php');

// Moodle core API.

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function virtualpc_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_GROUPMEMBERSONLY:
            return false;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return false;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the virtualpc into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param stdClass $virtualpc
 * @return int
 */
function virtualpc_add_instance(stdClass $virtualpc) {

    global $DB;

    $virtualpc->timecreated = time();

    if (!isset($virtualpc->modifiable)) {
        $virtualpc->modifiable = 0;
    }

    return $DB->insert_record('virtualpc', $virtualpc);
}

/**
 * Updates an instance of the virtualpc in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param stdClass $virtualpc
 * @return int
 */
function virtualpc_update_instance(stdClass $virtualpc) {

    global $DB;

    $virtualpc->timemodified = time();
    $virtualpc->id = $virtualpc->instance;

    if (!isset($virtualpc->modifiable)) {
        $virtualpc->modifiable = 0;
    }

    return $DB->update_record('virtualpc', $virtualpc);
}

/**
 * Removes an instance of the virtualpc from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function virtualpc_delete_instance($id) {

    global $DB;

    if (! $virtualpc = $DB->get_record('virtualpc', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here.

    $DB->delete_records('virtualpc', array('id' => $virtualpc->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param int $course
 * @param int $user
 * @param int $mod
 * @param \stdClass $virtualpc
 * @return \stdClass
 */
function virtualpc_user_outline($course, $user, $mod, $virtualpc) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $virtualpc the module instance record
 * @return void, is supposed to echp directly
 */
function virtualpc_user_complete($course, $user, $mod, $virtualpc) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in virtualpc activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param int $course
 * @param boolean $viewfullnames
 * @param integer $timestart
 * @return boolean
 */
function virtualpc_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  // True if anything was printed, otherwise false.
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link virtualpc_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function virtualpc_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item
 *
 * @param integer $activity
 * @param integer $courseid
 * @param boolean $detail
 * @param \array $modnames
 * @param boolean $viewfullnames
 */
function virtualpc_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function virtualpc_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function virtualpc_get_extra_capabilities() {
    return array();
}

// Gradebook API.

/**
 * Is a given scale used by the instance of virtualpc?
 *
 * This function returns if a scale is being used by one virtualpc
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param integer $virtualpcid
 * @param integer $scaleid
 * @return boolean
 */
function virtualpc_scale_used($virtualpcid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of virtualpc.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param integer $scaleid
 * @return boolean
 */
function virtualpc_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Creates or updates grade item for the give virtualpc instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php

 * @param stdClass $virtualpc
 * @param integer $grades
 */
function virtualpc_grade_item_update(stdClass $virtualpc, $grades=null) {

    global $CFG;

    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($virtualpc->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $virtualpc->grade;
    $item['grademin']  = 0;

    grade_update('mod/virtualpc', $virtualpc->course, 'mod', 'virtualpc', $virtualpc->id, 0, null, $item);
}

/**
 * Update virtualpc grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $virtualpc instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function virtualpc_update_grades(stdClass $virtualpc, $userid = 0) {

    global $CFG;

    require_once($CFG->libdir.'/gradelib.php');

    $grades = array(); // Populate array of grade objects indexed by userid.

    grade_update('mod/virtualpc', $virtualpc->course, 'mod', 'virtualpc', $virtualpc->id, 0, $grades);
}

// File API.

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function virtualpc_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * File browsing support for virtualpc file areas
 *
 * @package mod_virtualpc
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function virtualpc_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
    return null;
}

/**
 * Serves the files from the virtualpc file areas
 *
 * @package mod_virtualpc
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the virtualpc's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function virtualpc_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}