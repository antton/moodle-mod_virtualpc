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
 * Example how to insert log event during installation/update.
 *
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $DB;

$logs = array(
    array('module' => 'virtualpc', 'action' => 'add', 'mtable' => 'virtualpc', 'field' => 'name'),
    array('module' => 'virtualpc', 'action' => 'update', 'mtable' => 'virtualpc', 'field' => 'name'),
    array('module' => 'virtualpc', 'action' => 'view', 'mtable' => 'virtualpc', 'field' => 'name'),
    array('module' => 'virtualpc', 'action' => 'view all', 'mtable' => 'virtualpc', 'field' => 'name')
);
