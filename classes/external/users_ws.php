<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Users web service.
 *
 * @package     local_organization
 * @copyright   2024 York University <itinnovation@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use local_organization\users;

class local_organization_users_ws extends external_api
{
    /**
     * Returns users parameters
     * @return external_function_parameters
     **/

    public static function get_users_parameters() {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_TEXT, 'User first or last name', false)
            )
        );
    }

    /** Returns users
     * @global moodle_database $DB
     * @return string users
     **/

    public static function get_users($name="") {
        global $DB;
        $params = self::validate_parameters(self::get_users_parameters(), array('name' => $name));
        $mdl_users = [];
        if (strlen($name) >= 3) {
            $searchname = str_replace(' ', '%', $name);
            $searchparam = '%' . $DB->sql_like_escape($searchname) . '%';

            $sql = "SELECT * FROM {user} u WHERE " .
                   $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ':searchname1', false) . " OR " .
                   $DB->sql_like('u.idnumber', ':searchname2', false) . " OR " .
                   $DB->sql_like('u.email', ':searchname3', false) . " OR " .
                   $DB->sql_like('u.username', ':searchname4', false) . " " .
                   "ORDER BY u.lastname";

            $mdl_users = $DB->get_records_sql($sql, [
                'searchname1' => $searchparam,
                'searchname2' => $searchparam,
                'searchname3' => $searchparam,
                'searchname4' => $searchparam
            ]);
        }
        $users = [];
        $i = 0;
        foreach ($mdl_users as $u) {
            $users[$i]['id'] = $u->id;
            $users[$i]['firstname'] = $u->firstname;
            $users[$i]['lastname'] = $u->lastname;
            $users[$i]['email'] = $u->email;
            $users[$i]['idnumber'] = $u->idnumber;
            $i++;
        }
        return $users;
    }

    /** Get Users
     * @return single_structure_description
     **/

    public static function user_details() {
        $fields = array(
            'id' => new external_value(PARAM_INT, 'Record id', false),
            'firstname' => new external_value(PARAM_TEXT, 'User first name', true),
            'lastname' => new external_value(PARAM_TEXT, 'User last name', true),
            'email' => new external_value(PARAM_TEXT, 'email', true),
            'idnumber' => new external_value(PARAM_TEXT, 'ID Number', true));
        return new external_single_structure($fields);
    }

    /** Returns users result value
     *  @return external_description
     **/
    public static function get_users_returns() {
        return new external_multiple_structure(self::user_details());
    }

    /**
     * Returns users parameters
     * @return external_function_parameters
     **/

    public static function get_roles_parameters() {
        return new external_function_parameters(
            array(
                'name' => new external_value(PARAM_TEXT, 'User first or last name', VALUE_OPTIONAL)
            )
        );
    }

    /** Returns Roles
     * @global moodle_database $DB
     * @return string users
     **/

    public static function get_roles( $name="") {
        global $DB;
        $params = self::validate_parameters(
            self::get_users_parameters(),
            array(
                'name' => $name
            )
        );
        $existing_roles = [];
        if (strlen($name) >= 3) {
            $searchparam = '%' . $DB->sql_like_escape($name) . '%';

            $sql = "SELECT * FROM {role} u WHERE " .
                   $DB->sql_like('name', ':searchname1', false) . " OR " .
                   $DB->sql_like('shortname', ':searchname2', false);

            // Get the data
            $existing_roles = $DB->get_records_sql($sql, [
                'searchname1' => $searchparam,
                'searchname2' => $searchparam
            ]);
        }
        $roles = [];
        $i = 0;
        foreach ($existing_roles as $r) {
            $roles[$i]['id'] = $r->id;
            // System roles have no name
            if (empty($r->name)) {
                switch($r->shortname) {
                    case 'editingteacher':
                    case 'teacher':
                        $roles[$i]['name'] = get_string('legacy:' . $r->shortname, 'core_role');
                        break;
                    default:
                        $roles[$i]['name'] = get_string($r->shortname, 'core_role');
                        break;
                }

            }
            else {
                $roles[$i]['name'] = $r->name;
            }
            $i++;
        }
        return $roles;
    }

    /** Get Users
     * @return single_structure_description
     **/

    public static function roles_details() {
        $fields = array(
            'id' => new external_value(PARAM_INT, 'Record id', false),
            'name' => new external_value(PARAM_TEXT, 'User first name', true)
        );
        return new external_single_structure($fields);
    }

    /** Returns users result value
     *  @return external_description
     **/
    public static function get_roles_returns() {
        return new external_multiple_structure(self::roles_details());
    }
}