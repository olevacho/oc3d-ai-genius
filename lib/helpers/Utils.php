<?php

if (!class_exists('Oc3dAig_Utils')) {

    class Oc3dAig_Utils {
        
        /*
        retrieves list of models from option table that are used in Edit tab of metabox
         * returns models in format [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k'....];
         *          */
        public static function getEditModels() {

            
            return [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k',3=>'gpt-3.5-turbo-0613'
                ,4=>'gpt-3.5-turbo-0301',5=>'gpt-3.5-turbo-16k-0613']; //in format [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k'];
        }
        
        /*
        retrieves list of models from option table that are used in Expert tab of metabox
         * returns models in format [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k'....];
         *          */
        public static function getExpertModels() {

            return [1=>'gpt-3.5-turbo',2=>'gpt-3.5-turbo-16k',3=>'gpt-3.5-turbo-0613'
                ,4=>'gpt-3.5-turbo-0301',5=>'gpt-3.5-turbo-16k-0613']; 
        }

        /*
        retrieves list of models from option table that are used in Edit tab of metabox
         * returns models in format ['gpt-3.5-turbo','gpt-3.5-turbo-16k'];;
         *          */
        public static function getEditModelTexts() {

            
            return ['gpt-3.5-turbo','gpt-3.5-turbo-16k','gpt-3.5-turbo-0613'
                ,'gpt-3.5-turbo-0301','gpt-3.5-turbo-16k-0613'];  
        }

        /*
        retrieves list of models from option table that are used in Expert tab of metabox
         * returns models in format ['gpt-3.5-turbo','gpt-3.5-turbo-16k'];;
         *          */
        public static function getExpertModelTexts() {

            
            return ['gpt-3.5-turbo','gpt-3.5-turbo-16k','gpt-3.5-turbo-0613'
                ,'gpt-3.5-turbo-0301','gpt-3.5-turbo-16k-0613']; 
        }
        
        //checks if user has access to plugin
        
        public static function checkChatGPTAccess() {//TO-DO add capability and/or usermeta
            return self::checkEditAccess();
        }

        public static function getChatGptMaxTokens() {
            return get_option(OC3DAIG_PREFIX_LOW . 'max_tokens', 2048);
        }

        public static function getUsername($user_id) {

            $created_by = get_userdata($user_id);
            if (is_object($created_by) && isset($created_by->ID)) {
                $author = $created_by->user_login;
            } else {
                $author = esc_html__('System', 'oc3d-ai-genius');
            }
            return $author;
        }

        public static function checkDeleteInstructionAccess() {
            if (current_user_can('manage_options')) {
                return true;
            }
            return false;
        }

        public static function checkEditInstructionAccess() {
            if (current_user_can('edit_others_posts')) {
                return true;
            }
            return false;
        }

        public static function checkEditAccess() {
            if (current_user_can('edit_posts')) {
                return true;
            }
            return false;
        }
        
        public static function sanitizeArrayModels(&$models_array = []) {

            foreach ($models_array as $key => $iv) {
                $models_array[$key] = (int) $iv;
            }
            return $models_array;
        }

            
        //used in such constructions wp_kses($data['instruction'], Oc3dAig_Utils::getInstructionAllowedTags());
        
        public static function getInstructionAllowedTags() {
            return [];
        }
    }

}