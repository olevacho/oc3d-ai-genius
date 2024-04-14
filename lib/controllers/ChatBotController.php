<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3dAig_ChatBotController')) {

    class Oc3dAig_ChatBotController extends Oc3dAig_BaseController {

        public $global_bot_enabled = false;

        public $namespace = 'oc3daia/v1';
        public $bot_url = '/chat/submit';
        public $default_bot_id = 'default';
        
        
        private $nonce = null;
        

        public function __construct() {
            if (!class_exists('Oc3dAig_ChatBotUtils')) {
                require_once OC3DAIG_PATH . '/lib/helpers/ChatBotUtils.php';
            }
            $this->load_model('ChatBotModel');
            $this->global_bot_enabled = get_option( 'oc3daig_global_bot_enabled' );
            add_shortcode( 'oc3daig_chatbot', array( $this, 'chatShortcode' ) );
            add_action( 'rest_api_init', array( $this, 'restApiInit' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'registerScripts' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'registerStyles' ) );
            if (  $this->global_bot_enabled ) {
			$this->enqueueScripts();
			add_action( 'wp_footer', array( $this, 'injectChatBot' ) );
		}
                
		
        }
        


        public function registerScripts(){

            wp_enqueue_script( 'oc3daia', OC3DAIG_URL . '/views/frontend/resources/js/chatbot.js',  array( 'jquery' ), OC3DAIG_VERSION, false );
        }
        
        public function registerStyles(){
            wp_enqueue_style(
                    'oc3daia',
                    OC3DAIG_URL . '/views/frontend/resources/css/chatbot.css',
                    array(),
                    OC3DAIG_VERSION
            );
        }

        public function injectChatBot(){
            
        }
        public function enqueueScripts(){
            
        }
        
        function helperGetUserData() {
		$user = wp_get_current_user();
		if ( empty( $user ) || empty( $user->ID ) ) {
			return null;
		}
		$placeholders = array(
			'FIRST_NAME' => get_user_meta( $user->ID, 'first_name', true ),
			'LAST_NAME' => get_user_meta( $user->ID, 'last_name', true ),
			'USER_LOGIN' => isset( $user ) && isset($user->data) && isset( $user->data->user_login ) ? 
				$user->data->user_login : null,
			'DISPLAY_NAME' => isset( $user ) && isset( $user->data ) && isset( $user->data->display_name ) ?
				$user->data->display_name : null,
			'AVATAR_URL' => get_avatar_url( get_current_user_id() ),
		);
		return $placeholders;
	}
        
        function helperGetSessionId() {
		if ( isset( $_COOKIE['oc3daig_session_id'] ) ) {
			return $_COOKIE['oc3daig_session_id'];
		}
		return "N/A";
	}
        
        public function getFrontParams( $bot_attributes ) {
		$front_params = [
			'bot_id' => $bot_attributes['id'],
			'custom_id' => $bot_attributes['custom'],
			'user_data' => $this->helperGetUserData(),
			'session_id' => $this->helperGetSessionId(),
			'rest_nonce' => $this->getNonce(),
			'context_id' => get_the_ID(),
			'plugin_url' => OC3DAIG_URL,
			'rest_url' => untrailingslashit( get_rest_url().$this->namespace.$this->bot_url ),
		];
                
                $chat_bot_styles = $this->model->getChatBotSettings($bot_attributes['id']);
                $default_bot_options = Oc3dAig_ChatBotUtils::getChatBotDefaultStyles();
                if(!is_object($chat_bot_styles) || !isset($chat_bot_styles->id) || $chat_bot_styles->id <= 0 ){
                    $chat_bot_options = $default_bot_options;
                }else{
                    $chat_bot_options = $this->mergeOptions($this->prefixizeOptions($chat_bot_styles->bot_options), $default_bot_options);
                    
                }
                $res = array_merge($front_params,$chat_bot_options);
		return $res;
	}
        
        public function prefixizeOptions($chat_bot_options=[]){
            $new = [];
            foreach($chat_bot_options as $idx=>$val){
                $new[OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.$idx] = $val;
            }
            return $new;
        }
        
        public function mergeOptions($opts1,$opts2){
            $donottouched = [];
            foreach($opts2 as $key=>$value){
                if(!array_key_exists($key, $opts1)){
                    $donottouched[$key] = $value;
                }
            }
            $new = array_merge($donottouched, $opts1);
            return $new;
        }
        
        public function getBotInfo( $bot_id ){// id of bot which starts from default is for default modes 
            return ['botmode' => 'classic', 
                    'provider' => 'chatgpt',
                    'view' => 'default', 
                    'id' => $this->default_bot_id, 
                    'custom' => 0];
        }
        
        public function chatShortcode($atts){
            $atts = empty( $atts ) ? [] : $atts;
            $atts = apply_filters( 'oc3daig_chatbot_params', $atts );
            $bot_id = isset($atts['bot_id'])?$atts['bot_id']:'default';
            $resolved_bot = $this->getBotInfo( $bot_id );
            
            if ( isset( $resolved_bot['error'] ) ) {
              return $resolved_bot['error'];
            }
            $data_parameters = $this->getFrontParams($resolved_bot);
            $access_for_guest = isset($data_parameters[OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'access_for_guests'])?(int)$data_parameters[OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'access_for_guests']:1;
            $user_id = get_current_user_id();
            $content = '';
            if($access_for_guest < 1 && $user_id < 1){
                return $content;
            }
            $data_par = htmlspecialchars( json_encode( $data_parameters ), ENT_QUOTES, 'UTF-8' );
                
            switch($resolved_bot['botmode']){
                case 'classic':
                        if($resolved_bot['provider'] == 'chatgpt' && $resolved_bot['view'] == 'default'){
                            $content = $this->showClassicChatGPTDefaultHistory($data_par,$data_parameters);
                        }
                    break;
                default:
                        if($resolved_bot['chatgpt'] && $resolved_bot['view'] == 'default'){
                            $content = $this->showClassicChatGPTDefaultHistory($data_par,$data_parameters);
                        }
            }
		
		return $content;

        }
        
                
        public function showClassicChatGPTDefaultHistory($data_par,$data_parameters){
            if (!class_exists('Oc3dAig_ChatBotClassicView')) {
                                $classview_path = OC3DAIG_PATH . "/views/frontend/chatbot/ChatBotClassicHistoryView.php";
                                include_once $classview_path;
                            }
                            $this->view = new Oc3dAig_ChatBotClassicHistoryView();
                return    $this->view->render($data_par,$data_parameters);
        }
        
        function getNonce() {

		if ( isset( $this->nonce ) ) {
			return $this->nonce;
		}
		$this->nonce = wp_create_nonce( 'wp_rest' );
		return $this->nonce;
	}
       
        

        
        public function restApiInit(){
            
            register_rest_route( $this->namespace, $this->bot_url, array(
			'methods' => 'POST',
			'callback' => [ $this, 'restChat' ],
			'permission_callback' => array( $this, 'checkRestNonce' )
		) );
            
        }
        
        public function restChat($request){
            
            $params = $request->get_json_params();
            $filtered_params = $this->filterParameters($params);    
            $new_message = $filtered_params['message'];
            if ( !$this->basicsSecurityCheck( $filtered_params['bot_id'],  $new_message )) {
			return new WP_REST_Response( [ 
				'success' => false, 
				'message' => apply_filters( 'oc3daig_exception', 'Sorry, your query has been rejected.' )
			], 403 );
            }

            
	try {
			
                $data = $this->chatSubmitRequest( $new_message,  $filtered_params);
		return new WP_REST_Response( [
				'success' => true,
				'reply' => $data['reply'],
				'images' => $data['images'],
				'usage' => $data['usage']
			], 200 );
		}
	catch ( Exception $e ) {
			$message = apply_filters( 'oc3daig_exception', $e->getMessage() );
			return new WP_REST_Response( [ 
				'success' => false, 
				'message' => $message
			], 500 );
		}
        }
        
        public function filterParameters($params) {
            $filtered_pars = [];
            
            if (isset($params['messages']) && is_array($params['messages'])) {
                    $filtered_pars['messages'] = $this->filterMessages($params['messages']);

            }
            $filtered_pars['message'] = isset($params['message'])?sanitize_text_field(trim($params['message'])):'';
            $filtered_pars['bot_id'] = isset($params['bot_id'])?sanitize_text_field($params['bot_id']):$this->default_bot_id;
            
            return $filtered_pars;
        }

        public function filterMessages($messages) {

            foreach ($messages as $row) {
                if ($row['role'] == 'user') {
                    $filtered_messages[] = ['role' => 'user', 'content' => sanitize_text_field($row['content'])];
                } elseif ($row['role'] == 'assistant') {
                    $filtered_messages[] = ['role' => 'assistant', 'content' => sanitize_text_field($row['content'])];
                }
            }

            return $filtered_messages;
        }

        public function basicsSecurityCheck( $botId,  $new_message ) {
            
		if ( empty( $new_message ) ) {
			error_log("S2BAi Assistant: Message was empty.");
			return false;
		}
		
		$length = strlen( $new_message );
		if ( $length < 1 || $length > ( 4096 * 16 ) ) {
			error_log("S2BAi Assistant: Message was too short or too long.");
			return false;
		}
		return true;
	}
    
        public function getChatbot($bot_id) {

            $filtered_pars = [];
            $params = $this->getBotOptions($bot_id);

            $filtered_pars['botmode'] = isset($params['botmode']) ? sanitize_text_field($params['botmode']) : 'classic';        
            $filtered_pars['provider'] = isset($params['provider']) ? sanitize_text_field($params['provider']) : 'chatgpt';        
            $filtered_pars['view'] = isset($params['view']) ? sanitize_text_field($params['view']) : 'default';        
            $filtered_pars['textInputMaxLength'] = isset($params['textInputMaxLength']) ?  (int) $params['textInputMaxLength'] : 1024;
            $filtered_pars['custom'] = isset($params['custom']) ?  (int) $params['custom'] : 0;       
            
            $filtered_pars['system'] = isset($params['system']) ? sanitize_text_field($params['system']) : '';
            //
            $filtered_pars['instruction'] = isset($params['instruction']) ? sanitize_text_field($params['instruction']) : '';
            
            $filtered_pars['max_tokens'] = isset($params['max_tokens']) ? (int) $params['max_tokens'] : 1024;
            $filtered_pars['temperature'] = isset($params['temperature']) && is_numeric($params['temperature']) ? floatval($params['temperature']) : 0.7;
            $filtered_pars['top_p'] = isset($params['top_p']) && is_numeric($params['top_p']) ? floatval($params['top_p']) : 1;
            $filtered_pars['presence_penalty'] = isset($params['presence_penalty']) && is_numeric($params['presence_penalty']) ? floatval($params['presence_penalty']) : 0;
            $filtered_pars['frequency_penalty'] = isset($params['frequency_penalty']) && is_numeric($params['frequency_penalty']) ? floatval($params['frequency_penalty']) : 0;
            $filtered_pars['stream'] = isset($params['stream']) && $params['stream'] > 0? true:false;
            $model = isset($params['model']) ? sanitize_text_field($params['model']) : '';
            $exp_models = Oc3dAig_Utils::getExpertModelTexts();
            $edit_models = Oc3dAig_Utils::getEditModelTexts();
            if (!in_array($model, $exp_models) || !in_array($model, $edit_models)) {
                if (count($exp_models) > 0) {
                    $model = $exp_models[0];
                } else {
                    $model = 'gpt-3.5-turbo';
                }
            }
            $filtered_pars['model'] = $model;
            return $filtered_pars;
            
        }

        public function getBotOptions($bot_id){
        return  [
                    'botmode' => 'classic', 
                    'provider' => 'chatgpt',
                    'view' => 'default', 
                    'id' => 0, 
                    'custom' => 0,
                    'textInputMaxLength'=>1350,
                    'stream' => false
                    
                ];
    }
        
    public function chatSubmitRequest( $new_message,  $params = [] ) {
		try {

                        $bot_id = $params['bot_id'];
                        $chatbotinfo = $this->getChatbot( $bot_id );

			if ( !$chatbotinfo ) {
				error_log("S2baia: No chatbot was found for this query.");
				throw new Exception( 'Sorry, your query has been rejected.' );
			}

			$textInputMaxLength = $chatbotinfo['textInputMaxLength'] ?? null;
			if ( $textInputMaxLength && strlen( $new_message ) > (int)$textInputMaxLength ) {
				throw new Exception( 'Sorry, your query has been rejected.' );
			}
			
                        $stream = $chatbotinfo['stream'];
                        
			$mode = $chatbotinfo['botmode'] ?? 'classic';
                        $provider = $chatbotinfo['provider'] ?? 'chatgpt';
			
			$newParams = [];
                        $messages = [];
			foreach ( $chatbotinfo as $key => $value ) {
					$newParams[$key] = $value;
			}
			foreach ( $params as $key => $value ) {
                                if(isset($params['messages']) && is_array($params['messages'])){
                                    $messages = $params['messages'];
                                    continue;
                                }
					$newParams[$key] = $value;
			}
			switch($mode){
                            case 'classic':
                                if($provider == 'chatgpt'){
                                   $reply =  $this->classicChatGpt2Request($messages, $newParams);
                                }
                                
                                break;
                            default:
                                $reply =  $this->classicChatGpt2Request($messages, $newParams);
                        }
			
			$rawText = $reply['msg'];

			$restRes = [
				'reply' => $rawText,
				'images' =>  null,
				'usage' => '',
                                'code' => $reply['result']
			];

			
			return $restRes;

		}
		catch ( Exception $e ) {
			$message = $e->getMessage() ;
			if ( $stream ) { 
				$this->streamPush( [ 'type' => 'error', 'data' => $message ] );
				die();
			}
			else {
				throw $e;
			}
		}
	}
        
        public function streamPush( $data ) {
		$out = "data: " . json_encode( $data );
		echo $out;
		echo "\n\n";
		if (ob_get_level() > 0) {
			ob_end_flush();
		}
		flush();
	}
        
	public function classicChatGptRequest($message, $params){
            
            if (!class_exists('Oc3dAig_AiRequest')) {
                require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
            }
            $data = [];
            $data['model'] = $params['model'];
            
            $data['system'] = $params['system'];
            
            $data['max_tokens'] = $params['max_tokens'];

            $data['temperature'] = $params['temperature'];
            $data['instruction'] = $params['instruction'];

            $data['text'] = sanitize_text_field($message);

            $res = Oc3dAig_AiRequest::sendChatGptEdit($data);

            if ($res[0] == 1) {
                $response = json_decode($res[1]);
                if (Oc3dAig_AiRequest::testChatGptResponse($response)) {
                    $msg = Oc3dAig_AiRequest::getChatGptResponseEditMessage($response);
                    $r['result'] = 200;
                    $r['msg'] = wp_kses($msg, Oc3dAig_Utils::getInstructionAllowedTags());
                    return $r;
                }
            } else {//if we have an error
                if(is_array($res) && count($res) > 0 && is_string($res[1])){
                    $response = $res[1];
                }else{
                    $response = is_array($res) && count($res) > 0 && is_array($res[1]) && count($res[1]) > 0 ? esc_html__('Error', 'oc3d-ai-aiassistant') . ' ' . $res[1][0] . ' ' . $res[1][1] : esc_html__('Unknown error', 'oc3d-ai-aiassistant');
                }
                $r['result'] = 404;
                $r['msg'] = wp_kses($response, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            if (isset($response->error) && isset($response->error->message)) {//???
                $r['result'] = 404;
                $r['msg'] = wp_kses($response->error->message, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            return $r;
        }
        
        public function classicChatGpt2Request($inputmessages, $params){
            
            if (!class_exists('Oc3dAig_AiRequest')) {
                require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
            }
            $data = [];
            $data['model'] = sanitize_text_field($params['model']);
            
            $data['system'] = sanitize_text_field($params['system']);
            
            $data['max_tokens'] = (int)$params['max_tokens'];

            $data['temperature'] = is_numeric($params['temperature']) ? floatval($params['temperature']) : 0.7;
            $data['top_p'] = is_numeric($params['top_p']) ? floatval($params['top_p']) : 1;

            $data['presence_penalty'] = is_numeric($params['presence_penalty']) ? floatval($params['presence_penalty']) : 0;
            $data['frequency_penalty'] = is_numeric($params['frequency_penalty']) ? floatval($params['frequency_penalty']) : 0;



                $messages = [];

                    foreach ($inputmessages as $row) {
                        if ($row['role'] == 'user') {
                            $messages[] = ['role' => 'user', 'content' => sanitize_text_field($row['content'])];
                        } elseif ($row['role'] == 'assistant') {
                            $messages[] = ['role' => 'assistant', 'content' => sanitize_text_field($row['content'])];
                        }
                    }
              
                    $data['messages'] = $messages;
            $res = Oc3dAig_AiRequest::sendChatGptCompletion($data);
            if ($res[0] == 1) {
                $response = json_decode($res[1]);
                if (Oc3dAig_AiRequest::testChatGptResponse($response)) {
                    $msg = Oc3dAig_AiRequest::getChatGptResponseEditMessage($response);
                    $r['result'] = 200;
                    $r['msg'] = wp_kses($msg, Oc3dAig_Utils::getInstructionAllowedTags());
                    return $r;
                }
            } else {
                if(is_array($res) && count($res) > 0 && is_string($res[1])){
                    $response = $res[1];
                }else{
                    $response = is_array($res) && count($res) > 0 && is_array($res[1]) && count($res[1]) > 0 ? esc_html__('Error', 'oc3d-ai-aiassistant') . ' ' . $res[1][0] . ' ' . $res[1][1] : esc_html__('Unknown error', 'oc3d-ai-aiassistant');
                }
                $r['result'] = 404;
                $r['msg'] = wp_kses($response, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            if (isset($response->error) && isset($response->error->message)) {
                $r['result'] = 404;
                $r['msg'] = wp_kses($response->error->message, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            

            
            return $r;
        }
        
        
        public function checkRestNonce( $request ) {
            $nonce = $request->get_header( 'X-WP-Nonce' );
            $rest_nonce = wp_verify_nonce( $nonce, 'wp_rest' );
            return apply_filters( 'oc3daig_rest_authorized', $rest_nonce, $request );
          }
        
	
    }

}
