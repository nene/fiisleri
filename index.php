<?php

/**
 * The main file of VikiKodukas, all the pages are accessed through it
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

require_once 'init.php';

function redirect($uri)
{
    header("Location: ".BASE_URI.LANG."/$uri");
    exit();
}

function authenticate($username, $password)
{
    $db =& MDB2::singleton();
    $result = $db->extended->getOne(
        "SELECT COUNT(*) FROM users WHERE username=? AND password=SHA1(?)",
        null,
        array($username, $password)
    );

    if (PEAR::IsError($result)) {
        die($result->getMessage());
    }

    return ($result==1);
}

function change_password($username, $password)
{
    $db =& MDB2::singleton();
    $affectedRows = $db->extended->autoExecute(
        'users',
        array('password' => sha1($password)),
        MDB2_AUTOQUERY_UPDATE,
        'username = ' . $db->quote($username)
    );

    if (PEAR::IsError($affectedRows)) {
        die($affectedRows->getMessage());
    }
}

// define language
if ($_GET["lang"]) {
    define("LANG", $_GET["lang"]);
}
else {
    define("LANG", "et");
}
// set up gettext
$gettext_mapping = array(
    "et" => "et_EE",
    "en" => "en_US",
    "ru" => "ru_RU",
);
$gettext_lang = $gettext_mapping[LANG];
putenv("LANG=$gettext_lang");
setlocale(LC_ALL, $gettext_lang.".UTF-8");
setlocale(LC_TIME, $gettext_lang.".UTF-8");
bindtextdomain("kambja", getcwd() . "/locale");
textdomain("kambja");


// init WebPage
$webpage =& new WebPage();
$webpage->setGlobalTitle("Kambja Hotell");


// add main menu
$menu =& new Menu();
if ( isset($_GET['page']) ) {
    $menu->setSelected($_GET['page']);
}
else {
    $menu->setSelected();
}
$webpage->setMenu($menu);


// add page content
$page =& new Page();
$page->load( $menu->getSelected() );
$webpage->setPage($page);


// turn on admin mode if needed
if (isset($_SESSION['admin'])) {
    $webpage->enableAdminMode();
}


$action = ( isset($_GET['action']) ) ? $_GET['action'] : "normal";

if ( !($action == "normal" || $action == "login") && !isset($_SESSION['admin']) ) {
    die("Access denied!");
}

switch ($action) {
    case "normal":
        // do nothing in particular, just set the page title
        if ($page->getName() != "index") {
            $webpage->setLocalTitle( $page->getTitle() );
        }
    break;

    case "login":
        // set title
        $webpage->setLocalTitle("Administreerimisliidesesse sisenemine");

        // if form submitted, check login credentials
        if ( isset($_POST['username']) && isset($_POST['password']) ) {
            if ( authenticate($_POST['username'], $_POST['password']) ) {
                // login successful, set session var and redirect to internal page
                $_SESSION['admin'] = true;
                $_SESSION['username'] = $_POST['username'];
                redirect( $page->getUriName() );
            }
            else {
                // login failed, show notice
                $notice = "<p class='error'><strong>Vigane parool või kasutajanimi!</strong></p>";
            }
        }
        else {
            $notice = "";
        }

        $uri = $page->getUriName() . '/login';
        $webpage->setContent(
<<<EOHTML
<h2>Logi sisse</h2>
$notice
<form action="$uri" method="post">
<p><label for="username">Kasutajanimi:</label>
   <input type="text" name="username" id="username" /></p>
<p><label for="password">Parool:</label>
   <input type="password" name="password" id="password" /></p>
<p><input type="submit" value="Sisene" /></p>
</form>
EOHTML
        );
    break;

    case "logout":
        // unset session variable and redirect to public page
        unset($_SESSION['admin']);
        unset($_SESSION['username']);
        redirect( $page->getUriName() );
    break;

    case "edit":
        $webpage->setMode('edit');
        $webpage->setLocalTitle( $page->getTitle() );

        // if new content submitted, then update page object
        if ( isset($_POST['content']) ) {
            $page->setTitle($_POST['title']);
            $page->setContent($_POST['content']);
        }

        if ( !isset($_POST['submit']) ) {
            $_POST['submit'] = false;
        }

        // save, if needed and redirect to normal view
        if ( $_POST['submit']=='Salvesta' ) {
            $page->save();
            redirect( $page->getUriName() );
        }

        // show preview if needed
        if( $_POST['submit']!=false ) {
            $preview = $page->toHtml();
        }
        else {
            $preview = "";
        }


        $title = $page->getEscapedTitle();
        $text = $page->getEscapedContent();
        $uri = LANG."/".$page->getUriName() . '/edit';
        $webpage->setContent(
<<<EOHTML
$preview
<form action="$uri" method="post">
<p><label for="title">Pealkiri:</label>
   <input type="text" name="title" id="title" value="$title" /></p>
<p><textarea rows="20" cols="30" name="content">$text</textarea></p>
<p><input type="submit" name="submit" value="Eelvaade" />
<input type="submit" name="submit" value="Salvesta" />
(<a href="http://daringfireball.net/projects/markdown/syntax">Redigeerimisjuhend</a>)</p>
</form>
EOHTML
        );

    break;

    case "images":
        $webpage->setMode('images');
        $webpage->setLocalTitle( $page->getTitle() );

        // add or delete if needed
        if ( $_POST['submit']=='Lisa pilt' ) {
            $page->addImage( $_FILES['file'], $_POST['alt'] );
        }
        elseif ( $_POST['submit']=='Kustuta pilt' ) {
            $page->deleteImage( $_POST['src'] );
        }

        $uri = LANG."/".$page->getUriName() . '/images';

        // list of images with delete buttons
        $images = $page->getImages();
        $preview = "";
        foreach ($images as $img) {
            $preview .= <<<EOHTML
<form action="$uri" method="post">
<fieldset>
<legend>$img[file_title]</legend>
<p><img src="$img[src]" alt="$img[title]" /></p>
<p><input type="hidden" name="src" value="$img[src]" />
   <input type="submit" name="submit" value="Kustuta pilt" /></p>
</fieldset>
</form>
EOHTML;
        }

        $webpage->setContent(
<<<EOHTML
$preview
<form action="$uri" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Pildi lisamine</legend>
<p><label for="file">Pildifail:</label> <input type="file" name="file" id="file" /></p>
<p><label for="alt">Pildi kirjeldus:</label> <input type="text" name="alt" id="alt" /></p>
<p><input type="submit" name="submit" value="Lisa pilt" /></p>
</fieldset>
</form>
EOHTML
        );

    break;

    case "files":
        $webpage->setMode('files');
        $webpage->setLocalTitle( $page->getTitle() );

        // add or delete if needed
        if ( $_POST['submit']=='Lisa fail' ) {
            $page->addFile( $_FILES['file'], $_POST['title'] );
        }
        elseif ( $_POST['submit']=='Kustuta fail' ) {
            $page->deleteFile( $_POST['src'] );
        }

        $uri = LANG."/".$page->getUriName() . '/files';

        // list of files with delete buttons
        $files = $page->getFiles();
        $preview = "<ul>";
        foreach ($files as $file) {
            $preview .= <<<EOHTML
<li><form action="$uri" method="post">
<p><a href="$file[src]">$file[title]</a>
<input type="hidden" name="src" value="$file[src]" />
<input type="submit" name="submit" value="Kustuta fail" /></p>
</form></li>
EOHTML;
        }
        $preview.="</ul>";

        $webpage->setContent(
<<<EOHTML
$preview
<form action="$uri" method="post" enctype="multipart/form-data">
<fieldset>
<legend>Faili lisamine</legend>
<p><label for="file">Fail:</label> <input type="file" name="file" id="file" /></p>
<p><label for="title">Faili nimetus:</label> <input type="text" name="title" id="title" /></p>
<p><input type="submit" name="submit" value="Lisa fail" /></p>
</fieldset>
</form>
EOHTML
        );

    break;

    case "settings":
        // set title
        $webpage->setMode('settings');
        $webpage->setLocalTitle("Parooli muutmine");

        // if form submitted, check login credentials
        if ( isset($_POST['old-password']) &&
             isset($_POST['new-password']) &&
             isset($_POST['new-password-2'])
        ) {
            if ( !empty($_POST['old-password']) &&
                !empty($_POST['new-password']) &&
                !empty($_POST['new-password-2'])
            ) {
                if ( authenticate($_SESSION['username'], $_POST['old-password']) ) {
                    if ($_POST['new-password'] == $_POST['new-password-2']) {
                        change_password($_SESSION['username'], $_POST['new-password']);
                        $notice = "<p><strong>Parool edukalt muudetud.</strong></p>";
                    }
                    else {
                        $notice = "<p class='error'><strong>Paroolid ei kattu!</strong></p>";
                    }
                }
                else {
                    $notice = "<p class='error'><strong>Vale vana parool!</strong></p>";
                }
            }
            else {
                $notice = "<p class='error'><strong>Osad väljad on täitmata!</strong></p>";
            }
        }
        else {
            $notice = "";
        }

        $uri = LANG."/".$page->getUriName() . '/settings';
        $webpage->setContent(
<<<EOHTML
<h2>Parooli muutmine</h2>
$notice
<form action="$uri" method="post">
<p><label for="old-password">Vana Parool:</label>
   <input type="password" name="old-password" id="old-password" /></p>
<p><label for="new-password">Uus Parool:</label>
   <input type="password" name="new-password" id="new-password" /></p>
<p><label for="new-password-2">Uus Parool teist korda:</label>
   <input type="password" name="new-password-2" id="new-password-2" /></p>
<p><input type="submit" value="Muuda" /></p>
</form>
EOHTML
        );
    break;
}


echo $webpage->toHtml();
?>