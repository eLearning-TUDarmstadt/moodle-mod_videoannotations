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
 * This is the only page in this plugin.
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/externallib.php');

$title = "Title";
$pagetitle = "Page Title";
// Set up the page.
$url = new moodle_url("/mod/videoannotations/apitest.php");
$PAGE->set_context(context_system::instance());
$PAGE->set_url($url);
$PAGE->set_title($title);
$PAGE->set_heading($title);

global $OUTPUT;
//$output = $PAGE->get_renderer('local_hackfest');


//$page = new \local_hackfest\output\index_page();
//echo $output->render($page);

$params = array();
$params['annotationinstance'] = 1;
$params['timeposition'] = 10;
$params['duration'] = 1000;
$params['subject'] = "Mein erster Betreff";
$params['text'] = "Meine erste Annotation";
$params['isquestion'] = true;
$params['isanswered'] = false;

$newComment = [
    'annotationid' => 9,
    'text' => "<strong>Hello World!</strong>"
];

$getComments = [
    'annotationid' => 9
];

//\mod_videoannotations_external::create_annotation($params);
// \mod_videoannotations_external::get_annotations(1);
// \mod_videoannotations_external::create_comment($newComment);
// \mod_videoannotations_external::get_comments($getComments);
//$result = \mod_videoannotations_external::get_annotations(1);

require_once __DIR__ . '/plugins/plugin.php';

//$result = videoannotations_plugin::checkPlugins("https://www.youtube.com/watch?v=uK4ysfwpC4U");
$result = new stdClass();
echo $OUTPUT->header();
echo "<pre>".print_r($result, true)."</pre>";

echo $OUTPUT->footer();