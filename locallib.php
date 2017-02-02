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
 * Virtual PC specific functions, needed to implement all the module logic
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('uds_class.php');

global $USER;

/**
 * Login UDS and set valid token
 *
 * @return stdClass
 */
function uds_login() {

    global $CFG, $COURSE, $USER;

    $udsinstance = new uds();

    $postfields = array('authSmallName' => $udsinstance->get_authsmallname(),
                        'username' => $udsinstance->get_username(),
                        'password' => $udsinstance->get_password());

    $urlpath = 'auth/login' . $udsinstance->get_lang();

    $jsonresponse = $udsinstance->rest_request($udsinstance::POST, $urlpath,
                                               $postfields );

    if ($jsonresponse->result == $udsinstance::OK) {

        $udsinstance->set_token($jsonresponse->token);

        $udsinstance->add_header('X-Auth-Token: ' . $jsonresponse->token);

        return $udsinstance;

    } else {
        if (is_siteadmin($USER->id)) {
            notice(get_string('virtualpcresterror', 'virtualpc', $urlpath),
                   $CFG->wwwroot.'/admin/settings.php?section=modsettingvirtualpc');
        } else {
            $feedback = new stdClass();
            $feedback->url = $udsinstance->get_server();
            notice (get_string('errorconnection', 'virtualpc', $feedback),
                    $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id);
        }
    }

}

/**
 * Logout UDS
 *
 * @param stdClass $udsinstance UDS instance object
 * @return boolean
 */
function uds_logout($udsinstance) {

    $postfields = array('authSmallName' => $udsinstance->get_authsmallname(),
                        'username' => $udsinstance->get_username(),
                        'password' => $udsinstance->get_password());

    $urlpath = 'auth/logout' . $udsinstance->get_lang();

    $jsonresponse = $udsinstance->rest_request($udsinstance::POST, $urlpath,
                    $postfields);
    $udsinstance->remove_header('X-Auth-Token: ' . $udsinstance->get_token());

    $udsinstance->unset_token();

    return true;

}

/**
 * Gets servicespools
 *
 * @param stdClass $udsinstance UDS instance object
 * @return arr $jsonresponse json list of services pools
 */
function uds_servicespools_overview($udsinstance) {

    $urlpath = 'servicespools/overview';

    $jsonresponse = $udsinstance->rest_request($udsinstance::GET, $urlpath, '');

    return $jsonresponse;
}

/**
 * Get servicespools pool information
 *
 * @param stdClass $udsinstance UDS instance object
 * @param str $idpool of service
 * @return json pool stdClass
 */
function uds_servicespools($udsinstance, $idpool) {

    $urlpath = 'servicespools/' . $idpool;

    $jsonresponse = $udsinstance->rest_request($udsinstance::GET, $urlpath, '');

    return $jsonresponse;
}

/**
 *
 * Returns an array with printable services poolsnames
 *
 * @param stdClass $udsinstance
 * @param string $currentpoolname
 * @return \array
 */
function uds_servicespools_mod_form($udsinstance, $currentpoolname) {

    global $USER;

    $jsonresponse = uds_servicespools_overview($udsinstance);

    $poolsnoadmin = array();
    $poolsadmin = array();

    $filterpoolname = trim(get_config('virtualpc', 'filterpoolname'));
    if (empty($filterpoolname)) {
        $filterpoolname = '*';
    }

    $currentpoolnameexists = false;

    foreach ($jsonresponse as $key => $pool) {
        // Only active services.
        if ($pool->state == $udsinstance::POOLACTIVE) {
            if (is_siteadmin($USER->id) or
                    preg_match("/$filterpoolname/i", trim($pool->name))) {

                if ((empty($currentpoolname)) || trim($pool->name) == trim($currentpoolname)) {
                    $currentpoolnameexists = true;
                }

                if ($pool->comments) {
                    $comentsline = "<br>$pool->comments";
                } else {
                    $comentsline = "";
                }

                $thumb = "<img height='20' width='20' src='data:image/jpeg;base64," .
                        $pool->thumb . "'/>";

                if (is_siteadmin($USER->id) &&
                        !preg_match("/$filterpoolname/i", trim($pool->name))) {
                    $color = 'green';
                } else {
                    $color = 'black';
                }

                $html = "<span style=\"color:$color\">$thumb <b>$pool->name</b>";

                if ($pool->max_srvs != 0) {
                    $html .= " - " . get_string('maxusers', 'virtualpc', $pool->max_srvs).
                            $comentsline . "</span>";
                } else {
                    $html .= "$comentsline</span>";
                }

                $html .= "<br>";

                if (is_siteadmin($USER->id)) {
                    $poolsadmin[trim($pool->name)] = $html;
                } else {
                    $poolsnoadmin[trim($pool->name)] = $html;
                }
            }
        }
    }

    if ($currentpoolnameexists) {
        $poolsnonexist = array();
    } else {
        $poolsnonexist[""] = "<span style=\"color:red\">$currentpoolname "
            . " - " . get_string('virtualpcnotfound', 'virtualpc') . " </span><br>";
    }

    asort($poolsadmin , 0);

    $htmlpools = array_merge($poolsnonexist, $poolsnoadmin, $poolsadmin);

    return $htmlpools;
}

