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
        global $CFG, $PAGE, $USER, $OUTPUT;

        require_once($CFG->dirroot . '/blocks/wdsprefs/classes/wdsprefs.php');

        // Is the user an instructor?
        $instructor = wdsprefs::get_instructor($USER);

        // Is the user a student?
        $student = wdsprefs::get_student($USER);

        // TODO: take this out! For testing only!
        $student = true;
        $instructor = true;

        // If we have nothing, do not show the block.
        if ($this->content !== null) {
            return $this->content;
        }

        // Build this content object out.
        $this->content = new stdClass();

        // Ensure $USER is not null before accessing properties.
        $userid = isset($USER->id) ? $USER->id : 0;

        // Build out the list of items for everyone.
        $genericitems = [
            [
                'text' => get_string('wdsprefs:user', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/userview.php'),
                'icon' => 'fa-user'
            ],
        ];

        // Build out other priveleged items.
        $privelegeditems = [
            [
                'text' => get_string('wdsprefs:schedule', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/scheduleview.php'),
                'icon' => 'fa-calendar-check-o'
            ],
        ];


        // Build out the list of items for faculty.
        $facultyitems = [
            [
                'text' => get_string('wdsprefs:course', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/courseview.php'),
                'icon' => 'fa-graduation-cap'
            ],
            [
                'text' => get_string('wdsprefs:unwant', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/unwantview.php'),
                'icon' => 'fa-crosshairs'
            ],
            [
                'text' => get_string('wdsprefs:split', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/splitview.php'),
                'icon' => 'fa-clone'
            ],
            [
                'text' => get_string('wdsprefs:crosslist', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/crosslistview.php'),
                'icon' => 'fa-link'
            ],
            [
                'text' => get_string('wdsprefs:teamteach', 'block_wdsprefs'),
                'url' => new moodle_url('/blocks/wdsprefs/teamteachview.php'),
                'icon' => 'fa-group'
            ],
        ];

        // Set this up for later.
        $listitems = '';

        // If we're teaching a course.
        if ($instructor) {
            // Loop through all the faculty items.
            foreach ($facultyitems as $item) {
                $icon = html_writer::tag('i', '', ['class' => 'fa ' . $item['icon'], 'aria-hidden' => 'true']);
                $link = html_writer::link($item['url'], $icon . $item['text'], ['class' => 'menu-link']);
                $listitems .= html_writer::tag('li', $link, ['class' => 'menu-item']);
            }
        }

        // If we're either an instructor or a student.
        if ($instructor || $student) {
            // Loop through all the priveleged items.
            foreach ($privelegeditems as $item) {
                $icon = html_writer::tag('i', '', ['class' => 'fa ' . $item['icon'], 'aria-hidden' => 'true']);
                $link = html_writer::link($item['url'], $icon . $item['text'], ['class' => 'menu-link']);
                $listitems .= html_writer::tag('li', $link, ['class' => 'menu-item']);
            }
        }

        // Append these to the end.
        foreach ($genericitems as $item) {
            $icon = html_writer::tag('i', '', ['class' => 'fa ' . $item['icon'], 'aria-hidden' => 'true']);
            $link = html_writer::link($item['url'], $icon . $item['text'], ['class' => 'menu-link']);
            $listitems .= html_writer::tag('li', $link, ['class' => 'menu-item']);
        }

        // Build out the unordered list.
        $this->content->text = html_writer::tag('ul', $listitems, ['class' => 'menu-list']);

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
