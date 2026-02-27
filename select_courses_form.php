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

require_once("$CFG->libdir/formslib.php");

class select_courses_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Add the step = assign crap.
        $this->_form->addElement('hidden', 'step', 'course');
        $this->_form->setType('step', PARAM_TEXT);

        // Get the sections for this period and selectability (team-taught / already crosssplit).
        $sectionsbycourse = $this->_customdata['sectionsbycourse'] ?? [];
        $selectability = $this->_customdata['selectability'] ?? [];

        // Count selectable sections (for shellcount max); show all courses but disable those with no selectable sections.
        $totalselectable = 0;
        foreach ($sectionsbycourse as $sections) {
            foreach (array_keys($sections) as $sectionid) {
                if (!empty($selectability[$sectionid]['selectable'])) {
                    $totalselectable++;
                }
            }
        }

        $mform->addElement('header',
            'wdsprefs:selectcoursesheader',
            get_string('wdsprefs:selectcoursesheader', 'block_wdsprefs'));

        $mform->addElement('html',
            '<div class="alert alert-info"><p>' .
            get_string('wdsprefs:crosssplitinstructions2', 'block_wdsprefs') .
            '</p></div>'
        );

        foreach ($sectionsbycourse as $coursename => $sections) {
            $scount = count($sections);
            $selectablecount = 0;
            foreach (array_keys($sections) as $sectionid) {
                if (!empty($selectability[$sectionid]['selectable'])) {
                    $selectablecount++;
                }
            }
            $allnonselectable = ($selectablecount === 0);

            $sectionslabel = $scount == 1 ?
                ' (' . $scount . ' ' . get_string('wdsprefs:section', 'block_wdsprefs') . ')' :
                ' (' . $scount . ' ' . get_string('wdsprefs:sections', 'block_wdsprefs') . ')';

            $sanitized = str_replace([' ', '/'], ['_', '-'], $coursename);

            if ($allnonselectable) {
                $disclaimer = get_string('wdsprefs:course_disclaimer_teamtaught_crosssplit', 'block_wdsprefs');
                $mform->addElement('advcheckbox', 'selectedcourses_' . $sanitized,
                    '',
                    $coursename . $sectionslabel . ' <span class="text-muted">(' . $disclaimer . ')</span>',
                    ['disabled' => 'disabled']
                );
                $mform->setDefault('selectedcourses_' . $sanitized, 0);
            } else {
                $mform->addElement('advcheckbox', 'selectedcourses_' . $sanitized,
                    '',
                    $coursename . $sectionslabel
                );
            }
        }

        $maxshells = max(1, $totalselectable);
        $mform->addElement('select',
            'shellcount',
            get_string('wdsprefs:shellcount', 'block_wdsprefs'),
            array_combine(range(1, $maxshells), range(1, $maxshells)));

        // Set the default.
        $mform->setDefault('shellcount', 1);

        // Add the action buttons.
        $this->add_action_buttons(true, get_string('continue'));
    }
}
