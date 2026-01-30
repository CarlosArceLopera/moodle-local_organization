<?php

namespace local_organization;

require_once($CFG->libdir . '/tablelib.php');

use local_organization\base;

class unit_table extends \table_sql
{
    protected $showEditButtons = false;
    protected $showDelButtons = false;

    /**
     * unit_table constructor.
     * @param $uniqueid
     */
    public function __construct($uniqueid)
    {
        GLOBAL $USER;
        parent::__construct($uniqueid);

        // Define the columns to be displayed
        $columns = array('name', 'shortname', 'actions');
        $this->define_columns($columns);

        // Define the headers for the columns
        $headers = array(
            get_string('name', 'local_organization'),
            get_string('short_name', 'local_organization'),
            '',
        );
        //Capabilities
        $system_context = \context_system::instance();
        if (has_capability('local/organization:unit_edit', $system_context, $USER->id)) {
            $this->showEditButtons = true;
        }
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
        global $OUTPUT, $DB, $CFG, $USER;

        // Get number of departments in the unit
        $department_count = $DB->count_records('local_organization_dept', array('unit_id' => $values->id));
        // Get number of advisors in the dept
        $advisor_count = $DB->count_records('local_organization_advisor', array('instance_id' => $values->id, 'user_context' => base::CONTEXT_UNIT));

        $actions = [
            'edit_url' => $CFG->wwwroot . '/local/organization/edit_unit.php?id=' . $values->id . '&campus_id=' .  $values->campus_id,
            'id' => $values->id,
            'department_count' => $department_count,
            'advisor_count' => $advisor_count,
            'user_context' => base::CONTEXT_UNIT,
            'campus_id' =>  $values->campus_id,
            'showEditButtons' => $this->showEditButtons,
            'showDelButtons' => $this->showDelButtons
        ];
        return $OUTPUT->render_from_template('local_organization/unit_table_action_buttons', $actions);
    }
}
