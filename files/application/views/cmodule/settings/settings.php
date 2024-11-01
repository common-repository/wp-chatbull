<div ng-controller="SettingController" class="row">
    <div class="col-lg-12">
        <div class="header-panel clearfix">
            <h2><?php echo $this->lang->line('settings'); ?></h2>   
            <?php version_info();?>
        </div>

        <div class="alert alert-success" ng-show="notification.showMessage">
            <a href="#" class="close" aria-label="close" ng-click="notification.showMessage = false">&times;</a>
            <div ng-bind-html="'<strong>Success!</strong> ' + notification.message"></div>            
        </div>

        <div class="alert alert-danger" ng-show="notification.showErrors">
            <a href="#" class="close" aria-label="close" ng-click="notification.showErrors = false">&times;</a>
            <div ng-bind-html="notification.errors"></div>
        </div>
        <div class="row" ng-init="show_preview = true; show_form = true">
            <div class="" ng-class="{'col-md-7':show_preview,'col-md-12': show_preview == false}">
                <form name="settingForm" class="choose-theme" action="" method="post" ng-submit="settingForm.$valid">
                    <div class="theme-settings-container">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="active"><a ng-click="show_preview = true; show_form = true"  class="active"data-toggle="tab" role="tab" aria-controls="general-settings" href="#general-settings"><?php echo $this->lang->line('general'); ?></a></li>
                            <li><a ng-click="show_preview = true; show_form = true" data-toggle="tab" role="tab" aria-controls="content-info" href="#content-info"><?php echo $this->lang->line('content_and_info'); ?></a></li>
                            <li><a ng-click="show_preview = true; show_form = true" data-toggle="tab" role="tab" aria-controls="choose-theme" href="#choose-theme"><?php echo $this->lang->line('choose_a_theme'); ?></a></li>
                            <li><a ng-click="show_preview = false; show_form = true" data-toggle="tab" role="tab" aria-controls="site-settings" href="#site-settings"><?php echo $this->lang->line('site'); ?></a></li>
                            <li><a ng-click="show_preview = false; show_form = false" data-toggle="tab" role="tab" aria-controls="installation" href="#installation"><?php echo $this->lang->line('installation'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <div id="general-settings" class="active tab-pane">
                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('enable_disable_chat'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="chat_status" ng-model="settings.chat_status" value="enable"> <?php echo $this->lang->line('enable'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="chat_status" ng-model="settings.chat_status" value="disable"> <?php echo $this->lang->line('disable'); ?></label>
                                    </div>
                                </div>
                                
                                <div class="form-group form-divider">
                                    <h5>
                                        <?php echo $this->lang->line('time_interwal'); ?> <span class="small">( <?php echo $this->lang->line('in_seconds'); ?> )</span>
                                        <a href="#" ng-click="disable_click($event)" data-toggle="tooltip" data-placement="auto" title="<?php echo $this->lang->line('time_interwal_help'); ?>"><i class="fa fa-question-circle"></i></a>
                                    </h5>
                                    <input class="form-control" type="number" min="3" max="20" ng-min="3" ng-max="20" ng-model="settings.time_interwal" required />
                                </div>
                                
                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('chat_mode'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="chat_mode" ng-model="settings.chat_mode" value="online"> <?php echo $this->lang->line('online'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="chat_mode" ng-model="settings.chat_mode" value="offline"> <?php echo $this->lang->line('offline'); ?></label>
                                    </div>
                                </div>

                                <div ng-hide="settings.initiate_bypass_chat == 'yes'" class="form-group form-divider animate-slide">
                                    <h5><?php echo $this->lang->line('send_chat_transcript_to_visitor'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.send_chat_transcript_to_visitor" value="yes"> <?php echo $this->lang->line('yes'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.send_chat_transcript_to_visitor" value="no"> <?php echo $this->lang->line('no'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.send_chat_transcript_to_visitor" value="ask_to_visiter"> <?php echo $this->lang->line('ask_to_visiter'); ?></label>
                                    </div>
                                </div>
                                
                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('can_reply_attended_orequests'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.can_reply_attended_orequests" value="yes"> <?php echo $this->lang->line('yes'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.can_reply_attended_orequests" value="no"> <?php echo $this->lang->line('no'); ?></label>
                                    </div>
                                </div>

                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('show_depaertment_selection_box'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.show_depaertment_selection_box" value="yes"> <?php echo $this->lang->line('yes'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.show_depaertment_selection_box" value="no"> <?php echo $this->lang->line('no'); ?></label>
                                    </div>
                                </div>

                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('enable_feedback_form'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.enable_feedback_form" value="yes"> <?php echo $this->lang->line('yes'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="" ng-model="settings.enable_feedback_form" value="no"> <?php echo $this->lang->line('no'); ?></label>
                                    </div>
                                </div>
                            </div>
                            <div id="content-info" class="tab-pane">
                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('default_avatar'); ?></h5>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-sn btn-default btn-file">
                                                Browse&hellip; <input type="file" id="default_avatar_img" file-input="files">
                                            </span>
                                        </span>
                                        <input type="text" ng-model="filename" class="form-control" disabled="disabled">
                                        <span class="input-group-btn"><button class="btn btn-primary btn-sn btn-x2" ng-click="upload_avatar($event)"><?php echo $this->lang->line('upload'); ?></button></span>
                                    </div>
                                </div>
                                <hr />
                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('chat_start_title'); ?></h5>
                                    <input class="form-control" type="text" ng-model="settings.chat_start_title" required />
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('online_form_title'); ?></h5>
                                    <input class="form-control" type="text" ng-model="settings.online_form_title" required />
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('offline_form_title'); ?></h5>
                                    <input class="form-control" type="text" ng-model="settings.offline_form_title" required />
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_welcome_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.welcome_message" required><?php echo $this->lang->line('welcome_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_welcome_message'); ?></em></p>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_waiting_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.waiting_message" required><?php echo $this->lang->line('waiting_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_waiting_message'); ?></em></p>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_offline_heading_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.offline_heading_message" required><?php echo $this->lang->line('offline_heading_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_offline_heading_message'); ?></em></p>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_offline_submission_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.offline_submission_message" required><?php echo $this->lang->line('offline_submission_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_offline_submission_message'); ?></em></p>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_feedback_heading_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.feedback_heading_message" required><?php echo $this->lang->line('feedback_heading_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_feedback_heading_message'); ?></em></p>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('set_feedback_submission_message'); ?></h5>
                                    <textarea class="form-control" cols="40" rows="4" ng-model="settings.feedback_submission_message" required><?php echo $this->lang->line('feedback_submission_message'); ?></textarea>
                                    <p class="help-block"><em>Note: <?php echo $this->lang->line('set_feedback_submission_message'); ?></em></p>
                                </div>
                            </div>
                            <div id="choose-theme" class="tab-pane">
                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('background_color'); ?></h5>
                                    <input ng-change="update_theme()" class="form-control" colorpicker colorpicker-with-input="true" colorpicker-position="bottom" colorpicker-close-on-select type="text" ng-model="settings.background_color" ng-style="{'background-color':settings.background_color, 'color':settings.title_color}"  ng-readonly="settings.background_color" required />
                                </div>

                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('title_color'); ?></h5>
                                    <input ng-change="update_theme()" class="form-control" colorpicker colorpicker-with-input="true" colorpicker-position="bottom" colorpicker-close-on-select type="text" ng-model="settings.title_color" ng-style="{'background-color':settings.title_color}"  ng-readonly="settings.title_color" required />
                                </div>

                                <div class="form-group form-divider">
                                    <h5><?php echo $this->lang->line('set_position'); ?></h5>
                                    <div class="radio">
                                        <label class="radio-inline"><input type="radio" name="window_position" ng-model="settings.window_position" value="left"> <?php echo $this->lang->line('left'); ?></label>
                                        <label class="radio-inline"><input type="radio" name="window_position" ng-model="settings.window_position" value="right"> <?php echo $this->lang->line('right'); ?></label>
                                    </div>
                                </div>
                            </div>

                            <div id="site-settings" class="tab-pane">
                                <div class="form-group" ng-hide="settings.logo_empty">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5><?php echo $this->lang->line('site_logo'); ?></h5>                                            
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-sn btn-default btn-file">
                                                        Browse&hellip; <input type="file" id="site_logo_img" file-input="logofiles">
                                                    </span>
                                                </span>
                                                <input type="text" ng-model="logo_filename" class="form-control" disabled="disabled">
                                                <span class="input-group-btn"><button class="btn btn-primary btn-sn btn-x2" ng-click="upload_logo($event)"><?php echo $this->lang->line('upload'); ?></button></span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <img title="Chatbull Logo" alt="Chatbull Logo" ng-src="{{settings.site_logo}}" ng-if="settings.site_logo">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('site_name'); ?></h5>
                                    <input class="form-control" type="text" ng-model="settings.site_name" required />
                                </div>
                                <div class="form-group">
                                    <h5><?php echo $this->lang->line('site_email'); ?></h5>
                                    <input class="form-control" type="email" ng-model="settings.site_email" required />
                                </div>
                            </div>

                            <div id="installation" class="tab-pane">
                                <?php include 'access_tokens.php'; ?>
                            </div>
                        </div>
                    </div>

                    <div ng-show="show_form" class="form-group ">
                        <button class="btn btn-primary" ng-disabled="!settingForm.$valid" ng-click="update_settings($event)"><?php echo $this->lang->line('submit_save'); ?></button>
                    </div>
                </form>
            </div>

            <div class="col-md-5" ng-show="show_preview">
                <?php include theme_path('settings/preview.php'); ?>
            </div>
        </div>
    </div>
</div>