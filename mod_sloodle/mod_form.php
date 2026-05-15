<?php
/**
* Sloodle module add/edit form.
*
* @package sloodle
* @copyright Copyright (c) 2008 Sloodle (various contributors)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU GPL v3
*/

require_once($CFG->dirroot.'/mod/sloodle/init.php');
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once(SLOODLE_LIBROOT.'/general.php');


class mod_sloodle_mod_form extends moodleform_mod
{
    function definition()
    {
        global $CFG, $COURSE, $SLOODLE_TYPES;
        $mform =& $this->_form;

        $sloodletype = SLOODLE_TYPE_CTRL;

        if (empty($this->_instance)) {
            $sloodletype = required_param('type', PARAM_TEXT);
        } else {
            $rec = sloodle_get_record('sloodle', 'id', $this->_instance);
            if (!$rec) {
                throw new \moodle_exception('modulenotfound', 'sloodle');
            }
            if (empty($rec->type)) {
                throw new \moodle_exception('moduletypeunknown', 'sloodle');
            }
            $sloodletype = $rec->type;
        }

        $sloodletypefull = get_string("moduletype:$sloodletype", 'sloodle');

        $mform->addElement('header', 'general', get_string('general', 'form'));

        $typeelem = &$mform->addElement('select', 'type', get_string('moduletype', 'sloodle'), array($sloodletype => $sloodletypefull));
        $mform->setDefault('type', $sloodletype);
        $typeelem->freeze();
        $mform->addHelpButton('type', "moduletype_$sloodletype", 'sloodle');

        $mform->addElement('text', 'name', get_string('name', 'sloodle'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $this->standard_intro_elements();

        switch ($sloodletype) {
          case SLOODLE_TYPE_CTRL:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('checkbox', 'controller_enabled', get_string('enabled', 'sloodle'), get_string('controlaccess', 'sloodle'));
            $mform->setDefault('controller_enabled', 1);
            $mform->addElement('text', 'controller_password', get_string('primpass', 'sloodle'), array('size' => '12', 'maxlength' => '9'));
            $mform->addHelpButton('controller_password', 'primpass', 'sloodle');
            $mform->setType('controller_password', PARAM_INT);
            $mform->setDefault('controller_password', mt_rand(100000000, 999999999));
            $mform->addRule('controller_password', null, 'numeric', null, 'client');
            break;

          case SLOODLE_TYPE_DISTRIB:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('text', 'distributor_channel', get_string('xmlrpc:channel', 'sloodle').': ', array('size' => '40', 'readonly' => 'true', 'disabled' => 'true'));
            $mform->setType('distributor_channel', PARAM_TEXT);
            $mform->setDefault('distributor_channel', '');
            $mform->addElement('text', 'distributor_numobjects', get_string('numobjects', 'sloodle').': ', array('size' => '4', 'readonly' => 'true', 'disabled' => 'true'));
            $mform->setType('distributor_numobjects', PARAM_INT);
            $mform->setDefault('distributor_numobjects', '0');
            if (!empty($this->_instance)) {
                $mform->addElement('checkbox', 'distributor_reset', get_string('reset').': ', get_string('sloodleobjectdistributor:reset', 'sloodle'));
            }
            break;

          case SLOODLE_TYPE_PRESENTER:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('text', 'presenter_framewidth', get_string('framewidth', 'sloodle').': ', array('size' => '4'));
            $mform->addRule('presenter_framewidth', null, 'numeric', null, 'client');
            $mform->setType('presenter_framewidth', PARAM_INT);
            $mform->setDefault('presenter_framewidth', 512);
            $mform->addElement('text', 'presenter_frameheight', get_string('frameheight', 'sloodle').': ', array('size' => '4'));
            $mform->addRule('presenter_frameheight', null, 'numeric', null, 'client');
            $mform->setType('presenter_frameheight', PARAM_INT);
            $mform->setDefault('presenter_frameheight', 512);
            break;

          case SLOODLE_TYPE_TRACKER:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('checkbox', 'tracker_autosend', get_string('tracker:autosend', 'sloodle'), get_string('tracker:autosend_desc', 'sloodle'));
            $mform->setDefault('tracker_autosend', 1);
            $all_currencies = SloodleCurrency::FetchIDNameHash();
            $mform->addElement('select', 'tracker_currency', get_string('tracker:currency', 'sloodle'), $all_currencies);
            $mform->addHelpButton('tracker_currency', 'tracker:currency', 'sloodle');
            $mform->setDefault('tracker_currency', 1);
            break;

          case SLOODLE_TYPE_MAP:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('text', 'map_initialx', 'Initial position (X): ', array('size' => '10'));
            $mform->setDefault('map_initialx', '1000.0');
            $mform->addElement('text', 'map_initialy', 'Initial position (Y): ', array('size' => '10'));
            $mform->setDefault('map_initialy', '1000.0');
            $mform->addElement('text', 'map_initialzoom', 'Initial zoom level (1-6): ', array('size' => '3'));
            $mform->setDefault('map_initialzoom', '2');
            $mform->addRule('map_initialzoom', null, 'numeric', null, 'client');
            $mform->addElement('checkbox', 'map_showpan', 'Pan controls: ', 'If checked, pan controls will be visible on the map.');
            $mform->setDefault('map_showpan', 1);
            $mform->addElement('checkbox', 'map_showzoom', 'Zoom controls: ', 'If checked, zoom controls will be visible on the map.');
            $mform->setDefault('map_showzoom', 1);
            $mform->addElement('checkbox', 'map_allowdrag', 'Allow dragging: ', 'If checked, users will be able to click-and-drag the map to pan it.');
            $mform->setDefault('map_allowdrag', 1);
            break;

          case SLOODLE_TYPE_AWARDS:
            $mform->addElement('header', 'typeheader', $sloodletypefull);
            $mform->addElement('image', 'SloodleAwardImage', SLOODLE_WWWROOT.'/lib/media/awardsmall.gif');
            break;
        }

        $this->standard_coursemodule_elements(false);
        $this->add_action_buttons();
    }

    function definition_after_data() {
    }

    function data_preprocessing(&$default_values)
    {
        if (empty($this->_instance)) {
            return;
        }

        switch ($default_values['type']) {
          case SLOODLE_TYPE_CTRL:
            $controller = sloodle_get_record('sloodle_controller', 'sloodleid', $this->_instance);
            if (!$controller) {
                throw new \moodle_exception('secondarytablenotfound', 'sloodle');
            }
            $default_values['controller_enabled']  = $controller->enabled;
            $default_values['controller_password'] = $controller->password;
            break;

          case SLOODLE_TYPE_DISTRIB:
            $distributor = sloodle_get_record('sloodle_distributor', 'sloodleid', $this->_instance);
            if (!$distributor) {
                throw new \moodle_exception('secondarytablenotfound', 'sloodle');
            }
            $default_values['distributor_channel'] = $distributor->channel;
            $objects = sloodle_get_records('sloodle_distributor_entry', 'distributorid', $distributor->id);
            if (is_array($objects)) {
                $default_values['distributor_numobjects'] = count($objects);
            }
            break;

          case SLOODLE_TYPE_PRESENTER:
            $presenter = sloodle_get_record('sloodle_presenter', 'sloodleid', $this->_instance);
            if (!$presenter) {
                throw new \moodle_exception('secondarytablenotfound', 'sloodle');
            }
            $default_values['presenter_framewidth']  = (int)$presenter->framewidth;
            $default_values['presenter_frameheight'] = (int)$presenter->frameheight;
            break;

          case SLOODLE_TYPE_TRACKER:
            $tracker = sloodle_get_record('sloodle_tracker', 'sloodleid', $this->_instance);
            if (!$tracker) {
                throw new \moodle_exception('secondarytablenotfound', 'sloodle');
            }
            $default_values['tracker_autosend'] = (int)$tracker->autosend;
            $default_values['tracker_currency'] = (int)$tracker->currency;
            break;

          case SLOODLE_TYPE_MAP:
            $map = sloodle_get_record('sloodle_map', 'sloodleid', $this->_instance);
            if (!$map) {
                throw new \moodle_exception('secondarytablenotfound', 'sloodle');
            }
            $default_values['map_initialx']    = $map->initialx;
            $default_values['map_initialy']    = $map->initialy;
            $default_values['map_initialzoom'] = $map->initialzoom;
            $default_values['map_showpan']     = $map->showpan;
            $default_values['map_showzoom']    = $map->showzoom;
            $default_values['map_allowdrag']   = $map->allowdrag;
            break;
        }
    }

    function validation($data, $file)
    {
        global $SLOODLE_TYPES;
        $errors = array();

        switch ($data['type']) {
          case SLOODLE_TYPE_CTRL:
            $pwd = isset($data['controller_password']) ? $data['controller_password'] : '';
            if (empty($pwd)) {
                break;
            }
            $pwderrors = array();
            if (!sloodle_validate_prim_password_verbose($pwd, $pwderrors)) {
                $errors['controller_password'] = '';
                foreach ($pwderrors as $pe) {
                    $errors['controller_password'] .= get_string("primpass:$pe", 'sloodle') . '<br />';
                }
            }
            break;

          default:
            if (!isset($SLOODLE_TYPES[$data['type']])) {
                $errors['type'] = get_string('moduletypeunknown', 'sloodle');
            }
            break;
        }

        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }
}
