<?php

class Chat extends CP_AgentController {
    /*
     * Calling parent constructor
     */

    public function __construct() {
        parent::__construct();

        $this->data['title'] = 'Agent Panel';
        $this->data['layout'] = 'agent_layout';
        $this->body_classes[] = 'agent-logged-in';

        $this->addjs(theme_url("js/app/agents/header.js"), TRUE);
        $this->addjs(theme_url("js/app/agents/chat_list.js"), TRUE);
        $this->addjs(theme_url("js/app/agents/workroom.js"), TRUE);
        $this->add_js_inline(array('user' => $this->current_user));
    }

    /*
     * This function will use to get all recents chats.
     * 
     * @return json data of requests
     */

    function index() {
        $response = array('error' => '', 'result' => 'failed');

        // getting recent chat list
        $chat_list = $this->chat_session->get_recent_chats($this->current_user->id);

        $response['result'] = 'success';
        $response['data'] = array('chatListData' => $chat_list);

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This function will use to get all closed chats.
     * 
     * @return json data of requests
     */

    function closed() {
        $excepts = $this->input->post('excepts');
        $response = array('error' => '', 'result' => 'failed');

        // getting closed chat list
        $closed_chats = $this->chat_session->get_closed_chats($this->current_user->id, $excepts, 15);

        $response['result'] = 'success';
        $response['data'] = array('closed_chats' => $closed_chats);

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This function will return cureent server time.
     * 
     * @return milliseconds
     */

    function get_server_time() {
        $millitime = $this->get_time_miliseconds();
        $response = array('result' => 'success', 'milliseconds' => $millitime);
        
        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This function will call to accept a request
     * 
     * @return result and status
     */

    function accept_request() {
        $request_id = $this->input->get('request_id');
        $chat_session_id = $this->input->get('chat_session_id');

        $response = array('error' => '', 'result' => 'failed');

        // mark notification as read
        $this->user->mark_notification_read(array('chat_session_id' => $chat_session_id, 'notification_type' => 'online_request'));

        // get chat session
        $chat_session = $this->chat_session->get_single(array('id' => $chat_session_id));
        if ($chat_session->session_status == 'requested' or $chat_session->session_status == 'forward') {
            $request = $this->chat_request->get_single(array('id' => $request_id));
            if ($request->request_status == 'pending') {
                $this->chat_request->accept_request($request);

                $response['result'] = 'success';
                $response['message'] = $this->lang->line('request_accepted');
            } else {
                $response['error'] = $this->lang->line('request_expired');
            }
        } else {
            $response['error'] = $this->lang->line('request_expired');
            $this->close_requests($chat_session_id);
        }

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * this function will return all chat list
     * 
     * @return all chat list or error if any.
     */

    function workroom() {
        $chat_session_id = $this->input->get('chat_session_id');

        $this->addjs(theme_url("js/app/agents/workroom.js"), TRUE);
        $this->add_js_inline(array('chat_session_id' => $chat_session_id, 'user' => $this->current_user, 'settings' => $this->settings));

        //write code here to load view
        $this->bulid_layout("agents/workroom");
    }

    /*
     * This function calls to check session exists or not
     * 
     * @return json object with result
     */

    public function get_session() {
        $chat_session_id = $this->input->get('chat_session_id');

        $response = array('error' => '', 'result' => 'failed');
        $messages = array();
        $visitor = '';
        $chat_session = '';

        $workroomHistory = $this->session->userdata('workroomHistory');
        if ($workroomHistory and isset($workroomHistory[$chat_session_id]) and isset($workroomHistory[$chat_session_id]['chatHistory']) and $workroomHistory[$chat_session_id]['chatHistory']) {
            $messages = $workroomHistory[$chat_session_id]['chatHistory'];
            $visitor = $workroomHistory[$chat_session_id]['visitor'];
            $chat_session = $workroomHistory[$chat_session_id]['chat_session'];
            $last_id = $workroomHistory[$chat_session_id]['last_id'];
            $message_stored = $workroomHistory[$chat_session_id]['message_stored'];
        } else {
            //if (!$workroomHistory) {
            $workroomHistory = array();
            //}

            $chatHistory = $message_stored = array();
            $last_id = 0;

            $messages_list = $this->chat_message->get_chat_messages($chat_session_id);
            foreach ($messages_list as $key => $row) {
                $chat_row = array();
                $last_id = $row->id;
                $chat_row['id'] = $row->id;
                $chat_row['name'] = $row->name;
                $chat_row['profile_pic'] = $row->profile_pic;
                $chat_row['profilePic'] = $this->media->get_thumbnail($row->profile_pic, PROFILE_PICS, $row->email);
                $chat_row['chat_message'] = $row->chat_message;
                $chat_row['sort_order'] = $row->sort_order;
                $chat_row['message_status'] = $row->message_status;
                $chat_row['sender_id'] = $row->sender_id;
                $chat_row['class'] = '';

                if (!in_array($row->id, $message_stored)) {
                    $message_stored[] = $row->id;
                    $chatHistory[] = $chat_row;
                }

                $chat_row['class'] = 'new-message';
                $messages[$key] = $chat_row;
            }

            if ($last_id > 0) {
                $this->chat_message->mark_message_read($chat_session_id, $last_id);
            }

            $visitor = $this->chat_user->get_visitor($chat_session_id);

            $chat_session = $this->chat_session->get_single(array('id' => $chat_session_id));

            // storing chat history in session
            $workroomHistory[$chat_session_id] = array(
                'chatHistory' => $chatHistory,
                'last_id' => $last_id,
                'visitor' => $visitor,
                'chat_session' => $chat_session,
                'message_stored' => $message_stored
            );
            $this->session->set_userdata('workroomHistory', $workroomHistory);
        }

        //set visiter login status
        $online_users = $this->chat_user->get_online_users();
        $visitor->status = (isset($online_users[$visitor->id])) ? 'online' : 'offline';

        $response['result'] = 'success';
        $response['data'] = array(
            'chatHistory' => $messages,
            'visitor' => $visitor,
            'chat_session' => $chat_session,
            'last_id' => $last_id,
            'message_stored' => $message_stored
        );

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This the chatHeartbeat of chattinh.
     * 
     * @return chating data 
     */

    function chatHeartbeat() {
        $chat_session_id = $this->input->get('chat_session_id');
        $last_id = $this->input->get('last_message_id');
        $is_typing = $this->input->get('typing');

        $response = array('error' => '', 'result' => 'failed');

        $workroomHistory = $this->session->userdata('workroomHistory');
        $chatHistory = $workroomHistory[$chat_session_id]['chatHistory'];
        $visitor = $workroomHistory[$chat_session_id]['visitor'];
        $chat_session = $workroomHistory[$chat_session_id]['chat_session'];
        $message_stored = $workroomHistory[$chat_session_id]['message_stored'];

        // updating typing status.
        $this->chat_user->model_data = array('user_id' => $this->current_user->id, 'chat_session_id' => $chat_session->id);
        $this->chat_user->update_typing_status($is_typing);

        // mark notification as read
        $this->user->mark_notification_read(array('chat_session_id' => $chat_session_id, 'notification_type' => 'message'));

        $messages = array();


        $messages_list = $this->chat_message->get_chat_messages($chat_session_id, $last_id);
        foreach ($messages_list as $key => $row) {
            $chat_row = array();
            $last_id = $row->id;
            $chat_row['id'] = $row->id;
            $chat_row['name'] = $row->name;
            $chat_row['profile_pic'] = $row->profile_pic;
            $chat_row['profilePic'] = $this->media->get_thumbnail($row->profile_pic, PROFILE_PICS, $row->email);
            $chat_row['chat_message'] = $row->chat_message;
            $chat_row['sort_order'] = $row->sort_order;
            $chat_row['message_status'] = $row->message_status;
            $chat_row['sender_id'] = $row->sender_id;
            $chat_row['class'] = '';

            if (!in_array($row->id, $message_stored)) {
                $message_stored[] = $row->id;
                $chatHistory[] = $chat_row;
            }

            $chat_row['class'] = 'new-message';
            $messages[$key] = $chat_row;
        }

        if ($last_id > 0) {
            $this->chat_message->mark_message_read($chat_session_id, $last_id);
        }

        $chat_session = $this->chat_session->get_single(array('id' => $chat_session_id));
        $visitor = $this->chat_user->get_visitor($chat_session_id);

        //set visiter login status
        $online_users = $this->chat_user->get_online_users();
        $visitor->status = (isset($online_users[$visitor->id])) ? 'online' : 'offline';

        // storing chat history in session
        $workroomHistory[$chat_session_id] = array(
            'chatHistory' => $chatHistory,
            'last_id' => $last_id,
            'visitor' => $visitor,
            'chat_session' => $chat_session,
            'message_stored' => $message_stored
        );
        $this->session->set_userdata('workroomHistory', $workroomHistory);

        $response['result'] = 'success';
        $response['last_id'] = $last_id;
        $response['visitor'] = $visitor;
        $response['chat_session'] = $chat_session;
        $response['chatMessagesData'] = $messages;

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This function will create message entry
     * 
     * @return $result faild or success
     */

    public function send() {
        $response = array('error' => '', 'result' => 'failed');
        //check if data is valid or not
        $this->form_validation->set_rules($this->chat_message->validation_rules['new_message']);

        if ($this->form_validation->run() === true) {
            $chat_session_id = $this->input->post('chat_session_id');
            $this->chat_message->model_data = $this->input->post(array('chat_message', 'chat_session_id', 'message_status', 'sender_id', 'sort_order'));
            $local_id = 'awb' . $this->input->post('sender_id') . $this->input->post('sort_order');
            $this->chat_message->model_data['local_id'] = $local_id;

            $message_id = $this->chat_message->add_message($local_id);
            if ($message_id and is_object($message_id) === false) {
                $workroomHistory = $this->session->userdata('workroomHistory');
                $chatHistory = $workroomHistory[$chat_session_id]['chatHistory'];
                $visitor = $workroomHistory[$chat_session_id]['visitor'];
                $chat_session = $workroomHistory[$chat_session_id]['chat_session'];
                $last_id = $workroomHistory[$chat_session_id]['last_id'];

                $this->chat_message->model_data['id'] = $message_id;
                $chatHistory[] = $this->chat_message->model_data;

                $response['result'] = 'success';
                $response['message_row'] = $this->chat_message->model_data;
            } else {
                //$response['error'] = $this->lang->line('message_enterd');
            }
        } else {
            $response['error'] = validation_errors();
        }

        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

    /*
     * This function will end (close) chat.
     * 
     * @param Post params = chat_session_id
     * 
     * @return $error or $message
     */

    function end() {
        $chat_session_id = $this->input->get('chat_session_id');

        $response = array('error' => '', 'result' => 'failed');
        $this->chat_session->model_data['session_status'] = 'closed';
        if ($this->chat_session->update_chat_session($chat_session_id)) {
            $workroomHistory = $this->session->userdata('workroomHistory');
            $chatHistory = $workroomHistory[$chat_session_id]['chatHistory'];
            $visitor = $workroomHistory[$chat_session_id]['visitor'];
            $chat_session = $workroomHistory[$chat_session_id]['chat_session'];
            $last_id = $workroomHistory[$chat_session_id]['last_id'];

            $chat_session->session_status = 'closed';

            // storing chat history in session
            $workroomHistory[$chat_session_id] = array('chatHistory' => $chatHistory, 'last_id' => $last_id, 'visitor' => $visitor, 'chat_session' => $chat_session);
            $this->session->set_userdata('workroomHistory', $workroomHistory);

            $response['result'] = 'success';
            $response['chat_session'] = $chat_session;
            $response['message'] = $this->lang->line('chat_closed');
        } else {
            $response['error'] = $this->lang->line('process_error');
        }
        
        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }

}
