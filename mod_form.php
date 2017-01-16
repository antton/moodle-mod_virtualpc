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
 *  Config all Virtual PC instances in this course.
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once('uds_class.php');
require_once('locallib.php');

/**
 * Virtual PC settings form.
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */class mod_virtualpc_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {

        global $CFG, $USER, $COURSE;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('virtualpcname', 'virtualpc'), array('size' => '64'));

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'virtualpcname', 'virtualpc');

        // Adding the standard "intro" and "introformat" fields.
        $this->add_intro_editor();

        // Adding the rest of virtualpc settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic.

        $mform->addElement('header', 'virtualpcfieldset', get_string('virtualpcfieldset', 'virtualpc'));
        $mform->setExpanded('virtualpcfieldset');

        $broker = uds_login();

        if (empty($this->current->poolname)) {
            $poolname = "";
        } else {
            $poolname = $this->current->poolname;
        }

        $htmlarraypools = uds_servicespools_mod_form($broker, $poolname);

        uds_logout($broker);

        $mform->addElement('static', 'poollabel', get_string('poollabel', 'virtualpc'));
        $mform->addHelpButton('poollabel', 'poollabel', 'virtualpc');

        $arrayradio = array();
        $arraybr = array('');
        $i = 0;

        foreach ($htmlarraypools as $key => $servicio) {
            $arrayradio[] =& $mform->createElement('radio', 'poolname', null, $servicio, $key);
            $arraybr[$i++] = '<br />';
        }

        $mform->addGroup($arrayradio, 'radio', '', $arraybr, false);
        $mform->addRule('radio', null, 'required', null, 'client');

        if (!is_siteadmin($USER->id) and !empty($this->current->poolname)
                and $this->current->modifiable == false ) {
            $mform->hardFreeze('radio');
        }

        if (is_siteadmin($USER->id)) {
            $mform->addElement('checkbox', 'modifiable', '<font style="color:green">'.
                        get_string('modifiable', 'virtualpc'). "</font>" );
        } else {
            $mform->addElement('hidden', 'modifiable', '<font style="color:green">'.
                        get_string('modifiable', 'virtualpc'). "</font>" );
        }
        $mform->setType('modifiable', PARAM_BOOL);
        $mform->setDefault('modifiable', 0 );

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }

}
