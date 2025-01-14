<?php

class Feedback extends CP_Model {

    //model db table
    var $table = "";
    public $validation_rules = array(
        // rules for chat forward
        'feedback' => array(
            /*array(
                'field' => 'feedback_text',
                'label' => 'Feedback Text',
                'rules' => 'required'
            ),*/
            array(
                'field' => 'chat_session_id',
                'label' => 'Chat Session ID',
                'rules' => 'required'
            ),
            array(
                'field' => 'feedback_by',
                'label' => 'Sender',
                'rules' => 'required'
            ),
            array(
                'field' => 'feedback_to',
                'label' => 'Reciever',
                'rules' => 'required'
            ),
            array(
                'field' => 'rating',
                'label' => 'Rating',
                'rules' => 'required'
            )
        )
    );
    
    /*
     * define construct function
     */

    public function __construct() {
        parent::__construct();
        $this->table = TABLE_FEEDBACK;
    }
    
    /*
     * This function will enter new feedback in database.
     * 
     * @return $feedback_id or false
     */

    public function insert_feedback() {
        // creating session entry
        $this->model_data['created_at'] = date("Y-m-d H:i:s", now());
        $this->db->insert($this->table, $this->model_data);
        $feedback_id = $this->db->insert_id();

        return $feedback_id;
    }
    
    /*
     * This function will use to fetch get_feddbacks
     * 
     * @param $filters
     * 
     * @return $feedbacks
     */
    
    function get_feddbacks($filters) {        
        // select fileds
        $select = TABLE_FEEDBACK . '.id, ' 
                . TABLE_FEEDBACK . '.rating, '
                . TABLE_FEEDBACK . '.feedback_text as message, '
                . TABLE_FEEDBACK . '.created_at, '
                . 'sender.id as senderId, ' 
                . 'sender.name as senderName, ' 
                . 'sender.email as senderEmail, '
                . " CASE WHEN sender.profile_pic = '' THEN CONCAT(CONCAT(CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(sender.email))),'?s=200'), sender.profile_pic) ELSE CONCAT('".  base_url(UPLOAD_DIR.'/'.PROFILE_PICS)."/thumb/', sender.profile_pic) END  as senderProfilePic, "
                . 'receiver.id as receiverId, ' 
                . 'receiver.name as receiverName, ' 
                . 'receiver.email as receiverEmail, '
                . 'receiver.profile_pic as receiver_profile_pic, '
                . " CASE WHEN receiver.profile_pic = '' THEN CONCAT(CONCAT(CONCAT('http://www.gravatar.com/avatar/', MD5(LOWER(receiver.email))),'?s=200'), receiver.profile_pic) ELSE CONCAT('".  base_url(UPLOAD_DIR.'/'.PROFILE_PICS)."/thumb/', receiver.profile_pic) END  as receiverProfilePic";
        
        $this->db->select($select);
        
        if (isset($filters['agents']) and $filters['agents']) {
            $this->db->where_in(TABLE_FEEDBACK . '.feedback_to', $filters['agents']);
        }
        
        $feedbacks = $this->db->from(TABLE_FEEDBACK)
                ->join(TABLE_USERS.' sender', 'sender.id = ' . TABLE_FEEDBACK . '.feedback_by')
                ->join(TABLE_USERS.' receiver', 'receiver.id = ' . TABLE_FEEDBACK . '.feedback_to')
                ->limit($this->item_per_page, $filters['offset'])
                ->order_by(TABLE_FEEDBACK . ".id", 'desc')
                ->get()
                ->result();
        
        return $feedbacks;
    }
}