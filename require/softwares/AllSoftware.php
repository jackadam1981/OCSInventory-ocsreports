<?php
/*
 * Copyright 2005-2016 OCSInventory-NG/OCSInventory-ocsreports contributors.
 * See the Contributors file for more details about them.
 *
 * This file is part of OCSInventory-NG/OCSInventory-ocsreports.
 *
 * OCSInventory-NG/OCSInventory-ocsreports is free software: you can redistribute
 * it and/or modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation, either version 2 of the License,
 * or (at your option) any later version.
 *
 * OCSInventory-NG/OCSInventory-ocsreports is distributed in the hope that it
 * will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OCSInventory-NG/OCSInventory-ocsreports. if not, write to the
 * Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 */

 /**
  * Class for software categories
  */
class AllSoftware
{

    public function software_link_treatment() {
        // First clean software_link
        $delSoftLink = $this->delete_software_link();
        // Get all softwares
        $allSoft = $this->get_software_informations();
        // Get categories
        $allSoftCat = $this->get_software_categories_link_informations();

        $software = [];

        if($allSoft) {
            while($item_all_soft = mysqli_fetch_array($allSoft)) {
                $software[$item_all_soft['identifier']]['NAME_ID'] = intval($item_all_soft['NAME_ID']);
                $software[$item_all_soft['identifier']]['PUBLISHER_ID'] = intval($item_all_soft['PUBLISHER_ID']);
                $software[$item_all_soft['identifier']]['VERSION_ID'] = intval($item_all_soft['VERSION_ID']);
                $software[$item_all_soft['identifier']]['CATEGORY_ID'] = null;
                $software[$item_all_soft['identifier']]['COUNT'] = intval($item_all_soft['nb']);
            }
        }

        if($allSoftCat && $allSoftCat->rows != 0) {
            foreach($software as $identifier => $values) {
                while($items = mysqli_fetch_array($allSoftCat)) {
                    if($values['NAME_ID'] == $items['NAME_ID'] && $values['PUBLISHER_ID'] == $items['PUBLISHER_ID'] && $values['VERSION_ID'] == $items['VERSION_ID']) {
                        $software[$identifier]['CATEGORY_ID'] = intval($items['CATEGORY_ID']);
                    }
                }
            }
        }

        foreach($software as $identifier => $values) {
            $sql = "INSERT INTO `software_link` (`IDENTIFIER`, `NAME_ID`, `PUBLISHER_ID`, `VERSION_ID`, `CATEGORY_ID`, `COUNT`)
                    VALUES ('%s', %s, %s, %s, %s, %s)";
            $arg = array($identifier, $values['NAME_ID'], $values['PUBLISHER_ID'], $values['VERSION_ID'], $values['CATEGORY_ID'], $values['COUNT']);
            $result = mysql2_query_secure($sql, $_SESSION['OCS']["writeServer"], $arg);
            var_dump($result);
            die();
            if(!$result) {
                error_log(print_r("An error occure when attempt to insert software with identifier : ".$identifier, true));
            }
        }

    }

    private function delete_software_link() {
        $sql = "DELETE FROM `software_link`";
        $result = mysql2_query_secure($sql, $_SESSION['OCS']["writeServer"]);
    }

    private function get_software_informations() {
        $sql = "SELECT CONCAT(n.NAME,';',p.PUBLISHER,';',v.VERSION) as identifier, 
                s.VERSION_ID, s.NAME_ID, s.PUBLISHER_ID, 
                COUNT(CONCAT(s.NAME_ID, s.PUBLISHER_ID, s.VERSION_ID)) as nb 
                FROM software s 
                LEFT JOIN software_name n ON s.NAME_ID = n.ID 
                LEFT JOIN software_publisher p ON s.PUBLISHER_ID = p.ID 
                LEFT JOIN software_version v ON s.VERSION_ID = v.ID
                GROUP BY s.NAME_ID, s.PUBLISHER_ID, s.VERSION_ID";

        $result = mysql2_query_secure($sql, $_SESSION['OCS']["readServer"]);

        return $result;
    }

    private function get_software_categories_link_informations() {
        $sql = "SELECT * FROM `software_categories_link`";
        $result = mysql2_query_secure($sql, $_SESSION['OCS']["readServer"]);

        return $result;
    }

}