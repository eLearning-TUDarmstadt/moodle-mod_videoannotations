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
 * videoannotations module version information
 *
 * @package mod_videoannotations
 * @copyright  2009 Petr Skoda (http://skodak.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/videoannotations/lib.php');
require_once($CFG->dirroot.'/mod/videoannotations/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id      = optional_param('id', 0, PARAM_INT); // Course Module ID
$p       = optional_param('p', 0, PARAM_INT);  // videoannotations instance ID
$inpopup = optional_param('inpopup', 0, PARAM_BOOL);

if ($p) {
    if (!$videoannotations = $DB->get_record('videoannotations', array('id'=>$p))) {
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('videoannotations', $videoannotations->id, $videoannotations->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('videoannotations', $id)) {
        print_error('invalidcoursemodule');
    }
    $videoannotations = $DB->get_record('videoannotations', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/videoannotations:view', $context);

// Completion and trigger events.
videoannotations_view($videoannotations, $course, $cm, $context);

$videoannotations->set_url('/mod/videoannotations/view.php', array('id' => $cm->id));

$options = empty($videoannotations->displayoptions) ? array() : unserialize($videoannotations->displayoptions);

if ($inpopup and $videoannotations->display == RESOURCELIB_DISPLAY_POPUP) {
    $videoannotations->set_videoannotationslayout('popup');
    $videoannotations->set_title($course->shortname.': '.$videoannotations->name);
    $videoannotations->set_heading($course->fullname);
} else {
    $videoannotations->set_title($course->shortname.': '.$videoannotations->name);
    $videoannotations->set_heading($course->fullname);
    $videoannotations->set_activity_record($videoannotations);
}
echo $OUTPUT->header();
if (!isset($options['printheading']) || !empty($options['printheading'])) {
    echo $OUTPUT->heading(format_string($videoannotations->name), 2);
}

if (!empty($options['printintro'])) {
    if (trim(strip_tags($videoannotations->intro))) {
        echo $OUTPUT->box_start('mod_introbox', 'videoannotationsintro');
        echo format_module_intro('videoannotations', $videoannotations, $cm->id);
        echo $OUTPUT->box_end();
    }
}

$content = file_rewrite_pluginfile_urls($videoannotations->content, 'pluginfile.php', $context->id, 'mod_videoannotations', 'content', $videoannotations->revision);
$formatoptions = new stdClass;
$formatoptions->noclean = true;
$formatoptions->overflowdiv = true;
$formatoptions->context = $context;
$content = format_text($content, $videoannotations->contentformat, $formatoptions);
echo $OUTPUT->box($content, "generalbox center clearfix");

$strlastmodified = get_string("lastmodified");
echo "<div class=\"modified\">$strlastmodified: ".userdate($videoannotations->timemodified)."</div>";

echo $OUTPUT->footer();
