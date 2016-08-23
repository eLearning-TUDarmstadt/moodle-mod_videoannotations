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
 * Page module capability definition
 *
 * @package mod_page
 * @copyright 2009 Petr Skoda (http://skodak.org)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined ( 'MOODLE_INTERNAL' ) || die ();

$capabilities = array (
		'mod/videoannotations:addinstance' => array(
				'riskbitmask' => RISK_XSS,
		
				'captype' => 'write',
				'contextlevel' => CONTEXT_COURSE,
				'archetypes' => array(
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW
				),
				'clonepermissionsfrom' => 'moodle/course:manageactivities'
		),		
		'mod/videoannotations:view' => array (
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_ALLOW,
						'user' => CAP_ALLOW 
				) 
		),
		'mod/videoannotations:createannotation' => array (
				'captype' => 'write',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW 
				) 
		),	
		'mod/videoannotations:deleteannotation' => array (
				'captype' => 'write',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_PROHIBIT,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW
				)
		),
		'mod/videoannotations:editannotation' => array (
				'captype' => 'write',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_PROHIBIT,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW
				)
		),
		'mod/videoannotations:createcomment' => array (
				'captype' => 'write',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW 
				) 
		),
		'mod/videoannotations:readcomments' => array (
				'captype' => 'read',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_ALLOW,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW 
				) 
		),
		'mod/videoannotations:award' => array (
				'captype' => 'write',
				'contextlevel' => CONTEXT_MODULE,
				'archetypes' => array (
						'guest' => CAP_PROHIBIT,
						'editingteacher' => CAP_ALLOW,
						'manager' => CAP_ALLOW,
						'student' => CAP_PROHIBIT,
						'teacher' => CAP_ALLOW,
						'coursecreator' => CAP_ALLOW 
				) 
		)
);
