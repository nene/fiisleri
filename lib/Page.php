<?php

/**
 * Page class
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
 * Class to read and modify page data
 *
 * Example usage:
 *
 * <pre>
 * // init page
 * $p = new Page();
 * if (!$p->load( $_GET['page'] )) {
 *     die("Page '$_GET[page]' not found!");
 * }
 *
 * // update content if form submitted
 * if ($_POST['content']) {
 *     $p->setContent($_POST['content']);
 *     $p->save();
 * }
 *
 * // display the page
 * echo $p->toHtml();
 * </pre>
 */
class Page
{
    /**
     * WikiFormatter instance
     */
    var $wiki;

    /**
     * Page name
     */
    var $pageName;

    /**
     * Page title
     */
    var $title;

    /**
     * Page content
     */
    var $content;


    /**
     * Creates new page instance
     */
    function Page()
    {
    }

    /**
     * Loads page data for given page name
     *
     * @return false if page does not exist, true on success
     */
    function load($page_name)
    {
        if ( !$this->pageExists($page_name) ) {
            return false;
        }

        $this->pageName = $page_name;
        $this->title = $this->getTitle();
        $this->content = $this->getContent();

        return true;
    }

    function getName() {
        return $this->pageName;
    }

    function getHtmlName() {
        return htmlspecialchars($this->getName());
    }

    function getUriName() {
        return urlencode($this->getName());
    }

    /**
     * Perform simple query and return the single resulting field
     */
    function pageQuery($query, $parameters=array())
    {
        $db =& MDB2::singleton();
        $result = $db->extended->getOne(
            $query,
            null,
            $parameters
        );

        if (PEAR::IsError($result)) {
            die($result->getMessage());
        }

        return $result;
    }

    /**
     * Returns true, if page with given name exists
     */
    function pageExists($page_name)
    {
        $page_count = $this->pageQuery(
            'SELECT COUNT(*) FROM pages WHERE name LIKE ?',
            array($page_name)
        );

        return ($page_count==1);
    }

    /**
     * Returns the content of page in wiki markup
     */
    function getContent()
    {
        if ( isset($this->content) ) {
            return $this->content;
        }
        elseif ( isset($this->pageName) ) {
            return $this->pageQuery(
                'SELECT content_'.LANG.' AS content FROM pages WHERE name LIKE ?',
                array($this->pageName)
            );
        }
        else {
            die("No page name given.");
        }
    }

    /**
     * Returns the content of page in wiki markup
     */
    function getTitle()
    {
        if ( isset($this->title) ) {
            return $this->title;
        }
        elseif ( isset($this->pageName) ) {
            return $this->pageQuery(
                'SELECT title_'.LANG.' AS title FROM pages WHERE name LIKE ?',
                array($this->pageName)
            );
        }
        else {
            die("No page name given.");
        }
    }

    function getId()
    {
        if ( isset($this->pageName) ) {
            return $this->pageQuery(
                'SELECT id FROM pages WHERE name LIKE ?',
                array($this->pageName)
            );
        }
        else {
            die("No page name given.");
        }
    }

    /**
     * Returns the content of page formatted in HTML
     */
    function getHtmlContent()
    {
        return markdown( $this->getContent() );
    }

    /**
     * Returns the content of page formatted in HTML
     */
    function getEscapedContent()
    {
        return htmlspecialchars( $this->getContent() );
    }

    /**
     * Sets the new content for the page
     */
    function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Sets the new title for the page
     */
    function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Returns the title of page formatted in HTML
     */
    function getEscapedTitle()
    {
        return htmlspecialchars( $this->getTitle() );
    }

    /**
     * Saves page data to database
     */
    function save()
    {
        $db =& MDB2::singleton();

        $affectedRows = $db->extended->autoExecute(
            'pages',
            array(
              'title_'.LANG => $this->title,
              'content_'.LANG => $this->content,
            ),
            MDB2_AUTOQUERY_UPDATE,
            'name LIKE ' . $db->quote($this->pageName)
        );

        if (PEAR::IsError($affectedRows)) {
            die($affectedRows->getMessage());
        }
    }

