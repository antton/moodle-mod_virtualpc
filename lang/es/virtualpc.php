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
 *  Spanish translation of virtualpc module
 *
 * @package    mod_virtualpc
 * @copyright  2014 Universidad de Málaga - Enseñanza Virtual y Laboratorios Tecnólogicos
 * @author     Antonio Godino (asesoramiento [at] evlt [dot] uma [dot] es)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['modulename'] = 'PC Virtual';
$string['modulenameplural'] = 'PCs Virtuales';
$string['modulename_help'] = '<div class="indent">
<p><b>PC Virtual</b> ofrece al participante un equipo con el sistema operativo y aplicaciones ya instaladas.
El participante, tras pulsar el botón de acceso de la actividad, entrará al escritorio virtual seleccionado.</p>
<p>El módulo ha sido desarrollado por la Universidad de Málaga</p><img alt="Universidad de Málaga" src="'.$CFG->wwwroot.'/mod/virtualpc/pix/umalogo.png"/></div>';
$string['virtualpcintro'] = '<b>Descripción:</b>';
$string['virtualpcname'] = 'Título de la actividad';
$string['joinvirtualpc'] = 'Acceder al PC Virtual';
$string['virtualpcname_help'] = 'Introduzca el título de la actividad PC Virtual';
$string['virtualpc'] = 'PC Virtual';
$string['pluginadministration'] = 'Administración PC Virtual';
$string['pluginname'] = 'PC Virtual';
$string['url'] = 'URL del servidor UDS';
$string['configurl'] = 'URL del servidor UDS para peticiones REST (http://hostname)';
$string['port'] = 'Puerto';
$string['configport'] = 'Puerto para peticiones REST';
$string['username'] = 'Usuario UDS administrador';
$string['configusername'] = 'Usuario UDS con permisos de administrador que utilizará este módulo para interactuar con UDS en tiempo real';
$string['password'] = 'Contraseña del usuario UDS administrador';
$string['configpassword'] = 'Contraseña del usuario UDS con permisos de administrador';
$string['authsmallnameforactivity'] = 'Etiqueta&nbsp;del&nbsp;autenticador&nbsp;UDS para acceso a los PCs virtuales';
$string['authsmallnameforadmin'] = 'Etiqueta&nbsp;del&nbsp;autenticador&nbsp;UDS del usuario&nbsp;administrador';
$string['configauthsmallnameforadmin'] = 'Etiqueta del autenticador UDS al que pertenece el usuario administrador indicado en el campo anterior';
$string['configauthsmallnameforactivity'] = 'Etiqueta del autenticador UDS que se usará para crear a los usuarios en UDS que accedan desde Moodle y se conecten a la PC virtual';
$string['groupname'] = 'Nombre del grupo';
$string['configgroupname'] = 'Nombre del grupo que se creará en el anterior autenticador de UDS para permitir el acceso desde Moodle';
$string['groupname'] = 'Nombre del grupo';
$string['errorconnection'] = 'Error de conexión al servidor de PCs Virtuales: <em><a href="{$a->url}" target=_blank>UDS</a></em>. <br/ >Vuelva a acceder a la actividad y si el error persiste póngase en contacto con el administrador de Moodle.';
$string['virtualpchelp'] = 'Tras pulsar en el botón "Acceder al PC Virtual" puede aparecer un mensaje para descargar un programa necesario para acceder al PC Virtual, solo la primera vez. La dirección de descarga directa es {$a}';
$string['virtualpcerrorcreatingticketid'] = 'Usuario {$a->username} sin acceso al PC Virtual: <b>{$a->poolname}</b>. Intentelo más tarde, si el problema persiste póngase en contacto con el administrador de Moodle.';
$string['poollabel'] = '<div style="color:orange">Seleccione el tipo de PC Virtual:</div>';
$string['poollabel_help'] = 'Seleccione un <b>tipo de PC virtual</b> del listado. Puede que contengan algun comentario que facilite su elección. Tenga en cuenta el número de personas máximo que podrán acceder simultáneamente.';
$string['virtualpcfieldset'] = 'Opciones para PCs Virtuales';
$string['filtropool'] = 'Filtro en nombres pool de servicios';
$string['configfiltropool'] = 'Establece un filtro mediante una expresión regular sobre el listado de nombres de pooles a ofrecer en este centro. Por ejemplo: Introduzca "^CAV_", sin las comillas, para ofrecer unicamente aquellos pools de servicios que comiencen por CAV_';
$string['aboutuds'] = '<center><a href="{$a->umaurl}" target=_blank><img src="{$a->umalogo}"/></a></center><br />La <a href="{$a->umaurl}" target=_blank>Universidad de
    Málaga</a> ha desarrollado el módulo Virtual PC de código abierto integrando Moodle y UDS.
    Esta integración está diseñada para simplificar el acceso a escritorios remotos desde Moodle,
proporcionando un único inicio de sesión entre los dos sistemas, con una fácil creación y administración de actividades que permiten el acceso a diferentes tipos de maquinas virtuales.<br />
<br /><em><a href="{$a->udsurl}" target=_blank>
UDS</a></em> es un broker de conexiones a máquinas virtuales multiplataforma para:<br />
    <ul><li>eLearning<br />
	<li>VDI: Administración y despliegue escritorios virtuales Windows y Linux<br />
	<li>Gestión acceso usuarios a recursos TI en Data Center o Cloud<br />
	<li>Consolidación servicios usuario mediante nuevos módulos o existentes</ul><br>
    <p>UDS Enterprise se emplea en diferentes sectores y entornos de producción como educación, administración pública, call center, seguros, oficinas remotas, etc.</p>
    <p>Más de 10.000 escritorios virtuales Windows y Linux son gestionados y desplegados con UDS Enterprise cada día, dotando de sencillez y flexibilidad a los sistemas VDI
    de distintas empresas y entidades.</p>
    <p>Visite <a href="{$a->udsurl}" target=_blank>{$a->udsurl}</a> para obtener información sobre soporte para empresas.<br />';
$string['virtualpc:join'] = 'Acceder al recurso remoto proporcionado desde la actividad PCs Virtuales';
$string['virtualpc:view'] = 'Ver la actividad Pc Virtual';
$string['virtualpc:addinstance'] = 'Añadir actividades virtualpc (PCs Virtuales)';
$string['usernotenrolled'] = 'Usuario no tiene permisos, "virtualpc:join" consulte con el administrador de moodle.';
$string['idpoolnotfound'] = 'El Pc Virtual "{$a->name}" no está actualmente disponible, póngase en contacto con el administrador de Moodle.';
$string['virtualpcresterror'] = 'En estos momentos no se puede mostrar la actividad. Inténtelo más adelante, si el error continúa, póngase en contacto el administrador de Moodle indicando el siguiente error: Error en la petición REST: {$a}';
$string['modifiable'] = 'El profesor podrá cambiar el PC virtual seleccionado';
$string['maxusers'] = 'Hasta {$a} usuarios a la vez';
$string['virtualpcnotfound'] = 'No existe ningún PC virtual con este nombre en el servidor UDS';
$string['incorrectcourseid'] = 'Course Module ID was incorrect';
$string['misconfiguredcourse'] = 'Course is misconfigured';
$string['incorrectcoursemodule'] = 'Course module is incorrect';
$string['courseorinstanceid'] = 'You must specify a course_module ID or an instance ID';
$string['viewpermission'] = 'Debes solicitar permiso para ver este recurso';
$string['eventvirtualpcjoined'] = 'VirtualPC conectado';