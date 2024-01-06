<?php

if (!class_exists('Oc3dAig_InstructionsModel')) {

    class Oc3dAig_InstructionsModel {

        public static function getInstructions($type = 0) {
            global $wpdb;
            if ($type > 0) {
                return $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "oc3daig_instructions WHERE typeof_instruction = %d ",$type));
            }
            
            return $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "oc3daig_instructions");
        }

        public static function searchInstructions($type = 0, $search = '', $page = 1, $instructions_per_page = 20, $show_enabled_only = false) {
            global $wpdb;
            $par_arr = [];
            $default_where_parameter = 1;
            $type_str = '';
            if ($type > 0) {
                $type_str = " AND typeof_instruction = %d "  ;
                $par_arr[] = (int) $type;
            }
            $search_part = '';
            if (strlen($search) > 0) {
                $search_part = ' AND instruction LIKE %s';
                $par_arr[] = '%' . $search . '%';
            }
            $disabled_part = '';
            if ($show_enabled_only) {
                $disabled_part = ' AND disabled = 0 ';
            }

            
            if (count($par_arr) > 0) {
                $cnt = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3daig_instructions WHERE 1  " . $type_str . $search_part . $disabled_part, $par_arr));
            }else{
                $cnt = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "oc3daig_instructions WHERE %d  " . $type_str . $search_part . $disabled_part,[$default_where_parameter]));
            }
            
            $limit_part = ""  ;
            if($instructions_per_page > 0 && $page > 0){
                $limit_part = " LIMIT  %d,%d "  ;
                $par_arr[] = ($page - 1) * $instructions_per_page; 
                $par_arr[] =  $instructions_per_page;
            }
            
            if (count($par_arr) > 0) {
                $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "oc3daig_instructions WHERE 1  " . $type_str . $search_part . $disabled_part . $limit_part, $par_arr));
            }else{
                $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "oc3daig_instructions WHERE %d  " . $type_str . $search_part . $disabled_part . $limit_part,[$default_where_parameter]));
            }

            return ['cnt' => $cnt, 'rows' => $rows];
        }


        

        public static function toggleInstruction($id, $disabled) {

            global $wpdb;
            return $wpdb->update($wpdb->prefix . 'oc3daig_instructions', 
                    array(
                        'disabled' => (int) $disabled
                    ), 
                    array('id' => (int) $id),
                    array('%d'),
                    array('%d')
                    );
        }


        public static function getInstruction($id = 0) {
            global $wpdb;
            
            $res = $wpdb->get_row(
                    $wpdb->prepare(
                    "SELECT * FROM " . $wpdb->prefix . "oc3daig_instructions WHERE id =  %d", $id)
                    );
            if (is_object($res) && isset($res->id) && $res->id > 0) {
                return $res;
            }
            return false;
        }


        public static function getInstructionTypes() {
            return [1, 2];
        }


        
    }

}