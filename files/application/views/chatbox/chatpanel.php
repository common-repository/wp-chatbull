<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<link href="<?php echo base_url('assets/cmodule-chat/css/chatbox.css');?>" rel="stylesheet">
<script type="text/javascript">
    var cmodule_site_url = '<?php echo site_url('');?>';
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
</head>
<body ng-app="cmodule">
    <div ng-controller="BodyController" ng-cloak>    
        <?php include VIEWPATH . 'chatbox/minibox.php'; ?>
    </div>
    <script src="<?php echo base_url('assets/cmodule-chat/js/iframeResizer.contentWindow.min.js'); ?>"></script>
</body>
</html>