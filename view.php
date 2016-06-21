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
echo "<h1>DEBUG</h1>";
echo "<pre>" . print_r($obj, true) . "</pre>";
//echo $OUTPUT->heading(format_string('Name des Videos'), 2);
//echo '<video><source src="https://olw-material.hrz.tu-darmstadt.de/olw-konv-repository/material/7f/6d/69/90/73/46/11/e5/a1/df/00/50/56/bd/73/ae/2.mp4"></video>';
//echo '<hr>';

/*
require_once $CFG->dirroot . '/mod/videoannotations/classes/output/renderer.php';
require_once $CFG->dirroot . '/mod/videoannotations/classes/output/view_page.php';
$output = $PAGE->get_renderer('mod_videoannotations');
$tabs = [
  0 => [
    "id" => "tab1",
    "name" => "Annotationen zur aktuellen Stelle",
    "content" => "<b>aktuelle Annotationen</b>"
  ],
  1 => [
    "id" => "tab2",
    "name" => "alle Annotationen",
    "content" => "<b>alle Annotationen</b>"
  ]
];

$renderable = new \mod_videoannotations\output\view_page($tabs);
echo $output->render($renderable);
*/
/*
echo '<hr>';

echo '<style>
th, td {
    padding: 5px;
    text-align: left;
    vertical-align: top;
}
</style>';

echo '
<table>
  <tr>
    <td>
    <label>
      Gruppen
    <select>
      <option>Alle Gruppen anzeigen</option>
      <option>Gruppe 1</option>
      <option>Gruppe 2</option>
      <option>Gruppe 3</option>
    </select>
    </label>
    </td>
    <td>
    <label>
      Sortieren nach
      <select>
        <option>Likes</option>
        <option>Aktualität</option>
      </select>
    </label>
    </td>
  </tr>
  <tr>
    <td>
    <label>
      <input type="checkbox" name="zutat" value="salami">
      Nur unbeantwortete Annotationen anzeigen
    </label>
    </td>
    <tD>
    <fieldset>
      <input type="radio" id="mc" name="Zahlmethode" value="Mastercard" checked><label for="mc"> Nur Annonationen zur aktuellen Stelle</label><br>
      <input type="radio" id="vi" name="Zahlmethode" value="Visa"><label for="vi">  Alle Annonationen</label><br>
    </fieldset>
    </td>
  </tr>
</table>
';

echo '
<table style="vertical-align: top; padding: 5px;">
  <tr>
    <td style="vertical-align: top;"><img src="https://mdl-alpha.un.hrz.tu-darmstadt.de/pluginfile.php/30/user/icon/tudarmstadt/f1?rev=9359" alt="Nutzerbild Support Moodle" title="Nutzerbild Support Moodle" class="userpicture" width="64" height="64"></td>
    <td>
      <b>User XYZ</b>  vor 3 Stunden <br>
      Meine Frage...<BR>
      <button>Antworten</button><button>Like</button><button>Als beantwortet markieren</button><button>Annotation übernehmen</button><BR>
      <table>
      <tr>
        <td><img src="https://moodle.tu-darmstadt.de/pluginfile.php/34463/user/icon/tudarmstadt/f2?rev=295314" class="userpicture" width="64" height="64"></td>
        <td>
          <b>User 3</b>  vor 2 Stunden <br>
          Antwort<BR>
          <button>Antworten</button><button>Like</button><BR>
          <table>
          </table>
        </td>
      </tr>
      </table>
    </td>
  </tr>
</table>
';
/*
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
*/
echo $OUTPUT->footer();
