<?php

/**
 * Class to represent entire webpage
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

/**
 * Example usage:
 *
 * <pre>
 * </pre>
 */
class WebPage
{
    var $globalTitle = "Unknown Site";
    var $localTitle = "";
    var $adminMode = false;
    var $page = null;
    var $menu = null;
    var $content = null;
    var $mode = 'view';

    function WebPage()
    {
    }

    function setGlobalTitle($title)
    {
        $this->globalTitle = $title;
    }

    function setLocalTitle($title)
    {
        $this->localTitle = $title;
    }

    function getHtmlTitle()
    {
        // two titles separated with n-dash
        return
            ($this->localTitle ? htmlspecialchars($this->localTitle) . ' &#x2013; ' : "") .
            htmlspecialchars($this->globalTitle);
    }

    function setPage( $page )
    {
        $this->page = $page;
    }

    function setMenu( $menu )
    {
        $this->menu = $menu;
    }

    function enableAdminMode()
    {
        $this->adminMode = true;
    }

    function disableAdminMode()
    {
        $this->adminMode = false;
    }

    function setContent($content)
    {
        $this->content = $content;
    }

    function getContent()
    {
        if ( isset($this->content) ) {
            return $this->content;
        }

        return $this->page->toHtml();
    }

    function setMode($mode = "view") {
        $this->mode = $mode;
    }

    function getModePath() {
        return $this->mode == 'view' ? '' : "/".$this->mode;
    }

    function getEditMenu()
    {
        if ($this->mode == 'view') $selected = "Vaata lehte";
        if ($this->mode == 'edit') $selected = "Muuda teksti";
        if ($this->mode == 'images') $selected = "Muuda pilte";
        if ($this->mode == 'files') $selected = "Muuda faile";
        if ($this->mode == 'settings') $selected = "Seaded";

        $base_uri = BASE_URI . LANG . "/";

        $page_name = $this->page->getUriName();
        $menu = array(
            'Vaata lehte' => "$base_uri$page_name",
            'Muuda teksti' => "$base_uri$page_name/edit",
            'Muuda pilte' => "$base_uri$page_name/images",
            'Muuda faile' => "$base_uri$page_name/files",
            'Seaded' => "$base_uri$page_name/settings",
        );

        $ul = '<ul id="edit-menu">';
        foreach ($menu as $label => $uri) {
            if ($label == $selected) {
                $ul.= "<li><strong>$label</strong></li>";
            }
            else {
                $ul.= "<li><a href='$uri'>$label</a></li>";
            }
        }
        $ul.= '</ul>';

        return $ul;
    }

    function getLangMenu()
    {
        $page_uri = $this->page->getUriName() . $this->getModePath();
        
        $menu = array(
            'et' => array("label" => "EST", "uri" => BASE_URI . "et/" . $page_uri),
            'en' => array("label" => "ENG", "uri" => BASE_URI . "en/" . $page_uri),
            'ru' => array("label" => "RUS", "uri" => BASE_URI . "ru/" . $page_uri),
        );

        $ul = '<ul id="lang">';
        foreach ($menu as $lang => $m) {
            if ($lang == LANG) {
                $ul.= "<li><strong>$m[label]</strong></li>";
            }
            else {
                $ul.= "<li><a href='$m[uri]'>$m[label]</a></li>";
            }
        }
        $ul.= '</ul>';

        return $ul;
    }

    function toHtml()
    {
        $title = $this->getHtmlTitle();

        $page_name = $this->page->getUriName();
        if ($this->adminMode) {
            $body_class = 'class="admin"';
            $admin_link = "<a href='$base_uri$page_name/logout'>exit</a>";
            $edit_menu = $this->getEditMenu();
        }
        else {
            $body_class = '';
            $admin_link = "<a href='$base_uri$page_name/login'>admin</a>";
            $edit_menu = "";
        }

        $main_menu = $this->menu->toHtml();
        $lang_menu = $this->getLangMenu();
        $content = $this->getContent();

        // Load template
        ob_start();
        include "template.php";
        $page = ob_get_contents();
        ob_end_clean();
        
        return $page;
    }
}

?>