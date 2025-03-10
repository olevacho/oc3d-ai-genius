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
            //add_action('wp_ajax_oc3d_store_chatbot_assistant_tab', [$this, 'processAssistantSubmit']);
            add_action('admin_post_oc3d_store_chatbot_upload', [$this, 'processAssistantUpload']);
            add_action('admin_post_oc3d_create_assistant', [$this, 'processAssistantSubmit']);
            add_action('admin_post_oc3d_remove_assistant', [$this, 'processAssistantRemove']);
            //chatbot_provider
            add_action('admin_post_oc3d_chatbot_provider', [$this, 'processAssistantProvider']);
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
        
        public function processAssistantProvider() {
            $redirect_url = admin_url('admin.php?page=oc3daig_chatbot');
            $r = ['code' => 404, 'error_msg' => '', 'id' => '', 'model' => ''
                , 'created_at' => '', 'instruction' => '', 'name' => '', 'description' => ''];

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
                update_option(OC3DAIG_PREFIX_LOW . 'chat_bot_provider', $resp);
                wp_redirect(esc_url($redirect_url));
                exit;
            }


            $provider = sanitize_text_field($_POST['oc3daig_chatbot_config_chatbot_provider']);
            $chat_bot_providers = Oc3dAig_ChatBotUtils::getProviders();
            $found = false;
            foreach ($chat_bot_providers as $provideropt) {
                if ($provider === $provideropt) {
                    $found = true;
                }
            }
            if (!$found) {
                $r['code'] = 404;
                $r['error_msg'] = 'Provider not found';
                wp_redirect(esc_url($redirect_url));
                exit;
            }
            update_option(OC3DAIG_PREFIX_LOW . 'chat_bot_provider', $provider);

            wp_redirect(esc_url($redirect_url));
            exit;
            
        }

        public function processAssistantSubmit() {
            
            $redirect_url = admin_url('admin.php?page=oc3daig_chatbot');
            $r = ['code'=>404,'error_msg'=>'','id'=>'','model' =>''
            ,'created_at' =>'','instruction' =>'','name' =>'','description'=>''];
            
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
                update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                wp_redirect(esc_url($redirect_url));
                exit;
            }
            
            
    
                $data = [];
                $mod_id = sanitize_text_field($_POST['oc3daig_chatbot_config_chat_model2']);
                $models_allowed = Oc3dAig_Utils::getExpertModelTexts();
                if (!in_array($mod_id, $models_allowed)) {
                    $r['code'] = 403;
                    $r['msg'] = __('Model is not allowed');
                    $resp = serialize($r);
                    update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                    wp_redirect(esc_url($redirect_url));
                    exit;
                }
                $data['model'] = $mod_id;

                $data['assistant_name']  = isset($_POST['oc3daig_assistant_name'])?sanitize_text_field($_POST['oc3daig_assistant_name']):'assistant';
                $data['instructions']  = isset($_POST['oc3daig_assistant_instructions'])?sanitize_text_field($_POST['oc3daig_assistant_instructions']):'';
                $data['assistant_timeout'] = isset($_POST['oc3daig_assistant_timeout']) && ((int)$_POST['oc3daig_assistant_timeout']) > 0?(int)$_POST['oc3daig_assistant_timeout']:1;
                $uploaded_f = get_option(OC3DAIG_PREFIX_LOW . 'assistant_file');
                $uploaded_default_file = ['code'=>0,'error_msg'=>'','id'=>'','filename' =>''];
                if(is_string($uploaded_f)){
                        $uploaded_f_arr = unserialize($uploaded_f);
                        if(is_array($uploaded_f_arr)){
                                $uploaded_file =  $uploaded_f_arr;
                        }
                        else{
                                $uploaded_file = $uploaded_default_file;
                        }
                }else{
                        $uploaded_file = $uploaded_default_file;
                }
                $file_id = isset($uploaded_file['id'])?$uploaded_file['id']:'';
                if(strlen($file_id) <= 0){
                    $r['code'] = 403;
                    $r['msg'] = __('File is not uploaded');
                    $resp = serialize($r);
                    update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                    wp_redirect(esc_url($redirect_url));
                    exit;
                }
                $data['file_id'] = $file_id;
                if (!class_exists('Oc3dAig_AiRequest')) {
                    require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
                }
                $old_option = get_option(OC3DAIG_PREFIX_LOW . 'assistant_options','');
                if(strlen($old_option) <= 0){//insert
                    $response = Oc3dAig_AiRequest::createAssistantRetrievalV2($data);
                    if(is_array($response) && isset($response['id']) && strlen($response['id']) > 0){
                        $resp = serialize($response);
                        update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                    }
                }else{//update
                    //check if api fields changed
                    $old_assistant_opts_arr = unserialize($old_option);
                    $api_changed = $this->checkAssistantApiChanged($old_assistant_opts_arr, $data);
                    $this->updateAssistant($old_assistant_opts_arr, $data, $api_changed);
                    
                }
                

            wp_redirect(esc_url($redirect_url));
            exit;
            
        }
        
        public function processAssistantRemove(){
            $redirect_url = admin_url('admin.php?page=oc3daig_chatbot');
            /*$r = ['code'=>404,'error_msg'=>'','id'=>'','model' =>''
            ,'created_at' =>'','instruction' =>'','name' =>'','description'=>''];
            */
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
                update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                wp_redirect(esc_url($redirect_url));
                exit;
            }
            
            
    
                //$data = [];
                
                if (!class_exists('Oc3dAig_AiRequest')) {
                    require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
                }
                $old_option = get_option(OC3DAIG_PREFIX_LOW . 'assistant_options','');
                if(strlen($old_option) > 0){//delete
                    $old_assistant_opts_arr = unserialize($old_option);
                    if(is_array($old_assistant_opts_arr) && isset($old_assistant_opts_arr['id'])){
                        $assistant_id = sanitize_text_field($old_assistant_opts_arr['id']);
                        $response = Oc3dAig_AiRequest::removeAssistantV2($assistant_id);
                    if($response){
                        $resp = '';
                        update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
                    }
                    }
                    
                }
                

            wp_redirect(esc_url($redirect_url));
            exit;
        }
        
        public function updateAssistant($old_assistant_options,$new_assistant_options,$api_changed){//TO-DO implement $api_changed
            $res = ['code'=>100,'body'=>'','msg'=>''];
            if($api_changed){
                $new_assistant_options['assistant_id'] = $old_assistant_options['id'];
                $res = Oc3dAig_AiRequest::updateAssistantRetrievalV2($new_assistant_options);
                
            }
            if($res['code'] === 200){
            $response = ['code'=>200,
                'body'=>$res['body'],'error_msg' => $res['body'],'success'=>'true',
                'id'=>$old_assistant_options['id'],'model' =>$new_assistant_options['model']
            ,'created_at' =>$old_assistant_options['created_at'],
                'instruction' =>$new_assistant_options['instructions'],
                'name' =>$old_assistant_options['name'],'file_id' =>$old_assistant_options['file_id']];
            $response['assistant_timeout'] = (int)$new_assistant_options['assistant_timeout'];
            $resp = serialize($response);
            update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
            return $res;
            }elseif($res['code'] === 100){
                $response = ['code'=>$old_assistant_options['code'],
                    'body'=>$old_assistant_options['body'],'error_msg' => $old_assistant_options['body'],'success'=>'true',
                    'id'=>$old_assistant_options['id'],'model' =>$old_assistant_options['model']
                ,'created_at' =>$old_assistant_options['created_at'],
                    'instruction' =>$old_assistant_options['instruction'],
                    'name' =>$old_assistant_options['name'],'file_id' =>$old_assistant_options['file_id']];
                $response['assistant_timeout'] = (int)$new_assistant_options['assistant_timeout'];
                $resp = serialize($response);
                update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
            }
            else{
                $body = json_decode($res['body']);
                $error_msg = 'Some error happened';
                if(is_object($body) && isset($body->message) && isset($body->code)){
                    $error_msg = $body->code.':'.$body->message;
                }elseif(is_array($body) && isset($body['code']) && isset($body['message'])){
                    $error_msg = $body['code'].':'.$body['message'];
                }
                $response = ['code'=>$old_assistant_options['code'],
                    'body'=>$old_assistant_options['body'],'error_msg' => $error_msg,'success'=>'false',                    'id'=>$old_assistant_options['id'],'model' =>$old_assistant_options['model']
                ,'created_at' =>$old_assistant_options['created_at'],
                    'instruction' =>$old_assistant_options['instruction'],
                    'name' =>$old_assistant_options['name'],'file_id' =>$old_assistant_options['file_id']];
                $response['assistant_timeout'] = (int)$new_assistant_options['assistant_timeout'];
                $resp = serialize($response);
                update_option(OC3DAIG_PREFIX_LOW . 'assistant_options', $resp);
            }
            return $res;
        }
        
        public function checkAssistantApiChanged($old_assistant_options,$new_assistant_options){
            //TO-DO add checking get https://api.openai.com/v1/assistants/{assistant_id},
            // AND check file_id
            /*
             * $data['model'] = $mod_id;

                $data['assistant_name']  = isset($_POST['oc3daig_assistant_name'])?sanitize_text_field($_POST['oc3daig_assistant_name']):'assistant';
                $data['instructions']  = isset($_POST['oc3daig_assistant_instructions'])?sanitize_text_field($_POST['oc3daig_assistant_instructions']):'';
                $data['assistant_timeout']
             * 
             *  $res['id'] = $res_obj['id'];
                        $res['created_at'] = $res_obj['created_at'];
                        $res['model'] = $options['model'];
                        $res['instruction'] = $options['instructions'];
                        $res['name'] = $options['assistant_name'];
             */
            //$changed = false;
            if(!isset($old_assistant_options['instruction']) || $old_assistant_options['instruction'] != $new_assistant_options['instructions'] ){
                return true;
            }
            
            if(!isset($old_assistant_options['model']) || $old_assistant_options['model'] != $new_assistant_options['model'] ){
                return true;
            }
            if(!isset($old_assistant_options['name']) || $old_assistant_options['name'] != $new_assistant_options['assistant_name'] ){
                return true;
            }
            if(!isset($old_assistant_options['file_id']) || $old_assistant_options['file_id'] != $new_assistant_options['file_id'] ){
                return true;
            }
            //file_id
            return false;
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