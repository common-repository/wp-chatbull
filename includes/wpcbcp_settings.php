<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

class Gaxon_Wpcbcp_Settings {

    var $errors = array();
    var $response = array('result' => 'failed', 'errors' => '', 'message' => '');
    var $headers = array();

    /*
     * construct function will call very fast when class load
     */

    public function __construct() {
        if (is_admin()) {
            add_action('wp_ajax_wpcbcp_install_plugin', array($this, 'install_plugin'));
            add_action('wp_ajax_wpcbcp_check_n_save', array($this, 'check_n_save'));
            add_action('wp_ajax_wpcbcp_chack_status', array($this, 'chack_status'));
            add_action('wp_ajax_wpcbcp_save_settings', array($this, 'save_settings'));
        }
    }

    /*
     * defining default settings.
     * 
     * @return $settings;
     */

    private function __default_options() {
        $settings = array(
            'gaxon_wpcbcp_chatbox_method' => 'install-plugin',
            'gaxon_wpcbcp_widget_code' => '',
            'gaxon_wpcbcp_linked' => 'no',
            'gaxon_wpcbcp_chatbull_dir' => '',
            'gaxon_wpcbcp_enabled_chatbox' => 'yes',
            'gaxon_wpcbcp_pick_wpuser' => 'yes',
            'gaxon_wpcbcp_visiblity_tpe' => 'all',
            'gaxon_wpcbcp_cbwindow_pages' => array(),
        );

        apply_filters('gaxon_wpcbcp_default_options', $settings);

        return $settings;
    }

    /*
     * To install and set default settings.
     */

    function set_default() {
        $settings = $this->__default_options();

        foreach ($settings as $option_name => $dval) {
            add_option($option_name, $dval);
        }
    }

    /*
     * Get settings
     * 
     * @return $data;
     */

    function get() {
        $data = array();

        $settings = $this->__default_options();

        foreach ($settings as $option_name => $dval) {
            if ($option_name == 'gaxon_wpcbcp_widget_code') {
                $data[$option_name] = stripslashes(get_option($option_name));
            } else {
                $data[$option_name] = get_option($option_name);
            }
        }

        return $data;
    }

    /*
     * To save settings in database
     */

    function save_settings() {
        $default_options = $this->__default_options();
        $data = $_POST['settings'];

        if (wp_verify_nonce($data['settings_nonce'], 'settings_nonce_save')) {
            foreach ($default_options as $option_name => $dval) {
                if (isset($data[$option_name]) and $option_name != 'gaxon_wpcbcp_chatbull_dir') {
                    if ($option_name == 'gaxon_wpcbcp_widget_code') {
                        update_option($option_name, stripslashes($data[$option_name]));
                    } elseif ($option_name == 'gaxon_wpcbcp_cbwindow_pages') {
                        update_option($option_name, $data[$option_name]);
                    } else {
                        update_option($option_name, sanitize_text_field($data[$option_name]));
                    }
                }
            }

            $this->response['result'] = 'success';
            $this->response['message'] = __('Settings saved Successfully.', $this->plugin_domain);
            $this->response['settings'] = $this->get();

            $this->sendResponse();
        }
    }

    /*
     * To prepare install chatbull plugin
     */

    function install_plugin() {
        $data = $_POST['settings'];
        $gaxon_wpcbcp_chatbull_dir = $data['gaxon_wpcbcp_chatbull_dir'];
        $plugin_files = GAXON_WPCBCP_FILES;
        $plugin_path = ABSPATH . $gaxon_wpcbcp_chatbull_dir;


        if (wp_verify_nonce($data['settings_nonce'], 'settings_nonce_save')) {
            if (empty($gaxon_wpcbcp_chatbull_dir)) {
                $this->errors['directory_name_missing'] = __('Chatbull Directory field is required.', $this->plugin_domain);
            } elseif (is_dir($plugin_path)) {
                $this->errors['dir-exists'] = __('A directory with name "' . $gaxon_wpcbcp_chatbull_dir . '" already exists. Please remove it or change name.', $this->plugin_domain);
            } else {
                // Connecting to the filesystem.
                WP_Filesystem();

                global $wp_filesystem;

                // creating directory        
                $created = $wp_filesystem->mkdir($plugin_path);
                if ($created) {
                    // Now copy all the files in the source directory to the target directory.
                    $copied = copy_dir($plugin_files, $plugin_path);
                    if (is_wp_error($copied)) {
                        $this->errors['gaxon_wpcbcp_chatbull_dir'] = $copied->get_error_message();
                    } else {
                        $this->response['result'] = 'success';
                        $this->response['message'] = __('Chatbull plugin files copied and redirecting you...', $this->plugin_domain);
                        $this->response['plugin_url'] = site_url($gaxon_wpcbcp_chatbull_dir);
                        $this->response['settings_nonce'] = wp_create_nonce('settings_nonce_save');

                        update_option('gaxon_wpcbcp_linked', 'yes');
                        update_option('gaxon_wpcbcp_chatbox_method', 'install-plugin');
                        update_option('gaxon_wpcbcp_chatbull_dir', $gaxon_wpcbcp_chatbull_dir);
                    }
                } else {
                    $this->errors['directory_not_created'] = __('Unable to copy Chatbull files.', $this->plugin_domain);
                }
            }

            $this->sendResponse();
        }
    }

    /*
     * Check plugin path and save if valid.
     */

