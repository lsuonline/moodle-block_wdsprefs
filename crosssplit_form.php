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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    block_wdsprefs
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @copyright  2026 onwards Steve Mattsen
 * @license    http://www . gnu . org/copyleft/gpl . html GNU GPL v3 or later
 */

require_once("$CFG->libdir/formslib.php");

class crosssplit_form extends moodleform {

    public function definition() {
        global $CFG, $PAGE, $OUTPUT, $USER;

        // Add the step parameter.
        $this->_form->addElement('hidden', 'step', 'assign');
        $this->_form->setType('step', PARAM_TEXT);

        $mform = $this->_form;

        // Hidden fields for validation (userid and academic_period_id).
        $periodid = $this->_customdata['periodid'] ?? '';
        $mform->addElement('hidden', 'userid', $USER->id);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'academic_period_id', $periodid);
        $mform->setType('academic_period_id', PARAM_TEXT);

        // Get the secitons.
        $sectiondata = $this->_customdata['sectiondata'] ?? [];

        // Count the sections.
        $sectioncount = count($sectiondata);

        // Get the shell count.
        $shellcount = $this->_customdata['shellcount'] ?? 2;

        // The numbers for our strings.
        $formatter = new \NumberFormatter('en', \NumberFormatter::SPELLOUT);
        $secword = $formatter->format($sectioncount);
        $shellword = $formatter->format($shellcount);

        // Get the period.
        $period = $this->_customdata['period'] ?? [];

        // Get the teacher.
        $teacher = $this->_customdata['teacher'] ?? 2;

