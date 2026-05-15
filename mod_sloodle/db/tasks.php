<?php

defined('MOODLE_INTERNAL') || die();

$tasks = [
    [
        'classname'   => 'mod_sloodle\task\cron_task',
        'blocking'    => 0,
        'minute'      => '*/1',
        'hour'        => '*',
        'day'         => '*',
        'month'       => '*',
        'dayofweek'   => '*',
    ],
];
