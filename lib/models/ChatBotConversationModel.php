<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3dAig_ChatBotConversationModel')) {

    class Oc3dAig_ChatBotConversationModel {
        public $session_time = 3600;
        
        public function createChat($message = '',$options = [], $author = '', $exp_time = 0){
            $chat_hash = Oc3dAig_Utils::getToken(20);
            return $this->_updChat($chat_hash, $message, $options, $author, $exp_time);
        }
        
        public function updateChat($chat_hash = '', $message = '',$options = [],$author = '', $exp_time = 0){
            return $this->_updChat($chat_hash, $message, $options, $author, $exp_time);
        }
        
        private function _updChat($chat_hash = '', $message = '', $options = [], $author = '', $exp_time = 0){
            
            $values = $options;
            $values['last_msg'] = $message;
            $values['role'] = $author;
            $messages = isset($options['messages'])?$options['messages']:[];
            if(strlen($message) > 0){
                $messages[] = ['role'=>$author,'content'=>$message];
            }
            $values['messages'] = $messages;
            $expiration = $this->session_time;
            if($exp_time > 0){
                $expiration = $exp_time;
            }
            if(is_multisite()){
                set_site_transient('oc3daig_'.$chat_hash, $values, $expiration);
            }else{
                set_transient('oc3daig_'.$chat_hash, $values, $expiration);
            }
            return $chat_hash;
            
        }
        public function deleteChat($chat_hash = ''){
            if(is_multisite()){
                return delete_site_transient('oc3daig_'.$chat_hash);
            }else{
                return delete_transient('oc3daig_'.$chat_hash);
            }
        }
        
        public function getChat($chat_hash = ''){
            if(is_multisite()){
                return get_site_transient('oc3daig_'.$chat_hash);
            }else{
                return get_transient('oc3daig_'.$chat_hash);
            }
              
        }
        
        public function getDefaultChat(){
            return ['chat_status'=>"none",
                'last_msg' => '',
                'messages' => ['content'=>'','role'=>'user'],
                'role'=>'user'];//	
        }
        
        
    }

}