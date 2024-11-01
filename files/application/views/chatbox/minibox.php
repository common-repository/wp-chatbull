<div ng-show="form_title && settings.chat_status == 'enable' && settings.plugin_validated == 'yes'" class="chat-cmodule" id="chat-cmodule-mainContainer" ng-init="show_chatbox()">
    <div class="chat-cmodule-section" ng-class="{'chat-cmodule-minimized chat-cmodule-closed':visible_widget == 'start'}">
        <div ng-if="settings.visitor_widget_type == 'chatbar'" ng-show="visible_widget == 'start'" id="chat-cmodule-widget-bar" class="chat-cmodule-widget-head cmodule-clearfix animate-show widget-bar">
            <div ng-click="visible_form()" class="cmodule-window-widget-title">{{form_title}}</div>
        </div>

        <div ng-hide="visible_widget == 'start'" class="chat-cmodule-container">
            <div id="chat-cmodule-header" class="chat-cmodule-header cmodule-clearfix">
                <div class="cmodule-window-avatar" ng-init="rand_color = getColor()">
                    <img ng-show="settings.default_avatar" ng-src="{{settings.default_avatar}}" height="50" width="50" alt="Chatbull Avatar" title="Chatbull Avatar">
                    <span ng-hide="settings.default_avatar" style="background-color: {{rand_color}};" class="cmodule-user-avatar">{{form_title|oneCapLetter}}</span>
                </div>
                <div class="cmodule-window-title">{{form_title}}</div>
                <div class="cmodule-window-controls">
                    <a ng-hide="minimized == 'yes'" ng-click="minimize_chat($event)" title="Minimize window" id="cmodule-chat-minimize" class="chat-cmodule-minimize cmodule-window-control" href="javascript:void(0)"></a> 
                    <a ng-show="minimized == 'yes'" ng-click="maximize_chat($event)" title="Maximize window" id="cmodule-chat-maximize" class="chat-cmodule-maximize cmodule-window-control" href="javascript:void(0)"></a> 
                    <a ng-click="end_chat($event)" title="End Chat" id="cmodule-chat-close" class="chat-cmodule-close cmodule-window-control" href="javascript:void(0)"></a>
                </div>
            </div>

            <div class="cmodule-notification cmodule-error-message" ng-show="showError" ng-bind-html="errors" ng-class="{'in-cmodule': showError}"></div>
            <div class="cmodule-notification cmodule-success-message" ng-show="showMessage" ng-bind-html="success_message" ng-class="{'in-cmodule': showMessage}"></div>

            <div ng-show="visible_widget == 'initaiting-bypasschat-widget'" class="chat-cmodule-widget animate-show">
                <div class="chat-cmodule-view">
                    <div class="chat-cmodule-row">
                        <div class="chat-cmodule-widget-content">
                            <i class="cmodule-icon fa fa-circle-o-notch fa-spin fa-3x fa-fw" aria-hidden="true"></i> <span class="progress-text"><?php echo $this->lang->line('initiating_chat');?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div ng-show="visible_widget == 'offline-widget' && minimized != 'yes'" id="cmodule-offline-widget" class="chat-cmodule-widget animate-show">
                <form name="offlineForm" id="chatForm" action="" method="post" ng-submit="send_offline_request($event) && offlineForm.$valid">
                    <div class="chat-cmodule-view">
                        <div class="chat-cmodule-row">
                            <div class="chat-cmodule-widget-content">
                                <div class="cmodule-help-message">{{settings.offline_heading_message}}</div>
                                <div class="cmodule-form-group">
                                    <input class="cmodule-form-control" type="text" name="name" placeholder="Name" ng-class="{'cmodule-error': offlineForm.name.$dirty && offlineForm.name.$invalid}" ng-model="visitor.name" required>
                                </div>
                                <div class="cmodule-form-group">
                                    <input class="cmodule-form-control" type="email" name="email" placeholder="Email" ng-class="{'cmodule-error': offlineForm.email.$dirty && offlineForm.email.$invalid}" ng-model="visitor.email" required>
                                </div>
                                <div class="cmodule-form-group" ng-show="settings.show_depaertment_selection_box == 'yes'">
                                    <select class="cmodule-form-control" ng-model="visitor.requested_tag" ng-required="settings.show_depaertment_selection_box == 'yes'" name="department" ng-class="{'cmodule-error': offlineForm.department.$dirty && offlineForm.department.$invalid}">
                                        <option value="">Select Department</option>
                                        <option ng-repeat="option in tags" value="{{option.id}}">{{option.tag_name}}</option>
                                    </select>
                                </div>
                                <div class="cmodule-form-group cmodule-last-item">
                                    <textarea name="message" class="cmodule-form-control" cols="20" rows="4" placeholder="Your Question...." ng-class="{'cmodule-error': offlineForm.message.$dirty && offlineForm.message.$invalid}" ng-model="visitor.message" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>		
                    <div class="chat-cmodule-footer">
                        <button id="cmodule-offline-submit" class="chatnox-btn-default" type="submit">Send Now</button>
                    </div>
                </form>
            </div>
            
            <div ng-show="visible_widget == 'online-widget' && minimized != 'yes'" id="cmodule-online-widget" class="chat-cmodule-widget animate-show">
                <form name="onlineForm" id="chatForm" action="" method="post" ng-submit="send_request($event) && onlineForm.$valid">
                    <div class="chat-cmodule-view">
                        <div class="chat-cmodule-row">
                            <div class="chat-cmodule-widget-content">
                                <div class="cmodule-help-message">{{settings.welcome_message}}</div>
                                <div class="cmodule-form-group">
                                    <input class="cmodule-form-control" type="text" name="name" placeholder="Name" ng-model="visitor.name" ng-class="{'cmodule-error': onlineForm.name.$dirty && onlineForm.name.$invalid}" required>
                                </div>
                                <div class="cmodule-form-group">
                                    <input class="cmodule-form-control" type="email" name="email" placeholder="Email" ng-model="visitor.email" ng-class="{'cmodule-error': onlineForm.email.$dirty && onlineForm.email.$invalid}" required>
                                </div>
                                <div class="cmodule-form-group" ng-show="settings.show_depaertment_selection_box == 'yes'">
                                    <select class="cmodule-form-control" ng-model="visitor.requested_tag" ng-required="settings.show_depaertment_selection_box == 'yes'" name="department" ng-class="{'cmodule-error': onlineForm.department.$dirty && onlineForm.department.$invalid}">
                                        <option value="">Select Department</option>
                                        <option ng-repeat="option in tags" value="{{option.id}}">{{option.tag_name}}</option>
                                    </select>
                                </div>
                                <div class="cmodule-form-group cmodule-last-item">
                                    <textarea name="message" class="cmodule-form-control" cols="20" rows="4" placeholder="Your Question...." ng-model="visitor.message" ng-class="{'cmodule-error': onlineForm.message.$dirty && onlineForm.message.$invalid}" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>		
                    <div class="chat-cmodule-footer">
                        <button id="cmodule-online-submit" class="chatnox-btn-default" type="submit">Chat Now</button>
                    </div>
                </form>
            </div>
            <div ng-show="visible_widget == 'feedback-widget' && minimized != 'yes'" id="cmodule-feedback-widget" class="chat-cmodule-widget animate-show">
                <form name="feedbackForm" id="chatForm" action="" method="post" ng-submit="send_feedback($event) && feedbackForm.$valid">
                    <div class="chat-cmodule-view">
                        <div class="chat-cmodule-row">
                            <div class="chat-cmodule-widget-content">
                                <div class="cmodule-help-message"><strong class="cmodule-strong">{{settings.feedback_heading_message}}</strong></div>
                                <div class="cmodule-form-group">
                                    <p class="cmodule-rating">Rating: <span class="cmodule-badge-success">{{feedback.rating}}</span> <strong class="cmodule-rating-string">{{rating_status[feedback.rating]}}</strong></p>
                                    <div data-range-slider data-floor="1" data-ceiling="5" data-step="1" data-precision="2" data-highlight="left" data-ng-model="feedback.rating"></div>
                                </div>
                                <div class="cmodule-form-group cmodule-last-item">
                                    <textarea name="message" class="cmodule-form-control" cols="20" rows="4" placeholder="Write your Feedback...." ng-model="feedback.feedback_text" ng-class="{'cmodule-error': feedbackForm.message.$dirty && feedbackForm.message.$invalid}" ></textarea>
                                </div>
                            </div>
                        </div>
                    </div>		
                    <div class="chat-cmodule-footer">
                        <button id="cmodule-online-submit" class="chatnox-btn-default" type="submit">Submit</button>
                    </div>
                </form>
            </div>
            <div ng-show="visible_widget == 'chatting-widget' && minimized != 'yes'" id="live-chat-cmodule-widget" class="chat-cmodule-widget animate-show">
                <div class="chat-cmodule-view">
                    <div class="chat-cmodule-row">
                        <div class="chat-cmodule-widget-content" id="message_box">
                            <div ng-show="is_waiting" class="cmodule-help-message">{{settings.waiting_message}}</div>
                            <div ng-repeat="row in messages| orderBy:'sort_order'" class="{{row.class}}" ng-class="{'chat-hootud-message-reply-row': row.sender_id == visitor.id, 'chat-cmodule-message-row': row.sender_id != visitor.id}" ng-mouseover="row.class = ''"  on-last-repeat>
                                <img ng-show="row.profile_pic && row.sender_id != visitor.id && settings.theme == 'bubbles_with_avatar'" title="{{row.name}}" alt="" ng-src="{{row.profilePic}}" class="cmodule-img-circle cmodule-avatar">
                                <span ng-hide="row.profile_pic || row.sender_id == visitor.id || settings.theme != 'bubbles_with_avatar'" style="background-color: {{rand_color}};" class="cmodule-user-avatar">{{row.name|oneCapLetter}}</span>
                                <div class="chat-cmodule-message" ng-bind-html="row.chat_message | newlines | smilies"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cmodule-modal" ng-show="ask_to_confirm == 'yes' && confirm_close_session == 'no'" ng-class="{'cmodule-in':ask_to_confirm == 'yes'}">
                    <div class="cmodule-modal-dialog">
                        <div class="cmodule-modal-content">
                            <div class="cmodule-modal-body">Are you sure you want to end this chat session?</div>
                            <div class="cmodule-modal-footer">
                                <a href="#" ng-click="confirm_close_session = 'yes';end_chat($event)">Yes</a>
                                <a href="#" ng-click="ask_to_confirm = 'no';disable_click($event)">No</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cmodule-modal" ng-show="ask_for_transcript == 'yes'" ng-class="{'cmodule-in':ask_for_transcript == 'yes'}">
                    <div class="cmodule-modal-dialog">
                        <div class="cmodule-modal-content">
                            <div class="cmodule-modal-body">Do you want transcript in email?</div>
                            <div class="cmodule-modal-footer">
                                <a href="#" ng-click="settings.send_chat_transcript_to_visitor = 'yes';end_chat($event)">Yes</a>
                                <a href="#" ng-click="settings.send_chat_transcript_to_visitor = 'no';end_chat($event)">No</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-cmodule-typing-status"  ng-show="(is_typing || (new_msg_indecator && is_scroll_start)) && ask_for_transcript != 'yes'">
                    <div ng-show="is_typing" class="chat-cmodule-message chat-cmodule-typing"> 
                        <div class="chat-cmodule-typing-indicator"></div>
                        <div class="chat-cmodule-typing-indicator"></div>
                        <div class="chat-cmodule-typing-indicator"></div>
                    </div>
                    <div ng-show="new_msg_indecator && is_scroll_start" class="chat-cmodule-new-message-indecator"></div>
                </div>

                <div class="chat-cmodule-footer">                    
                    <form name="chatForm" id="chatForm" action="" method="post" ng-submit="send_message($event) && chatForm.$valid">
                        <div class="cmodule-message-box">
                            <textarea focus-on-change="new_message" ng-focus="chatboxState = 'focus'" ng-blur="chatboxState = 'blur'" ng-model="new_message" ng-keypress="submit_message($event)" ng-disabled="chat_session.session_status == 'closed'" id="message" cols="20" rows="2" class="cmodule-form-control" placeholder="Your Message..." required></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div ng-show="display_loader" class="cmodule-spinner-loader">
                <div class="cmodule-spinners">
                    <div class="cmodule-spinner-bounce"></div>
                    <div class="cmodule-spinner-bounce"></div>
                    <div class="cmodule-spinner-bounce"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<audio id="audio1">
    <source src="<?php echo theme_url("audio/ping.mp3"); ?>"></source>
</audio>
<style ng-bind-html="custom_styles"></style>