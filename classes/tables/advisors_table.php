<?php

namespace local_organization;

require_once($CFG->libdir . '/tablelib.php');

use local_organization\base;


class advisors_table extends \table_sql
{

    protected $showDelButtons = false;
    /**
     * advisors_table constructor.
     * @param $uniqueid
     */
    public function __construct($uniqueid)
    {
        GLOBAL $USER;
        parent::__construct($uniqueid);

        // Define the columns to be displayed
        $columns = array('firstname', 'lastname', 'role', 'context', 'actions');
        $this->define_columns($columns);

        // Define the headers for the columns
        $headers = array(
            get_string('firstname', 'local_organization'),
            get_string('lastname', 'local_organization'),
            get_string('role', 'local_organization'),
            get_string('context', 'local_organization'),
            '',
        );

        //Capabilities
        $system_context = \context_system::instance();
        if (has_capability('local/organization:unit_delete', $system_context, $USER->id)) {
            $this->showDelButtons = true;
        }

        $this->define_headers($headers);
    }

    /**
     * Function to define the actions column
     *
     * @param $values
     * @return string
     */
    public function col_actions($values)
    {
        global $OUTPUT, $DB, $USER;
        // Get number of advisors in unt or departments
        $advisor_count = $DB->count_records('local_organization_advisor', ['instance_id' => $values->instance_id, 'user_context' => $values->user_context]);

        $actions = [
            //'edit_url' => new \moodle_url('/local/organization/edit_advisor.php', array('id' => $values->id)),
            'id' => $values->id,
            'role_id' => $values->role_id,
            'user_context' => $values->user_context,
            'advisor_count' => $advisor_count,
            'showDelButtons' => $this->showDelButtons
        ];

        return $OUTPUT->render_from_template('local_organization/advisors_table_action_buttons', $actions);
    }
}