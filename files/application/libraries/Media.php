<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Media {

    var $file = '';

    /*
     * construct function load image and image upload library
     */

    function __construct() {
        $this->ci = & get_instance();
        $this->ci->load->library('upload');
        $this->ci->load->library('image_lib');
    }

    /*
     * This function upload file on server
     * 
     * @param $file (name of file element)
     * @param $file_config (upload path, types and file_name etc.)
     * @param $thumbnail_config ( thumbnail settings array)
     * 
     * @return $output (array of file upload class output)
     */

    function upload_media($file, $file_config = array(), $thumbnail_config = array()) {
        $output = array();
        $this->ci->upload->initialize($file_config);

        if (!$this->ci->upload->do_upload($file)) {
            $output['error'] = $this->ci->upload->display_errors();
        } else {
            $output = $this->ci->upload->data();

            if (isset($thumbnail_config['create_thumb']) and $thumbnail_config['create_thumb']) {
                $thumbnail_config['source_image'] = $output['full_path'];
                $thumbnail_config['new_image'] = $output['file_path'] . '/thumb/' . $output['file_name'];
                if (!isset($thumbnail_config['thumb_marker']))
                    $thumbnail_config['thumb_marker'] = false;
                $this->create_thumb($thumbnail_config);
            }
        }

        return $output;
    }

    /*
     * This function create a thumb version of an image.
     * 
     * @param $config
     */

    function create_thumb($config = array()) {
        $this->ci->image_lib->initialize($config);
        $this->ci->image_lib->resize();
        $this->ci->image_lib->clear();
    }

    /*
     * This function return thumbnail user of uploaded image
     * 
     * @param $filename
     * @param $filepath
     * @return image thumbnail url
     */

    function get_thumbnail($filename, $filepath, $email = '', $d = '') {
        if($filename) return base_url(UPLOAD_DIR . $filepath . 'thumb/' . $filename);
        
        /*if($email){
            $avatar_hash = md5( strtolower( trim( $email ) ) );
            $query_str = '';
            if($d) {
                $query_str = "?d=".$d;
            }
            return "http://www.gravatar.com/avatar/".$avatar_hash.$query_str;
        }*/
        
        return '';
    }
    
    /*
     * This function return thumbnail user of uploaded image
     * 
     * @param $filename
     * @param $filepath
     * @return image thumbnail url
     */

    function get_image($filename, $filepath, $email = '', $d = '') {
        if($filename) return base_url(UPLOAD_DIR . $filepath . $filename);
        
        /*if($email){
            $avatar_hash = md5( strtolower( trim( $email ) ) );
            $query_str = '';
            if($d) {
                $query_str = "?d=".$d;
            }
            return "http://www.gravatar.com/avatar/".$avatar_hash.$query_str;
        }*/
        
        return '';
    }

    /*
     * This function deleted files form server permanentaly
     * 
     * @param $filename
     * @param $filepath
     */

    function delete_media($filename, $filepath) {
        if ($filename and file_exists(upload_dir($filepath.$filename))) {
            unlink(upload_dir($filepath.$filename));
            
            $thumbnail = $filepath . 'thumb/' .$filename;
            if (file_exists(upload_dir($thumbnail))) {
                unlink(upload_dir($thumbnail));
            }
        }
    }

}
