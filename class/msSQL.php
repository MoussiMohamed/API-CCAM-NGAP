<?php
/*
 * This file is part of MedShakeEHR.
 *
 * Copyright (c) 2017
 * Bertrand Boutillier <b.boutillier@gmail.com>
 * http://www.medshake.net
 *
 * MedShakeEHR is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * MedShakeEHR is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MedShakeEHR.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Fonctions MySQL
 *
 * @author Bertrand Boutillier <b.boutillier@gmail.com>
 * @contrib fr33z00 <https://github.com/fr33z00>
 */

class msSQL
{

    /**
     * Se connecter à la base
     * @return resource connexion
     */
    public static function sqlConnect()
    {
        global $p;
        if (isset($_ENV['SQL_SERVEUR'])) {
            $mysqli = new mysqli($_ENV['SQL_SERVEUR'], $_ENV['SQL_USER'], $_ENV['SQL_PASS'], $_ENV['SQL_BASE']);
        } else {
            $mysqli = new mysqli($p['config']['sqlServeur'], $p['config']['sqlUser'], $p['config']['sqlPass'], $p['config']['sqlBaseCcam']);
        }
        $mysqli->set_charset("utf8");
        if (mysqli_connect_errno()) {
            die('Echec de connexion à la base de données');
        } else {
            return $mysqli;
        }
    }


    /**
     * Nettoyer une variable avant insertion en bdd
     * @param  string $var variable
     * @return string      variable échappée
     */
    public static function cleanVar($var)
    {
        global $mysqli;
        $var = $mysqli->real_escape_string(trim($var));
        return $var;
    }

    /**
     * Fonction query de base
     * @param  string $sql commande SQL
     * @return resource      résultat mysql
     */
    public static function sqlQuery($sql)
    {
        global $mysqli;
        $query = $mysqli->query($sql);
        if ($mysqli->connect_errno) {
            return null;
        } else {
            return $query;
        }
    }

    /**
     * Sortir un champ unique d'une ligne unique
     * @param  string $sql commande SQL
     * @return string      valeur du champ unique demandé
     */
    public static function sqlUniqueChamp($sql)
    {
        $query = msSQL::sqlQuery($sql);
        if ($query and mysqli_num_rows($query) == 1) {
            $query->data_seek(0);
            $row = $query->fetch_row();
            return $row[0];
        } else {
            return null;
        }
    }

    /**
     * Sortir une ligne unique en ArrayAccess
     * @param  string $sql commande SQL
     * @return array      array
     */
    public static function sqlUnique($sql)
    {
        $query = msSQL::sqlQuery($sql);
        if ($query and mysqli_num_rows($query) == 1) {
            $query->data_seek(0);
            return $query->fetch_array(MYSQLI_ASSOC);
        } else {
            return null;
        }
    }

    /**
     * Sortir des lignes en array
     * @param  string $sql commande SQL
     * @return array      array
     */
    public static function sql2tab($sql)
    {
        $query = msSQL::sqlQuery($sql);
        if ($query and mysqli_num_rows($query) > 0) {
            while ($row = $query->fetch_array(MYSQLI_ASSOC)) {
                if ($row) {
                    $result[] = $row;
                }
            };
            return $result;
        } else {
            return null;
        }
    }

    /**
     * Sortir un array avec en key le champ mysql sépcifié et l'éventuelle unique value
     * @param  string $sql   commande SQL
     * @param  string $key   colonne qui servira de clef
     * @param  string $value colonne qui servira de value
     * @return array        Array key => value
     */
    public static function sql2tabKey($sql, $key, $value = '')
    {
        if ($tab = msSQL::sql2tab($sql)) {
            foreach ($tab as $k => $v) {
                if ($value) {
                    $returntab[$v[$key]] = $v[$value];
                } else {
                    $returntab[$v[$key]] = $v;
                }
            }
            return $returntab;
        } else {
            return false;
        }
    }

    /**
     * Retourner un simple array avec clef numérique ascendante
     * @param  string $sql commande SQL
     * @return array      array 0=> 1=> ...
     */
    public static function sql2tabSimple($sql)
    {
        $query = msSQL::sqlQuery($sql);
        if ($query and mysqli_num_rows($query) > 0) {
            while ($row = $query->fetch_array(MYSQLI_NUM)) {
                if ($row) {
                    $result[] = $row[0];
                }
            };
            return $result;
        } else {
            return null;
        }
    }
}