    function check_n_save() {
        $data = $_POST['settings'];
        $gaxon_wpcbcp_chatbull_dir = $data['gaxon_wpcbcp_chatbull_dir'];
        $plugin_path = ABSPATH . $gaxon_wpcbcp_chatbull_dir;


        if (wp_verify_nonce($data['settings_nonce'], 'settings_nonce_save')) {
            if (empty($gaxon_wpcbcp_chatbull_dir)) {
                $this->errors['directory_name_missing'] = __('Chatbull Directory field is required.', $this->plugin_domain);
            } elseif (is_dir($plugin_path)) {
                $request_url = site_url($gaxon_wpcbcp_chatbull_dir . '/index.php?d=visitors&c=chat&m=get_server_time');
                $response = wp_remote_get($request_url);
                if (is_array($response)) {
                    $headers_response = $response['headers']; // array of http header lines
                    $body_response = json_decode($response['body']); // use the content

                    if ($body_response and is_object($body_response) and $body_response->result == 'success') {
                        $this->response['result'] = 'success';
                        $this->response['message'] = __('Chatbull plugin path is valid and saved information.', $this->plugin_domain);
                        $this->response['settings_nonce'] = wp_create_nonce('settings_nonce_save');

                        update_option('gaxon_wpcbcp_linked', 'yes');
                        update_option('gaxon_wpcbcp_chatbox_method', 'install-plugin');
                        update_option('gaxon_wpcbcp_chatbull_dir', $gaxon_wpcbcp_chatbull_dir);
                    } else {
                        $this->errors['invalid-plugin'] = __('Invalid plugin directory or invalid response.', $this->plugin_domain);
                    }
                } else {
                    $this->errors['invalid-plugin'] = __('Invalid plugin directory.', $this->plugin_domain);
                }
            } else {
                $this->errors['dir-not-exists'] = __($plugin_path . ' directory is not exists.', $this->plugin_domain);
            }

            $this->sendResponse();
        }
    }

    /*
     * Check plugin path and save if valid.
     */

    function chack_status() {
        $data = $_POST['settings'];
        $gaxon_wpcbcp_chatbull_dir = $data['gaxon_wpcbcp_chatbull_dir'];
        $plugin_path = ABSPATH . $gaxon_wpcbcp_chatbull_dir;


        if (wp_verify_nonce($data['settings_nonce'], 'settings_nonce_save')) {
            if (is_dir($plugin_path)) {
                if (is_dir($plugin_path . '/application') and is_dir($plugin_path . '/application/controllers')) {
                    $request_url = site_url($gaxon_wpcbcp_chatbull_dir . '/index.php?d=visitors&c=chatbox&m=is_installed&wp_url=' . site_url());
                    $response = wp_remote_get($request_url);

                    if (is_array($response)) {
                        $headers_response = $response['headers']; // array of http header lines
                        $body_response = json_decode($response['body']); // use the content

                        if ($body_response and is_object($body_response)) {
                            if ($body_response->result == 'success') {
                                $this->response['result'] = 'success';
                                $this->response['message'] = __($body_response->message, $this->plugin_domain);
                                $this->response['settings_nonce'] = wp_create_nonce('settings_nonce_save');
                            } else {
                                $error_message = '';
                                if (is_array($body_response->errors) or is_object($body_response->errors)) {
                                    foreach ($body_response->errors as $err) {
                                        $error_message .= "<p>" . $err . "</p>";
                                    }
                                } else {
                                    $error_message = $body_response->errors;
                                }
                                $this->errors['invalid-plugin'] = __($error_message, $this->plugin_domain);
                            }
                        } else {
                            $this->errors['invalid-plugin'] = __('Invalid plugin directory.', $this->plugin_domain);
                        }
                    } else {
                        $this->errors['invalid-plugin'] = __('Invalid plugin directory.', $this->plugin_domain);
                    }
                } else {
                    $this->errors['dir-not-exists'] = __($plugin_path . ' directory is empty or  not a ChatBull plugin.', $this->plugin_domain);
                }
            } else {
                $this->errors['dir-not-exists'] = __($plugin_path . ' directory is not exists.', $this->plugin_domain);
            }

            $this->sendResponse();
        }
    }

    /*
     * Get domain name from url
     * 
     * @param String $url
     * 
     * @return boolean or String domain name
     */

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

    /*
     * This function extract zip files
     * 
     * @param (path string) $fileurl
     * @param (path string) $extractPath
     */

    function extractZip($fileurl, $extractPath) {
        if (class_exists('ZipArchive')) {
            if (file_exists($fileurl)) {
                /* Open the Zip file */
                $zip = new ZipArchive;
                if ($zip->open($fileurl) != "true") {
                    $this->errors['unanle-to-open'] = "Error :- Unable to open the Zip File";
                } else {
                    /* Extract Zip File */
                    $zip->extractTo($extractPath);
                    $zip->close();

                    return TRUE;
                }
            } else {
                $this->errors['file-not-found'] = "File not found.";
            }
        } else {
            $this->errors['class-missing'] = 'Class \'ZipArchive\' not found. PHP needs to have the <a href="http://www.php.net/manual/en/zip.installation.php">zip extension</a> installed';
        }

        return FALSE;
    }

    /*
     * This function return response
     * 
     * @return (json) $this->response
     */

    function sendResponse() {
        header('Content-type: application/json');
        if (count($this->errors) > 0) {
            $errorString = '';
            $this->response['result'] = 'failed';

            $this->response['errors'] = $this->errors;
        }

        wp_die(wp_json_encode($this->response));
    }

}
