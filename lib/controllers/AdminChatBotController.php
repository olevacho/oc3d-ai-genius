<?php
if ( ! defined( 'ABSPATH' ) ) exit;




if (!class_exists('Oc3dAig_AdminChatBotController')) {

    class Oc3dAig_AdminChatBotController extends Oc3dAig_BaseController {


        
        public function __construct() {
            if (!class_exists('Oc3dAig_ChatBotUtils')) {
                require_once OC3DAIG_PATH . '/lib/helpers/ChatBotUtils.php';
            }
            $this->load_model('ChatBotModel');
            
            add_action('wp_ajax_oc3d_store_chatbot_general_tab', [$this, 'processGeneralSubmit']);
            add_action('wp_ajax_oc3d_store_chatbot_assistant_tab', [$this, 'processAssistantSubmit']);
            add_action('admin_post_oc3d_store_chatbot_upload', [$this, 'processAssistantUpload']);

        }

        public function registerAdminMenu() {

            
        }

        public function processGeneralSubmit() {
            if (('POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }

            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3d-ai-aiassistant')];
            $nonce = OC3DAIG_PREFIX_SHORT . 'chatbot_config_nonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied','oc3d-ai-aiassistant');
                wp_send_json($r);
                exit;
            }
            
            $data = [];
            if (isset($_POST['oc3daig_chatbot_access_for_guests'])) {
                
                if($_POST['oc3daig_chatbot_access_for_guests'] == 'on'){
                   $data['access_for_guests']  = 1;
                }else{
                   $data['access_for_guests']  = 0;
                }
            }else{
                $data['access_for_guests']  = 0;
            }
            
           
            //oc3daig_chatbot_config_chat_model
            if (isset($_POST[OC3DAIG_PREFIX_LOW . 'chatbot_config_chat_model'])) {
                $data['chat_model']  = sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW . 'chatbot_config_chat_model']);;
            }
            
            $data['chat_temperature'] = is_numeric($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chat_temperature']) ? floatval($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chat_temperature']) : 1;
            
            // Transforming [oc3daig_chatbot_config_chat_top_p]
            $data['chat_top_p'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chat_top_p']) ? (float)$_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chat_top_p'] : 1;

            // Transforming [oc3daig_chatbot_config_chatbot_name]
            $data['chatbot_name'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chatbot_name']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_chatbot_name']) : '';

            // Transforming [oc3daig_chatbot_config_context]
            $data['context'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_context']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_context']) : '';

            // Transforming [oc3daig_chatbot_config_greeting_message_text]
            $data['greeting_message_text'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_greeting_message_text']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_greeting_message_text']) : '';

            
            // Transforming [oc3daig_chatbot_config_language]
            $data['language'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_language']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_language']) : 'english';

            // Transforming [oc3daig_chatbot_config_max_tokens]
            $data['max_tokens'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_max_tokens']) ? (int)$_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_max_tokens'] : 1024;
            if($data['max_tokens'] <= 0){
                    $data['max_tokens'] = 1024;
            }
            // Transforming [oc3daig_chatbot_config_message_placeholder]
            $data['message_placeholder'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_message_placeholder']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_message_placeholder']) : '';

            
            // Transforming [oc3daig_chatbot_config_presence_penalty]
            $data['presence_penalty'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_presence_penalty']) ? (float)$_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_presence_penalty'] : 0;

            // Transforming [oc3daig_chatbot_config_frequency_penalty]
            $data['frequency_penalty'] = isset($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_frequency_penalty']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'chatbot_config_frequency_penalty']) : 0;
            
            $chat_bot_hash = isset($_POST[OC3DAIG_PREFIX_LOW .'oc3d_chatbot_hash']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'oc3d_chatbot_hash']) : 'default';
            
            $res = $this->model->storeChatBotOptions($chat_bot_hash,$data);
            
            
            
            if($res){
                $r['result'] = 200;
                $r['msg'] = __('OK','oc3d-ai-aiassistant');
            }else{
                $r['result'] = 500;
                $r['msg'] = __('Error','oc3d-ai-aiassistant');
            }
            wp_send_json($r);
            exit;
            
        }
        
        
        public function processAssistantSubmit() {
            
            if (('POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }

            $r = ['result' => 0, 'msg' => __('Unknow problem','oc3d-ai-aiassistant')];
            $nonce = OC3DAIG_PREFIX_SHORT . 'chatbot_styles_nonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied','oc3d-ai-aiassistant');
                wp_send_json($r);
                exit;
            }
            $data = [];
            
            
            $chat_bot_hash = isset($_POST[OC3DAIG_PREFIX_LOW .'oc3d_chatbot_hash']) ? sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW .'oc3d_chatbot_hash']) : 'default';
            
            $res = $this->model->storeChatBotOptions($chat_bot_hash,$data);
            
            if($res){
                $r['result'] = 200;
                $r['msg'] = __('OK','oc3d-ai-aiassistant');
            }else{
                $r['result'] = 500;
                $r['msg'] = __('Error','oc3d-ai-aiassistant');
            }
            wp_send_json($r);
            exit;
            
        }
        
        
        function processAssistantUpload(){
            $redirect_url = admin_url('admin.php?page=oc3daig_chatbot');
            $r = ['code'=>404,'error_msg'=>'','id'=>'','filename' =>'' ];
            
            //$redirect_url = add_query_arg('page', 'oc3daig_chatbot', admin_url('admin.php'));
            //$redirect_url = admin_url('admin.php?page=' . esc_attr($_REQUEST['page']));
            //$current_page = isset($_GET['page']) ? $_GET['page'] : '';
            //$redirect_url = add_query_arg('page', $current_page, admin_url('admin.php'));

            if (('POST' !== $_SERVER['REQUEST_METHOD'])) {
                wp_redirect(esc_url($redirect_url));
                exit;
            }

            
            $nonce = OC3DAIG_PREFIX_SHORT . 'chatbot_assistant_nonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                $r['code'] = 403;
                $r['error_msg'] = 'Access denied';
                $resp = serialize($r);
                update_option(OC3DAIG_PREFIX_LOW . 'assistant_file', $resp);
                wp_redirect(esc_url($redirect_url));
                exit;
            }
            $upload_dir = wp_upload_dir();
            if(is_array($upload_dir) && isset($upload_dir['path'])){
                $filepath = Oc3dAig_Utils::storeFile($upload_dir['path']);
                if($filepath === '' || $filepath == 'wrong_file_format.oc3daig'){
                    $r['result'] = 403;
                    $r['msg'] = 'Access denied';
                    $r['filename'] = $filepath;
                    $resp = serialize($r);
                    update_option(OC3DAIG_PREFIX_LOW . 'assistant_file', $resp);
                    wp_redirect(esc_url($redirect_url));
                    exit;
                }
                if (!class_exists('Oc3dAig_AiRequest')) {
                    require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
                }
                $response = Oc3dAig_AiRequest::uploadFile($filepath);
                if(is_array($response) && isset($response['id']) && strlen($response['id']) > 0){
                    $resp = serialize($response);
                    update_option(OC3DAIG_PREFIX_LOW . 'assistant_file', $resp);
                }
            }
            

            wp_redirect(esc_url($redirect_url));
            exit;
        }
        
        
        

        function showMainView(){
            $this->showChatbotSettings();
        }
        
        function showChatbotSettings() {
            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                return;
            }
            $default_bot = $this->model->getChatBotSettings('default');
            $conf_contr = $this;
            $conf_contr->load_view('backend/chatbot/chatbot', ['default_bot' => $default_bot]);
            $conf_contr->render();
        }

        


    }

}