        // If the user is doing something silly, send them back.
        if ($shellcount > $sectioncount) {
            redirect(
                $CFG->wwwroot . '/blocks/wdsprefs/crosssplit.php',
                get_string('wdsprefs:toomanyshells',
                    'block_wdsprefs', [
                        'shell' => $shellcount,
                        'sec' => $sectioncount,
                        'shellword' => $shellword,
                        'secword' => $secword,
                    ]
                ),
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }

        if ($shellcount == 1 && $sectioncount == 1) {
            redirect(
                $CFG->wwwroot . '/blocks/wdsprefs/crosssplit.php',
                get_string('wdsprefs:onetoone', 'block_wdsprefs'),
                null,
                core\output\notification::NOTIFY_WARNING
            );
        }

        $sectionids = array_keys($sectiondata);
        $unavailable_shell_tags = $this->get_unavailable_shell_tags(
            $USER->id,
            $periodid,
            $sectionids
        );
        // Instructions.
        $mform->addElement('html',
            '<div class="alert alert-info"><p>' .
            get_string('wdsprefs:crosssplitinstructions3',
                'block_wdsprefs', [
                    'shell' => $shellcount,
                    'sec' => $sectioncount,
                    'shellword' => $shellword,
                    'secword' => $secword,
                ]
            ) .
            '</p></div>'
        );

        $mform->addElement('header', 'assignshellsheader',
            get_string('wdsprefs:assignshellsheader', 'block_wdsprefs'));

        // Add hidden fields for each shell to store selections.
        for ($i = 1; $i <= $shellcount; $i++) {
            $mform->addElement('hidden', "shell_{$i}_data", '');
            $mform->setType("shell_{$i}_data", PARAM_RAW);
        }

        // Start dual list container.
        $mform->addElement('html', '<div class="duallist-container">');

        // Available sections (single box on left).
        $mform->addElement('html', '<div class="duallist-available"><label>' .
            get_string('wdsprefs:availablesections', 'block_wdsprefs') .
            '</label><select class="form-control" id="available_sections" ' .
            'multiple size="10">');

        // Loop through the sectiondata.
        foreach ($sectiondata as $value => $label) {
            $mform->addElement('html',
                '<option value="' . $value . '">' .
                $label . '</option>');
        }

        $mform->addElement('html', '</select></div>');

        // Add the control buttons.
        $mform->addElement('html', '
            <div class="duallist-controls">
                <button type="button" class="btn btn-secondary mb-2" id="move-to-shell-btn">
                    ' . $OUTPUT->pix_icon('t/right', '') . ' Add to Shell</button>
                <button type="button" class="btn btn-secondary" id="move-back-btn">
                    ' . $OUTPUT->pix_icon('t/left', '') . ' Remove</button>
            </div>');

        // Shell sections (multiple boxes on right). Pass period/teacher for live preview.
        $shelltagerror = get_string('wdsprefs:shelltaginvalid', 'block_wdsprefs');
        $shelltaguniqueerror = get_string('wdsprefs:shelltagunique', 'block_wdsprefs');
        $mform->addElement('html', '<div class="duallist-shells" data-period="' . s($period) . '" data-teacher="' . s($teacher) . '" data-shell-tag-error="' . s($shelltagerror) . '" data-shell-tag-unique-error="' . s($shelltaguniqueerror) . '"><label>' .
            get_string('wdsprefs:availableshells', 'block_wdsprefs') . '</label>'
        );

        // Create the shell select boxes: text input above, preview string below, then select.
        for ($i = 1; $i <= $shellcount; $i++) {
            $defaultname = "Shell $i";
            $previewtext = s($period) . ' ' . s($teacher) . ' (' . $defaultname . ')';
            $mform->addElement('html', '<div class="duallist-shell" data-shell-num="' . $i . '">');
            $mform->addElement('html', '<div class="duallist-shell-preview" data-shell-num="' . $i . '">' . $previewtext . '</div>');
            $mform->addElement('text', "shell_{$i}_tag", '', ['size' => 20, 'maxlength' => 128, 'class' => 'shell-tag', 'placeholder' => $defaultname]);
            $mform->setType("shell_{$i}_tag", PARAM_TEXT);
            $mform->setDefault("shell_{$i}_tag", '');
            $mform->addElement('html', '<select class="form-control shell-select" id="shell_' . $i . '" data-shell-num="' . $i . '" multiple size="2"></select></div>');
        }

        $mform->addElement('html', '</div></div>');

        // Add JavaScript INLINE to manage the dual list functionality.
        $mform->addElement('html', '
        <script>
        const unavailableShellTags = ' . json_encode(array_values($unavailable_shell_tags)) . ';
        console.log(unavailableShellTags);
        console.log("Period: ' . $this->_customdata['periodid'] . '");
        console.log("Teacher: ' . $USER->id . '");
        document.addEventListener("DOMContentLoaded", function() {
            let activeShellId = "shell_1";

            // Set active shell when clicked
            function setActiveShell(shellId) {
                document.querySelectorAll(".duallist-shell").forEach(shell => {
                    shell.classList.remove("active-shell");
                });

                const shellContainer = document.getElementById(shellId).parentElement;
                shellContainer.classList.add("active-shell");

                activeShellId = shellId;
            }

            // Initialize by setting first shell as active
            setActiveShell("shell_1");

            // Update shell tag preview as user types
            const shellsContainer = document.querySelector(".duallist-shells");
            const period = shellsContainer ? shellsContainer.getAttribute("data-period") || "" : "";
            const teacher = shellsContainer ? shellsContainer.getAttribute("data-teacher") || "" : "";
            function updateShellPreview(shellNum, value) {
                const preview = document.querySelector(".duallist-shell-preview[data-shell-num=\"" + shellNum + "\"]");
                if (preview) {
                    preview.textContent = period + " " + teacher + " (" + (value.trim() || "Shell " + shellNum) + ")";
                }
            }
            var shellTagRegex = /^[a-zA-Z0-9_ -]*$/;
            function validateShellTag(value) {
                return value === "" || shellTagRegex.test(value);
            }
            function getShellTagErrorEl(input) {
                var id = input.id || "";
                var errorId = id.replace(/^id_/, "id_error_");
                var el = document.getElementById(errorId);
                if (!el && input.closest) {
                    var felement = input.closest(".felement");
                    if (felement) el = felement.querySelector(".invalid-feedback");
                }
                return el;
            }
            function showShellTagError(input, message) {
                var fitem = input.closest(".fitem");
                var errorEl = getShellTagErrorEl(input);
                if (fitem) fitem.classList.add("has-danger");
                input.classList.add("is-invalid");
                if (errorEl) {
                    errorEl.textContent = message;
                    errorEl.style.display = "block";
                }
            }
            function hideShellTagError(input) {
                var fitem = input.closest(".fitem");
                var errorEl = getShellTagErrorEl(input);
                if (fitem) fitem.classList.remove("has-danger");
                input.classList.remove("is-invalid");
                if (errorEl) {
                    errorEl.textContent = "";
                    errorEl.style.display = "";
                }
            }
            var uniqueErrorMsg = shellsContainer ? shellsContainer.getAttribute("data-shell-tag-unique-error") || "" : "";
            function validateShellTagUniqueness() {
                var tagInputs = [];
                document.querySelectorAll(".duallist-shell").forEach(function(shellBlock) {
                    var num = shellBlock.getAttribute("data-shell-num");
                    var inp = shellBlock.querySelector("input[name*=\"shell_\"][name*=\"_tag\"]");
                    if (inp && num) tagInputs.push({ num: num, input: inp });
                });
                var fields_by_shelltag = {};
                tagInputs.forEach(function(item) {
                    var key = ((item.input.value || "").trim() || ("Shell " + item.num)).toLowerCase();
                    if (!fields_by_shelltag[key]) fields_by_shelltag[key] = [];
                    fields_by_shelltag[key].push(item.input);
                });
                tagInputs.forEach(function(item) {
                    var key = ((item.input.value || "").trim() || ("Shell " + item.num)).toLowerCase();
                    if (fields_by_shelltag[key].length > 1) {
                        showShellTagError(item.input, uniqueErrorMsg);
                    } else if (validateShellTag(item.input.value)) {
                        hideShellTagError(item.input);
                    }
                });
            }
            function bindShellTagInput(shellNum, input) {
                if (!input || !shellNum || input.dataset.shellPreviewBound) return;
                input.dataset.shellPreviewBound = "1";
                var errorMsg = shellsContainer ? shellsContainer.getAttribute("data-shell-tag-error") || "" : "";
                function onShellTagChange() {
                    updateShellPreview(shellNum, this.value);
                    if (validateShellTag(this.value)) {
                        hideShellTagError(this);
                    } else {
                        showShellTagError(this, errorMsg);
                    }
                    validateShellTagUniqueness();
                }
                input.addEventListener("input", onShellTagChange);
                input.addEventListener("change", onShellTagChange);
                onShellTagChange.call(input);
            }
            document.querySelectorAll(".duallist-shell").forEach(function(shellBlock) {
                const shellNum = shellBlock.getAttribute("data-shell-num");
                var input = shellBlock.querySelector("input[type=\"text\"]");
                if (!input) {
                    input = shellBlock.querySelector("input[name*=\"shell_\"][name*=\"_tag\"]");
                }
                bindShellTagInput(shellNum, input);
            });
            document.querySelectorAll("input[type=\"text\"]").forEach(function(input) {
                var name = input.getAttribute("name") || "";
                var m = name.match(/shell_(\d+)_tag/);
                if (m) {
                    bindShellTagInput(m[1], input);
                }
            });

            // Add click event to shells
            document.querySelectorAll(".duallist-shell").forEach(shell => {
                shell.addEventListener("click", function() {
                    const selectElement = this.querySelector("select");
                    if (selectElement) {
                        setActiveShell(selectElement.id);
                    }
                });
            });

            // Make select elements forward click events
            document.querySelectorAll(".shell-select").forEach(select => {
                select.addEventListener("click", function(e) {
                    e.stopPropagation();
                    setActiveShell(this.id);
                });
            });

            // Move selected options to active shell
            document.getElementById("move-to-shell-btn").addEventListener("click", function() {
                const available = document.getElementById("available_sections");
                const target = document.getElementById(activeShellId);

                if (available && target) {
                    const selectedOptions = Array.from(available.selectedOptions);

                    selectedOptions.forEach(option => {
                        const newOption = document.createElement("option");
                        newOption.value = option.value;
                        newOption.text = option.text;
                        target.appendChild(newOption);

                        available.removeChild(option);
                    });

                    // Update hidden fields
                    updateHiddenFields();
                }
            });

            // Move selected options back to available
            document.getElementById("move-back-btn").addEventListener("click", function() {
                const available = document.getElementById("available_sections");

                document.querySelectorAll(".shell-select").forEach(shellSelect => {
                    const selectedOptions = Array.from(shellSelect.selectedOptions);

                    selectedOptions.forEach(option => {
                        const newOption = document.createElement("option");
                        newOption.value = option.value;
                        newOption.text = option.text;
                        available.appendChild(newOption);

                        shellSelect.removeChild(option);
                    });
                });

                // Update hidden fields
                updateHiddenFields();
            });

            // Update hidden fields with current selections
            function updateHiddenFields() {
                document.querySelectorAll(".shell-select").forEach(select => {
                    const shellNum = select.getAttribute("data-shell-num");
                    const values = Array.from(select.options).map(opt => opt.value);

                    // Store as JSON in hidden field
                    document.querySelector(`input[name="shell_${shellNum}_data"]`).value =
                        JSON.stringify(values);
                });
                updateShellSelectSizes();
            }
            function updateShellSelectSizes() {
                document.querySelectorAll(".shell-select").forEach(function(select) {
                    const count = select.options.length;
                    select.size = Math.max(2, count);
                });
            }

            // Restore shell selections from hidden fields (e.g. after validation errors)
            function restoreFromHiddenFields() {
                const available = document.getElementById("available_sections");
                if (!available) return;

                document.querySelectorAll(".shell-select").forEach(function(shellSelect) {
                    const shellNum = shellSelect.getAttribute("data-shell-num");
                    let hiddenInput = document.querySelector("input[name=\"shell_" + shellNum + "_data\"]");
                    if (!hiddenInput) {
                        hiddenInput = document.querySelector("input[name$=\"shell_" + shellNum + "_data\"]");
                    }
                    if (!hiddenInput || !hiddenInput.value) return;

                    let sectionIds;
                    try {
                        sectionIds = JSON.parse(hiddenInput.value);
                    } catch (e) {
                        return;
                    }
                    if (!Array.isArray(sectionIds)) return;

                    sectionIds.forEach(function(sectionId) {
                        const sid = String(sectionId);
                        const opt = Array.from(available.options).find(function(o) { return o.value === sid || o.value === sectionId; });
                        if (opt) {
                            const newOpt = document.createElement("option");
                            newOpt.value = opt.value;
                            newOpt.text = opt.text;
                            shellSelect.appendChild(newOpt);
                            available.removeChild(opt);
                        }
                    });
                });
                updateShellSelectSizes();
            }
            restoreFromHiddenFields();

            // Handle form submission
            const form = document.querySelector("form.mform");
            if (form) {
                form.addEventListener("submit", function(e) {
                    updateHiddenFields();
                    validateShellTagUniqueness();
                    if (document.querySelector(".shell-tag.is-invalid")) {
                        e.preventDefault();
                    }
                });
            }
        });
        </script>
        ');

        $this->add_action_buttons(true, get_string('submit'));
    }

    /**
     * Form validation. Shell tags may only contain alphanumeric characters, dashes and underscores.
     *
     * @param array $data Form data
     * @param array $files Uploaded files
     * @return array Validation errors
     */
    public function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);
        $shellcount = $this->_customdata['shellcount'] ?? 2;

        $userid = isset($data['userid']) ? (int) $data['userid'] : $USER->id;
        $academicperiodid = isset($data['academic_period_id']) ? $data['academic_period_id'] : ($this->_customdata['periodid'] ?? '');

        // Collect section IDs from submitted shell_*_data (JSON arrays).
        $sectionids = [];
        for ($i = 1; $i <= $shellcount; $i++) {
            $fieldname = "shell_{$i}_data";
            if (!empty($data[$fieldname])) {
                $decoded = json_decode($data[$fieldname], true);
                if (is_array($decoded)) {
                    $sectionids = array_merge($sectionids, $decoded);
                }
            }
        }
        $sectionids = array_values(array_unique(array_map('intval', $sectionids)));

        $unavailable_shell_tags = $this->get_unavailable_shell_tags($userid, $academicperiodid, $sectionids);
        $tag_by_field = [];

        for ($i = 1; $i <= $shellcount; $i++) {
            $fieldname = "shell_{$i}_tag";
            $value = isset($data[$fieldname]) ? trim($data[$fieldname]) : '';
            if ($value !== '' && !preg_match('/^[a-zA-Z0-9_ -]+$/', $value)) {
                $errors[$fieldname] = get_string('wdsprefs:shelltaginvalid', 'block_wdsprefs');
            }
            $tag_by_field[$fieldname] = $value !== '' ? trim($value) : "Shell $i";
            if (in_array($tag_by_field[$fieldname], $unavailable_shell_tags)) {
                $errors[$fieldname] = get_string('wdsprefs:shelltagunavailable', 'block_wdsprefs');
            }
        }

        // Check uniqueness of shell tags.
        $fields_by_shelltag = [];
        foreach ($tag_by_field as $fn => $key) {
            if (!isset($errors[$fn])) {
                $fields_by_shelltag[$key][] = $fn;
            }
        }
        foreach ($fields_by_shelltag as $fieldnames) {
            // If there are multiple fields with the same shell tag, add an error to each field.
            if (count($fieldnames) > 1) {
                $err = get_string('wdsprefs:shelltagunique', 'block_wdsprefs');
                foreach ($fieldnames as $fn) {
                    $errors[$fn] = $err;
                }
            }
        }

        return $errors;
    }

