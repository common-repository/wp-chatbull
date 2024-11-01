<?php

class Chat_history extends CP_AdminController {
    /*
     * Calling parent constructor
     */

    public function __construct() {
        parent::__construct();
        
        // check has admin access
        $this->has_admin_access();
    }

    /*
     * To load view for chat history management
     */

    function index() {
        // add users.js file
        $this->addjs(theme_url("js/app/chat-history.js"), TRUE);

        // get all agents with admin
        $agents = $this->user->get_all(array('role !=' => 'visitor'), "id,name");
        $this->add_js_inline(array('agents' => $agents, 'user' => $this->current_user, 'settings' => $this->settings));

        // add filter file.
        $this->data['filter_view'] = 'chat-history/filters.php';

        //write code here to load view
        $this->bulid_layout("chat-history/list");
    }

    /*
     * this function will use to get offline request.
     * 
     * @return $requests (json array)
     */

    function get_chat_session() {
        $sessions = $this->chat_session->get_chat_session($this->input->post());
        
        return $this->output->set_content_type('application/json')->set_output($this->return_json($sessions));
    }

    /*
     * This function will be use to get conversation of chat session
     * 
     * @return $conversations (json array)
     */

    public function get_conversations() {
        $session_id = $this->input->get('id');
        $messages = $this->chat_message->get_chat_messages($session_id);        
        $visitor = $this->chat_user->get_visitor($session_id);
        
        return $this->output->set_content_type('application/json')->set_output($this->return_json(array('messages' => $messages, 'visitor' => $visitor)));
    }

    /*
     * Delete entry from database
     */
    function delete () {
        $response = array('errors' => '', 'result' => 'failed');
        $session_id = $this->input->get('id');

        if ($this->chat_session->delete_chat($session_id)) {
            $response['result'] = 'success';
            $response['message'] = $this->lang->line('chat_deleted');
        } else {
            $response['errors'] = $this->lang->line('process_error');
        }
        
        return $this->output->set_content_type('application/json')->set_output($this->return_json($response));
    }
}
