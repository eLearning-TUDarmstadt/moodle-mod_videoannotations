<?php

/**
 *
 * @package    mod_videoannotations
 * @copyright  2016 Steffen Pegenau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once __DIR__ . '/iplugin.php';

abstract class videoannotations_plugin implements i_videoannotations_plugin {

    private $version;

    public static function getVersion() {
        return self::VERSION;
    }

    public static function isUpToDate($version) {
        $str = forward_static_call('getVersion');
        return intval($str) === intval($version);
    }

    private static function getPhpFiles() {
        $result = array();
        // All php files
        $dir = opendir(__DIR__);
        
        while (false !== ($filename = readdir($dir))) {
            if ($filename != "." && $filename != ".." && $filename != "plugin.php" && $filename != "iplugin.php") {
                $info = new SplFileInfo($filename);
                if ($info->getExtension() == "php") {
                    $result[] = $filename;
                }
            }
        }
        return $result;
    }

    public static function checkPlugins($url) {
        $filenames = self::getPhpFiles();//forward_static_call('getPhpFiles');

        foreach ($filenames as $filename) {
            require_once __DIR__ . '/' . $filename;
        }

        foreach (get_declared_classes() as $class) {
            if (is_subclass_of($class, 'videoannotations_plugin')) {
                $isProperPlugin = call_user_func(array($class, 'isProperPlugin'), $url);
                if ($isProperPlugin) {
                    return call_user_func(array($class, 'getVideoUrl'), $url);
                }
            }
        }

        throw new Exception('No proper plugin found for the url ' . $url);
    }

    abstract public static function isProperPlugin($url);

    abstract public static function getVideoUrl($url);
}