    /**
     * Get the unavailable shell tags for the given context.
     *
     * When sectionids are provided, returns tags already used by any existing crosssplit
     * that shares at least one (academic_period_id, course_listing_id) with those sections.
     * When sectionids are empty, returns all shell tags for the user and period (legacy behavior).
     *
     * @param int $userid User id
     * @param string $academic_period_id Academic period id
     * @param array $sectionids Section ids in the current assignment (optional)
     * @return array Unavailable shell tags (trimmed)
     */
    public function get_unavailable_shell_tags($userid, $academic_period_id, array $sectionids = []) : array {
        global $DB;
        // Scoped: only shells for the same course listing(s) and period as the assignment sections.
        $sectionids = array_map('intval', $sectionids);
        list($insql, $inparams) = $DB->get_in_or_equal($sectionids, SQL_PARAMS_NAMED, 'sid');
        $params = array_merge(['academic_period_id' => $academic_period_id], $inparams);

        // Get distinct course_listing_ids for the assignment sections.
        $clquery = "SELECT DISTINCT course_listing_id
            FROM {enrol_wds_sections}
            WHERE academic_period_id = :academic_period_id
            AND id " . $insql;
        $courselistings = $DB->get_fieldset_sql($clquery, $params);
        if (empty($courselistings)) {
            return [];
        }

        list($clsql, $clparams) = $DB->get_in_or_equal($courselistings, SQL_PARAMS_NAMED, 'clid');
        $params = ['academic_period_id' => $academic_period_id];
        $params = array_merge($params, $clparams);

        $query = "SELECT DISTINCT
            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(cs.shell_name, '(', -1), ')', 1)) AS shell_tag
            FROM {block_wdsprefs_crosssplits} cs
            INNER JOIN {block_wdsprefs_crosssplit_sections} css ON css.crosssplit_id = cs.id
            INNER JOIN {enrol_wds_sections} sec ON sec.id = css.section_id
            WHERE sec.academic_period_id = :academic_period_id
            AND sec.course_listing_id " . $clsql;
        $shelltags = $DB->get_records_sql($query, $params);

        $tags = array_map(function($row) {
            return trim($row->shell_tag);
        }, $shelltags);
        return array_values(array_unique($tags));
    }
}
