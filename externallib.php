<?php

/**
 * videoannotations external file
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once ($CFG->libdir . "/externallib.php");
class mod_videoannotations_external extends external_api {
	
	//
	// Create comment
	//
	public static function create_comment_parameters() {
		return new external_function_parameters ( array (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				'annotationid' => new external_value ( PARAM_INT, 'The id of the commented annotation', VALUE_REQUIRED ),
				'text' => new external_value ( PARAM_RAW, 'The text of the comment', VALUE_REQUIRED ) 
		) );
	}
	public static function create_comment_returns() {
		return new external_single_structure ( array (
				'id' => new external_value ( PARAM_INT, 'The id of the newly created comment' ) 
		) );
	}
	public static function create_comment($annotationid, $text) {
		global $DB;
		
		// Parameters validation
		$params = self::validate_parameters ( self::create_comment_parameters (), array (
				'annotationid' => $annotationid,
				'text' => $text 
		) );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		// Capability validation
		require_capability ( 'mod/videoannotations:createcomment', $context );
		
		// Does the annotation exist?
		if (! $DB->record_exists ( 'videoannotations_annotations', array (
				'id' => $params ['annotationid'] 
		) )) {
			throw new dml_exception ( 'wrongdestpath', 'annotation not found', 'The annotation with the id ' . $params ['annotationid'] . ' does not exist' );
		}
		
		$data = new stdClass ();
		$data->annotationid = $params ['annotationid'];
		$data->text = $params ['text'];
		
		// Get the current user as author
		global $USER;
		$data->author = intval ( $USER->id );
		
		$data->timecreated = time ();
		$data->timemodified = time ();
		
		// Insert and return
		return array (
				'id' => $DB->insert_record ( 'videoannotations_comments', $data ) 
		);
	}
	
	// Delete comment
	public static function delete_comment_parameters() {
		return new external_function_parameters ( array (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				'commentid' => new external_value ( PARAM_INT, 'The id of the comment' ) 
		) );
	}
	public static function delete_comment_returns() {
		return new external_single_structure ( array () );
	}
	public static function delete_comment($commentid) {
		global $DB;
		$array = array (
				'commentid' => $commentid 
		);
		// Parameters validation
		$params = self::validate_parameters ( self::delete_comment_parameters (), $array );
		
		// Does the comment exist?
		if (! $DB->record_exists ( 'videoannotations_comments', array (
				'id' => $params ['commentid'] 
		) )) {
			throw new dml_exception ( 'wrongdestpath', 'annotation not found', 'The comment with the id ' . $params ['commentid'] . ' does not exist' );
		}
		
		$annotationinstance = $DB->get_field ( 'videoannotations_comments', 'annotationid', array (
				'id' => $params ['commentid'] 
		), MUST_EXIST );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $annotationinstance );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		// Capability validation - using the same cap when deleting an annotation
		require_capability ( 'mod/videoannotations:deleteannotation', $context );
		
		// delete comments
		$DB->delete_records ( 'videoannotations_comments', array (
				'id' => $params ['commentid'] 
		) );
		// delete likes
		$DB->delete_records ( 'videoannotations_likes', array (
				'referencetotype' => 'comment',
				'foreignkey' => $params ['commentid'] 
		) );
		
		// Insert and return
		return array ();
	}
	
	//
	// Delete annotation
	//
	public static function delete_annotation_parameters() {
		return new external_function_parameters ( array (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				'annotationid' => new external_value ( PARAM_INT, 'The id of the commented annotation' ) 
		) );
	}
	public static function delete_annotation_returns() {
		return new external_single_structure ( array () );
	}
	public static function delete_annotation($annotationid) {
		global $DB;
		$array = array (
				'annotationid' => $annotationid 
		);
		// Parameters validation
		$params = self::validate_parameters ( self::delete_annotation_parameters (), $array );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		// Capability validation
		require_capability ( 'mod/videoannotations:deleteannotation', $context );
		
		// Does the annotation exist?
		if (! $DB->record_exists ( 'videoannotations_annotations', array (
				'id' => $params ['annotationid'] 
		) )) {
			throw new dml_exception ( 'wrongdestpath', 'annotation not found', 'The annotation with the id ' . $params ['annotationid'] . ' does not exist' );
		}
		
		// Delete annotations
		$DB->delete_records ( 'videoannotations_annotations', array (
				'id' => $params ['annotationid'] 
		) );
		
		// delete comments
		$DB->delete_records ( 'videoannotations_comments', array (
				'annotationid' => $params ['annotationid'] 
		) );
		// delete likes
		$DB->delete_records ( 'videoannotations_likes', array (
				'referencetotype' => 'annotation',
				'foreignkey' => $params ['annotationid'] 
		) );
		
		// Insert and return
		return array ();
	}
	
	//
	// Get comments for annotation
	//
	public static function get_comments_parameters() {
		return new external_function_parameters ( array (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				'annotationid' => new external_value ( PARAM_INT, 'The id of the annotation', VALUE_REQUIRED ) 
		) );
	}
	public static function get_comments_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'id' => new external_value ( PARAM_INT, 'annotation id' ),
				'text' => new external_value ( PARAM_RAW, 'the text' ),
				'author' => new external_value ( PARAM_INT, 'the authors user id' ),
				'author_firstname' => new external_value ( PARAM_TEXT, 'the authors firstname' ),
				'author_lastname' => new external_value ( PARAM_TEXT, 'the authors lastname' ),
				'timecreated' => new external_value ( PARAM_INT, 'the unix creation timestamp' ),
				'timemodified' => new external_value ( PARAM_INT, 'the unix last change timestamp' ),
				'isuserallowedtoedit' => new external_value ( PARAM_BOOL, 'is the user allowed to edit this annotation?' ),
				'isuserallowedtodelete' => new external_value ( PARAM_BOOL, 'is the user allowed to delete this annotation?' ),
				'likes' => self::get_likes_returns(),
				'likedbyuser' => new external_value ( PARAM_BOOL, 'has the user liked this comment?' ),
		) ) );
	}
	public static function get_comments($array) {
		global $DB, $USER;
		
		// Parameters validation
		$params = self::validate_parameters ( self::get_comments_parameters (), $array );
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationid'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		// Capability validation
		require_capability ( 'mod/videoannotations:readcomments', $context );
		
		$sql = "SELECT " . "vc.id," . "vc.text," . "vc.author," . "vc.timecreated," . "vc.timemodified," . "u.firstname as author_firstname," . "u.lastname as author_lastname " . "FROM {videoannotations_comments} vc, {user} u " . "WHERE vc.author = u.id AND annotationid = " . $params ['annotationid'];
		
		$isuserallowedtoedit = has_capability ( 'mod/videoannotations:deleteannotation', $context ) ? 1 : 0;
		$isuserallowedtodelete = has_capability ( 'mod/videoannotations:editannotation', $context ) ? 1 : 0;
		
		$comments = $DB->get_records_sql ( $sql );
		
		foreach ( $comments as $id => $comment ) {
			$comments [$id]->isuserallowedtoedit = $isuserallowedtoedit;
			$comments [$id]->isuserallowedtodelete = $isuserallowedtodelete;
			$comments [$id]->likes = self::get_likes($params ['annotationid'], 'comment', $id);
			$comments [$id]->likedbyuser = self::hasUserLikedIt($USER->id, $comments [$id]->likes);
		}
		return $comments;
		// return $DB->get_records('videoannotations_comments', array('annotationid' => $params['annotationid']), null, 'id,text,author,timecreated,timemodified');
	}
	
	//
	// Create annotaion
	//
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function create_annotation_parameters() {
		// FUNCTIONNAME_parameters() always return an external_function_parameters().
		// The external_function_parameters constructor expects an array of external_description.
		return new external_function_parameters ( array (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				'annotationinstance' => new external_value ( PARAM_INT, 'The instance id of table {videoannotations}' ),
				'timeposition' => new external_value ( PARAM_INT, 'The position of the annotation in ms' ),
				'duration' => new external_value ( PARAM_INT, 'The duration of the annotation' ),
				'subject' => new external_value ( PARAM_TEXT, 'The subject of the annotation' ),
				'text' => new external_value ( PARAM_RAW, 'The text of the annotation' ),
				'isquestion' => new external_value ( PARAM_BOOL, 'Is this annotation a question?' ),
				'isanswered' => new external_value ( PARAM_BOOL, 'Is this question answered?' ) 
		) );
		// 'group' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL)
		
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function create_annotation_returns() {
		return new external_single_structure ( array (
				'id' => new external_value ( PARAM_INT, 'The id of the newly created annotation' ) 
		) );
	}
	
	/**
	 * Creates an annotation
	 *
	 * @return int The id of the newly created annotation
	 */
	public static function create_annotation($annotationinstance, $timeposition, $duration, $subject, $text, $isquestion, $isanswered) {
		$array = array (
				'annotationinstance' => $annotationinstance,
				'timeposition' => $timeposition,
				'duration' => $duration,
				'subject' => $subject,
				'text' => $text,
				'isquestion' => $isquestion,
				'isanswered' => $isanswered 
		);
		// Parameters validation
		$params = self::validate_parameters ( self::create_annotation_parameters (), $array );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		// Capability validation
		require_capability ( 'mod/videoannotations:createannotation', $context );
		
		$data = new stdClass ();
		$data->annotationinstance = $params ['annotationinstance'];
		$data->timeposition = $params ['timeposition'];
		$data->duration = $params ['duration'];
		$data->subject = $params ['subject'];
		$data->text = $params ['text'];
		$data->isquestion = $params ['isquestion'];
		$data->isanswered = $params ['isanswered'];
		
		// Get the current user as author
		global $USER;
		$data->author = intval ( $USER->id );
		
		// $data->group = $params['group'];
		$data->timecreated = time ();
		$data->timemodified = time ();
		
		// Insert and return
		global $DB;
		$id = $DB->insert_record ( 'videoannotations_annotations', $data );
		return array (
				'id' => $id 
		);
	}
	
	/**
	 * Returns description of method parameters
	 *
	 * @return external_function_parameters
	 */
	public static function get_annotations_parameters() {
		// FUNCTIONNAME_parameters() always return an external_function_parameters().
		// The external_function_parameters constructor expects an array of external_description.
		return new external_function_parameters ( 
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				array (
						'id' => new external_value ( PARAM_INT, 'The instance id of a videoannotations activity' ) 
				) );
	}
	
	/**
	 * The function itself
	 *
	 * @return string welcome message
	 */
	public static function get_annotations($instance) {
		// Parameters validation
		$params = self::validate_parameters ( self::get_annotations_parameters (), array (
				'id' => $instance 
		) );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['id'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		global $DB, $CFG, $USER;
		
		$sql = "SELECT " . "va.id, " . "va.annotationinstance, " . "va.timeposition, " . "va.duration, " . "va.group, " . "va.subject, " . "va.text, " . "va.isquestion, " . "va.isanswered, " . "va.author, " . "va.timecreated, " . "va.timemodified, " . "u.firstname AS author_firstname, " . "u.lastname AS author_lastname " . "FROM {videoannotations_annotations} va, {user} u " . "WHERE va.author = u.id AND va.annotationinstance = " . $params ['id'];
		
		$annotations = $DB->get_records_sql ( $sql );
		
		require_once $CFG->libdir . '/accesslib.php';
		$isuserallowedtoedit = has_capability ( 'mod/videoannotations:deleteannotation', $context ) ? 1 : 0;
		$isuserallowedtodelete = has_capability ( 'mod/videoannotations:editannotation', $context ) ? 1 : 0;
		foreach ( $annotations as $id => $annotation ) {
			$annotations [$id]->isuserallowedtoedit = $isuserallowedtoedit;
			$annotations [$id]->isuserallowedtodelete = $isuserallowedtodelete;
			
			$annotations [$id]->comments = self::get_comments ( array (
					'annotationid' => $id 
			) );
			$annotations [$id]->likes = self::get_likes($id, 'annotation', $id);
			$annotations [$id]->likedbyuser = self::hasUserLikedIt($USER->id, $annotations [$id]->likes);
		}
		
		// Hack to convert stdClass Object to array
		return json_decode ( json_encode ( $annotations ), True );
	}
	
	/**
	 * Returns description of method result value
	 *
	 * @return external_description
	 */
	public static function get_annotations_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'id' => new external_value ( PARAM_INT, 'annotation id' ),
				'annotationinstance' => new external_value ( PARAM_INT, 'id of annotation instance' ),
				'timeposition' => new external_value ( PARAM_INT, 'time position in the video' ),
				'duration' => new external_value ( PARAM_INT, 'the duration of this annotation' ),
				'subject' => new external_value ( PARAM_TEXT, 'the subject/topic of this annotation' ),
				'text' => new external_value ( PARAM_RAW, 'the text' ),
				'isquestion' => new external_value ( PARAM_BOOL, 'is this annotation a question?' ),
				'isanswered' => new external_value ( PARAM_BOOL, 'if a question, is it answered?' ),
				'isuserallowedtoedit' => new external_value ( PARAM_BOOL, 'is the user allowed to edit this annotation?' ),
				'isuserallowedtodelete' => new external_value ( PARAM_BOOL, 'is the user allowed to delete this annotation?' ),
				'group' => new external_value ( PARAM_INT, 'the group id if written in seperated groups' ),
				'author' => new external_value ( PARAM_INT, 'the authors user id' ),
				'author_firstname' => new external_value ( PARAM_TEXT, 'the authors firstname' ),
				'author_lastname' => new external_value ( PARAM_TEXT, 'the authors lastname' ),
				'timecreated' => new external_value ( PARAM_INT, 'the unix creation timestamp' ),
				'timemodified' => new external_value ( PARAM_INT, 'the unix last change timestamp' ),
				'comments' => self::get_comments_returns (),
				'likes' => self::get_likes_returns(),
				'likedbyuser' => new external_value ( PARAM_BOOL, 'has the user liked this annotation?' ),
		) ) );
	}
	
	public static function get_likes_parameters() {
		// FUNCTIONNAME_parameters() always return an external_function_parameters().
		// The external_function_parameters constructor expects an array of external_description.
		return new external_function_parameters ( 
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				array (
						'annotationinstance' => new external_value ( PARAM_TEXT, 'the activity instance' ),
						'referencetotype' => new external_value ( PARAM_TEXT, 'the type (annotation/comment)' ),
						'foreignkey' => new external_value ( PARAM_INT, 'the annotation/comment that is liked' ) 
				) );
	}
	public static function get_likes_returns() {
		return new external_multiple_structure ( new external_single_structure ( array (
				'id' => new external_value ( PARAM_INT, 'like id' ),
				'referencetotype' => new external_value ( PARAM_TEXT, 'the type (annotation/comment)' ),
				'foreignkey' => new external_value ( PARAM_INT, 'the annotation/comment that is liked' ),
				'author' => new external_value ( PARAM_INT, 'the authors user id' ),
				'author_firstname' => new external_value ( PARAM_TEXT, 'the authors firstname' ),
				'author_lastname' => new external_value ( PARAM_TEXT, 'the authors lastname' ),
				'timecreated' => new external_value ( PARAM_INT, 'the unix creation timestamp' ),
				'isaward' => new external_value ( PARAM_BOOL, 'is this like an award?' ) 
		) ) );
	}
	
	/**
	 * Returns the likes of an annotation/comment
	 *
	 * @param String $type
	 *        	annotation or comment
	 * @param int $fk
	 *        	the id of the annotation/comment
	 */
	public static function get_likes($annotationinstance, $referencetotype, $foreignkey) {
		// Parameters validation
		$params = self::validate_parameters ( self::get_likes_parameters (), array (
				'annotationinstance' => $annotationinstance,
				'referencetotype' => $referencetotype,
				'foreignkey' => $foreignkey
		) );
		
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		global $DB;
		$sql = "SELECT l.id, l.referencetotype, l.foreignkey, l.author, u.firstname as author_firstname, u.lastname as author_lastname, l.timecreated, l.isaward FROM {videoannotations_likes} l, {user} u WHERE referencetotype = '" . $params['referencetotype'] . "' AND foreignkey = " . $params['foreignkey'] . " AND l.author = u.id";
		return $DB->get_records_sql ( $sql );
	}
	
	public static function like_parameters() {
		// FUNCTIONNAME_parameters() always return an external_function_parameters().
		// The external_function_parameters constructor expects an array of external_description.
		return new external_function_parameters (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				array (
						'annotationinstance' => new external_value ( PARAM_TEXT, 'the activity instance' ),
						'referencetotype' => new external_value ( PARAM_TEXT, 'the type (annotation/comment)' ),
						'foreignkey' => new external_value ( PARAM_INT, 'the annotation/comment that is liked' )
				) );
	}
	public static function like_returns() {
		return new external_single_structure ( array('id' => new external_value ( PARAM_INT, 'like id' ))) ;
	}
	
	public static function like($annotationinstance, $referencetotype, $foreignkey) {
		// Parameters validation
		$params = self::validate_parameters ( self::get_likes_parameters (), array (
				'annotationinstance' => $annotationinstance,
				'referencetotype' => $referencetotype,
				'foreignkey' => $foreignkey
		) );
	
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );
		
		if($referencetotype == 'annotation') {
			require_capability('mod/videoannotations:createannotation', $context);
		} else {
			require_capability('mod/videoannotations:createcomment', $context);
		}
		
	
		global $DB, $USER;
		
		// Only one like per person and annotation/comment
		$cond = array(
				'referencetotype' => $params['referencetotype'],
				'foreignkey' => $params['foreignkey'],
				'author' => $USER->id,
		);
		
		if($DB->record_exists('videoannotations_likes', $cond)) {
			throw new dml_exception ( 'alreadyliked', 'you already liked this annotation/comment');
		}
		
		$data = new stdClass();
		$data->referencetotype = $params['referencetotype'];
		$data->foreignkey = $params['foreignkey'];
		$data->author = $USER->id;
		$data->timecreated = time();
		$data->isaward = false;
		
		$sql = "SELECT l.id, l.referencetotype, l.foreignkey, l.author, u.firstname as author_firstname, u.lastname as author_lastname, l.timecreated, l.isaward FROM {videoannotations_likes} l, {user} u WHERE referencetotype = '" . $params['referencetotype'] . "' AND foreignkey = " . $params['foreignkey'] . " AND l.author = u.id";
		return array('id' =>$DB->insert_record('videoannotations_likes', $data));
	}
	
	
	
	
	public static function unlike_parameters() {
		// FUNCTIONNAME_parameters() always return an external_function_parameters().
		// The external_function_parameters constructor expects an array of external_description.
		return new external_function_parameters (
				// a external_description can be: external_value, external_single_structure or external_multiple structure
				array (
						'annotationinstance' => new external_value ( PARAM_TEXT, 'the activity instance' ),
						'referencetotype' => new external_value ( PARAM_TEXT, 'the type (annotation/comment)' ),
						'foreignkey' => new external_value ( PARAM_INT, 'the annotation/comment that is liked' )
				) );
	}
	public static function unlike_returns() {
		return new external_single_structure ( array () );
	}
	
	public static function unlike($annotationinstance, $referencetotype, $foreignkey) {
		
		// Parameters validation
		$params = self::validate_parameters ( self::unlike_parameters (), array (
				'annotationinstance' => $annotationinstance,
				'referencetotype' => $referencetotype,
				'foreignkey' => $foreignkey
		) );
		//echo "<pre>".print_r($params, true)."</pre>";
		// Context validation
		$cmid = self::get_cmid_by_instance ( $params ['annotationinstance'] );
		$context = context_module::instance ( $cmid );
		self::validate_context ( $context );

		
		if($referencetotype == 'annotation') {
			require_capability('mod/videoannotations:createannotation', $context);
		} else {
			require_capability('mod/videoannotations:createcomment', $context);
		}
	
	
		global $DB, $USER;
	
		// Only one like per person and annotation/comment
		$cond = array(
				'referencetotype' => $params['referencetotype'],
				'foreignkey' => $params['foreignkey'],
				'author' => $USER->id,
		);
		if($DB->record_exists('videoannotations_likes', $cond)) {
			$DB->delete_records('videoannotations_likes', $cond);
			return array();
		} else {
			throw new dml_exception ( 'neverlikedit', 'you never liked it! :-(');
		}
		return array();
	}
	
	
	
	
	
	/**
	 *
	 * @param type $id
	 *        	The instance id
	 */
	public static function get_cmid_by_instance($id) {
		global $DB;
		$sql = "SELECT cm.id
                FROM {course_modules} cm, {modules} m 
                WHERE
                        cm.instance=1 AND
                        cm.module = m.id AND
                        m.name = 'videoannotations'";
		$result = $DB->get_records_sql ( $sql );
		$slice = array_slice ( $result, 0, 1 );
		$obj = array_shift ( $slice );
		return $obj->id;
	}
	
	private static function hasUserLikedIt($userid, $likes) {
		foreach ($likes as $like) {
			if($like->author == $userid) {
				return 1;
			}
		}
		return 0;
	}
}
