<?php

if (!defined('ABSPATH'))
    exit;

if (!class_exists('Oc3dAig_ChatBotUtils')) {

    class Oc3dAig_ChatBotUtils {
        //$data_parameters
        public static function getChatBotStyles($data_parameters){
            return '';
            
	
        }
        
        public static function getChatBotDefaultStyles(){
            $styles = [
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chat_icon_size' => 70,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'background_color' => '#ffffff',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'header_color' => '#0C476E',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'send_button_color' => '#0E5381',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'send_button_text_color' => '#ffffff',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'send_button_hover_color' => '#126AA5',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'color' => '#ffefea',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chatbot_border_radius' => 10,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'header_text_color' => '#ffffff',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'response_bg_color' => '#5AB2ED',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'response_text_color' => '#000000',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'response_icons_color' => '#000',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'message_border_radius' => 10,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'message_bg_color' => '#1476B8',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'message_text_color' => '#fff',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'message_font_size' => 16,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'message_margin' => 5,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chat_width' => 25,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chat_width_metrics' => '%',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chat_height' => 55,
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chat_height_metrics' => '%',
                OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'access_for_guests' => 1,

                
                

            ];
            return $styles;
        }
        
        
        
        
        
        public static function getMetrics() {
            return [
                'percent'=>'%',
                'pixels'=>'px'
                ];
        }
        
        public static function generateBotHash(){
            
            return Oc3dAig_Utils::getToken(10);
        }
        
        public static function getModels(){
            return Oc3dAig_Utils::getEditModels();
        }
        
        public static function getProviders(){
            return ['default','assistant'];
        }
        
        
    }

}