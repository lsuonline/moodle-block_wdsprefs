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

class crossenroll_sections_form extends moodleform {
    public function definition() {
        $mform = $this->_form;

        // Add the step parameter.
        $this->_form->addElement('hidden', 'step', 'sections');
        $this->_form->setType('step', PARAM_TEXT);

        // Get the sections data.
        $sectionsbyperiod = $this->_customdata['sectionsbyperiod'] ?? [];

        // Target period info (optional display).
        $targetperiodname = $this->_customdata['targetperiodname'] ?? '';

        // Add header.
        $mform->addElement('header',
            'wdsprefs:selectsections',
            get_string('wdsprefs:selectsections', 'block_wdsprefs'));

        // Instructions.
        $mform->addElement('html',
            '<div class="alert alert-info"><p>' .
            get_string('wdsprefs:crossenrollinstructions2', 'block_wdsprefs') .
            '</p></div>'
        );

        if ($targetperiodname) {
             $mform->addElement('html',
                '<p><strong>' . get_string('wdsprefs:targetperiod', 'block_wdsprefs') . ': ' . $targetperiodname . '</strong></p>'
            );
        }

        // Loop through periods.
        foreach ($sectionsbyperiod as $periodname => $courses) {

            $html = '';

            // Period Header.
            $html .= '<h4 class="mt-4 mb-3">' . $periodname . '</h4>';

            // Grid Container.
            $html .= '<div class="row">';

            foreach ($courses as $coursename => $sections) {

                // Column.
                $html .= '<div class="col-md-6 col-lg-4 mb-4">';

                // Card.
                $html .= '<div class="card h-100 shadow-sm border-0 wdsprefs-course-card">';

                // Card Header.
                $html .= '<div class="card-header font-weight-bold py-3">';
                $html .= $coursename;
                $html .= '</div>';

                // Card Body.
                $html .= '<div class="card-body">';

                foreach ($sections as $sectionid => $sectionname) {
                    $checkboxid = 'section_' . $sectionid;
                    $html .= '<div class="form-check mb-2">';
                    $html .= '<input class="form-check-input" type="checkbox" name="selectedsections[' . $sectionid . ']" value="1" id="' . $checkboxid . '">';
                    $html .= '<label class="form-check-label" for="' . $checkboxid . '">';
                    $html .= $sectionname;
                    $html .= '</label>';
                    $html .= '</div>';
                }

                $html .= '</div>'; // close card-body
                $html .= '</div>'; // close card
                $html .= '</div>'; // close col
            }

            $html .= '</div>'; // close row

            // Output the entire block for this period.
            $mform->addElement('html', $html);
        }

        // Add the action buttons.
        $this->add_action_buttons(true, get_string('submit'));
    }
}
