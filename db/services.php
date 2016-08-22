<?php
/**
 * videoannotations services file
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$services = array(
      'videoannotationsservice' => array(                                                //the name of the web service
          'functions' => array ('mod_videoannotations_create_annotation'), //web service functions of this service
          'requiredcapability' => '',                //if set, the web service user need this capability to access 
                                                                              //any function of this service. For example: 'some/capability:specified'                 
          'restrictedusers' =>0,                                             //if enabled, the Moodle administrator must link some user to this service
                                                                              //into the administration
          'enabled'=>1,                                                       //if enabled, the service can be reachable on a default installation
          'ajax' => true
          )
  );

$functions = array(
    'mod_videoannotations_create_annotation' => array(         //web service function name
        'classname'   => 'mod_videoannotations_external',  //class containing the external function
        'methodname'  => 'create_annotation',          //external function name
        'classpath'   => 'mod/videoannotations/externallib.php',  //file containing the class/external function
        'description' => 'Create a new annotation.',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
    'mod_videoannotations_create_comment' => array(         //web service function name
        'classname'   => 'mod_videoannotations_external',  //class containing the external function
        'methodname'  => 'create_comment',          //external function name
        'classpath'   => 'mod/videoannotations/externallib.php',  //file containing the class/external function
        'description' => 'Comment an annotation',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
    'mod_videoannotations_get_annotations' => array(         //web service function name
        'classname'   => 'mod_videoannotations_external',  //class containing the external function
        'methodname'  => 'get_annotations',          //external function name
        'classpath'   => 'mod/videoannotations/externallib.php',  //file containing the class/external function
        'description' => 'Get all annotations of an acitivity instance',    //human readable description of the web service function
        'type'        => 'read',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
    'mod_videoannotations_get_comments' => array(         //web service function name
        'classname'   => 'mod_videoannotations_external',  //class containing the external function
        'methodname'  => 'get_comments',          //external function name
        'classpath'   => 'mod/videoannotations/externallib.php',  //file containing the class/external function
        'description' => 'Get all comments of an annotation',    //human readable description of the web service function
        'type'        => 'read',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
    'mod_videoannotations_delete_annotation' => array(         //web service function name
        'classname'   => 'mod_videoannotations_external',  //class containing the external function
        'methodname'  => 'delete_annotation',          //external function name
        'classpath'   => 'mod/videoannotations/externallib.php',  //file containing the class/external function
        'description' => 'Delete a certain annotation from video',    //human readable description of the web service function
        'type'        => 'write',                  //database rights of the web service function (read, write)
        'ajax'        => true
        //'services' => array(MOODLE_OFFICIAL_MOBILE_SERVICE)    // Optional, only available for Moodle 3.1 onwards. List of built-in services (by shortname) where the function will be included.  Services created manually via the Moodle interface are not supported.
    ),
);