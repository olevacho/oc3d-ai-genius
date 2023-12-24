<?php

if (!class_exists('Oc3dAig_InstructionsModel')) {
    require_once OC3DAIG_PATH . '/lib/models/InstructionsModel.php';
}


if (!class_exists('Oc3dAig_AdminConfigController')) {

    class Oc3dAig_AdminConfigController extends Oc3dAig_BaseController {

        public $security_mode = 1;

        
        public function __construct() {

            
            add_action('wp_ajax_oc3d_gpt_toggle_instruction', [$this, 'gptToggleInstruction']);
            add_action('wp_ajax_oc3d_gpt_load_instruction', [$this, 'gptLoadInstructions']);
            add_action('wp_ajax_oc3d_gpt_delete_instruction', [$this, 'gptDeleteInstruction']);

            add_action('wp_ajax_oc3d_store_general_tab', [$this, 'processSettingsSubmit']);

        }

        public function registerAdminMenu() {

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                return false;
            }

            $settings_hook = add_submenu_page(OC3DAIG_PREFIX_LOW . 'image', __('Settings', OC3DAIG_TEXT_DOMAIN), __('Settings', OC3DAIG_TEXT_DOMAIN), 'edit_posts', OC3DAIG_PREFIX_LOW . 'settings', [$this, 'showSettings']);

            add_action('load-' . $settings_hook, [$this, 'processSettingsSubmit']);
        }

        public function processSettingsSubmit() {

            if (('POST' !== $_SERVER['REQUEST_METHOD'])) {
                return;
            }

            $r = ['result' => 0, 'msg' => __('Unknow problem')];
            $nonce = OC3DAIG_PREFIX_SHORT . 'config_nonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied');
                wp_send_json($r);
                exit;
            }

            if (isset($_POST['oc3daig_open_ai_gpt_key'])) {
                $api_key = sanitize_text_field($_POST['oc3daig_open_ai_gpt_key']);
                update_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', $api_key);
            }

            //if (isset($_POST[OC3DAIG_PREFIX_LOW . 'ptypes'])) {
            $p_types = get_post_types();
            $selected_post_types = isset($_POST[OC3DAIG_PREFIX_LOW . 'ptypes']) ? array_map('sanitize_key',array_keys($_POST[OC3DAIG_PREFIX_LOW . 'ptypes'])) : [];
            $ptres = [];
            foreach ($selected_post_types as $pt) {
                if (in_array($pt, $p_types)) {
                    $ptres[] = $pt;
                }
            }

            $selected_ptypes = serialize($ptres);
            update_option(OC3DAIG_PREFIX_LOW . 'selected_types', $selected_ptypes);
            //}

            if (isset($_POST[OC3DAIG_PREFIX_LOW . 'connection_timeout'])) {
                $connection_timeout = (int) $_POST[OC3DAIG_PREFIX_LOW . 'connection_timeout'];
                update_option(OC3DAIG_PREFIX_LOW . 'connection_timeout', $connection_timeout);
            }

            if (isset($_POST[OC3DAIG_PREFIX_LOW . 'response_timeout'])) {
                $response_timeout = (int) $_POST[OC3DAIG_PREFIX_LOW . 'response_timeout'];
                update_option(OC3DAIG_PREFIX_LOW . 'response_timeout', $response_timeout);
            }

            if (isset($_POST[OC3DAIG_PREFIX_LOW . 'max_tokens'])) {
                $max_tokens = (int) $_POST[OC3DAIG_PREFIX_LOW . 'max_tokens'];
                update_option(OC3DAIG_PREFIX_LOW . 'max_tokens', $max_tokens);
            }

            //count_of_instructions that are displayed in correction text meta box
            if (isset($_POST[OC3DAIG_PREFIX_LOW . 'count_of_instructions'])) {
                $count_of_instructions = (int) $_POST[OC3DAIG_PREFIX_LOW . 'count_of_instructions'];
                update_option(OC3DAIG_PREFIX_LOW . 'count_of_instructions', $count_of_instructions);
            }

            $r['result'] = 200;
            $r['msg'] = __('OK');
            wp_send_json($r);
            exit;
        }


        public function showSettings() {

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                return;
            }
            $oc3daig_open_ai_gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            $conf_contr = $this;
            $conf_contr->load_view('backend/config', ['oc3daig_open_ai_gpt_key' => $oc3daig_open_ai_gpt_key]);
            $conf_contr->render();
        }


        function gptLoadInstructionsSimple() {

            $instructions = Oc3dAig_InstructionsModel::getInstructions();
            $js_instructions = [];
            $i = 0;
            foreach ($instructions as $row) {
                $author = Oc3dAig_Utils::getUsername($row->user_id);
                $row->user_id = $author;
                $instructions[$i]->user_id = $author;
                $js_instructions[$row->id] = $row;
                $i++;
            }
            $res = ['js_instructions' => $js_instructions, 'instructions' => $instructions,
                'result' => 200];
            wp_send_json($res);
            exit;
        }

        function gptToggleInstruction() {

            $r = ['result' => 0, 'msg' => __('Unknow problem')];
            $nonce = 'oc3d_gpt_toggleinstructnonce';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }

            if (!isset($_POST['id']) || $_POST['id'] == 0) {
                $r['result'] = 4;
                $r['msg'] = __('Instruction not specified');
                wp_send_json($r);
                exit;
            }

            $id_instruction = (int) $_POST['id'];
            $instruction = Oc3dAig_InstructionsModel::getInstruction($id_instruction);
            if (!$instruction) {//verify id instruction
                $r['result'] = 4;
                $r['msg'] = __('Wrong instruction');
                wp_send_json($r);
                exit;
            }

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied');
                wp_send_json($r);
                exit;
            }

            $disabled = $instruction->disabled;
            if ($disabled == 1) {
                $toggle_val = 0;
            } else {
                $toggle_val = 1;
            }
            $upd_res = Oc3dAig_InstructionsModel::toggleInstruction($id_instruction, $toggle_val);
            if ($upd_res > 0) {
                $r['result'] = 200;
                $r['msg'] = 'OK';
                $r['new_instruction'] = ['id' => $id_instruction, 'disabled' => $toggle_val];
            }

            wp_send_json($r);
            exit;
        }


        function parseModelResponseObject($model) {
            $model_id = $model->id;
            $model_permission = $model->permission;
            $allow_sampling = 1;
            if (is_array($model_permission) && count($model_permission) > 0) {
                $mperm = $model_permission[0];
                if (is_object($mperm) && isset($mperm->allow_sampling)) {
                    $allow_sampling = (int) $mperm->allow_sampling;
                }
            }
            return ['id' => $model_id, 'textmodel' => $allow_sampling === 1];
        }
    }

}