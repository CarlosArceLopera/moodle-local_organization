<?php

namespace local_organization;

require_once($CFG->libdir . '/tablelib.php');

use local_organization\base;

class department_table extends \table_sql
{
    protected $showEditButtons = false;
    protected $showDelButtons = false;
    protected $campus_id; // passed to table since we dont get it table sql
    /**
     * department_table constructor.
     * @param $uniqueid
     */
    public function __construct($uniqueid, $params)
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
            ''
        );
        // campus_id doesn't come from the sql in the table so have get it from elsewhere/prev pages
        $this->campus_id = $params->campus_id;

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
        global $OUTPUT, $CFG, $DB, $USER;
        // Get count for button
        $advisor_count = $DB->count_records('local_organization_advisor', array('instance_id' => $values->id, 'user_context' => base::CONTEXT_DEPARTMENT));
        $actions = [
            'edit_url' => $CFG->wwwroot . '/local/organization/edit_department.php?id=' . $values->id . '&unit_id=' . $values->unit_id . '&campus_id=' . $this->campus_id,
            'id' => $values->id,
            'advisor_count' => $advisor_count,
            'user_context' => base::CONTEXT_DEPARTMENT,
            'unit_id' => $values->unit_id,
            'campus_id' => $this->campus_id,
            'showEditButtons' => $this->showEditButtons,
            'showDelButtons' => $this->showDelButtons,
        ];
        return $OUTPUT->render_from_template('local_organization/department_table_action_buttons', $actions);
    }
}