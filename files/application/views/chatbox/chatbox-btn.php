<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link href="<?php echo base_url('assets/cmodule-chat/css/chatbox.css');?>" rel="stylesheet">
<script type="text/javascript">
    var cmodule_site_url = '<?php echo site_url();?>/';
    var cmodule_base_url = '<?php echo base_url();?>';
    var access_token = '<?php echo $access_token;?>';
    
    var windowName = window.name;
    var strArray = windowName.split('[!]');
    var ptitle = strArray[0];
    var purl = strArray[1];
    var siteuser = '<?php echo json_encode($siteuser);?>';
    var cbwindow = <?php echo json_encode($cbwindow);?>;
</script>
<script type="text/javascript" src="<?php echo base_url('assets/cmodule-chat/js/angularjs/jquery-1.8.0.min.js');?>"></script>
<script src="<?php echo base_url('assets/scrollbar-plugin/js/jquery.mCustomScrollbar.concat.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/cmodule-chat/js/angularjs/angular.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/cmodule-chat/js/angularjs/angular-sanitize.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/cmodule-chat/js/angularjs/angular-animate.min.js'); ?>"></script>

<script src="<?php echo base_url("assets/angular-bootstrap/ui-bootstrap-tpls.min.js"); ?>"></script>
<script src="<?php echo base_url('assets/angular-rangeslider-directive-master/angular-range-slider.min.js'); ?>"></script>
<script src="<?php echo base_url("assets/angular-smilies/dist/angular-smilies.js"); ?>"></script>
<script src="<?php echo base_url('assets/cmodule-chat/js/app.js'); ?>"></script>
<style>
    .chat-cmodule .cmodule-chat-icon, .chat-cmodule-widget-head {
        color: <?php echo $this->settings->title_color;?> !important;
    }
    .chat-cmodule .cmodule-chat-btn, .chat-cmodule-widget-head{
        background-color: <?php echo $this->settings->background_color;?> !important;
    }
</style>
</head>
<body ng-app="cmodule">
    <div ng-controller="BodyController" class="chat-cmodule visitor-widget-box">
        <div ng-show="form_title && settings.chat_status == 'enable' && settings.plugin_validated == 'yes'" class="chat-cmodule" id="chat-cmodule-mainContainer" ng-init="show_chatbox()">
            <div class="chat-cmodule-section" ng-class="{'chat-cmodule-minimized chat-cmodule-closed':visible_widget == 'start'}">
                <?php if($this->settings->visitor_widget_type == 'chatbar'):?>
                    <div ng-show="visible_widget == 'start'" id="chat-cmodule-widget-bar" class="chat-cmodule-widget-head cmodule-clearfix animate-show widget-bar">
                        <div onclick="if ('parentIFrame' in window) window.parentIFrame.close()" class="cmodule-window-widget-title">{{form_title}}</div>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('assets/cmodule-chat/js/iframeResizer.contentWindow.min.js'); ?>"></script>
</body>
</html>