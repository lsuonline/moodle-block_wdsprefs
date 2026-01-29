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

// Plugin stuff.
$string['pluginname'] = 'Workday Preferences';
$string['wdsprefs:addinstance'] = 'Add Workday Preferences Block Instance';
$string['wdsprefs:myaddinstance'] = 'Add Workday Preferences Block Instance to my page';
$string['wdsprefs:pluginname'] = 'Workday Preferences';

// Basic stuff.
$string['wdsprefs:cancel'] = 'Preference modification cancelled.';
$string['wdsprefs:error'] = 'Error saving preferences!';
$string['wdsprefs:saveprefs'] = 'Save Preferences';
$string['wdsprefs:success'] = 'Preferences saved successfully!';

// Link stuff.
$string['wdsprefs:course'] = 'Course preferences';
$string['wdsprefs:coursename'] = 'Course';
$string['wdsprefs:crossenroll'] = 'Cross Enrollment';
$string['wdsprefs:crosssplit'] = 'Crosslist & Split';
$string['wdsprefs:schedule'] = 'Course schedule';
$string['wdsprefs:teamteach'] = 'Team teaching';
$string['wdsprefs:unwant'] = 'Unwanted sections';
$string['wdsprefs:user'] = 'User preferences';

$string['help_course_preferences'] = 'Course creation and enrollment preferences.';
$string['help_unwanted_sections'] = 'Unwanted section enrollments.';
$string['help_cross_listing'] = 'Crosslisting and splitting courses and sections into any combination of course shells.';
$string['help_cross_enrollment_help'] = 'Combine sections from multiple academic periods into a single course shell. This is useful for combining sections from different terms into one shell.';
$string['help_course_preferences_help'] = 'Set your course creation and enrollment date preferences.';
$string['help_unwanted_sections_help'] = 'Remove unwanted course sections from your Moodle course shells.';
$string['help_cross_listing_help'] = 'Flexibly combine or separate course sections into any arrangement of course shells - from combining multiple courses into one shell to splitting individual sections into separate shells or any hybrid combination. Enrollments will be kept up to date and grades can be posted for all sections directly from their assigned shells.';

// WDS Page strings.
$string['wdsprefs:format'] = 'Course Format';
$string['wdsprefs:cdaysprior'] = 'Create Days Prior';
$string['wdsprefs:cdaysprior_help'] = 'Number of days prior to the semester starting to <strong>create courses</strong>.';
$string['wdsprefs:cdaysprior_desc'] = 'Number of days prior to the semester starting to <strong>create courses</strong>.';
$string['wdsprefs:edaysprior'] = 'Enroll Days Prior';
$string['wdsprefs:edaysprior_help'] = 'Number of days prior to the semester starting to <strong>enroll students</strong>.';
$string['wdsprefs:edaysprior_desc'] = 'Number of days prior to the semester starting to <strong>enroll students</strong>.';
$string['wdsprefs:courselimit'] = 'Course Limit';
$string['wdsprefs:courselimit_help'] = 'Only create courses below this threshold.';
$string['wdsprefs:courselimit_desc'] = 'Only create courses below this threshold.';

// Unwant Page Strings.
$string['wdsprefs:period'] = 'Academic Period';

// Schedule view strings.
$string['wdsprefs:courselink'] = 'Course Link';
$string['wdsprefs:scheduleview'] = 'Course Schedule View';
$string['wdsprefs:courseschedule'] = 'Your Course Schedule';
$string['wdsprefs:courseheading'] = 'Course';
$string['wdsprefs:sectionheading'] = 'Section';
$string['wdsprefs:statusheading'] = 'Moodle Status';
$string['wdsprefs:instructorheading'] = 'Instructor';
$string['wdsprefs:daysheading'] = 'Days';
$string['wdsprefs:timesheading'] = 'Times';
$string['wdsprefs:wdstatusheading'] = 'Section Status';
$string['wdsprefs:deliverymodeheading'] = 'Delivery Mode';
$string['wdsprefs:nocourses'] = 'You are not registered for courses in Workday.';