    /**
     * Returns array of files associated with the page
     *
     * @return assoc. array of files
     */
    function getFilesByType($filetype)
    {
        $db =& MDB2::singleton();
        $files = $db->extended->getAll(
            'SELECT
                f.src,
                f.title_'.LANG.' AS title
            FROM
                pages p JOIN files f ON (p.id=f.page_id)
            WHERE
                p.name LIKE ? AND
                f.type = ?
            ORDER BY
                f.id',
            null,
            array($this->pageName, $filetype)
        );

        if (PEAR::IsError($files)) {
            die($files->getMessage());
        }

        return $files;
    }

    /**
     * Returns array of images associated with the page
     *
     * @return assoc. array of images
     */
    function getImages()
    {
        return $this->getFilesByType('IMAGE');
    }

    /**
     * Returns array of images associated with the page
     *
     * @return assoc. array of images
     */
    function getFiles()
    {
        return $this->getFilesByType('FILE');
    }

    /**
     * Returns array of images associated with the page
     *
     * @return array of HTML img elements
     */
    function getHtmlImages()
    {
        $images = $this->getImages();

        $img_list = array();
        foreach ( $images as $img ) {
            $img_list[]= "<img src='$img[src]' alt='$img[title]' />";
        }

        return $img_list;
    }

    /**
     * Returns array of files associated with the page
     *
     * @return array of HTML links
     */
    function getHtmlFiles()
    {
        $files = $this->getFiles();

        $file_list = array();
        foreach ( $files as $file ) {
            $file_list[]= "<a href='$file[src]'>$file[title]</a>";
        }

        return $file_list;
    }

    /**
     * Adds new image record to database and associates with this page
     */
    function addFileRecordOfType($src, $title, $type)
    {
        $db =& MDB2::singleton();

        $affectedRows = $db->extended->autoExecute(
            'files',
            array(
                'src' => $src,
                'title_'.LANG => $title || $src,
                'type' => $type,
                'page_id' => $this->getId(),
            ),
            MDB2_AUTOQUERY_INSERT
        );

        if (PEAR::IsError($affectedRows)) {
            die($affectedRows->getMessage());
        }
    }

    /**
     * Adds new image record to database and associates with this page
     */
    function addImageRecord($src, $alt)
    {
        $this->addFileRecordOfType($src, $alt, 'IMAGE');
    }

    /**
     * Adds new image record to database and associates with this page
     */
    function addFileRecord($src, $title)
    {
        $this->addFileRecordOfType($src, $title, 'FILE');
    }

    /**
     * Moves uploaded file to specified directory
     */
    function moveUploadedFileToDir($file, $dir)
    {
        if (!is_uploaded_file($file['tmp_name'])) {
            die("File not uploaded.");
        }

        // create unique filename
        $dest = $dir . basename($file['name']);
        $i=1;
        while (file_exists($dest)) {
            $dest = $dir . $i . basename($file['name']);
            $i++;
        }

        // perform moving
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            return $dest;
        }
        else {
            die("Error on moving uploaded file.");
        }
    }

    /**
     * Adds new image file to page
     */
    function addImage($file, $alt)
    {
        // first move the file itself
        $src = $this->moveUploadedFileToDir($file, 'content-img/');
        // then add the record to database
        $this->addImageRecord( $src, $alt );
    }

    /**
     * Adds new file to page
     */
    function addFile($file, $title)
    {
        // first move the file itself
        $src = $this->moveUploadedFileToDir($file, 'content-img/');
        // then add the record to database
        $this->addFileRecord( $src, $title );
    }

    /**
     * Removes file record from database and filesystem
     */
    function deleteFile($src)
    {
        $db =& MDB2::singleton();

        $affectedRows = $db->extended->execParam(
            'DELETE FROM files WHERE src=?',
            array($src)
        );

        if (PEAR::IsError($affectedRows)) {
            die($affectedRows->getDebugInfo());
        }

        unlink($src);
    }

    /**
     * Removes image record from database and filesystem
     */
    function deleteImage($src)
    {
        $this->deleteFile($src);
    }

    /**
     * Returns the whole formatted page content along with images
     * suitable for inseriting into HTML.
     */
    function toHtml()
    {
        $content = $this->getHtmlContent();
        $images = implode(' ', $this->getHtmlImages());
        $files = implode('</li><li>', $this->getHtmlFiles());
        if (strlen($files) > 0) {
            $files = "<ul><li>$files</li></ul>";
        }

        return "$content <div class='images'>$images</div>$files";
    }
}


?>