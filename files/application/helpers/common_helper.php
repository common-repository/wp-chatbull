<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * This is function will be call to get body classes
 * 
 * @return string of body classes
 */

function body_classes() {
    $CI = & get_instance();
    $body_classes = implode(",", $CI->body_classes);

    return $body_classes;
}

/*
 * This is function will call to change password md5 format
 * 
 * $param $password
 * @return $password (md5 format)
 */

function hashpassword($password) {
    return md5($password);
}

/*
 * This function check current class and methos to add active status.
 * 
 * @param $class_name (Name of controller class)
 * @param $method_name (Name of Class method)
 * 
 * @return empty or actve (status)
 */

function is_active($class_name, $method_name = '') {
    $CI = & get_instance();

    $status = '';
    if ($CI->router->class == $class_name) {
        if ($method_name) {
            if ($method_name == $CI->router->method) {
                $status = 'active';
            }
        } else {
            $status = 'active';
        }
    }

    return $status;
}

/*
 * This function converts string to uniqe hash value
 * 
 * @param $strval
 * @return $hashed_val
 */

function convert_to_hash($strval) {
    $value = unpack('H*', $strval);
    $hashed_val = base_convert($value[1], 16, 2);

    return $hashed_val;
}

/*
 * this function reverse hash value to normal value
 * 
 * @param $hashed_val
 * @return $strval
 */

function convert_to_string($hashed_val) {
    $strval = pack('H*', base_convert($hashed_val, 2, 16));

    return $strval;
}

/*
 * This function will be use to get user location by his ip address
 * 
 * @param $ip_address
 * @return object of geo location
 */

function getLocationByIp($ip_address) {
    $location_url = "http://www.geoplugin.net/json.gp?ip=" . $ip_address;

    $CI = & get_instance();
    $CI->curl->create($location_url);
    $response = $CI->curl->execute();
    $loaction = json_decode($response);

    if ($loaction and isset($loaction->geoplugin_status) and $loaction->geoplugin_status == 200) {
        return $loaction;
    }

    return FALSE;
}

/*
 * This function will return all http header and values
 */
if (!function_exists('getallheaderst')) {

    function getallheaderst() {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }

}

/*
 * This function extract zip files
 * 
 * @param (path string) $fileurl
 * @param (path string) $extractPath
 */
if (!function_exists('extractZip')) {

    function extractZip($fileurl, $extractPath) {
        $output = array('result' => 'failed', 'errors' => '', 'message' => '');
        if (class_exists('ZipArchive')) {
            if (file_exists($fileurl)) {
                /* Open the Zip file */
                $zip = new ZipArchive;
                if ($zip->open($fileurl) != "true") {
                    $output['errors'] = "Error :- Unable to open the Zip File";
                } else {
                    /* Extract Zip File */
                    $zip->extractTo($extractPath);
                    $zip->close();
                }

                $output['result'] = 'success';
                $output['message'] = "File extracted successfully.";
            } else {
                $output['errors'] = "File not found.";
            }
        } else {
            $output['errors'] = 'Class \'ZipArchive\' not found. PHP needs to have the <a href="http://www.php.net/manual/en/zip.installation.php">zip extension</a> installed';
        }

        return $output;
    }

}

/*
 * Getting MAC Address using PHP
 * Md. Nazmul Basher
 */
if (!function_exists('getMacAddress')) {

    function getMacAddress() {
        ob_start(); // Turn on output buffering
        system('ipconfig / all'); //Execute external program to display output
        $mycom = ob_get_contents(); // Capture the output into a variable
        ob_clean(); // Clean (erase) the output buffer

        $findme = "Physical";
        $pmac = strpos($mycom, $findme); // Find the position of Physical text
        $mac = substr($mycom, ($pmac + 36), 17); // Get Physical Address

        return $mycom;
    }

}

/*
 * To show plugin info.
 */
if (!function_exists('version_info')) {

    function version_info() {
        // change chatbull brand name.
        $pname = str_replace('chatbull', 'ChatBull', PRODUCT_NAME);

        // remove dash from name
        $pname = str_replace('-', ' ', $pname);

        echo '<p class="text-capitalize">Current Version ' . $pname . ' ' . CHATBULL_VERSION . '</p>';
    }

}

/*
 * To show powered by info.
 */
if (!function_exists('powered_by')) {

    function powered_by() {
        // change chatbull brand name.
        $pname = str_replace('chatbull', 'ChatBull', PRODUCT_NAME);

        // remove dash from name
        $pname = str_replace('-', ' ', $pname);
        
        echo '<p class="text-capitalize">Powered By <a href="http://g-axon.com/" target="_new">G-Axon</a> <span class="text-capitalize pull-right">Current Version <a href="'.CHATBULL_SITEURL.'" target="_new">' . $pname . ' ' . CHATBULL_VERSION . '</a></span></p>';
    }

}

/*
 * To show powered by info.
 */
if (!function_exists('plugin_name')) {

    function plugin_name() {
        // change chatbull brand name.
        $pname = str_replace('chatbull', 'ChatBull', PRODUCT_NAME);

        // remove dash from name
        $pname = str_replace('-', ' ', $pname);
        
        return $pname;
    }

}