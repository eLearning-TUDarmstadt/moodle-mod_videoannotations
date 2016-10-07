/*jslint vars: true, plusplus: true, devel: true, nomen: true, indent: 4, maxerr: 50 */
/*global define */
/* jshint unused:false */
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
	['jquery', 'core/ajax', 'core/templates', 'core/notification', 'jqueryui', 'js/rx.all.min.js'],
	function ($, ajax, templates, notification, jqui, Rx) {
		
		/*
		var rerenderAnnotationList = function (modinstance) {
			setLoaderVisible('annotationsloader');
			$(".annotation").css("visibility", "hidden");

			var d = $.Deferred();
			var promises = ajax.call([{
				methodname: 'mod_videoannotations_get_annotations',
				args: { id: modinstance },
				fail: function (e) {
					//notification.exception;
					console.log(e);
					d.reject();
				}
			}]);
			promises[0].done(function (rawdata) {
				var data = { numberOfAnnotations: rawdata.length, annotations: rawdata };
				var promise = templates.render('mod_videoannotations/annotation_list', data);
				promise.done(function (html, js) {
					$('[data-region="annotation-list"]').replaceWith(html);
					templates.runTemplateJS(js);
					d.resolve();
				});
				promise.fail(function (e) {
					console.log("ERROR!");
					console.log(e);
					//notification.exception
					d.reject();
				});
			});
			return d.promise();
		};
		*/


		var deleteAnnotation = function (id) {
			var d = $.Deferred();

			ajax.call([{
				methodname: 'mod_videoannotations_delete_annotation',
				args: { annotationid: id },
				fail: notification.exception,
				done: function () {
					d.resolve();
				}
			}]);
			return d.promise();
		};
		/*
		var setLoaderInvisible = function(id) {
			var loader = $("annotationsloader");
			loader.css("visibility", "hidden");
		};
		*/
		var setLoaderVisible = function (id) {
			var loader = $("#" + id);
			//console.log(loader);
			loader.css("visibility", "visible");
		};

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
				// Accordion effect
				$("#createannotationform").accordion({
					active: false,
					collapsible: true,
					icons: {
						header: "icon-arrow-right",//"ui-icon-circle-arrow-e",
						activeHeader: "icon-arrow-down"//"ui-icon-circle-arrow-s"
					}
				});

				$('#newannotationbutton').on('click', function () {
					$(".annotation").css("visibility", "hidden");
					$("#createannotationform").accordion({
						active: false,
					});
					var modinstance = $("#newannotation_modinstance").val();
					// First - reload the data for the page.

					ajax.call([{
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
						done: function () {
							$("#newannotation_subject").val("");
							$("#newannotation_text").val("");
							//rerenderAnnotationList(modinstance);
						}
					}]);
				});
			},
			deleteAnnotationListener: function () {
				var modinstance = $("#newannotation_modinstance").val();

				$('.deleteAnnotationButton').on('click', function () {
					var annotationid = $(this).attr('annotationid');
					deleteAnnotation(annotationid).done(function () {
						//rerenderAnnotationList(modinstance);
					});
				});
			},

			deleteCommentListener: function() {
				var modinstance = $("#newannotation_modinstance").val();
				$('.deletecommentbutton').on('click', function() {
					var commentid =  $(this).attr("commentid");

					ajax.call([{
						methodname: 'mod_videoannotations_delete_comment',
						args: {
							commentid: commentid,
						},
						fail: notification.exception,
						done: function () {
							//rerenderAnnotationList(modinstance);
						}
					}]);
				});
			},

			createNewCommentListener: function () {
				var modinstance = $("#newannotation_modinstance").val();
				$('.createnewcomment').unbind('click').on('click', function (e) {
					var annotationid = $(this).attr("id").replace("newcomment_button_", "");
					var text = $("#newcomment_text_" + annotationid).val();

					ajax.call([{
						methodname: 'mod_videoannotations_create_comment',
						args: {
							annotationid: annotationid,
							text: text,
						},
						fail: notification.exception,
						done: function () {
							$("#newcomment_text_" + annotationid).val("");
							//rerenderAnnotationList(modinstance);
						}
					}]);
				});
			},

			likeListener: function() {
				var modinstance = $("#newannotation_modinstance").val();
				$(".likebutton").on('click', function() {
					var type = $(this).attr("type");
					var fk = $(this).attr("fk");
					ajax.call([{
						methodname: 'mod_videoannotations_like',
						args: {
							annotationinstance: modinstance,
							referencetotype: type,
							foreignkey: fk
						},
						fail: notification.exception,
						done: function () {
							//rerenderAnnotationList(modinstance);
						}
					}]);

				});
			},
			unlikeListener: function() {
				var modinstance = $("#newannotation_modinstance").val();
				$(".unlikebutton").on('click', function() {
					
				console.log("Unlike button pressed!");
					var type = $(this).attr("type");
					var fk = $(this).attr("fk");
					ajax.call([{
						methodname: 'mod_videoannotations_unlike',
						args: {
							annotationinstance: modinstance,
							referencetotype: type,
							foreignkey: fk
						},
						fail: notification.exception,
						done: function () {
							//rerenderAnnotationList(modinstance);
						}
					}]);

				});
			}
		};
	});

