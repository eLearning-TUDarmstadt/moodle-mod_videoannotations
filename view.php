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

$url = new moodle_url("/mod/videoannotations/apitest.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);

$PAGE->requires->js(new moodle_url('js/rx.all.min.js'));

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



$modpath = $CFG->dirroot . '/mod/videoannotations';
require_once $modpath . '/classes/output/renderer.php';
require_once $modpath . '/classes/output/view_page.php';
require_once $modpath . '/externallib.php';
require_once $modpath . '/plugins/plugin.php';

$output = $PAGE->get_renderer('mod_videoannotations');

$plugin = videoannotations_plugin::getProperPlugin($videoannotations->url);
$annotations = \mod_videoannotations_external::get_annotations($videoannotations->id);

$annosAsArray = array();
foreach ($annotations as $i => $a) {
    $comments = array();
    foreach ($a['comments'] as $key => $c) {
        $comments[] = (object) $c;
    }
    $a['comments'] = $comments;
    
    $annosAsArray[] = (object) $a;
}

$data = [
    'cmid' => $cm->id,
    'course' => $cm->course,
    'videoannotations' => $videoannotations,
    'videourls' => $plugin->getVideoUrls(),
    'details' => $plugin->getDetails(),
    'annotations' => $annosAsArray
];


//$obj = json_decode(json_encode($data));
$obj = $data;
echo $OUTPUT->header();
$renderable = new \mod_videoannotations\output\view_page($obj);
echo $output->render($renderable);
$annotation_list = new \mod_videoannotations\output\annotation_list($data);
echo $output->render($annotation_list);
echo "<h1>DEBUG</h1>";
echo "<pre>" . print_r($obj, true) . "</pre>";

echo $OUTPUT->footer();
