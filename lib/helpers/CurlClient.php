<?php

if (!class_exists('Oc3dAig_CurlClient')) {

    class Oc3dAig_CurlClient {
        public static $stream_answer = '';
        
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

        public static function // Function to handle incoming SSE messages
            handleEvent($data) {
            // Process the event data
            if(strlen($data) > 0){
                $obj = json_decode($data, true);
                return $obj;
            }
            //echo "Received event: {$data}\n";
            return $data;
        }

        
        public static function openStream($url, $options) {


                    // URL of the SSE endpoint
                    //$url = 'http://example.com/sse_endpoint';

                    // Initialize cURL session
                    $ch = curl_init();
                    $buffer = self::$stream_answer; // Buffer to store incoming data
                    // Set cURL options
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 0); // Set timeout to 0 for persistent connection
                    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
                    //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: text/event-stream'));
                    if ($options) {
                        $stream_options = stream_context_get_options($options);
                        if (isset($stream_options['http']['method'])) {
                            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $stream_options['http']['method']);
                        }
                        if (isset($stream_options['http']['header'])) {
                            $headers = $stream_options['http']['header'];
                            //$heasers[] = 'Accept: text/event-stream';
                            curl_setopt($ch, CURLOPT_HTTPHEADER,$headers );
                        }
                        if (isset($stream_options['http']['content'])) {
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $stream_options['http']['content']);
                        }
                    }
                    // Function to handle each received chunk
                    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use (&$buffer) {
                        $buffer .= $chunk; // Append chunk to buffer
                        $lines = explode("\n", $buffer); // Split buffer into lines
                        $fl2=__DIR__."/response_assistant_lines.txt";  
                        $logvar = $lines ; 
                        //error_log(print_r($logvar,true),3,$fl2);
                        foreach ($lines as $line) {
                            if (strpos($line, 'data:') === 0) {
                                // Extract event data
                                $data = trim(substr($line, strlen('data:')));
                                self::handleEvent($data); // Handle the event
                            }
                        }

                        $buffer = end($lines); // Update buffer with last incomplete line
                        return strlen($chunk); // Return the length of processed chunk
                    });

                    // Execute cURL request
                    curl_exec($ch);

                    // Close cURL session
                    curl_close($ch);

        }
        
    }

}
