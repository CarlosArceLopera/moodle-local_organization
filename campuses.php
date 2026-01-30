<?php
require_once('../../config.php');
include_once('classes/tables/campus_table.php');
include_once('classes/forms/campus_filter_form.php');

use local_organization\base;
use local_organization\campus_table;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;


require_login(1, false);

$context = context_system::instance();

if (!has_capability('local/organization:unit_view', $PAGE->context, $USER->id)) {
    redirect($CFG->wwwroot . '/my');
}

// Load AMD module
$PAGE->requires->js_call_amd('local_organization/campuses', 'init');
// Load CSS file
$PAGE->requires->css('/local/organization/css/general.css');

$term = optional_param('q', '', PARAM_TEXT);
$formdata = new stdClass();
$formdata->name = $term;

$mform = new campus_filter_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    // Handle form cancel operation, if cancel button is present
    redirect($CFG->wwwroot . '/local/organization/campuses.php');
} else if ($data = $mform->get_data()) {
    // Process validated data
    $term_filter = $data->q;
} else {
    // Display the form
//    $mform->display();
}

$table = new campus_table('local_organization_campus_table');
$params = array();
// Define the SQL query to fetch data
$sql = "id != 0";
if (!empty($term_filter)) {
    $sql .= " AND (" . $DB->sql_like('name', ':searchname', false) . " OR " .
            $DB->sql_like('shortname', ':searchshort', false) . ")";
    $params['searchname'] = '%' . $DB->sql_like_escape($term_filter) . '%';
    $params['searchshort'] = '%' . $DB->sql_like_escape($term_filter) . '%';
}

// Define the SQL query to fetch data
$table->set_sql('*', '{local_organization_campus}', $sql, $params);

// Define the base URL for the table
$table->define_baseurl(new moodle_url('/local/organization/campuses.php'));

base::page(
    new moodle_url('/local/organization/campuses.php'),
    get_string('campuses', 'local_organization'),
    get_string('campuses', 'local_organization')
);

echo $OUTPUT->header();
// Set up the table
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

