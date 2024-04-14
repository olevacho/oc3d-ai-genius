<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3dAig_ChatBotModel')) {

    class Oc3dAig_ChatBotModel {

        public function getChatBotSettings($chatbot_hash = ''){
            if($chatbot_hash == 'default'){
            $default_option = [
                'background_color'=>'#ffffff',
                'header_text_color'=>'#ffffff',
                'color'=>'#ffefea',
                'header_color'=>'#0C476E',
                'send_button_color'=>'#0E5381',
                'send_button_text_color'=>'ffffff',
                'position'=>'right',
                'icon_position'=>'bottom-right',
                'chat_icon_size'=>70,
                'chat_width'=> 25,
                'chat_width_metrics'=>'%',
                'chat_height'=>55,
                'chat_height_metrics'=>'%',
                'greeting_message' => 0,
                'greeting_message_text'=>esc_html__('Hello! I am an AI Assistant. How can I help you?','s2b-ai-aiassistant'),
                'message_placeholder'=>esc_html__('Ctrl+Enter to send request','s2b-ai-aiassistant'),
                'chatbot_name'=>'GPT Assistant',
                'chat_temperature'=>0.8,
                'chat_model'=>'gpt-3.5-turbo-16k',
                'chat_top_p'=>1,
                'max_tokens'=>2048,
                'frequency_penalty'=> 0,
                'presence_penalty'=> 0,
                'context'=>'',
                'language'=>'english',
                'message_font_size'=>16,
                'message_margin'=>7,
                'message_border_radius'=> 10,
                'chatbot_border_radius'=> 10,
                'message_bg_color'=>'#1476B8',
                'message_text_color'=>'#ffffff',
                'response_bg_color'=>'#5AB2ED',
                'response_text_color'=>'#000000',
                'response_icons_color'=>'#000',
                'access_for_guests'=> 1,
                'send_button_text' => esc_html__('Send','s2b-ai-aiassistant'),
                'clear_button_text' => esc_html__('Clear','s2b-ai-aiassistant')
                ];
            }else{
                $default_option = [
                'background_color'=>'#ffffff',
                'header_text_color'=>'#ffffff',
                'color'=>'#ffefea',
                'header_color'=>'#0C476E',
                'send_button_color'=>'#0E5381',
                'send_button_text_color'=>'ffffff',
                'position'=>'right',
                'icon_position'=>'bottom-right',
                'chat_icon_size'=>70,
                'chat_width'=> 25,
                'chat_width_metrics'=>'%',
                'chat_height'=>55,
                'chat_height_metrics'=>'%',
                'greeting_message' => 0,
                'greeting_message_text'=>esc_html__('Hello! I am an AI Assistant. How can I help you?','s2b-ai-aiassistant'),
                'message_placeholder'=>esc_html__('Ctrl+Enter to send request','s2b-ai-aiassistant'),
                'chatbot_name'=>'GPT Assistant',
                'chat_temperature'=>0.8,
                'chat_model'=>'gpt-3.5-turbo-16k',
                'chat_top_p'=>1,
                'max_tokens'=>2048,
                'frequency_penalty'=> 0,
                'presence_penalty'=> 0,
                'context'=>'',
                'language'=>'english',
                'message_font_size'=>16,
                'message_margin'=>7,
                'message_border_radius'=> 10,
                'chatbot_border_radius'=> 10,
                'message_bg_color'=>'#1476B8',
                'message_text_color'=>'#ffffff',
                'response_bg_color'=>'#5AB2ED',
                'response_text_color'=>'#000000',
                'response_icons_color'=>'#000',
                'access_for_guests'=> 1,
                'chatbot_picture_url' => '',
                'send_button_text' => esc_html__('Send','s2b-ai-aiassistant'),
                'clear_button_text' => esc_html__('Clear','s2b-ai-aiassistant')
                ];
            }
            //get_row
            $row = new stdClass();
            $row->bot_options = $default_option;
            
            $row->id_author = (int) 0;
            $row->comment = '';
            $row->datetimecreated = 0;
            
            return $row;
            
        }
        
        
        public function storeChatBotOptions($chatbot_hash = '',$data = []){

            $current_chat_bot = $this->getChatBotSettings($chatbot_hash);
            if(is_object($current_chat_bot) && isset($current_chat_bot->id) && $current_chat_bot->id > 0){
                $res = $this->updateChatBotOptions($chatbot_hash, $data,$current_chat_bot->bot_options);
            }else{
                $res = $this->insertChatBotSettingsOptions($chatbot_hash, $data);
            }
            
            
            return $res !== false;
            
        }
        
        public function insertChatBotSettingsOptions($chatbot_hash = '',$data = []) {

            return true;
        }
        
        public static function updateChatBotOptions($chatbot_hash = '',$data = [], $old_data = []) {

            
        }
        
        
    }

}