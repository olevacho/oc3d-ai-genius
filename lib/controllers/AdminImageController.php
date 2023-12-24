<?php

if (!class_exists('Oc3dAig_AdminImageController')) {

    class Oc3dAig_AdminImageController extends Oc3dAig_BaseController {

        public $security_mode = 1;

        const ORIGIN = 'https://api.openai.com';
        const API_VERSION = 'v1';
        const OPEN_AI_URL = self::ORIGIN . "/" . self::API_VERSION;

        public function __construct() {
            if (!class_exists('Oc3dAig_ImageUtils')) {
                require_once OC3DAIG_PATH . '/lib/helpers/ImageUtils.php';
            }                       
            add_action('wp_ajax_oc3daig_image_generate', [$this, 'imageGenerate']);
            add_action('wp_ajax_nopriv_oc3daig_image_generate', [$this, 'imageGenerate']);
            add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
            add_action('wp_ajax_nopriv_oc3daig_save_image_media', [$this, 'saveImageToMedia']);
            add_action('wp_ajax_oc3daig_save_image_media', [$this, 'saveImageToMedia']);
            add_action('wp_ajax_oc3daig_img_default_settings', [$this,'setDefaultSettings']);
            $this->headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', ''),
            ];
        }
        
        function showMainView(){
            $this->showImage();
        }
        
        function showImage() {
            if (!Oc3dAig_Utils::checkEditInstructionAccess()) {
                return;
            }
            $oc3daig_open_ai_gpt_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            $conf_contr = $this;
            $conf_contr->load_view('backend/image', ['oc3daig_open_ai_gpt_key' => $oc3daig_open_ai_gpt_key]);
            $conf_contr->render();
            $this->addModalWindow();
        }
        
        function addModalWindow(){
            ?>
            <div class="oc3daig-overlay" style="display: none">
                <div class="oc3daig_modal">
                    <div class="oc3daig_modal_head">
                        <span class="oc3daig_modal_title"><?php echo esc_html__('GPT3 Modal','oc3d-ai-genius')?></span>
                        <span class="oc3daig_modal_close">&times;</span>
                    </div>
                    <div class="oc3daig_modal_content"></div>
                </div>
            </div>
            <div class="oc3daig-overlay-second" style="display: none">
                <div class="oc3daig_modal_second">
                    <div class="oc3daig_modal_head_second">
                        <span class="oc3daig_modal_title_second"><?php echo esc_html__('GPT3 Modal','oc3d-ai-genius')?></span>
                        <span class="oc3daig_modal_close_second">&times;</span>
                    </div>
                    <div class="oc3daig_modal_content_second"></div>
                </div>
            </div>
            <div class="wpcgai_lds-ellipsis" style="display: none">
                <div class="oc3daig-generating-title"><?php echo esc_html__('Generating content..','oc3d-ai-genius')?></div>
                <div class="oc3daig-generating-process"></div>
                <div class="oc3daig-timer"></div>
            </div>
            <script>
                let oc3daig_ajax_url = '<?php echo esc_url(admin_url('admin-ajax.php'))?>';
            </script>
            <?php
        }

        public function enqueueScripts() {
            $screen = get_current_screen();
            if (strpos($screen->id, 'oc3daig_image') !== false) {
                wp_enqueue_script('oc3daig-init', OC3DAIG_URL . '/views/resources/js/oc3daig-image.js', array(), null, true);
                wp_localize_script('oc3daig-init', 'oc3daigParams', array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'search_nonce' => wp_create_nonce('oc3daig-chatbox'),
                    'logged_in' => is_user_logged_in() ? 1 : 0,
                    'languages' => array(
                        'source' => esc_html__('Sources', 'oc3d-ai-genius'),
                        'no_result' => esc_html__('No result found', 'oc3d-ai-genius'),
                        'wrong' => esc_html__('Something went wrong', 'oc3d-ai-genius'),
                        'error_image' => esc_html__('Please select least one image for generate', 'oc3d-ai-genius'),
                        'save_image_success' => esc_html__('Save images to media successfully', 'oc3d-ai-genius'),
                        'select_all' => esc_html__('Select All', 'oc3d-ai-genius'),
                        'unselect' => esc_html__('Unselect', 'oc3d-ai-genius'),
                        'select_save_error' => esc_html__('Please select least one image to save', 'oc3d-ai-genius'),
                        'alternative' => esc_html__('Alternative Text', 'oc3d-ai-genius'),
                        'title' => esc_html__('Title', 'oc3d-ai-genius'),
                        'edit_image' => esc_html__('Edit Image', 'oc3d-ai-genius'),
                        'caption' => esc_html__('Caption', 'oc3d-ai-genius'),
                        'description' => esc_html__('Description', 'oc3d-ai-genius'),
                        'save' => esc_html__('Save', 'oc3d-ai-genius')
                        
                    )
                ));
            }
        }

        public function imageGenerate() {
            $oc3daig_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong', 'oc3d-ai-genius'));

            $oc3daig_nonce = sanitize_text_field($_REQUEST['oc3d_imggen_nonce']);
            if (!wp_verify_nonce($oc3daig_nonce, 'oc3d_imggen_nonce')) {
                $oc3daig_result['msg'] = esc_html__('Nonce verification failed', 'oc3d-ai-genius');
            } else {


                $prompt = sanitize_text_field($_POST['oc3daig_text_c']);
                $prompt_title = sanitize_text_field($_POST['oc3daig_text_c']);
                $img_size = sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW . 'size_opt']);
                $img_model = sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW . 'models_opt']);
                // Initialize the quality variable.
                $quality = '';

                if ($img_model === 'dall-e-3-hd') {
                    $img_model = 'dall-e-3'; // Remove '-hd' part
                    $quality = 'hd'; // Set quality to 'hd'
                }
                $num_images = (int) sanitize_text_field($_POST[OC3DAIG_PREFIX_LOW . 'images_count']);
                // Set the number of images to 1 if the model is 'dall-e-3' or 'dall-e-3-hd'.
                // Set the number of images to 1 if the model is 'dall-e-3' or 'dall-e-3-hd'.
                if ($img_model === 'dall-e-3' || $img_model === 'dall-e-3-hd') {
                    $num_images = 1;

                    // If the image size is either '256x256' or '512x512', set it to '1024x1024'.
                    if (in_array($img_size, ['256x256', '512x512'])) {
                        $img_size = '1024x1024';
                    }
                }

                $prompt_elements = array(
                    OC3DAIG_PREFIX_LOW . 'artist_opt' => esc_html__('Painter', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'art_style_opt' => esc_html__('Style', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'photography_style_opt' => esc_html__('Photography Style', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'composition_opt' => esc_html__('Composition', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'resolution_opt' => esc_html__('Resolution', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'color_opt' => esc_html__('Color', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'special_effects_opt' => esc_html__('Special Effects', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'lighting_opt' => esc_html__('Lighting', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'subject_opt' => esc_html__('Subject', 'oc3d-ai-genius'),
                    OC3DAIG_PREFIX_LOW . 'camera_opt' => esc_html__('Camera Settings', 'oc3d-ai-genius'),
                );
                foreach ($prompt_elements as $key => $value) {
                    if ($_POST[$key] != "None") {
                        $prompt = $prompt . ". " . $value . ": " . sanitize_text_field($_POST[$key]);
                    }
                }
                //$imgresult = [];//remove

                $imgresult = $this->imageRequest([
                    "model" => $img_model,
                    "prompt" => $prompt,
                    "n" => $num_images,
                    "size" => $img_size,
                    "response_format" => "url",
                ]);
                // If quality is set to 'hd', add it to the request array.
                if ($quality === 'hd') {
                    $image_request_array['quality'] = $quality;
                }
                $img_result = json_decode($imgresult);
                if (isset($img_result->error)) {
                    $oc3daig_result['msg'] = trim($img_result->error->message);
                    if (strpos($oc3daig_result['msg'], 'limit has been reached') !== false) {
                        $oc3daig_result['msg'] .= ' ' . esc_html__('Please note that this message is coming from OpenAI and it is not related to our plugin. It means that you do not have enough credit from OpenAI. You can check your usage here: https://platform.openai.com/account/usage', 'oc3d-ai-genius');
                    }
                } else {
                    $oc3daig_result['imgs'] = array();
                    //$num_images = 9;//remove
                    //sleep(2);//remove
                    for ($i = 0; $i < $num_images; $i++) {
                        $oc3daig_result['imgs'][] = $img_result->data[$i]->url;//$this->getTestImage();//remove//$img_result->data[$i]->url;//
                    }
                    $oc3daig_result['title'] = $prompt_title;
                    $oc3daig_result['status'] = 'success';
                    
                }
            }
            wp_send_json($oc3daig_result);
        }


        public function saveImageToMedia() {
            $oc3daig_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong', 'oc3d-ai-genius'));
            if (!wp_verify_nonce($_POST['nonce'], 'oc3daig-ajax-nonce')) {
                $oc3daig_result['status'] = 'error';
                $oc3daig_result['msg'] = esc_html__('Nonce verification failed', 'oc3d-ai-genius');
                wp_send_json($oc3daig_result);
            }
            //sleep(7);//remove
            if (
                    isset($_POST['image_url']) && !empty($_POST['image_url'])
            ) {
                $url = sanitize_url($_POST['image_url']);
                $image_title = isset($_POST['image_title']) && !empty($_POST['image_title']) ? sanitize_text_field($_POST['image_title']) : '';
                $image_alt = isset($_POST['image_alt']) && !empty($_POST['image_alt']) ? sanitize_text_field($_POST['image_alt']) : '';
                $image_caption = isset($_POST['image_caption']) && !empty($_POST['image_caption']) ? sanitize_text_field($_POST['image_caption']) : '';
                $image_description = isset($_POST['image_description']) && !empty($_POST['image_description']) ? sanitize_text_field($_POST['image_description']) : '';
                $oc3daig_image_attachment_id = $this->saveImage($url, $image_title);
                if ($oc3daig_image_attachment_id['status'] == 'success') {
                    wp_update_post(array(
                        'ID' => $oc3daig_image_attachment_id['id'],
                        'post_content' => $image_description,
                        'post_excerpt' => $image_caption
                    ));
                    update_post_meta($oc3daig_image_attachment_id['id'], '_wp_attachment_image_alt', $image_alt);
                    $oc3daig_result['status'] = 'success';
                } else {
                    $oc3daig_result['msg'] = $oc3daig_image_attachment_id['msg'];
                }
            }
            wp_send_json($oc3daig_result);
        }

        public function saveImage($imageurl, $image_title = '') {
            global $wpdb;
            $result = array('status' => 'error', 'msg' => esc_html__('Can not save image to media', 'oc3d-ai-genius'));
            if (!function_exists('wp_generate_attachment_metadata')) {
                include_once( ABSPATH . 'wp-admin/includes/image.php' );
            }
            if (!function_exists('download_url')) {
                include_once( ABSPATH . 'wp-admin/includes/file.php' );
            }
            if (!function_exists('media_handle_sideload')) {
                include_once( ABSPATH . 'wp-admin/includes/media.php' );
            }
            try {
                $array = explode('/', getimagesize($imageurl)['mime']);
                $imagetype = end($array);
                $uniq_name = md5($imageurl);
                $filename = $uniq_name . '.' . $imagetype;
                $checkExist = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE meta_value LIKE %s", '%/' . $wpdb->esc_like($filename)));
                if ($checkExist) {
                    $result['status'] = 'success';
                    $result['id'] = $checkExist->post_id;
                } else {
                    $tmp = download_url($imageurl);
                    if (is_wp_error($tmp)) {
                        $result['msg'] = $tmp->get_error_message();
                        return $result;
                    }
                    $args = array(
                        'name' => $filename,
                        'tmp_name' => $tmp,
                    );
                    $attachment_id = media_handle_sideload($args, 0, '', array(
                        'post_title' => $image_title,
                        'post_content' => $image_title,
                        'post_excerpt' => $image_title
                    ));
                    if (!is_wp_error($attachment_id)) {
                        update_post_meta($attachment_id, '_wp_attachment_image_alt', $image_title);
                        $imagenew = get_post($attachment_id);
                        $fullsizepath = get_attached_file($imagenew->ID);
                        $attach_data = wp_generate_attachment_metadata($attachment_id, $fullsizepath);
                        wp_update_attachment_metadata($attachment_id, $attach_data);
                        $result['status'] = 'success';
                        $result['id'] = $attachment_id;
                    } else {
                        $result['msg'] = $attachment_id->get_error_message();
                        return $result;
                    }
                }
            } catch (\Exception $exception) {
                $result['msg'] = $exception->getMessage();
            }
            return $result;
        }

        public function registerAdminMenu() {
            
        }

        public function imageRequest($opts) {
            $url = self::imageUrl() . "/generations";

            return $this->sendRequest($url, 'POST', $opts);
        }

        public static function imageUrl(): string {
            return self::OPEN_AI_URL . "/images";
        }

        private function sendRequest(string $url, string $method, array $opts = []) {

            $post_fields = json_encode($opts);
            if (array_key_exists('file', $opts)) {
                $boundary = wp_generate_password(24, false);
                $this->headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
                $post_fields = $this->create_body_for_file($opts['file'], $boundary);
            } elseif (array_key_exists('audio', $opts)) {
                $boundary = wp_generate_password(24, false);
                $this->headers['Content-Type'] = 'multipart/form-data; boundary=' . $boundary;
                $post_fields = $this->create_body_for_audio($opts['audio'], $boundary, $opts);
            } else {
                $this->headers['Content-Type'] = 'application/json';
            }
            $stream = false;
            if (array_key_exists('stream', $opts) && $opts['stream']) {
                $stream = true;
            }
            $timeout = get_option(OC3DAIG_PREFIX_LOW . 'connection_timeout', 50);
            $request_options = array(
                'timeout' => $timeout,
                'headers' => $this->headers,
                'method' => $method,
                'body' => $post_fields,
                'stream' => $stream
            );
            if ($post_fields == '[]') {
                unset($request_options['body']);
            }
            $response = wp_remote_request($url, $request_options);
            if (is_wp_error($response)) {
                return json_encode(array('error' => array('message' => $response->get_error_message())));
            } else {
                if ($stream) {
                    return $this->response;
                } else {
                    return wp_remote_retrieve_body($response);
                }
            }
        }


        
        public function setDefaultSettings()
        {
            if ( ! wp_verify_nonce( $_POST['oc3d_imggen_nonce'], 'oc3d_imggen_nonce' ) ) {
                $oc3daig_result['status'] = 'error';
                $oc3daig_result['msg'] = esc_html__('Nonce verification failed','oc3d-ai-genius');
                wp_send_json($oc3daig_result);
            }
                
                $keys = Oc3dAig_ImageUtils::getSettingsKeys();
                $result = array();
                foreach($keys as $key){
                    if(isset($_REQUEST[$key]) && !empty($_REQUEST[$key])){
                        $result[$key] = sanitize_text_field($_REQUEST[$key]);
                    }
                }
                $images_count = (int)$_REQUEST['oc3daig_images_count'];
                
                update_option(OC3DAIG_PREFIX_LOW . 'image_generator_cnt',$images_count);
                update_option(OC3DAIG_PREFIX_LOW . 'image_generator',$result);
                
            wp_send_json(array('status' => 'success'));
        }
        

    }

}
