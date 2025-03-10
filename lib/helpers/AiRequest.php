<?php

if (!class_exists('Oc3dAig_AiRequest')) {

    class Oc3dAig_AiRequest {

        public static $gpt_key = '';
        public static $model = 'text-davinci-003';
        public static $files_api_url = 'https://api.openai.com/v1/files';
        public static $chat_completion_endpoint = 'https://api.openai.com/v1/chat/completions';
        public static $assistant_api = 'https://api.openai.com/v1/assistants';
        public static $thread_url = "https://api.openai.com/v1/threads";
        public static $http_client = 'curl';
        
        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
        @param $data     array  received from Edit&Extend metabox form
         * see https://platform.openai.com/docs/api-reference/chat/create 
         Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */
        
        public static function sendChatGptEdit($data) {

            $model = $data['model'];
            $temperature = $data['temperature'];
            $instruction = $data['instruction'];
            $max_tokens = $data['max_tokens'];
            $body = ["model" => $model, "temperature" => $temperature, "max_tokens" => $max_tokens,
                "messages" => [
                    ["role" => "system", "content" => "Help to change user\'s text according to such instruction:" . $instruction],
                    ["role" => "user", "content" => $data['text']]
                ]
            ];
            $body_str = json_encode($body);
            $result = self::sendChatGptRequest($body_str);
            //var_dump($result);
            return $result;
        }
        
        /* prepares chat completion chatGPT API request and calls :sendChatGptRequest
        @param $data     array  received from client side form
         * see https://platform.openai.com/docs/api-reference/chat/create 
         Returns the array in format  [error_code,response] see sendChatGptRequest
         *          */
        
        public static function sendChatGptCompletion($data) {

            $model = $data['model'];
            $temperature = $data['temperature'];

            $max_tokens = $data['max_tokens'];
            $msgs = [["role" => "system", "content" => $data['system']]];
            if (isset($data["messages"])) {
                foreach ($data["messages"] as $msg) {
                    $msgs[] = ['role' => $msg['role'], 'content' => $msg['content']];
                }
            }
            $body = ["model" => $model, "temperature" => $temperature, "max_tokens" => $max_tokens,
                "top_p" => $data['top_p'], "presence_penalty" => $data['presence_penalty'],
                "frequency_penalty" => $data['frequency_penalty'],
                "messages" => $msgs
            ];
            $body_str = json_encode($body);
            $result = self::sendChatGptRequest($body_str);
            //var_dump($result);
            return $result;
        }
        
        
        /* sends request to chatGPT API and returns response in format:       [error_code,response]
        @param $body_str     string  that is json encoded array in format 
         * defined https://platform.openai.com/docs/api-reference/chat/create */
        
        public static function sendChatGptRequest($body_str = '') {

            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', ''); //oc3daig_open_ai_gpt_key
            }//oc3daig_open_ai_gpt_key
            $headers = [
                "Content-Type: application/json",
                "Authorization: Bearer " . self::$gpt_key
            ];
            global $wp_version;
            $response_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'response_timeout', 120);
            $connection_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'connection_timeout', 10);
            $ch = curl_init(self::$chat_completion_endpoint);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body_str);
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($response_timeout));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connection_timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_REFERER, self::$chat_completion_endpoint);
            curl_setopt($ch, CURLOPT_USERAGENT, $wp_version . '; ' . home_url());
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_ENCODING, '');
            curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $response = curl_exec($ch);
            if ($response === FALSE) {

                $err = curl_error($ch);
                $errno = curl_errno($ch);
                return array(0, [$errno, $err]);
            }
            return [1, $response];
        }
        
        /* sends GET request to chatGPT API . It is used for example when getting all list of models
        @param $url     string  that is url of API Endpoint
        */
        
        public static function getFromUrl($url) {

            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $apiKey = self::$gpt_key; //

            $headers = [
                "Authorization: Bearer $apiKey"
            ];
            $response_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'response_timeout', 120);
            $connection_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'connection_timeout', 10);
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, ceil($response_timeout));
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connection_timeout);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Use CURLOPT_HTTPHEADER to set request headers
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Disable SSL Cert checks
            curl_setopt($ch, CURLOPT_ENCODING, ""); // Set the Accept-Encoding: gzip header

            $response = curl_exec($ch);
            if ($response === FALSE) {

                $err = curl_error($ch);
                $errno = curl_errno($ch);
                return array(0, [$errno, $err]);
            }
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpcode != 200) {
                return array(0, $response);
            }
            return [1, $response];
        }
        
        
        /* tests if response from ChatGPT API has correct fromat and contains all respected fields
         * $response - json decoded response from ChatGPT API
         *  */
        
        public static function testChatGptResponse($response) {
            if (is_object($response) && isset($response->choices) && is_array($response->choices) && count($response->choices) > 0) {
                $choice = $response->choices[0];
                if (is_object($choice) && isset($choice->message) && is_object($choice->message) && isset($choice->message->content)) {
                    return true;
                }
            }
            return false;
        }
        
        
        /*
        method parses response from ChatGPT chat completion API and gets message
         *  $response - json decoded response from ChatGPT API
         *          */
        public static function getChatGptResponseEditMessage($response) {

            if (is_object($response) && isset($response->choices) && is_array($response->choices) && count($response->choices) > 0) {
                $choice = $response->choices[0];
                if (is_object($choice) && isset($choice->message) && is_object($choice->message) && isset($choice->message->content)) {
                    $resp_text = $choice->message->content;
                    return $resp_text;
                }
            }
            return '';
        }
        
        public static function uploadFile3($file_path) {
            //$file_path = '/path/to/knowledge.pdf';
            $res = ['code'=>404,'error_msg'=>'','body'=>''];
            $request_url = 'https://api.openai.com/v1/files';
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $headers = array(
                'Authorization' => 'Bearer ' . self::$gpt_key
            );
            //$file_contents = file_get_contents($file_path); // Read the file contents
            //$file_name = basename($file_path); // Get the file name
            $post_data = array(
                'purpose' => 'assistants',
                'stream' => true,
                'filename' =>$file_path
            );
            $response = wp_remote_post(
                $request_url,
                array(
                    'headers' => $headers,
                    'body' => $post_data, // Send file contents as raw data
                    'timeout' =>120,
                )
            );

                        

            if (is_wp_error($response)) {
                $res['error_msg'] = $response->get_error_message();
                $res['code'] = 500;
                
            } else {
                $res['code']  = wp_remote_retrieve_response_code($response);
                $res['body'] = wp_remote_retrieve_body($response);
                // Handle the response body
            }
            return $res;
        }
        
        public static function uploadFile2($file_path) {
            //$file_path = '/path/to/knowledge.pdf';
            $res = ['code'=>404,'error_msg'=>'','body'=>''];
            $request_url = 'https://api.openai.com/v1/files';
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $file_contents = file_get_contents($file_path);

            $headers = array(
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'Content-Type' => 'multipart/form-data',
            );

            $body = array(
                'purpose' => 'assistants',
                'file' => $file_contents,
            );

            $args = array(
                'headers' => $headers,
                'body' => $body,
                'method' => 'POST',
            );

            $response = wp_remote_request('https://api.openai.com/v1/files', $args);


            if (is_wp_error($response)) {
                $res['error_msg'] = $response->get_error_message();
                $res['code'] = 500;
                
            } else {
                $res['code']  = wp_remote_retrieve_response_code($response);
                $res['body'] = wp_remote_retrieve_body($response);
                // Handle the response body
            }
            return $res;
        }
        

        
        public static function uploadFile($file_path) {

            $res = ['code'=>404,'error_msg'=>'','id'=>'','filename' =>$file_path];
            $request_url = self::$files_api_url;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $request_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . self::$gpt_key));

            $post_fields = array(
                'purpose' => 'assistants',
                'file' => new CURLFile($file_path)
            );
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

     
            $response = curl_exec($ch);
            $http_status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            if (curl_errno($ch)) {
                $res = ['code'=>$http_status,
                    'error_msg'=>'Error:' . curl_error($ch),
                    'id'=>'',
                    'filename' =>$file_path];
                
            } else {
                $response = json_decode($response, true);
                if ($http_status != 200 || isset($response['error'])) {
                    $error_message = $response['error']['message'] ?? 'Unknown error.';
                    $res = array(
                        'error_msg' => $error_message,
                        'code' => $http_status,
                        'id'=>'',
                        'filename' =>$file_path
                    );
                } else {
                    
                    //unlink($file_path); //

                    $res = array(
                        'code' => $http_status,
                        'filename' => $file_path,
                        'id' => $response['id'],
                        'error_msg' => ""
                    );

                    // DIAG - Diagnostic - Ver 1.9.2
                    // back_trace( 'NOTICE', 'responses', print_r($responses, true));

                }
            }

            curl_close($ch);
            return $res;
        }
        
        public static function createAssistantRetrieval($options){
            
            $options['assistant_id'] = '';
            $res = self::updateAssistantRetrieval($options);
            return $res;
            
        }
        
        public static function createAssistantRetrievalV2($options){
            
            $res = ['code'=>404,'body'=>'','error_msg' => '','id'=>'','model' =>''
            ,'created_at' =>'','instruction' =>'','name' =>''];
            if(isset($options['assistant_id']) && strlen($options['assistant_id']) > 0){//update
                $assistant_id = $options['assistant_id'];
                $request_url = self::$assistant_api.'/'.$assistant_id;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'tools' => array(
                        array(
                            'type' => 'retrieval'
                        )
                    ),
                    'model' => $options['model']
                );
            }else{//create
                $request_url = self::$assistant_api;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'name' => $options['assistant_name'],
                    'tools' => array(
                        array(
                            'type' => 'file_search'
                        )
                    ),
                    'model' => $options['model'],
                    'tool_resources'=>[
                        'file_search'=>[
                            'vector_stores'=>[[
                                'file_ids' => array($options['file_id'])
                                ]]
                            ]
                        ],
                    
                );
                
            }
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'OpenAI-Beta' => 'assistants=v2'
            );
            
            //$options['file_id']  $options['model'] = 'gpt-4-turbo' $options['assistant_name'] = 'Bubble.io Tutor' $options['instructions'] ='You are a website support chatbot. Use your knowledge base to best respond to customer queries.'
            $response = wp_remote_request(
                $request_url,
                array(
                    'method' => 'POST',
                    'headers' => $headers,
                    'body' => wp_json_encode($post_data),
                    'timeout'=>120
                )
            );

            // Handle the response as needed
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                $res['error_msg']  = $error_message;
                $res['code']  = 500;
            } else {
                $res['code']  =  wp_remote_retrieve_response_code( $response );
                $res['body']  =  wp_remote_retrieve_body( $response );
                if(strlen($res['body']) > 0){
                    $res_obj = json_decode($res['body'], true);
                    if(is_array($res_obj) && isset($res_obj['id'])){
                        $res['id'] = $res_obj['id'];
                        $res['created_at'] = $res_obj['created_at'];
                        $res['model'] = $options['model'];
                        $res['instruction'] = $options['instructions'];
                        $res['name'] = $options['assistant_name'];
                        $res['file_id'] = $options['file_id'];
                        $res['vector_store_ids'] = '';
                        if(is_array($res_obj['tool_resources']) && count($res_obj['tool_resources']) > 0){
                            $t_r = $res_obj['tool_resources'];
                            if(is_array($t_r['file_search']) && count($t_r['file_search']) > 0){
                                $f_s = $t_r['file_search'];
                                if(is_array($f_s['vector_store_ids']) && count($f_s['vector_store_ids']) > 0){
                                    $res['vector_store_ids'] = $f_s['vector_store_ids'][0];
                                }
                            }
                        }
                    }
                }
                // Handle the response body
            }
            return $res;
            
        }
        
        public static function updateAssistantRetrievalV2($options){

            $res = ['code'=>404,'body'=>'','error_msg' => '','id'=>'','model' =>''
            ,'created_at' =>'','instruction' =>'','name' =>''];
            if(isset($options['assistant_id']) && strlen($options['assistant_id']) > 0){//update
                $assistant_id = $options['assistant_id'];
                $request_url = self::$assistant_api.'/'.$assistant_id;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'tools' => array(
                        array(
                            'type' => 'file_search'
                        )
                    ),
                    'model' => $options['model']
                );
            }else{//create
                $request_url = self::$assistant_api;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'name' => $options['assistant_name'],
                    'tools' => array(
                        array(
                            'type' => 'file_search'
                        )
                    ),
                    'model' => $options['model'],
                    'tool_resources'=>[
                        'file_search'=>[
                            'vector_stores'=>[[
                                'file_ids' => array($options['file_id'])
                                ]]
                            ]
                        ],
                );
                
            }
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'OpenAI-Beta' => 'assistants=v2'
            );
            
            //$options['file_id']  $options['model'] = 'gpt-4-turbo' $options['assistant_name'] = 'Bubble.io Tutor' $options['instructions'] ='You are a website support chatbot. Use your knowledge base to best respond to customer queries.'
            $response = wp_remote_request(
                $request_url,
                array(
                    'method' => 'POST',
                    'headers' => $headers,
                    'body' => wp_json_encode($post_data),
                    'timeout'=>120
                )
            );

            // Handle the response as needed
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                $res['error_msg']  = $error_message;
                $res['code']  = 500;
            } else {
                $res['code']  =  wp_remote_retrieve_response_code( $response );
                $res['body']  =  wp_remote_retrieve_body( $response );
                if(strlen($res['body']) > 0){
                    $res_obj = json_decode($res['body'], true);
                    if(is_array($res_obj) && isset($res_obj['id'])){
                        $res['id'] = $res_obj['id'];
                        $res['created_at'] = $res_obj['created_at'];
                        $res['model'] = $options['model'];
                        $res['instruction'] = $options['instructions'];
                        $res['name'] = $options['assistant_name'];
                        $res['file_id'] = $options['file_id'];
                    }
                }
                // Handle the response body
            }
            return $res;
        }
        
        public static function updateAssistantRetrieval($options){

            $res = ['code'=>404,'body'=>'','error_msg' => '','id'=>'','model' =>''
            ,'created_at' =>'','instruction' =>'','name' =>''];
            if(isset($options['assistant_id']) && strlen($options['assistant_id']) > 0){//update
                $assistant_id = $options['assistant_id'];
                $request_url = self::$assistant_api.'/'.$assistant_id;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'tools' => array(
                        array(
                            'type' => 'retrieval'
                        )
                    ),
                    'model' => $options['model']
                );
            }else{//create
                $request_url = self::$assistant_api;
                $post_data = array(
                    'instructions' => $options['instructions'] ,
                    'name' => $options['assistant_name'],
                    'tools' => array(
                        array(
                            'type' => 'retrieval'
                        )
                    ),
                    'model' => $options['model'],
                    'file_ids' => array($options['file_id'])
                );
                
            }
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'OpenAI-Beta' => 'assistants=v1'
            );
            
            //$options['file_id']  $options['model'] = 'gpt-4-turbo' $options['assistant_name'] = 'Bubble.io Tutor' $options['instructions'] ='You are a website support chatbot. Use your knowledge base to best respond to customer queries.'
            $response = wp_remote_request(
                $request_url,
                array(
                    'method' => 'POST',
                    'headers' => $headers,
                    'body' => wp_json_encode($post_data),
                    'timeout'=>120
                )
            );

            // Handle the response as needed
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                $res['error_msg']  = $error_message;
                $res['code']  = 500;
            } else {
                $res['code']  =  wp_remote_retrieve_response_code( $response );
                $res['body']  =  wp_remote_retrieve_body( $response );
                if(strlen($res['body']) > 0){
                    $res_obj = json_decode($res['body'], true);
                    if(is_array($res_obj) && isset($res_obj['id'])){
                        $res['id'] = $res_obj['id'];
                        $res['created_at'] = $res_obj['created_at'];
                        $res['model'] = $options['model'];
                        $res['instruction'] = $options['instructions'];
                        $res['name'] = $options['assistant_name'];
                        $res['file_id'] = $options['file_id'];
                    }
                }
                // Handle the response body
            }
            return $res;
        }
        
        
        public static function removeAssistantV2($assistant_id = ''){
            
            
            $request_url = self::$assistant_api.'/'.$assistant_id;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'OpenAI-Beta' => 'assistants=v2'
            );
            
            //$options['file_id']  $options['model'] = 'gpt-4-turbo' $options['assistant_name'] = 'Bubble.io Tutor' $options['instructions'] ='You are a website support chatbot. Use your knowledge base to best respond to customer queries.'
            $response = wp_remote_request(
                $request_url,
                array(
                    'method' => 'DELETE',
                    'headers' => $headers,
                    'timeout'=>120
                )
            );

            // Handle the response as needed
            if ( is_wp_error( $response ) ) {
                $error_message = $response->get_error_message();
                $res['error_msg']  = $error_message;
                $res['code']  = 500;
            } else {
                $res['code']  =  wp_remote_retrieve_response_code( $response );
                $res['body']  =  wp_remote_retrieve_body( $response );
                if(strlen($res['body']) > 0){
                    $res_obj = json_decode($res['body'], true);
                    if(is_array($res_obj) && isset($res_obj['id']) && isset($res_obj['deleted']) && $res_obj['deleted'] == true){
                        return true;
                    }
                }
                // Handle the response body
            }
            return false;
        }
        
        public static function sendHttpRequest($url, $options) {

            $http_client = self::$http_client;
            switch($http_client){
                case 'curl':
                    if (!class_exists('Oc3dAig_CurlClient')) {
                        require_once OC3DAIG_PATH . '/lib/helpers/CurlClient.php';
                    }
                    return Oc3dAig_CurlClient::sendCurlRequest($url, $options);
                    break;
                    
                default:
                    if (!class_exists('Oc3dAig_CurlClient')) {
                        require_once OC3DAIG_PATH . '/lib/helpers/CurlClient.php';
                    }
                    return Oc3dAig_CurlClient::sendCurlRequest($url, $options);
                    
            }
        }
        
        public static function sendCurlRequest($url, $options) {

            $curl = curl_init($url);

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            
            if ($options) {
                $stream_options = stream_context_get_options($options);
                if (isset($stream_options['http']['method'])) {
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $stream_options['http']['method']);
                }
                if (isset($stream_options['http']['header'])) {
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $stream_options['http']['header']);
                }
                if (isset($stream_options['http']['content'])) {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $stream_options['http']['content']);
                }
            }

            $response = curl_exec($curl);
            curl_close($curl);

            return $response;
        }
        
        public static function createThread($user_msg = '') {

            $url = self::$thread_url;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $headers = array(
                "Content-Type: application/json",
                "OpenAI-Beta: assistants=v2",
                "Authorization: Bearer " . self::$gpt_key
            );
            
            if(strlen($user_msg) > 0){
                $data = array(
                    "role" => "user",
                    "content" => $user_msg
                );
                $options = stream_context_create(array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => $headers,
                        'ignore_errors' => true,
                        'content' => $data
                    )
                ));
            }else{
                $options = stream_context_create(array(
                    'http' => array(
                        'method' => 'POST',
                        'header' => $headers,
                        'ignore_errors' => true 
                    )
                ));
            }
            $response = self::sendHttpRequest($url, $options);

            return json_decode($response, true);
        }
        
        public static function addAssistantMessage($thread_id, $user_msg) {
            $url = self::$thread_url."/".$thread_id."/messages";
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $headers = array(
                "Content-Type: application/json",
                "OpenAI-Beta: assistants=v2",
                "Authorization: Bearer " . self::$gpt_key
            );
            $data = array(
                "role" => "user",
                "content" => $user_msg
            );

            $options = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => json_encode($data)
            )));
            $response = self::sendHttpRequest($url, $options);

            return json_decode($response, true);

        }
        
        
        public static function runAssistant($thread_id, $assistant_id, $instruction) {
            
            $url = self::$thread_url."/".$thread_id."/runs";
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                "Content-Type: application/json",
                "OpenAI-Beta: assistants=v2",
                "Authorization: Bearer " . self::$gpt_key
            );
            if(strlen($instruction) > 1){
                $data = array(
                    "assistant_id" => $assistant_id,
                    "instructions" => $instruction
                );
            }else{
                $data = array(
                    "assistant_id" => $assistant_id
                );
            }
            $options = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => json_encode($data),
                    'ignore_errors' => true // This allows the function to proceed even if there's an HTTP error
                )
            ));

            $response = self::sendHttpRequest($url, $options);

            if ($response === FALSE) {
                
                return "Error: Unable to fetch response.";
            }

            if (http_response_code() != 200) {
                // back_trace( 'ERROR', 'HTTP response code: ' . print_r(http_response_code()));
                return "Error: HTTP response code " . http_response_code();
            }

            return json_decode($response, true);
        }
        
        
        public static function runAssistantStream($thread_id, $assistant_id, $instruction) {
            
            $url = self::$thread_url."/".$thread_id."/runs";
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            
            $headers = array(
                "Content-Type: application/json",
                "OpenAI-Beta: assistants=v2",
                "Authorization: Bearer " . self::$gpt_key
            );
            if(strlen($instruction) > 1){
                $data = array(
                    "assistant_id" => $assistant_id,
                    "instructions" => $instruction,
                    "stream" => true
                );
            }else{
                $data = array(
                    "assistant_id" => $assistant_id,
                    "stream" => true
                );
            }
            $options = stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => json_encode($data),
                    'ignore_errors' => true // This allows the function to proceed even if there's an HTTP error
                )
            ));

            if (!class_exists('Oc3dAig_CurlClient')) {
                        require_once OC3DAIG_PATH . '/lib/helpers/CurlClient.php';
                    }
            $response = Oc3dAig_CurlClient::openStream($url, $options);

            if ($response === FALSE) {
                
                return "Error: Unable to fetch response.";
            }

            if (http_response_code() != 200) {
                // back_trace( 'ERROR', 'HTTP response code: ' . print_r(http_response_code()));
                return "Error: HTTP response code " . http_response_code();
            }

            return json_decode($response, true);
        }
        

        // $url = "https://api.openai.com/v2/threads/" . $thread_id . "/messages";
        public static function listAssistantMessages($thread_id) {
            $url = self::$thread_url . "/" . $thread_id . "/messages";
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }

            
            $url = get_threads_api_url() . '/' . $thread_id . '/messages';
            $headers = array(
                "Content-Type: application/json",
                "OpenAI-Beta: assistants=v2",
                "Authorization: Bearer " . self::$gpt_key
            );

            $options = stream_context_create(array(
                'http' => array(
                    'method' => 'GET',
                    'header' => $headers
            )));
            $response = self::sendHttpRequest($url, $options);

            return json_decode($response, true);
        }
        
        

        public static function getRunStepsStatus($thread_id, $run_id) {
            $status = false;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }

                // $url = "https://api.openai.com/v2/threads/" . $thread_id . "/runs/" . $runId . "/steps";
                $url = self::$thread_url . '/' . $thread_id . '/runs/' . $run_id . '/steps';
                $headers = array(
                    "Content-Type: application/json",
                    "OpenAI-Beta: assistants=v2",
                    "Authorization: Bearer " . self::$gpt_key
                );

                $options = stream_context_create(array(
                    'http' => array(
                        'method' => 'GET',
                        'header' => $headers
                )));
                $response = self::sendHttpRequest($url, $options);

                $responseArray = json_decode($response, true);

                if (array_key_exists("data", $responseArray) && !is_null($responseArray["data"])) {
                    $data = $responseArray["data"];
                } else {
                    // DIAG - Handle error here
                    $status = "failed";
                    // DIAG - Diagnostics
                    // back_trace( 'ERROR', "Error - GPT Assistant - Step 7.");
                    exit;
                }

                foreach ($data as $item) {
                    if ($item["status"] == "completed") {
                        $status = true;
                        break;
                    }
                }

                return $status;
            
        }
    }

}