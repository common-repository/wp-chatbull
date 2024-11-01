<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * This function will use to send notifications.
 * 
 * @param $user_id
 * @param $message
 * @param $badge
 * @param $sedor_device_id
 * 
 * @return true
 */

function push_notification($user_id, $message, $badge, $sedor_device_id = '') {
    return true;
}

/*
 * This function will be use to send template email
 * 
 * @param $template_file
 * @param $to
 * @param $data
 * 
 * @return true or false
 */

function send_template_email($template_file, $to, $subject, $data = array()) {
    if (empty($data)) {
        return false;
    }

    $CI = & get_instance();

    $settings = $CI->configuration->get_settings();
    if (empty($settings->site_logo)) {
        $settings->site_logo = base_url("assets/cmodule/images/logo.png");
    }

    $data['settings'] = $settings;

    $CI->load->library('email');
    $config = array('priority' => 1, 'mailtype' => 'html');
    $CI->email->initialize($config);
    $CI->email->from($settings->site_email, $settings->site_name);
    $CI->email->to($to);

    if (isset($data['cc']) and $data['cc']) {
        $CI->email->cc($data['cc']);
    }

    if (isset($data['bcc']) and $data['bcc']) {
        $CI->email->cc($data['bcc']);
    }

    $CI->email->subject($subject . ' - Chatbull');
    $message = $CI->load->view($CI->data['theme'] . '/emails/' . $template_file, $data, true);
    $CI->email->message($message);

    return $CI->email->send();
}
