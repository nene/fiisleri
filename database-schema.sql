--
-- Database structure for VikiKodukas
--
-- LICENSE: VikiKodukas is free software; you can redistribute it and/or modify it
-- under the terms of the GNU General Public License as published by the Free
-- Software Foundation; either version 2 of the License, or (at your option)
-- any later version.
--
-- VikiKodukas is distributed in the hope that it will be useful, but WITHOUT ANY
-- WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
-- FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
-- details.
--
-- You should have received a copy of the GNU General Public License along with
-- VikiKodukas; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
-- Suite 330, Boston, MA  02111-1307  USA
--
-- copyright  2007 Rene Saarsoo
--


DROP TABLE IF EXISTS pages;
CREATE TABLE pages (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE,
    title_et VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    title_ru VARCHAR(255) NOT NULL,
    content_et TEXT NOT NULL,
    content_en TEXT NOT NULL,
    content_ru TEXT NOT NULL
) Engine=InnoDB
  CHARACTER SET utf8
  COLLATE utf8_estonian_ci;

DROP TABLE IF EXISTS files;
CREATE TABLE files (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    src VARCHAR(255) NOT NULL,
    title_et VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) NOT NULL,
    title_ru VARCHAR(255) NOT NULL,
    type ENUM('FILE', 'IMAGE') NOT NULL DEFAULT 'FILE',
    page_id INT NOT NULL,
    FOREIGN KEY (page_id) REFERENCES pages (id)
) Engine=InnoDB
  CHARACTER SET utf8
  COLLATE utf8_estonian_ci;

DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
) Engine=InnoDB
  CHARACTER SET utf8
  COLLATE utf8_estonian_ci;
