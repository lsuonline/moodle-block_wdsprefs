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

require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/wdsprefs/classes/wdsprefs.php');
require_once($CFG->dirroot . '/blocks/wdsprefs/classes/teamteach.php');

// Require login.
require_login();

// Check for capability.
if (!is_siteadmin()) {
    require_capability('block/wdsprefs:peelsection', context_system::instance());
}

$url = new moodle_url('/blocks/wdsprefs/peel.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Peel Section');
$PAGE->set_heading('Peel Section from Course Shell');

echo $OUTPUT->header();
echo $OUTPUT->heading('Peel Section from Course Shell');

// Get parms.
$search_query = optional_param('search_query', '', PARAM_TEXT);
$courseid = optional_param('courseid', 0, PARAM_INT);
$section_ids = optional_param_array('section_ids', [], PARAM_INT);
$peel_confirm = optional_param('peel_confirm', 0, PARAM_INT);

// Search Form.
echo html_writer::start_tag('form', ['action' => $url, 'method' => 'get', 'class' => 'form-inline mb-4']);
echo html_writer::label('Search Course (ID or Name): ', 'search_query', false, ['class' => 'mr-2']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search_query',
    'value' => $search_query,
    'class' => 'form-control mr-2',
    'placeholder' => 'Enter Course ID or Name'
]);
echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => 'Search', 'class' => 'btn btn-primary']);
echo html_writer::end_tag('form');

// Process Search.
if ($search_query && !$courseid) {

    $courses = [];

    // If numeric, try to find by ID first.
    if (is_numeric($search_query)) {
        $course = $DB->get_record('course', ['id' => $search_query]);
        if ($course) {
            $courses[] = $course;
        }
    }

    // If not found by ID, search by name.
    if (empty($courses)) {
        $sql = "SELECT * FROM {course}
                WHERE fullname LIKE :fullname
                   OR shortname LIKE :shortname
                   OR idnumber LIKE :idnumber
                ORDER BY fullname ASC
                LIMIT 20";
        $params = [
            'fullname' => '%' . $search_query . '%',
            'shortname' => '%' . $search_query . '%',
            'idnumber' => '%' . $search_query . '%'
        ];
        $courses = $DB->get_records_sql($sql, $params);
    }

    if (empty($courses)) {
        echo $OUTPUT->notification('No courses found matching your query.', 'warning');
    } elseif (count($courses) == 1) {

        // Redirect or set courseid if only one found.
        $course = reset($courses);
        $courseid = $course->id;
    } else {

        // Display list of courses.
        echo html_writer::tag('h3', 'Select a Course');
        echo html_writer::start_tag('ul', ['class' => 'list-group']);
        foreach ($courses as $c) {
            $link = new moodle_url($url, ['courseid' => $c->id, 'search_query' => $search_query]);
            echo html_writer::tag('li',
                html_writer::link($link, $c->fullname . ' (' . $c->shortname . ')', ['class' => 'text-decoration-none']),
                ['class' => 'list-group-item']
            );
        }
        echo html_writer::end_tag('ul');
    }
}

// Display selected course info if we have one.
if ($courseid) {
    $course = $DB->get_record('course', ['id' => $courseid]);
    if ($course) {
        echo html_writer::tag('h3', 'Selected Course: ' . $course->fullname);
        echo html_writer::link(
            new moodle_url(
                '/course/view.php',
                ['id' => $course->id]
            ),
            'View Course',
            ['target' => '_blank', 'class' => 'btn btn-sm btn-info mb-3']
        );
    } else {
        echo $OUTPUT->notification('Invalid Course ID.', 'error');
        $courseid = 0;
    }
}

echo $OUTPUT->footer();
