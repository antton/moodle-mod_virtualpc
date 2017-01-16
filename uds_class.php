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
 * Connect to UDS Server by rest request and return json
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Class REST client for OpenUDS.org
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class uds {

    /**
     * Uds url server.
     * @var string
     */
    public $_server;

    /**
     * Uds url port.
     * @var integer
     */
    public $_port;

    /**
     * Uds admin username.
     * @var string
     */
    private $_username;

    /**
     * Uds admin password.
     * @var string
     */
    private $_password;

    /**
     * Uds authenticator TAG label.
     * @var string
     */
    public $_authsmallname;

    /**
     * UDS authenticator label to which the user belongs created
     * @var string
     */
    public $_authsmallnameforactivity;

    /**
     * The name of the group within the authenticator where users from moodle will be created.
     * @var string
     */
    public $_groupname;

    /**
     * Curl default options.
     * @var \array
     */
    public $_curl_default_options = array();

    /**
     * Token for moodleuser in UDS
     * @var string
     */
    private $_token;

    /**
     * Request rest url.
     * @var string
     */
    public $_requesturl;

    /**
     * Last curl response.
     * @var string
     */
    public $_response;

    /**
     * Http headers.
     * @var \array
     */
    public $_headers = array();

    /**
     * Uds lang.
     * @var string
     */
    public $_lang;

    /**
     * Pool is active
     */
    const POOLACTIVE = "A";

    /**
     * Pool are in mantenance mode
     */
    const POOLMAINTENANCE = "M";

    /**
     * HTTP request return ok code
     */
    const HTTP_OK = 200;

    /**
     * HTTP request return created code
     */
    const HTTP_CREATED = 201;

    /**
     * HTTP return acepted code
     */
    const HTTP_ACEPTED = 202;

    /**
     *  Request return error
     */
    const ERROR = 'error';

    /**
     * Request return OK
     */
    const OK    = 'ok';

    /**
     * Rest request is POST
     */
    const POST = 'POST';

    /**
     * Rest request is GET
     */
    const GET = 'GET';

    /**
     *  Rest request is PUT
     */
    const PUT = 'PUT';

    /**
     *  Protocol is  HTTP
     */
    const HTTP = 'http';

    /**
     *  Protocol is HTTPS
     */
    const HTTPS = 'https';

    /**
     * public function __construct()
     */
    public function __construct() {

        $this->add_header('Content-type: ' . "application/json");
        $this->add_header('User-Agent: ' . "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1");

        $this->set_server(get_config('virtualpc', 'serverurl'));
        $this->set_port(get_config('virtualpc', 'serverport'));

        $this->set_authsmallname(get_config('virtualpc', 'authsmallnameforadmin'));
        $this->set_authsmallnameforactivity(get_config('virtualpc', 'authsmallnameforactivity'));
        $this->set_username(get_config('virtualpc', 'username'));
        $this->set_password(get_config('virtualpc', 'password'));
        $this->set_groupname(get_config('virtualpc', 'groupname'));

        $this->set_lang('/es');

        $curldefaultoptions = array( CURLOPT_RETURNTRANSFER => true,
                                       CURLOPT_HEADER => false,
                                       CURLOPT_TIMEOUT => 30);
        $this->set_curl_default_options ($curldefaultoptions);
    }

    /**
     * Accessor methods
     *
     */

    /**
     * Set default options for curl conection
     * @param \array $curldefaultoptions
     */
    public function set_curl_default_options($curldefaultoptions = array()) {
        $this->_curl_default_options = $curldefaultoptions;
    }
    /**
     * Set rest url request in curl conection
     *
     * @param string $requesturl
     */
    public function set_requesturl( $requesturl = null) {
        $this->_requesturl = $requesturl;
    }

    /**
     * Set full url string conection to uds server
     * @param string $server
     */
    public function set_server( $server = null) {
        $this->_server = $server;
    }
    /**
     * Set integer port for the connection to UDS server
     * @param integer $serverport
     */
    public function set_port( $serverport = null) {
        $this->_port = $serverport;
    }
    /**
     * Set username for rest request with UDS server
     * @param string $username
     */
    public function set_username( $username = null) {
        $this->_username = $username;
    }
    /**
     * Set password for rest request with UDS server
     * @param string $password
     */
    public function set_password( $password = null) {
        $this->_password = $password;
    }
    /**
     * Set then token
     * @param string $token
     */
    public function set_token( $token) {
        $this->_token = $token;
    }
    /**
     * Set the string into the array header passed to the connection UDS server
     * @param string $header
     */
    public function add_header($header = null) {
        $this->_headers[] = $header;
    }
    /**
     * Unset the string $header into the array headers to the connection UDS server.
     * @param string $header
     */
    public function remove_header($header = null) {
        $key = array_search($header, $this->_headers);
        if ($key !== false) {
            unset($this->_headers[$key]);
        }
    }
    /**
     * Set the default lang into UDS conection
     * @param string $lang
     */
    public function set_lang($lang = '/es') {
        $this->_lang = $lang;
    }
    /**
     * Set UDS authenticator label to which the user belongs Administrator indicated in the previous field
     * @param string $auth
     */
    public function set_authsmallname( $auth = null) {
        $this->_authsmallname = $auth;
    }
    /**
     * Set UDS authenticator label where users will be created.
     * @param string $auth
     */
    public function set_authsmallnameforactivity( $auth = null) {
        $this->_authsmallnameforactivity = $auth;
    }
    /**
     * Set Name of the group to be created in the previous authenticator to allow access to the activities created from Moodle
     * @param string $groupname
     */
    public function set_groupname( $groupname = null) {
        $this->_groupname = $groupname;
    }
    /**
     * Get default curl options
     * @return /array
     */
    public function get_curl_default_options() {
        return $this->_curl_default_options;
    }
    /**
     * Get request rest url
     * @return string
     */
    public function get_requesturl() {
        return $this->_requesturl;
    }
    /**
     * Get request server url
     * @return string
     */
    public function get_server() {
        return $this->_server;
    }
    /**
     * Get server port
     * @return integer
     */
    public function get_port() {
        return $this->_port;
    }
    /**
     * Get user with UDS administrator permissions
     * @return string
     */
    public function get_username() {
        return $this->_username;
    }
    /**
     * Get password with UDS administrator permisions
     * @return string
     */
    public function get_password() {
        return $this->_password;
    }
    /**
     * Get lang
     * @return string
     */
    public function get_lang() {
        return $this->_lang;
    }

    /**
     * Get token uds connection
     * @return token
     */
    public function get_token() {
        return $this->_token;
    }
    /**
     * Get headers
     * @return /array
     */
    public function get_headers() {
        return $this->_headers;
    }
    /**
     * Get UDS authenticator label to which the user belongs Administrator
     * @return string
     */
    public function get_authsmallname() {
        return $this->_authsmallname;
    }
    /**
     * UDS authenticator label where users will be created.
     * @return string
     */
    public function get_authsmallnameforactivity() {
        return $this->_authsmallnameforactivity;
    }
    /**
     * Name of the UDS group to be created to allow access to the activities created from Moodle
     * @return string
     */
    public function get_groupname() {
        return $this->_groupname;
    }

    /**
     * Clean headers.
     */
    public function unset_headers() {
        unset($this->_headers);
    }

    /**
     * Unset token.
     */
    public function unset_token() {
        unset($this->_token);
    }

    /**
     * Rest request
     *
     * @param string $type
     * @param string $urlpath
     * @param string $postfields
     * @return \stdClass
     */
    public function rest_request ( $type, $urlpath, $postfields ) {

        global $USER, $CFG, $COURSE;

        $connection = curl_init();

        if (preg_match('/^https/i', get_config('virtualpc', 'serverurl')) or ($this->_port == 0)) {

            if (preg_match('/^https/i', $this->get_server() )) {
                curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($connection, CURLOPT_CUSTOMREQUEST, self::POST);
            }

            $this->_requesturl = $this->get_server().
                             '/rest/' . $urlpath;
        } else {

            $this->_requesturl = $this->get_server() .':' .
                             $this->get_port() .
                             '/rest/' . $urlpath;
        }

        switch ($type) {
            case self::POST:
                curl_setopt($connection, CURLOPT_POST, true);
                if (!empty($postfields)) {
                    curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($postfields));
                }
                break;
            case self::PUT:
                if (!empty($postfields)) {
                    curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($postfields));
                }
                curl_setopt($connection, CURLOPT_CUSTOMREQUEST, self::PUT);
                break;
            case self::GET:
                if (!empty($postfields)) {
                    curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($postfields));
                }
                curl_setopt($connection, CURLOPT_CUSTOMREQUEST, self::GET);
                break;
        }

        curl_setopt($connection, CURLOPT_URL, $this->_requesturl);

        curl_setopt($connection, CURLOPT_HTTPHEADER, $this->get_headers());
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_VERBOSE, true);

        if (isset($CFG->proxyhost)) {
            curl_setopt($connection, CURLOPT_PROXY, $CFG->proxyhost);
            if (isset($CFG->proxyport)) {
                curl_setopt($connection, CURLOPT_PROXYPORT, $CFG->proxyport);
            }
        }

        $curlresponse = curl_exec($connection);
        $status = curl_getinfo($connection, CURLINFO_HTTP_CODE);

        switch ($status) {
            case self::HTTP_OK:
            case self::HTTP_CREATED:
            case self::HTTP_ACEPTED:
                $jsonresponse = json_decode($curlresponse);
                break;
            default:
                if (is_siteadmin($USER->id)) {
                    printf("cUrl error (#%d): %s<br>\n", curl_errno($connection),
                            htmlspecialchars(curl_error($connection)));

                    curl_close($connection);

                    debugging('HTTP_CODE error: '.($status), DEBUG_DEVELOPER);
                }
                $feedback = new stdClass();
                $feedback->url = $this->get_server();
                notice (get_string('errorconnection', 'virtualpc', $feedback),
                        $CFG->wwwroot . '/course/view.php?id=' . $COURSE->id);
                break;
        }

        curl_close($connection);

        return $jsonresponse;
    }
}