// CrossSplit strings.
$string['wdsprefs:crosssplittitle'] = 'Crosslist & Split Sections';
$string['wdsprefs:crosssplitheading'] = 'Crosslist & Split Course Sections';
$string['wdsprefs:selectcoursesheader'] = 'Step 1: Choose Courses and Number of Shells';
$string['wdsprefs:assignshellsheader'] = 'Step 2: Assign Sections to Shells';
$string['wdsprefs:selectcourses'] = 'Courses to include';
$string['wdsprefs:shellcount'] = 'Number of course shells';
$string['wdsprefs:shell'] = 'Shell {$a}';
$string['wdsprefs:onetoone'] = 'Splitting one (1) course with one (1) section will not do anything.';
$string['wdsprefs:toomanyshells'] = 'You have selected more course shells: {$a->shellword} ({$a->shell}) than sections: {$a->secword} ({$a->sec}).';
$string['wdsprefs:atleastonesections'] = 'You must select at least one section to enable Crosslisting & Splitting.';
$string['wdsprefs:crosssplitsuccess'] = 'Cross-Splitting setup successful.';
$string['wdsprefs:duplicatesection'] = 'Each section can only be assigned to one shell.';
$string['wdsprefs:availablesections'] = 'Available Sections';
$string['wdsprefs:availableshells'] = 'Shell Containers';
$string['wdsprefs:selectedsections'] = 'Shell {$a} Sections';
$string['wdsprefs:selectperiodsheader'] = 'Current / Near-Future Periods';
$string['wdsprefs:selectperiod'] = 'Select a term/period to use';
$string['wdsprefs:periodwithcount'] = '{$a->name} &mdash; {$a->count} available sections';
$string['wdsprefs:crosssplitfail'] = 'Cross-Splitting Failed';
$string['wdsprefs:shellname'] = 'Shell Name';
$string['wdsprefs:datecreated'] = 'Date Created';
$string['wdsprefs:actions'] = 'Actions';
$string['wdsprefs:viewcourse'] = 'View Course';
$string['wdsprefs:viewsections'] = 'View Sections';
$string['wdsprefs:existingcrosssplits'] = 'Your Existing Crosslisted, Split, and Cross-Enrolled Shells';
$string['wdsprefs:crosssplitsections'] = 'Crosslisted, Split, and Cross-Enrolled Sections';
$string['wdsprefs:crosssplitinstructions2'] = '<strong>Crosslisting:</strong> Select the courses to combine, then enter how many shells you need.<br><strong>Splitting:</strong> Choose a course to split into multiple shells, then enter how many shells you need.';
$string['wdsprefs:crosssplitinstructions3'] = '<ol><li>Click on a shell container to select it (highlighted in blue)</li><li>Select any number of sections from the left and click "Add to Shell" to assign them to the highlighted shell.</li></ol><br>You can select sections from any shell and click "Remove" to return them to the available list.<br>You can create up to {$a->shellword} ({$a->shell}) shell(s) from the {$a->secword} ({$a->sec}) available sections.<br>If there are unused shells, they will not be creaed. Any unassigned sections will be left in the original course shell.';
$string['wdsprefs:nocrosssplitperiods'] = 'No courses are eligible for cross-splitting. Cross-splitting requires multiple sections or courses in a common academic period.';
$string['wdsprefs:nocrosssplit'] = 'Crosslisted, Split, or Cross-Enrolled shell not found or you do not have permission to view it.';
$string['wdsprefs:nosections'] = 'No sections have been assigned to this crosslisted, split, or cross-enrolled shell.';
$string['wdsprefs:status'] = 'Status';
$string['wdsprefs:sectionstatus_pending'] = 'Pending';
$string['wdsprefs:sectionstatus_enrolled'] = 'Enrolled';
$string['wdsprefs:sectionstatus_unenrolled'] = 'Unenrolled';
$string['wdsprefs:section'] = 'Section';
$string['wdsprefs:sections'] = 'Sections';
$string['wdsprefs:nosectionsavailable'] = 'Fewer than two (2) sections are available for crosslisting, splitting, or cross-enrolling. You need at least two (2) sections to split a course. You need at least one (1) section in two (2) courses to crosslist or cross-enroll a group of courses. Alternatively, all your courses may already be part of crosslisted, split, or cross-enrolled courses.';

