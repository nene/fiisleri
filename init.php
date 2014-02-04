<?php

/**
 * Initialization file
 *
 * PHP versions 4 and 5
 *
 * LICENSE: VikiKodukas is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option)
 * any later version.
 *
 * VikiKodukas is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * VikiKodukas; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA  02111-1307  USA
 *
 * @package    VikiKodukas
 * @author     Rene Saarsoo <nene@triin.net>
 * @copyright  2007 Rene Saarsoo
 * @license    http://www.gnu.org/licenses/gpl.html  GPL 2.0
 */

initialize_php_include_path();

require_once "conf.php";
require_once "MDB2.php";
require_once "markdown.php";
require_once 'Menu.php';
require_once 'Page.php';
require_once 'WebPage.php';

session_start();
$db = establish_database_connection();


/**
 * Modifies PHP include path to contain path to PEAR code libraries
 */
function initialize_php_include_path()
{
    // get current working directory
    $cwd = getcwd();

    // create path to Fiisleri.ee and PEAR code libraries
    $lib_path = "$cwd/lib";
    $PEAR_path = "$cwd/PEAR";

    // get the current PHP include path
    $old_path = ini_get("include_path");

    // insert the Fiisleri.ee and PEAR library paths at the beginning of PHP default include path
    ini_set(
        'include_path',
        $lib_path . PATH_SEPARATOR .
        $PEAR_path . PATH_SEPARATOR .
        $old_path
    );
}

function establish_database_connection()
{
    $dsn = array(
        'phptype' => 'mysql',
        'username' => DB_USERNAME,
        'password' => DB_PASSWORD,
        'hostspec' => DB_HOSTNAME,
        'database' => DB_NAME,
    );
	
    $mdb2 =& MDB2::singleton($dsn);
    if (PEAR::isError($mdb2)) {
        die($mdb2->getMessage());
    }

    // use associative fetch mode
    $mdb2->setFetchMode(MDB2_FETCHMODE_ASSOC);

    // also, the results should be returned in UTF-8 encoding
    $enc_res = $mdb2->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
    if ( PEAR::isError($enc_res) ) {
        die( $enc_res->getMessage() );
    }

    // allow autoprepare and -execute
    $mdb2->loadModule('Extended');

    return $mdb2;
}

?>
