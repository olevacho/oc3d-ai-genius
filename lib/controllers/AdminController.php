<?php

if (!class_exists('Oc3dAig_AdminController')) {

    class Oc3dAig_AdminController extends Oc3dAig_BaseController {

        const ADMIN_MENU = OC3DAIG_PREFIX_LOW . 'settings'; //
        const ADMIN_MENU_CALLBACK = null; //can be view file or some callback function

        public $config_controller = false;
        
        public $admlogo = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjMiIGhlaWdodD0iMTMiIHZpZXdCb3g9IjAgMCAyMyAxMyIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTcuMjg2OTIgMy44MTgxOEM3LjIyMyAzLjI3ODQxIDYuOTYzNzcgMi44NTkzNyA2LjUwOTIyIDIuNTYxMDhDNi4wNTQ2OCAyLjI2Mjc4IDUuNDk3MTUgMi4xMTM2MyA0LjgzNjY0IDIuMTEzNjNDNC4zNTM2OSAyLjExMzYzIDMuOTMxMSAyLjE5MTc2IDMuNTY4ODggMi4zNDgwMUMzLjIxMDIyIDIuNTA0MjYgMi45Mjk2OCAyLjcxOTEgMi43MjcyNiAyLjk5MjU0QzIuNTI4NCAzLjI2NTk4IDIuNDI4OTcgMy41NzY3IDIuNDI4OTcgMy45MjQ3MUMyLjQyODk3IDQuMjE1OSAyLjQ5ODIyIDQuNDY2MjYgMi42MzY3MSA0LjY3NTc4QzIuNzc4NzYgNC44ODE3NCAyLjk1OTg2IDUuMDUzOTcgMy4xODAwMyA1LjE5MjQ3QzMuNDAwMiA1LjMyNzQxIDMuNjMxMDMgNS40MzkyNyAzLjg3MjUxIDUuNTI4MDVDNC4xMTM5OCA1LjYxMzI4IDQuMzM1OTMgNS42ODI1MiA0LjUzODM0IDUuNzM1NzlMNS42NDYzIDYuMDM0MDlDNS45MzAzOSA2LjEwODY2IDYuMjQ2NDQgNi4yMTE2NCA2LjU5NDQ1IDYuMzQzMDRDNi45NDYwMSA2LjQ3NDQzIDcuMjgxNiA2LjY1Mzc2IDcuNjAxMiA2Ljg4MTAzQzcuOTI0MzUgNy4xMDQ3NSA4LjE5MDY5IDcuMzkyNCA4LjQwMDIxIDcuNzQzOTZDOC42MDk3MiA4LjA5NTUyIDguNzE0NDggOC41MjY5OCA4LjcxNDQ4IDkuMDM4MzVDOC43MTQ0OCA5LjYyNzg0IDguNTYwMDEgMTAuMTYwNSA4LjI1MTA2IDEwLjYzNjRDNy45NDU2NiAxMS4xMTIyIDcuNDk4MjIgMTEuNDkwNCA2LjkwODczIDExLjc3MDlDNi4zMjI3OSAxMi4wNTE1IDUuNjEwNzkgMTIuMTkxOCA0Ljc3MjcyIDEyLjE5MThDMy45OTE0NyAxMi4xOTE4IDMuMzE0OTggMTIuMDY1NyAyLjc0MzI0IDExLjgxMzZDMi4xNzUwNiAxMS41NjE0IDEuNzI3NjIgMTEuMjA5OSAxLjQwMDkyIDEwLjc1ODlDMS4wNzc3NiAxMC4zMDc5IDAuODk0ODc4IDkuNzg0MDkgMC44NTIyNjQgOS4xODc1SDIuMjE1OUMyLjI1MTQxIDkuNTk5NDMgMi4zODk5MSA5Ljk0MDM0IDIuNjMxMzggMTAuMjEwMkMyLjg3NjQxIDEwLjQ3NjYgMy4xODUzNiAxMC42NzU0IDMuNTU4MjMgMTAuODA2OEMzLjkzNDY1IDEwLjkzNDcgNC4zMzk0OCAxMC45OTg2IDQuNzcyNzIgMTAuOTk4NkM1LjI3Njk4IDEwLjk5ODYgNS43Mjk3NSAxMC45MTY5IDYuMTMxMDMgMTAuNzUzNUM2LjUzMjMxIDEwLjU4NjYgNi44NTAxMyAxMC4zNTU4IDcuMDg0NTEgMTAuMDYxMUM3LjMxODg4IDkuNzYyNzggNy40MzYwNyA5LjQxNDc3IDcuNDM2MDcgOS4wMTcwNEM3LjQzNjA3IDguNjU0ODMgNy4zMzQ4NiA4LjM2MDA4IDcuMTMyNDUgOC4xMzI4MUM2LjkzMDAzIDcuOTA1NTQgNi42NjM3IDcuNzIwODggNi4zMzM0NCA3LjU3ODgzQzYuMDAzMTkgNy40MzY3OSA1LjY0NjMgNy4zMTI1IDUuMjYyNzggNy4yMDU5NkwzLjkyMDQ1IDYuODIyNDRDMy4wNjgxNyA2LjU3NzQxIDIuMzkzNDYgNi4yMjc2MiAxLjg5NjMgNS43NzMwOEMxLjM5OTE0IDUuMzE4NTMgMS4xNTA1NiA0LjcyMzcyIDEuMTUwNTYgMy45ODg2M0MxLjE1MDU2IDMuMzc3ODQgMS4zMTU2OSAyLjg0NTE3IDEuNjQ1OTQgMi4zOTA2MkMxLjk3OTc1IDEuOTMyNTIgMi40MjcxOSAxLjU3NzQxIDIuOTg4MjcgMS4zMjUyOEMzLjU1MjkgMS4wNjk2IDQuMTgzMjMgMC45NDE3NTcgNC44NzkyNSAwLjk0MTc1N0M1LjU4MjM4IDAuOTQxNzU3IDYuMjA3MzggMS4wNjc4MiA2Ljc1NDI1IDEuMzE5OTVDNy4zMDExMyAxLjU2ODUzIDcuNzM0MzcgMS45MDk0NCA4LjA1Mzk3IDIuMzQyNjhDOC4zNzcxMiAyLjc3NTkyIDguNTQ3NTggMy4yNjc3NSA4LjU2NTMzIDMuODE4MThINy4yODY5MloiIGZpbGw9IiNGRjA2MDYiLz4KPHBhdGggZD0iTTE1LjMyMSAxMlYxLjA5MDkxSDE5LjEzNDlDMTkuODk0OSAxLjA5MDkxIDIwLjUyMTcgMS4yMjIzIDIxLjAxNTMgMS40ODUwOEMyMS41MDg5IDEuNzQ0MzEgMjEuODc2NCAyLjA5NDEgMjIuMTE3OSAyLjUzNDQ0QzIyLjM1OTQgMi45NzEyMyAyMi40ODAxIDMuNDU1OTYgMjIuNDgwMSAzLjk4ODYzQzIyLjQ4MDEgNC40NTczOCAyMi4zOTY3IDQuODQ0NDYgMjIuMjI5NyA1LjE0OTg1QzIyLjA2NjQgNS40NTUyNSAyMS44NDk4IDUuNjk2NzMgMjEuNTc5OSA1Ljg3NDI5QzIxLjMxMzYgNi4wNTE4NCAyMS4wMjQxIDYuMTgzMjMgMjAuNzExNiA2LjI2ODQ2VjYuMzc1QzIxLjA0NTQgNi4zOTYzIDIxLjM4MSA2LjUxMzQ5IDIxLjcxODQgNi43MjY1NkMyMi4wNTU3IDYuOTM5NjMgMjIuMzM4MSA3LjI0NTAyIDIyLjU2NTMgNy42NDI3NUMyMi43OTI2IDguMDQwNDggMjIuOTA2MiA4LjUyNjk4IDIyLjkwNjIgOS4xMDIyN0MyMi45MDYyIDkuNjQ5MTQgMjIuNzgyIDEwLjE0MSAyMi41MzM0IDEwLjU3NzhDMjIuMjg0OCAxMS4wMTQ2IDIxLjg5MjQgMTEuMzYwOCAyMS4zNTYyIDExLjYxNjVDMjAuODE5OSAxMS44NzIyIDIwLjEyMjIgMTIgMTkuMjYyOCAxMkgxNS4zMjFaTTE2LjY0MiAxMC44MjgxSDE5LjI2MjhDMjAuMTI1NyAxMC44MjgxIDIwLjczODMgMTAuNjYxMiAyMS4xMDA1IDEwLjMyNzRDMjEuNDY2MyA5Ljk5MDA1IDIxLjY0OTEgOS41ODE2NyAyMS42NDkxIDkuMTAyMjdDMjEuNjQ5MSA4LjczMjk1IDIxLjU1NSA4LjM5MjA0IDIxLjM2NjggOC4wNzk1NEMyMS4xNzg2IDcuNzYzNDkgMjAuOTEwNSA3LjUxMTM2IDIwLjU2MjUgNy4zMjMxNUMyMC4yMTQ1IDcuMTMxMzkgMTkuODAyNSA3LjAzNTUxIDE5LjMyNjcgNy4wMzU1MUgxNi42NDJWMTAuODI4MVpNMTYuNjQyIDUuODg0OTRIMTkuMDkyM0MxOS40OSA1Ljg4NDk0IDE5Ljg0ODcgNS44MDY4MSAyMC4xNjgzIDUuNjUwNTZDMjAuNDkxNSA1LjQ5NDMxIDIwLjc0NzIgNS4yNzQxNCAyMC45MzU0IDQuOTkwMDVDMjEuMTI3MSA0LjcwNTk2IDIxLjIyMyA0LjM3MjE2IDIxLjIyMyAzLjk4ODYzQzIxLjIyMyAzLjUwOTIzIDIxLjA1NjEgMy4xMDI2MiAyMC43MjIzIDIuNzY4ODJDMjAuMzg4NSAyLjQzMTQ2IDE5Ljg1OTQgMi4yNjI3OCAxOS4xMzQ5IDIuMjYyNzhIMTYuNjQyVjUuODg0OTRaIiBmaWxsPSIjRkUwNzA3Ii8+CjxwYXRoIGQ9Ik0xMS44Mzg4IDlMMTEuMzQ2NiA4LjUxNDJMMTMuMzcyOSA2LjQ4NzkySDguMTI0OTlWNS43ODQ4SDEzLjM3MjlMMTEuMzQ2NiAzLjc2NDkxTDExLjgzODggMy4yNzI3MkwxNC43MDI0IDYuMTM2MzZMMTEuODM4OCA5WiIgZmlsbD0iYmxhY2siLz4KPC9zdmc+';

        public function __construct() {
            if (!class_exists('Oc3dAig_AdminConfigController')) {
                $contr_path = OC3DAIG_PATH . "/lib/controllers/AdminConfigController.php";
                include_once $contr_path;
            }
            $this->config_controller = new Oc3dAig_AdminConfigController();
            add_action('admin_menu', array($this, 'registerAdminMenu'));

            add_action('add_meta_boxes', [$this, 'addChatMetaBox']);

            add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);

            //oc3d_gpt_correct
            add_action('wp_ajax_oc3d_gpt_correct', [$this, 'oc3dGptCorrect']);
            add_action('wp_ajax_oc3d_gpt_generate', [$this, 'oc3dGptGenerate']);
            add_action('wp_ajax_oc3d_gpt_load_correct_instruction', [$this, 'gptLoadInstructions']);
        }

        function addChatMetaBox() {
            if (!Oc3dAig_Utils::checkEditAccess()) {
                return;
            }
            global $post;

            if (!empty($post)) {

                $selected_p_types = unserialize(get_option(OC3DAIG_PREFIX_LOW . 'selected_types'));
                //var_dump($selected_p_types);
                if (!is_array($selected_p_types) || count($selected_p_types) == 0) {
                    //$selected_p_types = ['post', 'page'];
                    return;
                }

                add_meta_box(
                        'oc3daig_gpt_box', // $id
                        'OC3D AI Assistant', // $title
                        [$this, 'showGptMeta'], // $callback
                        $selected_p_types, // $page
                        'normal', // $context
                        'high'); // $priority
            }
        }

        function oc3dGptCorrect() {

            $r = ['result' => 1, 'msg' => __('Unknow problem')];

            if (!array_key_exists('oc3d_gpt_nonce', $_POST)) {
                $r['result'] = 2;
                $r['msg'] = __('Security issues');
                wp_send_json($r);
                exit;
            }
            $verify_nonce = check_ajax_referer('oc3d_gpt_nonce', 'oc3d_gpt_nonce', false);

            if (!$verify_nonce) {
                $r['result'] = 3;
                $r['msg'] = __('Security issues');
                wp_send_json($r);
                exit;
            }

            $user_can_chat_gpt = Oc3dAig_Utils::checkChatGPTAccess();
            if (!$user_can_chat_gpt) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied');
                wp_send_json($r);
            }

            if (!class_exists('Oc3dAig_AiRequest')) {
                require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
            }
            $data = [];
            $mod_id = $_POST['model'];
            $models_allowed = Oc3dAig_Utils::getEditModelTexts();
            if (!in_array($mod_id, $models_allowed)) {
                $r['result'] = 5;
                $r['msg'] = __('Model is not allowed');
                wp_send_json($r);
            }
            $data['model'] = $mod_id;
            $data['instruction'] = $_POST['instruction'];
            if (strlen($data['instruction']) == 0) {
                $r['result'] = 6;
                $r['msg'] = __('Instruction is empty');
                wp_send_json($r);
            }
            $data['max_tokens'] = (int) $_POST['max_tokens'];

            $data['temperature'] = is_numeric($_POST['temperature']) ? floatval($_POST['temperature']) : 1;

            $data['text'] = $_POST['text'];

            $res = Oc3dAig_AiRequest::sendChatGptEdit($data);

            if ($res[0] == 1) {
                $response = json_decode($res[1]);
                if (Oc3dAig_AiRequest::testChatGptResponse($response)) {
                    $msg = Oc3dAig_AiRequest::getChatGptResponseEditMessage($response);
                    $r['result'] = 200;
                    $r['msg'] = wp_kses($msg, Oc3dAig_Utils::getInstructionAllowedTags());
                    wp_send_json($r);
                    exit;
                }
            } else {//if we have curl error
                $response = is_array($res) && count($res) > 0 && is_array($res[1]) && count($res[1]) > 0 ? esc_html__('Error', 'oc3d-ai-genius') . ' ' . $res[1][0] . ' ' . $res[1][1] : esc_html__('Unknown error', 'oc3d-ai-genius');

                $r['result'] = 404;
                $r['msg'] = wp_kses($response, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            if (isset($response->error) && isset($response->error->message)) {
                $r['result'] = 200;
                $r['msg'] = wp_kses($response->error->message, Oc3dAig_Utils::getInstructionAllowedTags());
            }

            wp_send_json($r);
            exit;
        }

        function oc3dGptGenerate() {

            $r = ['result' => 1, 'msg' => __('Unknow problem')];
            if (!isset($_POST)) {
                wp_send_json($r);
                exit;
            }

            if (!array_key_exists('oc3d_gpt_nonce', $_POST)) {
                $r['result'] = 2;
                $r['msg'] = __('Security issues');
                wp_send_json($r);
                exit;
            }

            $verify_nonce = check_ajax_referer('oc3d_gpt_nonce', 'oc3d_gpt_nonce', false);
            //$verify_nonce = wp_verify_nonce($_POST['oc3d_gpt_nonce'], 'oc3d_gpt_nonce');

            if (!$verify_nonce) {
                $r['result'] = 3;
                $r['msg'] = __('Security issues');
                wp_send_json($r);
                exit;
            }

            $user_can_chat_gpt = Oc3dAig_Utils::checkChatGPTAccess();
            if (!$user_can_chat_gpt) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied');
                wp_send_json($r);
            }

            if (!class_exists('Oc3dAig_AiRequest')) {
                require_once OC3DAIG_PATH . '/lib/helpers/AiRequest.php';
            }
            $data = [];
            $mod_id = $_POST['model'];
            $models_allowed = Oc3dAig_Utils::getExpertModelTexts();
            if (!in_array($mod_id, $models_allowed)) {
                $r['result'] = 5;
                $r['msg'] = __('Model is not allowed');
                wp_send_json($r);
            }
            $data['model'] = $mod_id;

            $data['max_tokens'] = (int) $_POST['max_tokens'];

            $data['temperature'] = is_numeric($_POST['temperature']) ? floatval($_POST['temperature']) : 1;

            $data['top_p'] = is_numeric($_POST['top_p']) ? floatval($_POST['top_p']) : 1;

            $data['presence_penalty'] = is_numeric($_POST['presence_penalty']) ? floatval($_POST['presence_penalty']) : 0;
            $data['frequency_penalty'] = is_numeric($_POST['frequency_penalty']) ? floatval($_POST['frequency_penalty']) : 0;

            $data['system'] = $_POST['system'];
            $messages = [];
            if (isset($_POST['actors']) && is_array($_POST['actors']) && isset($_POST['msgs']) && is_array($_POST['msgs'])) {
                $actors = $_POST['actors'];
                $msgs = $_POST['msgs'];

                if (count($actors) > 0 && count($actors) == count($msgs)) {
                    foreach ($actors as $idx => $actor) {
                        if ($actor == 'User') {
                            $messages[] = ['role' => 'user', 'content' => $msgs[$idx]];
                        } elseif ($actor == 'Assistant') {
                            $messages[] = ['role' => 'assistant', 'content' => $msgs[$idx]];
                        }
                    }
                }
            }
            $data['messages'] = $messages;
            $res = Oc3dAig_AiRequest::sendChatGptCompletion($data);
            if ($res[0] == 1) {
                $response = json_decode($res[1]);
                if (Oc3dAig_AiRequest::testChatGptResponse($response)) {
                    $msg = Oc3dAig_AiRequest::getChatGptResponseEditMessage($response);
                    $r['result'] = 200;
                    $r['msg'] = wp_kses($msg, Oc3dAig_Utils::getInstructionAllowedTags());
                    wp_send_json($r);
                    exit;
                }
            } else {
                $response = is_array($res) && count($res) > 0 && is_array($res[1]) && count($res[1]) > 0 ? esc_html__('Error', 'oc3d-ai-genius') . ' ' . $res[1][0] . ' ' . $res[1][1] : esc_html__('Unknown error', 'oc3d-ai-genius');
                $r['result'] = 404;
                $r['msg'] = wp_kses($response, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            if (isset($response->error) && isset($response->error->message)) {
                $r['result'] = 200;
                $r['msg'] = wp_kses($response->error->message, Oc3dAig_Utils::getInstructionAllowedTags());
            }
            wp_send_json($r);
            exit;
        }

        function enqueueStyles() {
            //https://code.jquery.com/ui/1.13.1/themes/smoothness/jquery-ui.css
            $screen = get_current_screen();

            $selected_p_types = unserialize(get_option(OC3DAIG_PREFIX_LOW . 'selected_types'));
			
            if (!is_array($selected_p_types) || count($selected_p_types) == 0) {
                $selected_p_types = ['post', 'page'];
            }

            if (in_array($screen->id, $selected_p_types) || strpos($screen->id, 'oc3daig_settings') !== false) {
                wp_enqueue_style(
                        'jquery-ui',
                        OC3DAIG_URL . '/views/resources/css/jquery-ui.css',
                        array()
                );
                wp_enqueue_script('jquery-ui-tabs');
            }

            wp_enqueue_style(
                    'oc3daig',
                    OC3DAIG_URL . '/views/resources/css/oc3daig.css',
                    array()
            );
        }

        function showGptMeta() {

            $edit_models = Oc3dAig_Utils::getEditModels();
            $expert_models = Oc3dAig_Utils::getExpertModels();
            if (!class_exists('Oc3dAig_InstructionsModel')) {
                require_once OC3DAIG_PATH . '/lib/models/InstructionsModel.php';
            }
            $edit_instructions = Oc3dAig_InstructionsModel::getInstructions(1);
            $mx_tokens = Oc3dAig_Utils::getChatGptMaxTokens();
            $pathh = OC3DAIG_PATH . '/views/backend/metabox_index.php';
            include $pathh;
        }

        public function registerAdminMenu() {

            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                return;
            }

            add_menu_page('Settings', 'OC3D AI Genius',
                    'edit_posts', self::ADMIN_MENU, null,
                    $this->admlogo);

            $this->config_controller->registerAdminMenu();
        }

        function showSettings() {
            
        }

        function gptLoadInstructions() {

            $r = ['result' => 0, 'msg' => __('Unknow problem')];
            $nonce = 'oc3d_gpt_loadnoncec';
            $r = $this->verifyPostRequest($r, $nonce, $nonce);
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }
            $user_can_chat_gpt = Oc3dAig_Utils::checkChatGPTAccess();
            if (!$user_can_chat_gpt) {
                $r['result'] = 10;
                $r['msg'] = __('Access denied');
                wp_send_json($r);
            }
            $instructions_per_page = (int) $_POST['instructions_per_page'];
            $search = sanitize_text_field($_POST['search']);
            $page = isset($_POST['page']) && ((int) $_POST['page']) > 0 ? (int) $_POST['page'] : 1;
            $show_enabled_only = true;
            $res_arr = Oc3dAig_InstructionsModel::searchInstructions(0, $search, $page, $instructions_per_page, $show_enabled_only);
            $instructions = $res_arr['rows'];
            $total = $res_arr['cnt'];
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
                'result' => 200, 'total' => $total, 'page' => $page,
                'instructions_per_page' => $instructions_per_page];
            wp_send_json($res);
            exit;
        }
    }

}
