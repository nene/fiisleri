<?php

/**
 * Main menu class
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

class Menu
{
    var $selected = "";

    var $pages = null;

    /**
     * Declares which menu item is selected
     * If the given item is not in the list of actual page names,
     * use the first page name in list.
     */
    function setSelected($selected_item="")
    {
        $menu = $this->getPages();

        if (array_key_exists($selected_item, $menu)) {
            $this->selected = $selected_item;
        }
        else {
            $this->selected = $this->getFirstMenuItemName();
        }
    }

    function getSelected()
    {
        return $this->selected;
    }

    function getFirstMenuItemName()
    {
        $keys = array_keys($this->getPages());
        return $keys[0];
    }

    /**
     * loads all available pagenames from database
     */
    function loadPages()
    {
        $db =& MDB2::singleton();
        $pages = $db->extended->getAll(
            'SELECT name, title_'.LANG.' AS title FROM pages ORDER BY id'
        );

        if (PEAR::IsError($pages)) {
            die($pages->getMessage());
        }

        $this->pages = array();
        foreach ($pages as $p) {
            $this->pages[$p["name"]] = $p["title"];
        }
    }

    /**
     * Returns array of all available pages
     */
    function getPages()
    {
        if ( !isset($this->pages) ) {
            $this->loadPages();
        }

        return $this->pages;
    }

    /**
     * Converts menu to HTML unordered list
     */
    function toHtml() {
        $pages = $this->getPages();

        $ul = "<ul>\n";
        $nr = 0;
        foreach ($pages as $name => $title) {
            $title = htmlspecialchars($title);
            $url = BASE_URI . LANG . "/" . urlencode($name);

            if ($nr > 0) {
                if ($name == $this->selected) {
                    $ul.= "<li><strong>$title</strong></li>\n";
                }
                else {
                    $ul.= "<li><a href='$url'>$title</a></li>\n";
                }
            }
            $nr++;
        }
        $ul.= "</ul>\n";

        return $ul;
    }

}

?>