<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if (!class_exists('Oc3dAig_ChatBotClassicHistoryView')) {

    class Oc3dAig_ChatBotClassicHistoryView {
        /*
         * $data_par - string that represents array encoded to json
         * $data_parameters - 
         */
        public function render($data_par,$data_parameters){
            
                ob_start();
                //var_dump($data_parameters);
                
                $chatbot_picture_url = OC3DAIG_URL."/views/resources/img/chatbot.svg";
				
                ?>
	<div class="oc3daia-bot-chatbot" style="" data-parameters='<?php echo $data_par;?>' >
        <div class="oc3daia-bot-chatbot-closed-view" style=""> 
            <div class="oc3daia-bot-closed-ic-container">
                <img class="oc3daia-bot-chatbot-logo-img" src="<?php echo esc_url($chatbot_picture_url); ?>" alt="Chat Assistant Icon">
            </div>
        </div>
        <div class="oc3daia-bot-chatbot-maximized-bg" style="display: none;"></div>
        <div class="oc3daia-bot-chatbot-main-container" style="display: none;">
            <div class="oc3daia-bot-chatbot-main-chat-modal" style="display: none;">
                
            </div>
            <div class="oc3daia-bot-chatbot-main-chat-box">
                <div class="oc3daia-bot-chatbot-header-row"> 

                    <div class="oc3daia-bot-header-row-logo-row">
                        <div class="oc3daia-bot-header-row-logo">
                            <img class="oc3daia-bot-header-row-logo-image" src="<?php echo esc_url($chatbot_picture_url); ?>" alt="Chat Assistant Icon">
                        </div>
                        <p class="oc3daia-bot-chatbot-header-text"><?php echo isset($data_parameters[OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chatbot_name'])?esc_html($data_parameters[OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'chatbot_name']):esc_html__('AI Assistant','oc3d-ai-aiassistant'); ?></p>
                    </div>
                    <div class="oc3daia-bot-chatbot-logo">
                        <img src="<?php echo esc_url(OC3DAIG_URL); ?>/views/resources/img/maximize.svg" alt="Maximize" class="oc3daia-bot-chatbot-resize-bttn">
                        <img src="<?php echo esc_url(OC3DAIG_URL); ?>/views/resources/img/end-button.svg" alt="End" class="oc3daia-bot-chatbot-end-bttn">
                    </div>
                </div>
                <div class="oc3daia-bot-chatbot-messages-box"> 
                    <div class="oc3daia-bot-chatbot-loading-box" style=" display: none;"> 
                        <div class="oc3daia-bot-chatbot-loader-ball-2">
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                    </div>
                    
                </div>
                <?php
                $send_btn_key = OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'send_button_text';
                $clear_btn_key = OC3DAIG_CHATGPT_BOT_OPTIONS_PREFIX.'clear_button_text';
                if(isset($data_parameters[$send_btn_key])){
                    $send_button_text = $data_parameters[$send_btn_key];
                }else{
                    $send_button_text = __('Send','oc3d-ai-aiassistant');
                }
                if(isset($data_parameters[$send_btn_key])){
                    $clear_button_text = $data_parameters[$clear_btn_key];
                }else{
                    $clear_button_text = __('Clear','oc3d-ai-aiassistant');
                }
                
                
                
                ?>
                <div class="oc3daia-bot-chatbot-input-box"> 
                    <textarea style="overflow: hidden scroll; overflow-wrap: break-word; height: 60px;" rows="1" id="oc3daiabotchatbotpromptinput" class="oc3daia-bot-chatbot-prompt-input oc3daia-bot-chatbot-prompt-inputs-all" name="oc3daig_bot_chatbot_prompt" id="oc3daia-bot-chatbot-prompt" placeholder=""></textarea>
                    <button class="oc3daia-bot-chatbot-send-button"  onclick="oc3daiaSendMessage(event);">
                        <span><?php echo esc_html($send_button_text);  ?></span>	
                    </button>
                </div>
                <?php

                ?>
                <input type="hidden" id="oc3daiaidbot" value="<?php echo esc_html($data_parameters['bot_id']); ?>"/>
            </div>
        </div>
    </div>
    <script>
    let oc3daig_button_config_general_send = '<?php echo esc_html($send_button_text); ?>';
    let oc3daig_button_config_general_clear = '<?php echo esc_html($clear_button_text); ?>';
    </script>

				<?php
           
		$content = ob_get_clean();
                return $content;
        }
    }
    
}
