<?php
require_once('config.php');

$action = optional_param('action', '', PARAM_RAW);
$from = optional_param('from', '', PARAM_INT);
$to = optional_param('to', '', PARAM_INT);

switch ($action) {
    case 'enrolments_by_period':
        count_enrolments_by_period($from, $to);
        break;
    default:
        get_stats();
        break;
}

function count_enrolments_by_period($from, $to) {
    global $DB;

    if (empty($from)) {
        $from = 1262304000; #01012010 00:00:00
    }

    if (empty($to)) {
        $to = time();
    }

    $enrolments = $DB->get_records_sql('SELECT COUNT(*) FROM {user_enrolments} WHERE timecreated >= ? AND timecreated <= ?', array($from, $to));

    echo serialize(array('count_enrolments' => key($enrolments)));
}

function get_stats() {
    global $DB;

    $now = time();
    $t1 = $now - (24 * 60 * 60);
    $t7 = $now - (7 * 24 * 60 * 60);
    $t50 = $now - (50 * 24 * 60 * 60);
    $t365 = $now - (365 * 24 * 60 * 60);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE deleted = 0');
    $count_users = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE lastaccess >= ? and deleted = ?', array($t1, 0));
    $count_t1 = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE lastaccess >= ? and deleted = ?', array($t7, 0));
    $count_t7 = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE lastaccess >= ? and deleted = ?', array($t50, 0));
    $count_t50 = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE lastaccess >= ? and deleted = ?', array($t365, 0));
    $count_t365 = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user} WHERE lastaccess = ? and deleted = ?', array(0, 0));
    $count_t0 = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {course}');
    $count_courses = key($result);

    $result = $DB->get_records_sql('SELECT COUNT(*) FROM {user_enrolments}');
    $count_enrolments = key($result);

    $result = serialize(array("count_users" => $count_users,
            "count_t1" => $count_t1,
            "count_t7" => $count_t7,
            "count_t50" => $count_t50,
            "count_t365" => $count_t365,
            "count_t0" => $count_t0,
            "count_courses" => $count_courses,
            "count_enrolments" => $count_enrolments
        )
    );

    echo $result;
}