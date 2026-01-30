<?php
require_once('../../config.php');
include_once('classes/tables/unit_table.php');
include_once('classes/forms/unit_filter_form.php');

use local_organization\base;
use local_organization\unit_table;
use local_organization\unit_filter_form;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;


require_login(1, false);

$context = context_system::instance();

// Capability to view/edit page
$hasCapability_view_edit = has_capability('local/organization:unit_view', $PAGE->context, $USER->id);
if (!$hasCapability_view_edit) {
    redirect($CFG->wwwroot . '/my');
}

// Load AMD module
$PAGE->requires->js_call_amd('local_organization/units', 'init');
// Load CSS file
$PAGE->requires->css('/local/organization/css/general.css');

$term = optional_param('q', '', PARAM_TEXT);
$campus_id = optional_param('campus_id', 0, PARAM_INT);

$formdata = new stdClass();
$formdata->name = $term;
$formdata->campus_id = $campus_id;

$mform = new unit_filter_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present
    redirect($CFG->wwwroot . '/local/organization/units.php?campus_id=' . $campus_id);
} else if ($data = $mform->get_data()) { // form is submitted with filter
    // Process validated data
    $term_filter = $data->q;
    $campus_id = $data->campus_id;
} else {
    // Display the form
//    $mform->display();
}

$table = new unit_table('local_organization_units_table');
$params = array('campus_id' => $campus_id);
// Define the SQL query to fetch data
//retrieve campus id from form data when submit
$sql = "campus_id = :campus_id";
if (!empty($term_filter)) {
    $sql .= " AND (" . $DB->sql_like('LOWER(name)', ':searchname', false) . " OR " .
            $DB->sql_like('LOWER(shortname)', ':searchshort', false) . ")";
    $params['searchname'] = '%' . $DB->sql_like_escape(strtolower($term_filter)) . '%';
    $params['searchshort'] = '%' . $DB->sql_like_escape(strtolower($term_filter)) . '%';
}
// Define the SQL query to fetch data
$table->set_sql('*', '{local_organization_unit}', $sql, $params);

// Define the base URL for the table
$table->define_baseurl(new moodle_url('/local/organization/units.php?campus_id=' . $campus_id));

base::page(
    new moodle_url('/local/organization/units.php?campus_id=' . $campus_id),
    get_string('units', 'local_organization'),
    get_string('units', 'local_organization')
);

echo $OUTPUT->header();
// Set up the table
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