/**
 * Create ticket from user
 *
 * @param stdClass $udsinstance
 * @param string $username
 * @param string $idpool
 * @param string $fullname
 * @return json
 */
function uds_tickets_create($udsinstance, $username, $idpool, $fullname) {

    global $CFG, $COURSE;

    $urlpath = '/tickets/create';

    $postfields = array(
                "username" => "$username",
                "authSmallName" => $udsinstance->get_authsmallnameforactivity(),
                "groups" => $udsinstance->get_groupname(),
                "servicePool" => "$idpool",
                "realname" => "$fullname",
                "force" => "1");

    $jsonresponse = $udsinstance->rest_request($udsinstance::PUT,
                                               $urlpath, $postfields);
    if (empty($jsonresponse->result)) {
        $feedback = new stdClass();
        $pool = uds_servicespools($udsinstance, $idpool);
        $feedback->poolname = $pool->name;
        $feedback->username = "$username";
        notice(get_string('virtualpcerrorcreatingticketid', 'virtualpc', $feedback),
           $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id);
    } else {
        return $jsonresponse->result;
    }
}

/**
 * Get provider services
 *
 * @param stdClass $udsinstance
 * @param string $providerid
 * @param string $serviceçid
 * @return json
 */
function uds_providers_services($udsinstance, $providerid, $serviceçid) {

    $urlpath = 'providers/' . $providerid . '/services/' . $serviceçid;

    $jsonresponse = $udsinstance->rest_request($udsinstance::GET, $urlpath, '');

    return $jsonresponse;
}

/**
 * Get active pool services by name
 *
 * @param stdClass $udsinstance
 * @param string $poolname
 * @return array
 */
function uds_servicespools_byname($udsinstance, $poolname) {

    $jsonresponse = uds_servicespools_overview($udsinstance);

    foreach ($jsonresponse as $key => $pool) {
        if (($pool->state == $udsinstance::POOLACTIVE) &&
            (trim($pool->name) == trim($poolname))) {
            return uds_servicespools($udsinstance, $pool->id);
        }
    }

    return false;
}

/**
 * Get authenticators overview
 *
 * @param stdClass $udsinstance
 * @return arr
 */
function uds_authenticators_overview($udsinstance) {

    $urlpath = '/authenticators/overview';

    $jsonresponse = $udsinstance->rest_request($udsinstance::GET, $urlpath, '');

    $arrresponse = array();

    foreach ($jsonresponse as $key => $authmethod) {
        $arrresponse[$authmethod->id] = $authmethod->name;
    }
    return $arrresponse;

}

/**
 * Verifies that the specified authenticator exists in the pool group list,
 * and if it does not exist, it is undone using the forced option.
 *
 * @see uds_tickets_create
 * @param stdClass $udsinstance
 * @param string $idpool
 * @param string $idauthenticator
 * @param string $groupname
 * @return json
 */
function uds_servicespools_groups($udsinstance, $idpool, $idauthenticator, $groupname) {

    $arraygroups = uds_authenticators_overview($udsinstance);

    $groupid = in_array_field($groupname, 'name', $arraygroups);

    if ($groupid == false) {
          return false;
    }

    $postfields = array('id' => "$groupid");

    $urlpath = "servicespools/$idpool/groups";

    $jsonresponse = $udsinstance->rest_request($udsinstance::PUT, $urlpath, $postfields);

    return $jsonresponse;
}

/**
 * Get service pool group overview specific by idpool
 *
 * @param stdClass $udsinstance
 * @param string $idpool
 * @return array
 */
function uds_servicespools_groups_overview($udsinstance, $idpool) {

    $urlpath = "/servicespools/$idpool/groups/overview";

    $jsonresponse = $udsinstance->rest_request($udsinstance::GET, $urlpath, '');

    $arrresponse = array();

    foreach ($jsonresponse as $key => $authmethod) {
        $arrresponse[$authmethod->id] = $authmethod->name;
    }

    return $arrresponse;

}

/**
 * Find field in array
 *
 * @param string $needle
 * @param string $needlefield
 * @param string $haystack
 * @param boolean $strict
 * @return boolean
 */
function in_array_field($needle, $needlefield, $haystack, $strict = false) {

    if ($strict) {
        foreach ($haystack as $item) {
            if (isset($item->$needlefield) && $item->$needlefield === $needle) {
                return $item->id;
            }
        }
    } else {
        foreach ($haystack as $item) {
            if (isset($item->$needlefield) && $item->$needlefield === needle) {
                return $item->id;
            }
        }
    }

    return false;
}
