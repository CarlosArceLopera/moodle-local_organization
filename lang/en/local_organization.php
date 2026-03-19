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
 * Plugin strings are defined here.
 *
 * @package     local_organization
 * @category    string
 * @copyright   2024 York University <itinnovation@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['actions'] = 'Actions';
$string['add_campus'] = 'Adding Campus';
$string['add_department'] = 'Adding Department';
$string['add_unit'] = 'Adding Unit/Faculty';
$string['advisors'] = 'Advisors';
$string['campus'] = 'Campus';
$string['campuses'] = 'Campuses';
$string['cancel'] = 'Cancel';
$string['context'] = 'Unit/Department';
$string['could_not_delete_campus'] = 'The campus record could not be deleted. This could be because one or more units '
    . 'are associated with this campus or an error has occurred.';
$string['could_not_delete_unit'] = 'The unit record could not be deleted. This could be because one or more departments '
    . 'are associated with this unit or an error has occurred.';
$string['could_not_delete_advisor'] = 'The advisor record could not be deleted.';
$string['delete'] = 'Delete';
$string['delete_campus'] = 'Are you sure you want to delete the campus? This action cannot be undone.';
$string['delete_department'] = 'Are you sure you want to delete the department? This action cannot be undone.';
$string['delete_unit'] = 'Are you sure you want to delete the unit? This action cannot be undone.';
$string['delete_advisor'] = 'Are you sure you want to delete the advisor? This action cannot be undone.';
$string['department'] = 'Department';
$string['departments'] = 'Departments';
$string['add_advisor'] = 'Add Advisor';
$string['edit'] = 'Edit';
$string['edit_campus'] = 'Edit Campus';
$string['edit_department'] = 'Edit Department';
$string['edit_unit'] = 'Edit Unit/Faculty';
$string['filter'] = 'Filter';
$string['filters'] = 'Filters';
$string['firstname' ] = 'First Name';
$string['id_number'] = 'ID Number';
$string['lastname'] = 'Last Name';
$string['name'] = 'Name';
$string['new'] = 'New';
$string['select_campus'] = 'Select Campus';
$string['select_department'] = 'Select Department';
$string['select_unit'] = 'Select Unit/Faculty';
$string['select_user'] = 'Select User';
$string['short_name'] = 'Abbreviation';
$string['toomanyresults'] = 'Too many results..';
$string['pluginname'] = 'Organization';
$string['manageorg'] = 'Manage Organization';
$string['reset'] = 'Reset';
$string['return'] = 'Return';
$string['role'] = 'Role';
$string['unit' ] = 'Unit/Faculty';
$string['units'] = 'Units/Faculties';
$string['users'] = 'Users';

/**
 * Capabilites
 */
$string['organization:advisor_edit'] = 'Create/Edit Advisors';
$string['organization:advisor_view'] = 'View Advisors';
$string['organization:advisor_delete'] = 'Delete Advisors';
$string['organization:unit_edit'] = 'Create/Edit Campuses, Units/Faculties, Departments';
$string['organization:unit_view'] = 'View Campuses, Units/Faculties, Departments';
$string['organization:unit_delete'] = 'Delete Campuses, Units/Faculties, Departments';

/**
 * Privacy API
 */
$string['privacy:campus'] = 'Campus';
$string['privacy:campus:usermodified'] = 'User who last modified the campus';
$string['privacy:campus:timecreated'] = 'Time campus was created';
$string['privacy:campus:timemodified'] = 'Time campus was last modified';
$string['privacy:campuses'] = 'Campuses';

$string['privacy:unit'] = 'Unit/Faculty';
$string['privacy:unit:usermodified'] = 'User who last modified the unit';
$string['privacy:unit:timecreated'] = 'Time unit was created';
$string['privacy:unit:timemodified'] = 'Time unit was last modified';
$string['privacy:units'] = 'Units/Faculties';

$string['privacy:department'] = 'Department';
$string['privacy:department:usermodified'] = 'User who last modified the department';
$string['privacy:department:timecreated'] = 'Time department was created';
$string['privacy:department:timemodified'] = 'Time department was last modified';
$string['privacy:departments'] = 'Departments';

$string['privacy:advisor'] = 'Advisor Assignment';
$string['privacy:advisor:user_id'] = 'The advisor user';
$string['privacy:advisor:role_id'] = 'The role of the advisor';
$string['privacy:advisor:instance_id'] = 'The unit or department instance';
$string['privacy:advisor:user_context'] = 'Whether advisor is at unit or department level';
$string['privacy:advisor:usermodified'] = 'User who last modified the advisor assignment';
$string['privacy:advisor:timecreated'] = 'Time advisor assignment was created';
$string['privacy:advisor:timemodified'] = 'Time advisor assignment was last modified';
$string['privacy:advisors'] = 'Advisor Assignments';