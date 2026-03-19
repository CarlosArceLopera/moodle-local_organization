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

namespace local_organization\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\userlist;
use core_privacy\local\request\approved_userlist;

/**
 * Privacy provider implementation for local_organization.
 *
 * @package     local_organization
 * @copyright   2026 Carlos Arce <carlosarcelopera@catalyst-ca.net>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements
    \core_privacy\local\request\core_userlist_provider,
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider {

    /**
     * Return the fields which contain personal data.
     *
     * @param collection $collection the iterable of data to add metadata to.
     * @return collection the collection of metadata
     */
    public static function get_metadata(collection $collection): collection {
        $collection->add_database_table(
            'local_organization_campus',
            [
                'usermodified' => 'privacy:campus:usermodified',
                'timecreated' => 'privacy:campus:timecreated',
                'timemodified' => 'privacy:campus:timemodified',
            ],
            'privacy:campus'
        );

        $collection->add_database_table(
            'local_organization_unit',
            [
                'usermodified' => 'privacy:unit:usermodified',
                'timecreated' => 'privacy:unit:timecreated',
                'timemodified' => 'privacy:unit:timemodified',
            ],
            'privacy:unit'
        );

        $collection->add_database_table(
            'local_organization_dept',
            [
                'usermodified' => 'privacy:department:usermodified',
                'timecreated' => 'privacy:department:timecreated',
                'timemodified' => 'privacy:department:timemodified',
            ],
            'privacy:department'
        );

        $collection->add_database_table(
            'local_organization_advisor',
            [
                'user_id' => 'privacy:advisor:user_id',
                'role_id' => 'privacy:advisor:role_id',
                'instance_id' => 'privacy:advisor:instance_id',
                'user_context' => 'privacy:advisor:user_context',
                'usermodified' => 'privacy:advisor:usermodified',
                'timecreated' => 'privacy:advisor:timecreated',
                'timemodified' => 'privacy:advisor:timemodified',
            ],
            'privacy:advisor'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information for the specified user.
     *
     * @param int $userid the user id
     * @return contextlist the list of contexts containing user data
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Early return if user has no data in any organization tables.
        if (!self::user_has_data($userid)) {
            return $contextlist;
        }

        $contextlist->add_system_context();

        return $contextlist;
    }

    /**
     * Check if a user has data in any organization tables.
     *
     * @param int $userid the user id
     * @return bool true if user has any data in organization tables
     */
    private static function user_has_data(int $userid): bool {
        global $DB;

        return $DB->record_exists('local_organization_campus', ['usermodified' => $userid]) ||
               $DB->record_exists('local_organization_unit', ['usermodified' => $userid]) ||
               $DB->record_exists('local_organization_dept', ['usermodified' => $userid]) ||
               $DB->record_exists_select(
                   'local_organization_advisor',
                   'user_id = :u1 OR usermodified = :u2',
                   ['u1' => $userid, 'u2' => $userid]
               );
    }

    /**
     * Export user data stored in local_organization tables.
     *
     * @param approved_contextlist $contextlist the list of contexts approved for export
     */
    public static function export_user_data(approved_contextlist $contextlist) {
        global $DB;

        $userid = $contextlist->get_user()->id;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            // Export campuses.
            $campuses = $DB->get_records_select(
                'local_organization_campus',
                'usermodified = :userid',
                ['userid' => $userid]
            );
            if (!empty($campuses)) {
                $data = [];
                foreach ($campuses as $campus) {
                    $data[] = (object) [
                        'name' => $campus->name,
                        'shortname' => $campus->shortname,
                        'usermodified' => transform::user($campus->usermodified),
                        'timecreated' => transform::datetime($campus->timecreated),
                        'timemodified' => transform::datetime($campus->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_organization'), get_string('privacy:campuses', 'local_organization')],
                    (object) ['campuses' => $data]
                );
            }

            // Export units.
            $units = $DB->get_records_select(
                'local_organization_unit',
                'usermodified = :userid',
                ['userid' => $userid]
            );
            if (!empty($units)) {
                $data = [];
                foreach ($units as $unit) {
                    $data[] = (object) [
                        'name' => $unit->name,
                        'shortname' => $unit->shortname,
                        'id_number' => $unit->id_number,
                        'usermodified' => transform::user($unit->usermodified),
                        'timecreated' => transform::datetime($unit->timecreated),
                        'timemodified' => transform::datetime($unit->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_organization'), get_string('privacy:units', 'local_organization')],
                    (object) ['units' => $data]
                );
            }

            // Export departments.
            $departments = $DB->get_records_select(
                'local_organization_dept',
                'usermodified = :userid',
                ['userid' => $userid]
            );
            if (!empty($departments)) {
                $data = [];
                foreach ($departments as $dept) {
                    $data[] = (object) [
                        'name' => $dept->name,
                        'shortname' => $dept->shortname,
                        'id_number' => $dept->id_number,
                        'usermodified' => transform::user($dept->usermodified),
                        'timecreated' => transform::datetime($dept->timecreated),
                        'timemodified' => transform::datetime($dept->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_organization'), get_string('privacy:departments', 'local_organization')],
                    (object) ['departments' => $data]
                );
            }

            // Export advisor assignments.
            $advisors = $DB->get_records_select(
                'local_organization_advisor',
                'user_id = :uid1 OR usermodified = :uid2',
                ['uid1' => $userid, 'uid2' => $userid]
            );
            if (!empty($advisors)) {
                $data = [];
                foreach ($advisors as $advisor) {
                    $data[] = (object) [
                        'user_id' => $advisor->user_id ? transform::user($advisor->user_id) : null,
                        'is_current_user' => ($advisor->user_id == $userid),
                        'role_id' => $advisor->role_id,
                        'instance_id' => $advisor->instance_id,
                        'user_context' => $advisor->user_context,
                        'usermodified' => transform::user($advisor->usermodified),
                        'timecreated' => transform::datetime($advisor->timecreated),
                        'timemodified' => transform::datetime($advisor->timemodified),
                    ];
                }
                writer::with_context($context)->export_data(
                    [get_string('pluginname', 'local_organization'), get_string('privacy:advisors', 'local_organization')],
                    (object) ['advisors' => $data]
                );
            }
        }
    }

    /**
     * Delete all user data for the specified user, in the specified contexts.
     *
     * @param approved_contextlist $contextlist the list of contexts approved for deletion
     */
    public static function delete_data_for_user(approved_contextlist $contextlist) {
        global $DB;

        foreach ($contextlist->get_contexts() as $context) {
            if ($context->contextlevel !== CONTEXT_SYSTEM) {
                continue;
            }

            $userid = $contextlist->get_user()->id;

            // Anonymize campus modifications.
            $DB->set_field_select(
                'local_organization_campus',
                'usermodified',
                0,
                'usermodified = :userid',
                ['userid' => $userid]
            );

            // Anonymize unit modifications.
            $DB->set_field_select(
                'local_organization_unit',
                'usermodified',
                0,
                'usermodified = :userid',
                ['userid' => $userid]
            );

            // Anonymize department modifications.
            $DB->set_field_select(
                'local_organization_dept',
                'usermodified',
                0,
                'usermodified = :userid',
                ['userid' => $userid]
            );

            // Anonymize advisor records.
            $DB->set_field_select(
                'local_organization_advisor',
                'user_id',
                0,
                'user_id = :userid',
                ['userid' => $userid]
            );

            $DB->set_field_select(
                'local_organization_advisor',
                'usermodified',
                0,
                'usermodified = :userid',
                ['userid' => $userid]
            );
        }
    }

    /**
     * Delete all user data for all users in the specified context.
     *
     * @param context $context the context to delete data for
     */
    public static function delete_data_for_all_users_in_context(\context $context) {
        // Only system context is relevant.
        if ($context->contextlevel != CONTEXT_SYSTEM) {
            return;
        }

        global $DB;

        // Set all usermodified to 0 (anonymize).
        $DB->set_field('local_organization_campus', 'usermodified', 0);
        $DB->set_field('local_organization_unit', 'usermodified', 0);
        $DB->set_field('local_organization_dept', 'usermodified', 0);
        $DB->set_field('local_organization_advisor', 'user_id', 0);
        $DB->set_field('local_organization_advisor', 'usermodified', 0);
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist the userlist object to add users to
     */
    public static function get_users_in_context(userlist $userlist) {
        // Only system context is relevant.
        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $sql = "
            SELECT usermodified AS userid FROM {local_organization_campus} WHERE usermodified <> 0
            UNION
            SELECT usermodified FROM {local_organization_unit} WHERE usermodified <> 0
            UNION
            SELECT usermodified FROM {local_organization_dept} WHERE usermodified <> 0
            UNION
            SELECT user_id FROM {local_organization_advisor} WHERE user_id <> 0
            UNION
            SELECT usermodified FROM {local_organization_advisor} WHERE usermodified <> 0
        ";

        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Delete multiple users within a context.
     *
     * @param approved_userlist $userlist the approved list of users to delete
     */
    public static function delete_data_for_users(approved_userlist $userlist) {
        global $DB;

        // Only system context is relevant.
        if ($userlist->get_context()->contextlevel !== CONTEXT_SYSTEM) {
            return;
        }

        $userids = $userlist->get_userids();
        if (empty($userids)) {
            return;
        }

        [$usersql, $params] = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED);

        // Anonymize campus modifications.
        $DB->set_field_select(
            'local_organization_campus',
            'usermodified',
            0,
            "usermodified $usersql",
            $params
        );

        // Anonymize unit modifications.
        $DB->set_field_select(
            'local_organization_unit',
            'usermodified',
            0,
            "usermodified $usersql",
            $params
        );

        // Anonymize department modifications.
        $DB->set_field_select(
            'local_organization_dept',
            'usermodified',
            0,
            "usermodified $usersql",
            $params
        );

        // Anonymize advisor user assignments.
        $DB->set_field_select(
            'local_organization_advisor',
            'user_id',
            0,
            "user_id $usersql",
            $params
        );

        // Anonymize advisor modifications.
        $DB->set_field_select(
            'local_organization_advisor',
            'usermodified',
            0,
            "usermodified $usersql",
            $params
        );
    }
}
