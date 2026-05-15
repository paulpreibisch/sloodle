<?php

namespace mod_sloodle\task;

class cron_task extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('pluginname', 'mod_sloodle') . ' cron';
    }

    public function execute() {
        global $CFG;
        require_once($CFG->dirroot . '/mod/sloodle/lib.php');
        sloodle_cron();
    }
}
