<?php

/*
  plugin name: WP Chatbull
  Plugin URI: http://chatbull.in/
  Description: Now chat with your website visitors with WP ChatBull. This is a perfect fit for Small Business for both who sell products and services. It hosts data & files completely on your own server. No need to connect with any third party or pay subscriptions. Install WP ChatBull today to find your potential customers and improve your conversion rate. You can upgrade to ChatBull Lite anytime to get full features. Or to ChatBull Pro to have Desktop (for both Widnows & Mac) and Android apps for operators.
  Author: G-axon
  Author URI: http://g-axon.com
  Version: 1.1
  License: GPL2

  WP Chatbull is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 2 of the License, or
  any later version.

  WP Chatbull is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with WP Chatbull. If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt.
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!class_exists('Wpcbcp_Chatbull')) {
    define('GAXON_WPCBCP_FILES', plugin_dir_path(__FILE__) . "files/");

    include_once plugin_dir_path(__FILE__) . "includes/wpcbcp_settings.php";

    class Wpcbcp_Chatbull {

        var $plugin_domain = "gaxon_wpcbcp";
        private $settings;

        /*
         * construct function will call very fast when class load
         */

        public function __construct() {
            $this->settings = new Gaxon_Wpcbcp_Settings();
            $this->settings->plugin_domain = $this->plugin_domain;

            register_activation_hook(__FILE__, array($this, 'wpcbcp_activate'));
            add_action('after_switch_theme', array($this, 'wpcbcp_rewrite_flush'));
            add_action('init', array($this, 'wpcbcp_init_plugin'));
            add_action('admin_menu', array($this, 'wpcbcp_admin_menu'), 4);
            add_action('wp_footer', array($this, 'wpcbcp_chatbox_widget'));

            /* filters */
            $plugin = plugin_basename(__FILE__);
            add_filter("network_admin_plugin_action_links_$plugin", array($this, 'wpcbcp_plugin_add_settings_link'));
            add_filter("plugin_action_links_$plugin", array($this, 'wpcbcp_plugin_add_settings_link'));
            add_filter('admin_footer_text', array($this, 'wpcbcp_footer'));

            /* register and including js files */
            add_action('admin_enqueue_scripts', array($this, 'wpcbcp_load_admin_scripts'));
        }

        /*
         * To add plugin settings link on plugin listing page.
         * 
         * @param {Array} $links
         * 
         * @return $links
         */

        function wpcbcp_plugin_add_settings_link($links) {
            $settings_link = '<a href="' . admin_url('admin.php?page=wpcbcp-chatbull') . '">' . __('Settings', $this->plugin_domain) . '</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        /*
         * To register and load admin js files
         */

        function wpcbcp_load_admin_scripts() {
            wp_enqueue_script('jquery');

            //Load angular
            wp_enqueue_script('angularjs');
            wp_enqueue_script('angular-sanitize');
            wp_enqueue_script('cbsettings', plugins_url('admin/js/wpcbcp-settings.js', __FILE__), array('jquery', 'angularjs'), '1.1', true);
            wp_enqueue_style('sbstyle');

            $gaxon_wpcbcp_linked = get_option('gaxon_wpcbcp_linked');
            $settings_data = $this->settings->get();
            $settings_data['settings_nonce'] = wp_create_nonce('settings_nonce_save');

            /* Prepare variables for JavaScript. */
            wp_localize_script('angularjs', 'cbscript', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'site_url' => site_url(),
                'settings' => $settings_data,
                'labels' => array(
                    'install' => __('Install', $this->plugin_domain),
                    'check_n_save' => __('Check & Save', $this->plugin_domain),
                    'chack_status' => __('Check Status', $this->plugin_domain),
                    'change' => __('Change', $this->plugin_domain)
                )
            ));
        }

        /*
         * Function attach wirh init action
         */

        function wpcbcp_init_plugin() {
            wp_register_script('angularjs', plugins_url('admin/js/angular.min.js', __FILE__), array('jquery'), '1.6.4', true);
            wp_register_script('angular-sanitize', plugins_url('admin/js/angular-sanitize.min.js', __FILE__), array('jquery'), '1.6.4', true);
            wp_register_style('sbstyle', plugins_url('admin/css/wpcbcp-style.css', __FILE__), array(), '1.1');
        }

        /*
         * To flush rewrite rules
         */

        function wpcbcp_rewrite_flush() {
            flush_rewrite_rules();
        }

        /*
         * WIll call when plugin activated.
         */

        function wpcbcp_activate() {
            // add a new option
            $gaxon_wpcbcp_linked = get_option('gaxon_wpcbcp_linked');
            if (!$gaxon_wpcbcp_linked) {
                $this->settings->set_default();
            }

            $this->wpcbcp_rewrite_flush();
        }

        /*
         * To add Plugin menu in admin section.
         */

        function wpcbcp_admin_menu() {
            add_menu_page('Chatbull', 'Chatbull', 'manage_options', 'wpcbcp-chatbull', array($this, 'wpcbcp_load_pages'), plugins_url('admin/images/chatbull-32x32.png', __FILE__));
        }

        /**
         * Adds the WP ChatBull footer contents to the relevant pages
         * @param  string $footer_text current footer text available to us
         * @return string footer contents with our branding in it
         */
        function wpcbcp_footer($footer_text) {
            if (isset($_GET['page']) && $_GET['page'] == 'wp-chatbull') {
                $chatbull_link = 'http://chatbull.in/';
                $chatbull_wp_link = 'https://wordpress.org/support/plugin/wp-chatbull/reviews/';
                $poweredby_link = 'http://g-axon.com/';

                $gaxon_wpcbcp_footer_text = sprintf(__('Thank you for using <a href="%1$s" target="_blank"> ChatBull </a>! Please rate us on <a href="%2$s" target="_blank">WordPress.org</a>', $this->plugin_domain), $chatbull_link, $chatbull_wp_link);

                return str_replace('</span>', '', $footer_text) . ' | ' . $gaxon_wpcbcp_footer_text . ' | ' . __('ChatBull is Powered by ') . ' <a target="_new" href="' . $poweredby_link . '">G-Axon</a></span>';
            } else {
                return $footer_text;
            }
        }

        /*
         * Loading page.
         */

        public function wpcbcp_load_pages() {
            include 'includes/welcome_page.php';
        }

        /*
         * To setup chatbox widget on fronted 
         */

        function wpcbcp_chatbox_widget() {
            $settings = $this->settings->get();

            if ($settings['gaxon_wpcbcp_enabled_chatbox'] == 'yes') {
                global $post;
                $show_chatbox = false;
                $post_slug = '';

                if($post and is_object($post) and $post->post_name) {
                    $post_slug = $post->post_name;
                    if ($settings['gaxon_wpcbcp_visiblity_tpe'] == 'show_on_selected' and in_array($post_slug, $settings['gaxon_wpcbcp_cbwindow_pages'])) {
                        $show_chatbox = true;
                    } elseif ($settings['gaxon_wpcbcp_visiblity_tpe'] == 'hide_on_selected' and ! in_array($post_slug, $settings['gaxon_wpcbcp_cbwindow_pages'])) {
                        $show_chatbox = true;
                    }
                }

                if ($settings['gaxon_wpcbcp_visiblity_tpe'] == 'all' or $show_chatbox) {

                    // if chatbox method is install-plugin
                    if ($settings['gaxon_wpcbcp_chatbox_method'] == 'install-plugin' and $settings['gaxon_wpcbcp_chatbull_dir'] != '' and $settings['gaxon_wpcbcp_linked'] == 'yes') {
                        $response = wp_remote_get(site_url($settings['gaxon_wpcbcp_chatbull_dir'] . '/index.php?d=visitors&c=chatbox&m=is_installed&wp_url=' . site_url() . '&token=yes'));
                        
                        if ($response and is_array($response)) {
                            $headers_response = $response['headers']; // array of http header lines
                            $body_response = json_decode($response['body']); // use the content

                            if ($body_response and is_object($body_response) and $body_response->result == 'success') {
                                $html_code = '<script type="text/javascript">';
                                if ($settings['gaxon_wpcbcp_pick_wpuser'] == 'yes' and is_user_logged_in()) {
                                    global $current_user;
                                    wp_get_current_user();
                                    $fullname = $current_user->display_name;

                                    if ($current_user->user_firstname) {
                                        $fullname = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                                    }

                                    $html_code .= "var cbuser = {name: '" . $fullname . "', email: '" . $current_user->user_email . "', message: ''};";
                                } else {
                                    $html_code .= "var cbuser = {name: '', email: '', message: ''};";
                                }

                                $html_code .= "var cburl = '" . site_url($settings['gaxon_wpcbcp_chatbull_dir'] . '/') . "', access_token = '" . $body_response->token->token . "';";
                                $html_code .= "document.write('<script type=\"text/javascript\" src=\"' + cburl + 'assets/cmodule-chat/js/chatbull-init.js\"></' + 'script>');";
                                $html_code .= '</script>';

                                echo $html_code;
                            }
                        } 
                        else {
                            $plugin_path = ABSPATH . $settings['gaxon_wpcbcp_chatbull_dir'];
                            if (is_dir($plugin_path) and is_dir($plugin_path . '/application') and is_dir($plugin_path . '/application/controllers')) {

                            } else if($settings['gaxon_wpcbcp_chatbull_dir'] or $settings['gaxon_wpcbcp_linked'] == 'yes'){
                                update_option('gaxon_wpcbcp_chatbull_dir', '');
                                update_option('gaxon_wpcbcp_linked', 'no');
                            }
                        }
                    } elseif ($settings['gaxon_wpcbcp_chatbox_method'] == 'widget-code' and $settings['gaxon_wpcbcp_widget_code']) {
                        // if chatbox method is widget-code

                        $html_code = $settings['gaxon_wpcbcp_widget_code'];
                        if ($settings['gaxon_wpcbcp_pick_wpuser'] == 'yes' and is_user_logged_in()) {
                            global $current_user;
                            wp_get_current_user();
                            $fullname = $current_user->display_name;

                            if ($current_user->user_firstname) {
                                $fullname = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                            }

                            $html_code = str_replace("name: ''", "name: '" . $fullname . "'", $html_code);
                            $html_code = str_replace("email: ''", "email: '" . $current_user->user_email . "'", $html_code);
                        }

                        echo $html_code;
                    }
                }
            }
        }

    }

    new Wpcbcp_Chatbull();
}