<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_wdsprefs extends block_base {

    // Base function for the block.
    public function init() {
        $this->title = get_string('wdsprefs:pluginname', 'block_wdsprefs');
    }

    // Function for getting content.
    public function get_content() {
        global $USER, $OUTPUT;

        // If we have nothing, do not show the block.
        if ($this->content !== null) {
            return $this->content;
        }

        // Build this content object out.
        $this->content = new stdClass();

        // Ensure $USER is not null before accessing properties.
        $userid = isset($USER->id) ? $USER->id : 0;

        // Get existing preference.
        $currentpref = get_user_preferences('wdspref_daysprior', '', $userid);

        // Define the url for the form view.
        $formurl = new moodle_url('/blocks/wdsprefs/view.php');

        // Define block content.
        $this->content->text = '<p>' .
            get_string('wdsprefs:daysprior', 'block_wdsprefs') .
            ': <strong>' . s($currentpref) . '</strong></p>';

        // Add the link to the user prefs page.
        $this->content->text .= '<a href="' . $formurl . '">' .
            get_string('wdsprefs:editprefs', 'block_wdsprefs') .
            '</a>';

        return $this->content;
    }

    // Function for Moodle to know where the block should show up.
    public function applicable_formats() {
        return array('site' => true, 'course-view' => false, 'my' => false);
    }

    // Do not allow multipls.
    public function instance_allow_multiple() {
        return false;
    }

    // This has no config of it's own, but TODO: get base prefs from enrol_workdaystudent.
    public function has_config() {
        return false;
    }
}
