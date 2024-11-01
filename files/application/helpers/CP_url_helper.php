<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * This function will return theme raltive path
 */

function theme_path($file_path) {
    $CI = & get_instance();
    return VIEWPATH . $CI->data['theme'] . '/' . $file_path;
}

/*
 * This function will load file from themes
 */

function theme_url($file_path = '') {
    $CI = & get_instance();
    return base_url('assets/' . $CI->data['theme'] . '/' . $file_path);
}

/*
 * This function will return upload dir path
 */

function upload_dir($imgpath) {
    $CI = & get_instance();
    return FCPATH . UPLOAD_DIR . $imgpath;
}

/*
 * This function returns referer url
 */

function referer_url() {
    return $_SERVER['HTTP_REFERER'];
}

/*
 * Get domain name from url
 * 
 * @param String $url
 * 
 * @return boolean or String domain name
 */

if (!function_exists('get_domain')) {

    function get_domain($url) {
        $pieces = parse_url($url);
        $domain = isset($pieces['host']) ? $pieces['host'] : '';
        if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-\.]{1,63}\.[a-z\.]{2,20})$/i', $domain, $regs)) {
            return str_replace("www.", "", $regs['domain']);
        }

        $parse = parse_url($url);
        if (isset($parse['host']) and $parse['host']) {
            return str_replace("www.", "", $parse['host']);
        }
        return false;
    }

}

/*
 * to check is same domain
 * 
 * @param String $domain
 * @param String $res_domain
 * 
 * @return boolean
 */

if (!function_exists('is_same_domain')) {

    function is_same_domain($domain, $res_domain) {
        $domain_array = explode(".", $domain);

        if ($domain == $res_domain) {
            return TRUE;
        } elseif (count($domain_array) > 2) {
            if (strstr($res_domain, $domain_array[0]) or strstr($res_domain, $domain_array[1])) {
                return TRUE;
            }
        } elseif (count($domain_array) == 2 and strstr($res_domain, $domain_array[0])) {
            return TRUE;
        }

        return false;
    }

}