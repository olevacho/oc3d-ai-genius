<?php

if (!class_exists('Oc3dAig_AiRequest')) {

    class Oc3dAig_AiRequest {

        public static $gpt_key = '';
        public static $model = 'text-davinci-003';
        public static $files_api_url = 'https://api.openai.com/v1/files';
        public static $chat_completion_endpoint = 'https://api.openai.com/v1/chat/completions';
        public static $assistant_api = 'https://api.openai.com/v1/assistants';
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
        
        public static function createAssistant($options){
            $res = ['code'=>404,'body'=>'','error_msg' => ''];
            $request_url = self::$assistant_api;
            if (strlen(self::$gpt_key) == 0) {
                self::$gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            }
            $headers = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . self::$gpt_key,
                'OpenAI-Beta' => 'assistants=v1'
            );
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
            //$options['file_id']  $options['model'] = 'gpt-4-turbo' $options['assistant_name'] = 'Bubble.io Tutor' $options['instructions'] ='You are a website support chatbot. Use your knowledge base to best respond to customer queries.'
            $response = wp_remote_request(
                $request_url,
                array(
                    'method' => 'POST',
                    'headers' => $headers,
                    'body' => wp_json_encode($post_data)
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
                // Handle the response body
            }
            return $res;
        }
    }

}