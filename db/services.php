<?php

$functions = array(
    'local_organization_campus_delete' => array(
        'classname' => 'local_organization_campus_ws',
        'methodname' => 'delete',
        'classpath' => 'local/organization/classes/external/campus_ws.php',
        'description' => 'Delete campus record. It will only delete if no units are associated',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_organization_unit_delete' => array(
        'classname' => 'local_organization_unit_ws',
        'methodname' => 'delete',
        'classpath' => 'local/organization/classes/external/unit_ws.php',
        'description' => 'Delete unit record. Will only delete if no departments are associated',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_organization_department_delete' => array(
        'classname' => 'local_organization_department_ws',
        'methodname' => 'delete',
        'classpath' => 'local/organization/classes/external/department_ws.php',
        'description' => 'Delete department record',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_organization_advisor_delete' => array(
        'classname' => 'local_organization_advisors_ws',
        'methodname' => 'delete',
        'classpath' => 'local/organization/classes/external/advisors_ws.php',
        'description' => 'Delete advisor record',
        'type' => 'write',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_organization_users_get' => array(
        'classname' => 'local_organization_users_ws',
        'methodname' => 'get_users',
        'classpath' => 'local/organization/classes/external/users_ws.php',
        'description' => 'gets user records',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true
    ),
    'local_organization_roles_get' => array(
        'classname' => 'local_organization_users_ws',
        'methodname' => 'get_roles',
        'classpath' => 'local/organization/classes/external/users_ws.php',
        'description' => 'gets existing roles',
        'type' => 'read',
        'capabilities' => '',
        'ajax' => true
    ),
);