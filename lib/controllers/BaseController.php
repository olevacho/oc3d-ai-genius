<?php
if (!class_exists('Oc3dAig_BaseController')) {

    class Oc3dAig_BaseController {

        public $view;
        public $view_vars;
        public $model;

        public function load_view($name, $data) {
            $path = OC3DAIG_PATH . "/views/" . $name . ".php";
            $this->view_vars = $data;
            if (file_exists($path)) {


                $this->view = $path; //new $view_name($data);
                //ucfirst()
            } else {
                $this->view = false;
            }
        }

        public function load_model($name) {
            $path = OC3DAIG_PATH . "/lib/models/" . $name . ".php";
            if (file_exists($path)) {
                $model_name = OC3DAIG_CLASS_PREFIX . ucfirst($name);

                $this->model = new $model_name();
                //ucfirst()
            } else {
                $this->model = false;
            }
        }

        public function render() {
            extract($this->view_vars, EXTR_SKIP);
            ob_start();
            include $this->view;
            echo ob_get_clean();
        }

        function verifyPostRequest($r, $nonce = '', $action = '') {


            if (!isset($_POST)) {
                $r['result'] = 1;
                $r['msg'] = __('Invalid request');
                return $r;
            }

            if (!array_key_exists($nonce, $_POST)) {
                $r['result'] = 2;
                $r['msg'] = __('Security issues');
                return $r;
            }

            $verify_nonce = check_ajax_referer($action, $nonce, false);

            if (!$verify_nonce) {
                $r['result'] = 3;
                $r['msg'] = __('Security issues');
                return($r);
            }


            return $r;
        }

        function gptLoadInstructions() {

            $r = ['result' => 0, 'msg' => __('Unknow problem')];
            $nonce = 'oc3d_gpt_loadnonce';
            $r = $this->verifyPostRequest($r, $nonce, 'oc3d_gpt_loadnonce');
            if ($r['result'] > 0) {
                wp_send_json($r);
                exit;
            }

            if (!class_exists('Oc3dAig_InstructionsModel')) {
                require_once OC3DAIG_PATH . '/lib/models/InstructionsModel.php';
            }
            $instructions_per_page = (int) $_POST['instructions_per_page'];
            $search = sanitize_text_field($_POST['search']);
            $page = isset($_POST['page']) && ((int) $_POST['page']) > 0 ? (int) $_POST['page'] : 1;
            $show_enabled_only = isset($_POST['show_enabled_only']) && (int) $_POST['show_enabled_only'] > 1 ? true : false;

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
