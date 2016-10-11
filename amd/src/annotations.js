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
		var modinstance = $("#newannotation_modinstance").val();
		console.log("Instance: " + modinstance);
		var subject = new Rx.Subject();

		var observablesThatTriggerSubject = {
			obs: {
				//timer: Rx.Observable.timer(0, 1000),
				likeBtn: Rx.Observable.fromEvent($(".likebutton"), 'click')
					.throttle(100)
					.pluck('currentTarget')
					.flatMap(function (e) {
						console.log("likeBtn");
						var type = $(e).attr("type");
						var fk = $(e).attr("fk");
						var p = ajax.call([{
							methodname: 'mod_videoannotations_like',
							args: {
								annotationinstance: modinstance,
								referencetotype: type,
								foreignkey: fk
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
				unlikeBtn: Rx.Observable.fromEvent($(".unlikebutton"), 'click')
					.pluck('currentTarget')
					.flatMap(function (e) {
						console.log("unlikeBtn");
						var type = $(e).attr("type");
						var fk = $(e).attr("fk");
						var p = ajax.call([{
							methodname: 'mod_videoannotations_unlike',
							args: {
								annotationinstance: modinstance,
								referencetotype: type,
								foreignkey: fk
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
				createCommentBtn: Rx.Observable.fromEvent($('.createnewcomment'), 'click')
					.pluck('currentTarget')
					.flatMap(function (e) {
						console.log("createCommentBtn");
						var annotationid = $(e).attr("id").replace("newcomment_button_", "");
						var text = $("#newcomment_text_" + annotationid).val();
						$("#newcomment_text_" + annotationid).val("");
						var p = ajax.call([{
							methodname: 'mod_videoannotations_create_comment',
							args: {
								annotationid: annotationid,
								text: text,
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
				deleteAnnotationBtn: Rx.Observable.fromEvent($('.deleteAnnotationButton'), 'click')
					.pluck('currentTarget')
					.flatMap(function (btn) {
						console.log("deleteAnnotationBtn");
						var annotationId = $(btn).attr('annotationid');
						return Rx.Observable.from([annotationId]);
					})
					.flatMap(function (id) {
						var p = ajax.call([{
							methodname: 'mod_videoannotations_delete_annotation',
							args: {
								annotationid: id
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
				deleteCommentBtn: Rx.Observable.fromEvent($('.deletecommentbutton'), 'click')
					.pluck('currentTarget')
					.flatMap(function (btn) {
						console.log("deleteCommentBtn");
						var commentid = $(btn).attr("commentid");
						var p = ajax.call([{
							methodname: 'mod_videoannotations_delete_comment',
							args: {
								commentid: commentid
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
				newAnnotationBtn: Rx.Observable.fromEvent($('#newannotationbutton'), 'click')
					.flatMap(function () { 
						console.log("newAnnotationBtn");
						$("#createannotationform").accordion({
							active: false,
						});
						var p = ajax.call([{
							methodname: 'mod_videoannotations_create_annotation',
							args: {
								annotationinstance: modinstance,
								timeposition: 0,
								duration: 10,
								subject: $("#newannotation_subject").val(),
								text: $("#newannotation_text").val(),
								isquestion: $("#newannotation_isquestion").is(":checked"),
								isanswered: false,
							}
						}]);
						return Rx.Observable.fromPromise(p[0]);
					}),
			},
			subscriptions: {},
			subscribe: function (s) {
				for (var o in this.obs) {
					this.subscriptions[o] = this.obs[o].subscribe(s);
				}
			}
		};
		observablesThatTriggerSubject.subscribe(subject);
		console.log(Object.keys(observablesThatTriggerSubject.subscriptions).length + " subscriptions:");
		console.log(observablesThatTriggerSubject.subscriptions);


		var fetchData = subject.throttle(800).do(function(x) {console.log(x);}).flatMap(function () {
			var p = ajax.call([{
				methodname: 'mod_videoannotations_get_annotations',
				args: {
					id: modinstance
				}
			}]);
			return Rx.Observable.fromPromise(p[0]);
		});

		var extractNewOrModified = fetchData.flatMap(function (rawdata) {
				return Rx.Observable.from(rawdata);
			})
			.distinct();

		var renderNew = extractNewOrModified.filter(function (o) {
				return $('#annotation-id-' + o.id).length === 0;
			})
			.flatMap(function (o) {
				console.log("Found new:");
				console.log(o);
				var p = templates.render('mod_videoannotations/annotation', $.extend(true, {}, o));
				return Rx.Observable.fromPromise(p);
			})
			.subscribe(function (html, js) {
				$("#annotations").append(html);
				templates.runTemplateJS(js);
			});

		var renderModified = extractNewOrModified.filter(function (o) {
				return $('#annotation-id-' + o.id).length !== 0;
			})
			.flatMap(function (o) {
				console.log("Found modified:");
				console.log(o);
				var p = templates.render('mod_videoannotations/annotation', $.extend(true, {}, o));
				return Rx.Observable.fromPromise(p);
			})
			.subscribe(function (html, js) {
				var selector = "#" + html.match("annotation-id-[0-9]+");
				$(selector).html(html);
				$("#annotations").append(html);
				templates.runTemplateJS(js);
			});

		var removeDeletedAnnotations = fetchData.subscribe(function (currentData) {
			var ids = currentData.map(function (element) {
				return element.id;
			});
			$(".annotation").filter(function (i, element) {
				var id = $(element).attr('id').replace("annotation-id-", "");
				return ids.indexOf(parseInt(id)) === -1;
			}).remove();
		});

	});