<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ . '/plugin.php';

class videoannotations_openlearnware extends videoannotations_plugin {

    private static function urlToVideoUrls($url) {
        $details = self::getVideoDetails($url);
        
        // extract uuid
        $uuid = str_replace('-', '', $details->uuid);
        $uuidForUrl = implode('/', str_split($uuid, 2));
        
        // add url to every possible material
        $resourceUrlBase = 'https://olw-material.hrz.tu-darmstadt.de/olw-konv-repository/material/'. $uuidForUrl . '/';
        $filenames = [
            '3.mp4',
            '1.mp4',
            '2.mp4',
            '7.mp3',
            '4.mp4',
            '13.pdf',
            '9.mp4',
            '8.ogg',
            '30.zip',
            '90.mp4',
            '105.webm',
            '106.webm',
            '206.webm',
            '206.webm'
        ];
        
        $urls = array();
        
        foreach ($filenames as $filename) {
            $obj = new stdClass();
            $obj->url = $resourceUrlBase . $filename;
            $urls[] = $obj;
        }
        
        return $urls;
    }
    
    private static function getVideoDetails($url) {
        $explosion = explode('-', $url);
        $resourceId = array_pop($explosion);
        
        $apiUrl = "https://openlearnware.tu-darmstadt.de/olw-rest-db/api/resource-detailview/index/" . $resourceId;
        
        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $details = json_decode(curl_exec($curl));
        curl_close($curl);
        return $details;
    }

    public static function isProperPlugin($url) {
        return (strpos($url, "openlearnware.tu-darmstadt.de") !== false) ? true : false;
    }

    public function getDetails() {
        return $this->getVideoDetails($this->url);
    }

    public function getVideoUrls() {
        return self::urlToVideoUrls($this->url);
    }

    public static function getVideoUrlsFor($url) {
        return self::urlToVideoUrls($url);
    }

    public static function getDetailsFor($url) {
        return self::getVideoDetails($url);
    }

    public static function getVideoUrl($url) {
        
    }

}
