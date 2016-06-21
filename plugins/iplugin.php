<?php

/**
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

interface i_videoannotations_plugin {
    public static function getVersion();
    
    public static function isUpToDate($version);
    
    public static function isProperPlugin($url);
    
    public function getVideoUrls();
    
    public static function getVideoUrlsFor($url);
    
    public function getDetails();
    
    public static function getDetailsFor($url);
}