// Blueprint shells related strings.
$string['wdsprefs:blueprint'] = 'Blueprint Shells';
$string['wdsprefs:blueprinttitle'] = 'Blueprint Shells';
$string['wdsprefs:blueprintheading'] = 'Blueprint Shells for Courses';
$string['wdsprefs:requestblueprint'] = 'Request a Blueprint Shell';
$string['wdsprefs:existingblueprints'] = 'Your Existing Blueprint Shells';
$string['wdsprefs:createblueprint'] = 'Create Blueprint Shell';
$string['wdsprefs:selectcourseforblueprint'] = 'Select Course for Blueprint';
$string['wdsprefs:blueprintexplanation'] = 'Blueprint shells allow you to create a template course that can be used as a starting point for your future courses. Create a blueprint shell for any course you teach and set it up exactly how you want. You can use this to import materials into in the future.';
$string['wdsprefs:blueprintnotes'] = 'Notes (optional)';
$string['wdsprefs:blueprintnotes_help'] = 'Add any notes or reminders about this blueprint shell for your future reference.';
$string['wdsprefs:blueprintsuccess'] = 'Blueprint shell created successfully!';
$string['wdsprefs:blueprintfailed'] = 'Failed to create blueprint shell. Please try again or contact support.';
$string['wdsprefs:blueprintalreadyexists'] = 'You already have a blueprint shell for this course.';
$string['wdsprefs:noinstructor'] = 'Only instructors can create blueprint shells.';
$string['wdsprefs:nocourses'] = 'You are not teaching any courses in the current or upcoming terms.';
$string['wdsprefs:blueprintstatus_pending'] = 'Pending';
$string['wdsprefs:blueprintstatus_created'] = 'Created';
$string['wdsprefs:blueprintstatus_failed'] = 'Failed';

// Undo cross-splitting strings.
$string['wdsprefs:undo'] = 'Undo Crosslisting, Splitting & Cross-Enrollment';
$string['wdsprefs:undoconfirm'] = 'Are you sure you want to undo this Crosslisting, Splitting, or Cross-Enrollment? This will reset sections to their original course shells and move enrollments accordingly. This action cannot be undone.';
$string['wdsprefs:undosuccess'] = 'Crosslisting, Splitting, and Cross-Enrollment successfully undone. Sections have been reset to their original course shells.';
$string['wdsprefs:undofailed'] = 'Failed to undo Crosslisting, Splitting, or Cross-Enrollment. Please contact support.';

// Cross-Enrollment strings.
$string['wdsprefs:crossenrolltitle'] = 'Cross-Enrollment';
$string['wdsprefs:crossenrollheading'] = 'Cross-Enrollment';
$string['wdsprefs:crossenrollinstructions1'] = 'Select the academic period that will serve as the "primary" or "target" period for the new course shell. This determines the course shell\'s name and dates.';
$string['wdsprefs:crossenrollinstructions2'] = 'Select the sections you wish to combine into a single course shell. You can select sections from multiple academic periods as long as their dates line up.';
$string['wdsprefs:crossenrollsuccess'] = 'Cross-Enrollment setup successful.';
$string['wdsprefs:crossenrollfail'] = 'Cross-Enrollment Failed';
$string['wdsprefs:targetperiod'] = 'Target-Period';
$string['wdsprefs:selectsections'] = 'Select Sections';
$string['wdsprefs:nosectionsselected'] = 'You must select at least one section.';
$string['wdsprefs:nocrossenrollperiods'] = 'No courses are eligible for cross-enrollment. Cross-enrollment requires courses in at least two different academic periods sharing the same start and end dates.';
$string['wdsprefs:alreadycrosssplit'] = 'This section is cross-split or cross-enrolled, please {$a} to cross-enroll this section.';
$string['wdsprefs:undoaction'] = 'undo this action';
