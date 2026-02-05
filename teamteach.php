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

require_login();

$url = new moodle_url('/blocks/wdsprefs/teamteach.php');
$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('wdsprefs:teamteach', 'block_wdsprefs'));
$PAGE->set_heading(get_string('wdsprefs:teamteachheading', 'block_wdsprefs'));

if (!wdsprefs::faster_get_instructor_status($USER->id) && !is_siteadmin()) {
    print_error('wdsprefs:noinstructor', 'block_wdsprefs');
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('wdsprefs:teamteach', 'block_wdsprefs'));
echo html_writer::tag('p', get_string('wdsprefs:teamteach_desc', 'block_wdsprefs'));

// Get parms.
$target_course_id = optional_param('target_course_id', 0, PARAM_INT);
$search_query = optional_param('search_query', '', PARAM_TEXT);
$selected_teacher_id = optional_param('selected_teacher_id', 0, PARAM_INT);
$section_ids = optional_param_array('section_ids', [], PARAM_INT);
$send_request = optional_param('send_request', '', PARAM_TEXT);

// Get user's WDS courses (shells).
$my_courses = wdsprefs::get_courses($USER->id);
$shells = [];
foreach ($my_courses as $period_id => $period_sections) {
    foreach ($period_sections as $section) {
        if (!empty($section->moodle_courseid) && is_numeric($section->moodle_courseid)) {
            $course_obj = $DB->get_record('course', ['id' => $section->moodle_courseid]);
            if ($course_obj) {
                $shells[$section->moodle_courseid] = [
                    'id' => $course_obj->id,
                    'fullname' => $course_obj->fullname,
                    'period_id' => $section->academic_period_id
                ];
            }
        }
    }
}

// Process Request.
if ($send_request && $target_course_id && $selected_teacher_id && !empty($section_ids)) {
    require_sesskey();
    
    $request_id = block_wdsprefs_teamteach::create_request($USER->id, $target_course_id, $selected_teacher_id, $section_ids);
    
    if ($request_id) {
        echo $OUTPUT->notification(get_string('wdsprefs:teamteach_request_created', 'block_wdsprefs'), 'success');
        // Link back to start.
        echo $OUTPUT->continue_button(new moodle_url('/blocks/wdsprefs/teamteach.php'));
        echo $OUTPUT->footer();
        exit;
    } else {
        echo $OUTPUT->notification(get_string('wdsprefs:teamteach_request_failed', 'block_wdsprefs'), 'error');
    }
}

// Form Start.
echo html_writer::start_tag('form', ['action' => $url, 'method' => 'post']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

// Target Course Dropdown.
$options = ['' => get_string('wdsprefs:teamteach_select_target_course', 'block_wdsprefs')];
foreach ($shells as $shell) {
    $options[$shell['id']] = $shell['fullname'];
}

echo html_writer::label(get_string('wdsprefs:teamteach_target_course', 'block_wdsprefs'), 'target_course_id');
echo html_writer::select($options, 'target_course_id', $target_course_id, null, ['class' => 'form-control', 'onchange' => 'this.form.submit()']);
echo html_writer::empty_tag('br');

// Search Teacher.
if ($target_course_id) {

    // Show search box.
    echo html_writer::tag('h4', get_string('wdsprefs:teamteach_search_teacher', 'block_wdsprefs'));
    echo html_writer::empty_tag('input', [
        'type' => 'text', 
        'name' => 'search_query', 
        'value' => $search_query, 
        'class' => 'form-control',
        'placeholder' => get_string('wdsprefs:teamteach_search_placeholder', 'block_wdsprefs')
    ]);
    echo html_writer::empty_tag('br');
    echo html_writer::empty_tag('input', ['type' => 'submit', 'value' => get_string('search'), 'class' => 'btn btn-secondary']);
    echo html_writer::empty_tag('br');
    echo html_writer::empty_tag('br');

    // Teacher Results.
    if ($search_query) {
        $teachers = block_wdsprefs_teamteach::search_teachers($search_query);
        
        if (empty($teachers)) {
            echo $OUTPUT->notification(get_string('wdsprefs:teamteach_no_teacher_found', 'block_wdsprefs'), 'warning');
        } else {
            echo html_writer::tag('h4', 'Select Instructor');
            echo html_writer::start_tag('ul', ['class' => 'list-group']);
            foreach ($teachers as $teacher) {
                $name = $teacher->firstname . ' ' . $teacher->lastname . ' (' . $teacher->email . ')';
                $link = new moodle_url($url, [
                    'target_course_id' => $target_course_id, 
                    'search_query' => $search_query,
                    'selected_teacher_id' => $teacher->userid
                ]);
                
                $active = ($selected_teacher_id == $teacher->userid) ? 'active' : '';
                echo html_writer::tag('li', 
                    html_writer::link($link, $name, ['class' => 'text-decoration-none ' . ($active ? 'text-white' : '')]), 
                    ['class' => 'list-group-item ' . $active]
                );
            }
            echo html_writer::end_tag('ul');
            echo html_writer::empty_tag('br');
        }
    }
}

// Select Sections.
if ($target_course_id && $selected_teacher_id) {

    // Get period of target course.
    $target_period_id = $shells[$target_course_id]['period_id'];
    
    // Get teacher's sections in that period.
    $sections = block_wdsprefs_teamteach::get_teacher_sections($selected_teacher_id, $target_period_id);
    
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'selected_teacher_id', 'value' => $selected_teacher_id]);

    if (empty($sections)) {
        echo $OUTPUT->notification(get_string('wdsprefs:teamteach_no_sections_found', 'block_wdsprefs'), 'warning');
    } else {
        echo html_writer::tag('h4', get_string('wdsprefs:teamteach_select_sections', 'block_wdsprefs'));
        
        foreach ($sections as $section) {

            // Check if already in target course?
            if ($section->moodle_status == $target_course_id) {
                continue;
            }
            
            $label = $section->course_subject_abbreviation . ' ' . $section->course_number . ' ' . $section->section_number;

            // Add checkbox.
            echo html_writer::start_tag('div', ['class' => 'form-check']);
            echo html_writer::empty_tag('input', [
                'type' => 'checkbox', 
                'name' => 'section_ids[]', 
                'value' => $section->id,
                'class' => 'form-check-input',
                'id' => 'section_' . $section->id
            ]);
            echo html_writer::label($label, 'section_' . $section->id, false, ['class' => 'form-check-label']);
            echo html_writer::end_tag('div');
        }
        
        echo html_writer::empty_tag('br');
        echo html_writer::empty_tag('input', [
            'type' => 'submit', 
            'name' => 'send_request',
            'value' => get_string('wdsprefs:teamteach_submit_request', 'block_wdsprefs'), 
            'class' => 'btn btn-primary'
        ]);
    }
}

echo html_writer::end_tag('form');

echo $OUTPUT->footer();
