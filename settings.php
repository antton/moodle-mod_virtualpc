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
 * Settings for Virtual PC
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if ($ADMIN->fulltree) {

    global $SITE;

    $settings->add(new admin_setting_configtext ('virtualpc/serverurl',
        get_string('url', 'virtualpc'),
        get_string('configurl', 'virtualpc'), 'https://demo.udsenterprise.com'));

    $settings->add(new admin_setting_configtext ('virtualpc/serverport',
        get_string('port', 'virtualpc'),
        get_string('configport', 'virtualpc'), '443', PARAM_INT));

    $settings->add(new admin_setting_configtext ('virtualpc/username',
        get_string('username', 'virtualpc'),
        get_string('configusername', 'virtualpc'), 'udsadmin'));

    $settings->add(new admin_setting_configpasswordunmask ('virtualpc/password',
        get_string('password', 'virtualpc'),
        get_string('configpassword', 'virtualpc'), 'UDSAdmin2016'));

    $settings->add(new admin_setting_configtext ('virtualpc/authsmallnameforadmin',
        get_string('authsmallnameforadmin', 'virtualpc'),
        get_string('configauthsmallnameforadmin', 'virtualpc'), 'mood'));

    $settings->add(new admin_setting_configtext ('virtualpc/filterpoolname',
        get_string('filtropool', 'virtualpc'),
        get_string('configfiltropool', 'virtualpc'), 'Win7 Moodle'));

    $settings->add(new admin_setting_configtext ('virtualpc/authsmallnameforactivity',
        get_string('authsmallnameforactivity', 'virtualpc'),
        get_string('configauthsmallnameforactivity', 'virtualpc'), 'mood'));

    $settings->add(new admin_setting_configtext ('virtualpc/groupname',
        get_string('groupname', 'virtualpc'),
        get_string('configgroupname', 'virtualpc'), 'users'));

    $param = new stdClass();
    $param->umalogo = $CFG->wwwroot.'/mod/virtualpc/pix/umalogo.png';
    $param->umaurl = 'http://www.uma.es/';
    $param->evlturl = 'http://www.evlt.uma.es/';
    $param->udsurl = 'http://www.udsenterprise.com/';

    $settings->add(new admin_setting_heading('virtualpc/intro', '',
                    get_string('aboutuds', 'virtualpc', $param)));

}
