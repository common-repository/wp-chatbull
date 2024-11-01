<?php

// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

delete_option('gaxon_wpcbcp_chatbox_method');
delete_option('gaxon_wpcbcp_widget_code');
delete_option('gaxon_wpcbcp_linked');
delete_option('gaxon_wpcbcp_chatbull_dir');
delete_option('gaxon_wpcbcp_enabled_chatbox');
delete_option('gaxon_wpcbcp_pick_wpuser');
delete_option('gaxon_wpcbcp_visiblity_tpe');
delete_option('gaxon_wpcbcp_cbwindow_pages');