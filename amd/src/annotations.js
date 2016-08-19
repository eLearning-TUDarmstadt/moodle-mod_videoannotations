/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 *//*global define */
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
 * This is an empty module, that is required before all other modules. Because
 * every module is returned from a request for any other module, this forces the
 * loading of all modules with a single request.
 * 
 * @module mod_videoannotations/annotations
 * @package mod_videoannotations
 * @copyright 2016 Steffen Pegenau <steffen.pegenau@hrz.tu-darmstadt.de>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since 2.9
 */
define(
	['jquery', 'core/ajax', 'core/templates', 'core/notification'],
	function ($, ajax, templates, notification) {
		var renderAnnotationList = function (modinstance) {
			ajax.call([{
				methodname: 'mod_videoannotations_get_annotations',
				args: { id: modinstance },
				fail: notification.exception,
				done: function (rawdata) {
					var data = { annotations: rawdata };
					templates.render('mod_videoannotations/annotation_list',
						data).done(function (html, js) {
							$('[data-region="annotation-list"]').replaceWith(html);
							// And execute any JS that was in the template.
							templates.runTemplateJS(js);
						});
				}
			}]);
		}

		return /** @alias module:mod_annotations/annotations */	{
			/**
			 * Refresh the middle of the page!
			 * 
			 * @method refresh
			 */
			/*
			 * help: function() { // Add a click handler to the button.
			 * $('#newannotationbutton').on('click', function(e) {
			 * console.log("Hello world"); console.log(e); }); },
			 */
			createNewAnnotation: function () {
				$('#newannotationbutton').on('click', function () {
					var modinstance = $("#newannotation_modinstance").val();
					// First - reload the data for the page.
					var promises = ajax.call([{
						methodname: 'mod_videoannotations_create_annotation',
						args: {
							annotationinstance: modinstance,
							timeposition: 0,
							duration: 10,
							subject: $("#newannotation_subject").val(),
							text: $("#newannotation_text").val(),
							isquestion: $("#newannotation_isquestion").is(":checked"),
							isanswered: false,
						},
						fail: notification.exception,
					}]);
					promises[0].done(function () {
						renderAnnotationList(modinstance);
					});
				});
			}
		};
	});
