<?php
require_once('../../config.php');
include_once('classes/tables/department_table.php');
include_once('classes/forms/department_filter_form.php');

use local_organization\base;
use local_organization\department_table;
use local_organization\department_filter_form;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;


require_login(1, false);

$context = context_system::instance();
if (!has_capability('local/organization:unit_view', $PAGE->context, $USER->id)) {
    redirect($CFG->wwwroot . '/my');
}
// Load AMD module
$PAGE->requires->js_call_amd('local_organization/departments', 'init');
// Load CSS file
$PAGE->requires->css('/local/organization/css/general.css');
const USER_CONTEXT = 'DEPARTMENT';

$term = optional_param('q', '', PARAM_TEXT);
$campus_id = required_param('campus_id', PARAM_INT);
$unit_id = required_param('unit_id', PARAM_INT);

$formdata = new stdClass();
$formdata->name = $term;
$formdata->user_context = USER_CONTEXT;
$formdata->campus_id = $campus_id;
$formdata->unit_id = $unit_id;

$mform = new department_filter_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present
    redirect($CFG->wwwroot . '/local/organization/departments.php?unit_id= '.$unit_id. '&campus_id='.$campus_id);

} else if ($data = $mform->get_data()) { // on submit and reset/refresh of the button group form
    // Process validated data
    $term_filter = $data->q;
    $campus_id = $data->campus_id;
    $unit_id = $data->unit_id;
} else {
    // Display the form
//    $mform->display();
}

$params = array('unit_id' => $unit_id);
$table = new department_table('local_organization_departments_table', $formdata);

// Define the SQL query to fetch data
$sql = "unit_id = :unit_id";
if (!empty($term_filter)) {
    $sql .= " AND (" . $DB->sql_like('LOWER(name)', ':searchname', false) . " OR " .
            $DB->sql_like('LOWER(shortname)', ':searchshort', false) . ")";
    $params['searchname'] = '%' . $DB->sql_like_escape(strtolower($term_filter)) . '%';
    $params['searchshort'] = '%' . $DB->sql_like_escape(strtolower($term_filter)) . '%';
}

// Define the SQL query to fetch data
$table->set_sql('*', '{local_organization_dept}', $sql, $params);

// Define the base URL for the table
$table->define_baseurl(new moodle_url('/local/organization/departments.php', ['unit_id' => $unit_id, 'campus_id' => $campus_id]));

base::page(
    new moodle_url('/local/organization/departments.php'),
    get_string('departments', 'local_organization'),
    get_string('departments', 'local_organization')
);

echo $OUTPUT->header();
// Set up the table
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

