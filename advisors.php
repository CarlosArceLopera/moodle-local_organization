<?php
require_once('../../config.php');
include_once('classes/tables/advisors_table.php');
include_once('classes/forms/advisors_filter_form.php');


use local_organization\advisors_filter_form;
use local_organization\base;
use local_organization\advisors_table;

global $CFG, $OUTPUT, $PAGE, $DB, $USER;


require_login(1, false);

$context = context_system::instance();
if (!has_capability('local/organization:advisor_view', $PAGE->context, $USER->id)) {
    redirect($CFG->wwwroot . '/my');
}
// Load AMD module
$PAGE->requires->js_call_amd('local_organization/advisors', 'init');
// Load CSS file
$PAGE->requires->css('/local/organization/css/general.css');

$instance_id = required_param('instance_id', PARAM_INT);
$user_context = required_param('user_context',PARAM_TEXT);
$campus_id = required_param('campus_id',PARAM_TEXT);
$unit_id = optional_param('unit_id', 0, PARAM_INT);

$formdata = new stdClass();
$formdata->instance_id = $instance_id;
$formdata->user_context = $user_context;
$formdata->unit_id = $unit_id;
$formdata->campus_id = $campus_id;

$mform = new advisors_filter_form(null, array('formdata' => $formdata));

if ($mform->is_cancelled()) {
    if ($user_context == base::CONTEXT_UNIT) {
         redirect($CFG->wwwroot .'/local/organization/units.php?campus_id=' . $formdata->campus_id);
    } else if ($user_context == base::CONTEXT_DEPARTMENT) {
        redirect($CFG->wwwroot . '/local/organization/departments.php?campus_id=' . $formdata->campus_id . '&unit_id='. $formdata->unit_id);
    }
    else {
         redirect($CFG->wwwroot . '/local/organization/campuses.php?campus_id=' . $formdata->campus_id . '&unit_id='. $formdata->unit_id);
    }
} else if ($data = $mform->get_data()) {
    // Process validated data
    $term_filter = $data->q;
    $instance_id = $data->instance_id;
    $user_context = $data->user_context;
    $unit_id = $data->unit_id;
    $campus_id = $data->campus_id;
} else {
    // Display the form
    // $mform->display();
}

$table = new advisors_table('local_organization_advisors_table');
$sql = "instance_id != 0";
$params = array('instance_id' => $instance_id, 'user_context' => $user_context);

// Define the SQL query to fetch data
if (!empty($instance_id) && !empty($user_context)) {
    $fields = "a.id as id,
            u.firstname AS firstname,
            u.lastname AS lastname,
            r.shortname AS role,
            r.id AS role_id,
            a.user_id as user_id,
            un.name AS context,
            a.instance_id as instance_id,
            a.user_context as user_context";
    $from = '{user} u JOIN {local_organization_advisor} a ON u.id = a.user_id
            JOIN {role} r ON r.id = a.role_id';
    if ($user_context == base::CONTEXT_UNIT) {
        $from .= ' JOIN {local_organization_unit} un ON un.id = a.instance_id';
        $conditions = "a.user_context = 'UNIT' and a.instance_id = " . $instance_id;
    } else if ($user_context == base::CONTEXT_CAMPUS) {
        $from .= ' JOIN {local_organization_campus} un ON un.id = a.instance_id';
        $conditions = "a.user_context = 'CAMPUS' and a.instance_id = " . $instance_id;
    } else {
        $from .= ' JOIN {local_organization_dept} un ON un.id = a.instance_id';
        $conditions = "a.user_context = 'DEPARTMENT' and a.instance_id = " . $instance_id;
    }
    //TODO: Parameterize this
    if (!empty($term_filter)) {
        $conditions .= " AND (u.firstname LIKE '%$term_filter%') OR (u.lastname LIKE '%$term_filter%')";
    }
    $table->set_sql($fields, $from, $conditions);
}

// Define the base URL for the table
$table->define_baseurl(new moodle_url('/local/organization/advisors.php?instance_id=' . $instance_id . '&user_context=' . $user_context));

base::page(
    new moodle_url('/local/organization/advisors.php?instance_id=' . $instance_id . '&user_context=' . $user_context),
    get_string('advisors', 'local_organization'),
    get_string('advisors', 'local_organization')
);

echo $OUTPUT->header();
// Set up the table
$mform->display();
$table->out(20, true);
echo $OUTPUT->footer();

