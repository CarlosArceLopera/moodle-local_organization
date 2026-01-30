<?php

namespace local_organization;

require_once($CFG->libdir . '/tablelib.php');

use local_organization\base;

class campus_table extends \table_sql
{

    protected $showEditButtons = false;
    protected $showDelButtons = false;

    /**
     * campus_table constructor.
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
        global $OUTPUT, $DB;
        // Get number of units in the campus
        $unit_count = $DB->count_records('local_organization_unit', array('campus_id' => $values->id));
        // Get number of advisors in the dept
        $advisor_count = $DB->count_records('local_organization_advisor', array('instance_id' => $values->id, 'user_context' => base::CONTEXT_CAMPUS));


        $actions = [
            'edit_url' => new \moodle_url('/local/organization/edit_campus.php', array('id' => $values->id)),
            'id' => $values->id,
            'advisor_count' => $advisor_count,
            'user_context' => base::CONTEXT_CAMPUS,
            'unit_count' => $unit_count,
            'campus_id' => $values->id,
            'showEditButtons' => $this->showEditButtons,
            'showDelButtons' => $this->showDelButtons,
        ];
        return $OUTPUT->render_from_template('local_organization/campus_table_action_buttons', $actions);
    }
}