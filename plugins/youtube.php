<?php

use videoannotations_plugin;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ . '/plugin.php';

class videoannotations_youtube extends videoannotations_plugin {

    public static function getVideoUrl($url) {
        $youtube = "http://www.youtube.com/oembed?url=" . $url . "&format=json";

        $curl = curl_init($youtube);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $return = curl_exec($curl);
        curl_close($curl);
        return json_decode($return, true);
    }

    public static function isProperPlugin($url) {
        return (strpos($url, "youtube.com/watch") !== false) ? true : false;
    }

}
