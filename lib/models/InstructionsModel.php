<?php

if (!class_exists('Oc3dAig_InstructionsModel')) {

    class Oc3dAig_InstructionsModel {

        public static function getInstructions($type = 0) {
            global $wpdb;
            $type_str = '';
            if ($type > 0) {
                $type_str = 'AND typeof_instruction = ' . (int) $type;
            }
            $q = 'SELECT * FROM ' . $wpdb->prefix . 'oc3daig_instructions WHERE 1 ' . $type_str;
            return $wpdb->get_results($q);
        }

        public static function searchInstructions($type = 0, $search = '', $page = 1, $instructions_per_page = 20, $show_enabled_only = false) {
            global $wpdb;
            $par_arr = [];
            $type_str = '';
            if ($type > 0) {
                $type_str = ' AND typeof_instruction = ' . (int) $type;
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

            $qcount = 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'oc3daig_instructions WHERE 1  ' . $type_str . $search_part . $disabled_part;
            if (count($par_arr) > 0) {
                $qcount = $wpdb->prepare($qcount, $par_arr);
            }
            $cnt = $wpdb->get_var($qcount);
            $limit_part = ' LIMIT ' . ($page - 1) * $instructions_per_page . ',' . $instructions_per_page;

            $q = 'SELECT * FROM ' . $wpdb->prefix . 'oc3daig_instructions WHERE 1  ' . $type_str . $search_part . $disabled_part . $limit_part;
            if (count($par_arr) > 0) {
                $q = $wpdb->prepare($q, $par_arr);
            }
            $rows = $wpdb->get_results($q);
            return ['cnt' => $cnt, 'rows' => $rows];
        }


        

        public static function toggleInstruction($id, $disabled) {

            global $wpdb;
            return $wpdb->update($wpdb->prefix . 'oc3daig_instructions', array(
                        'disabled' => (int) $disabled), array('id' => (int) $id));
        }


        public static function getInstruction($id = 0) {
            global $wpdb;
            $q = 'SELECT * FROM ' . $wpdb->prefix . 'oc3daig_instructions WHERE id = ' . (int) $id;
            $res = $wpdb->get_row($q);
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