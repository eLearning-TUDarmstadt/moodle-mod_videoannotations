<?php

/**
 * videoannotations external file
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class mod_videoannotations_external extends external_api {

    //
    // Create comment
    //
    
    //
    // Get annotation
    //
    
    //
    // Create annotaion
    //
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function create_annotation_parameters() {
        // FUNCTIONNAME_parameters() always return an external_function_parameters(). 
        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(array(
                // a external_description can be: external_value, external_single_structure or external_multiple structure
                array('annotationinstance' => new external_value(PARAM_INT, 'The instance id of table {videoannotations}'), VALUE_REQUIRED), 
                array('timeposition' => new external_value(PARAM_INT, 'The position of the annotation in ms'), VALUE_REQUIRED), 
                array('duration' => new external_value(PARAM_INT, 'The duration of the annotation'), VALUE_REQUIRED), 
                array('subject' => new external_value(PARAM_TEXT, 'The subject of the annotation'), VALUE_REQUIRED), 
                array('text' => new external_value(PARAM_RAW, 'The text of the annotation'), VALUE_REQUIRED), 
                array('isquestion' => new external_value(PARAM_BOOL, 'Is this annotation a question?'), VALUE_REQUIRED), 
                array('isanswered' => new external_value(PARAM_BOOL, 'Is this question answered?'), VALUE_REQUIRED), 
                array('group' => new external_value(PARAM_INT, 'Group id', VALUE_OPTIONAL, -1))
            )
        );
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function create_annotation_returns() {
        return new external_single_structure(
                array(
            'id' => new external_value(PARAM_INT, 'The id of the newly created annotation')
                )
        );
    }

    /**
     * Creates an annotation
     * @return int The id of the newly created annotation
     */
    public static function create_annotation($array) {
        echo "<pre>".print_r($array)."</pre>";
        //Parameters validation
        $params = self::validate_parameters(self::create_annotation_parameters(), $array);

        // Context validation
        $cmid = self::get_cmid_by_instance($params['annotationinstance']);
        $context = context_module::instance($cmid);
        self::validate_context($context);
        // Capability validation
        require_capability('mod/videoannotations:createannotation', $context);

        $data = new stdClass();
        $data->annotationinstance = $params['annotationinstance'];
        $data->timeposition = $params['timeposition'];
        $data->duration = $params['duration'];
        $data->subject = $params['subject'];
        $data->text = $params['text'];
        $data->isquestion = $params['isquestion'];
        $data->isanswered = $params['isanswered'];
        $data->group = $params['group'];
        $data->timecreated = time();
        $data->timemodified = time();

        return $DB->insert_record('videoannotations_annotations', $data);
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_annotations_parameters() {
        // FUNCTIONNAME_parameters() always return an external_function_parameters(). 
        // The external_function_parameters constructor expects an array of external_description.
        return new external_function_parameters(
                // a external_description can be: external_value, external_single_structure or external_multiple structure
                array('id' => new external_value(PARAM_INT, 'The instance id of a videoannotations activity'))
        );
    }

    /**
     * The function itself
     * @return string welcome message
     */
    public static function get_annotations($instance) {
        //Parameters validation
        $params = self::validate_parameters(self::get_annotations_parameters(), array('id' => $instance));

        // Context validation
        $cmid = self::get_cmid_by_instance($params['id']);
        $context = context_module::instance($cmid);
        self::validate_context($context);

        global $DB;
        $annotations = $DB->get_records('videoannotations_annotations', array('annotationinstance' => $params->id));
        //echo "<pre>".print_r($annotations, true)."</pre>";


        $returnedvalue = 42;

        return $returnedvalue;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_annotations_returns() {
        return new external_value(PARAM_INT, 'human description of the returned value');
    }

    /**
     * 
     * 
     * @param type $id The instance id
     */
    public static function get_cmid_by_instance($id) {
        global $DB;
        $sql = "SELECT cm.id
                FROM {course_modules} cm, {modules} m 
                WHERE
                        cm.instance=1 AND
                        cm.module = m.id AND
                        m.name = 'videoannotations'";
        $result = $DB->get_records_sql($sql);
        $obj = array_shift(array_slice($result, 0, 1));
        return $obj->id;
    }

}
