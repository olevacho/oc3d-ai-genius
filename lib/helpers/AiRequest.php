<?php

if (!class_exists('Oc3dAig_AiRequest')) {

    class Oc3dAig_AiRequest {

        public static $gpt_key = '';
        public static $model = 'text-davinci-003';

        public static $chat_completion_endpoint = 'https://api.openai.com/v1/chat/completions';
        
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
    }

}