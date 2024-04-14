<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(true){
$wp_nonce = wp_create_nonce(OC3DAIG_PREFIX_SHORT . 'chatbot_config_nonce');

$metrics = Oc3dAig_ChatBotUtils::getMetrics();
//var_dump($metrics);

$max_tokens = (int) get_option(OC3DAIG_PREFIX_LOW . 'max_tokens', 1024);

$models = Oc3dAig_ChatBotUtils::getModels();


?>
<div id="oc3daia-tabs-1" class="oc3daig_tab_panel" data-oc3daia="1">
    <div class="inside">
        <div class="oc3daig_config_items_wrapper">
            <?php
            //var_dump($default_chat_bot);
            $need_key_enter = true;
            $api_key = get_option(OC3DAIG_PREFIX_LOW . 'open_ai_gpt_key', '');
            if(strlen($api_key) > 0){
                $need_key_enter = false;
            }
if(true){
?>
            <form action="" method="post" id="oc3daig_chatbot_gen_form">    
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_config_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_hash" value="<?php echo esc_html($chatbot_hash); ?>"/>
                <input type="hidden" name="action" value="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>store_chatbot_general_tab"/>
                <div class="oc3daig_block_content">

                    <div class="oc3daig_row_content oc3daig_pr">

                        <div class="oc3daig_bloader oc3daig_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-aiassistant') ?>" 
                                       name="oc3daig_submit" 
                                       id="oc3daig_submit" class="button button-primary button-large" 
                                       onclick="oc3daiaSaveChatbotGeneral(event);" >

                            </div>

                            <div class="oc3daia-custom-loader oc3daia-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                <?php
                if($need_key_enter){
                
                ?>
                <h1 style="color:red;text-align: center;"><?php echo esc_html__('You need to enter Open I API Key before configurations start working', 'oc3d-ai-aiassistant'); ?>. <?php echo esc_html__('Please open', 'oc3d-ai-aiassistant'); ?>  <a href="<?php echo esc_url(admin_url()) . 'admin.php?page=oc3daig_settings'; ?>"><?php echo esc_html__('this page', 'oc3d-ai-aiassistant'); ?></a> <?php esc_html__('and enter Open AI key', 'oc3d-ai-aiassistant'); ?>.</h1>
                <?php
                }
                
                ?>
                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Assistant behavior', 'oc3d-ai-aiassistant'); ?></h3>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_access_for_guests2">
                                            <?php esc_html_e('Access for guests', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php 
                                            $checked = '';
                                            $access_for_guests2 = isset($chat_bot_options['access_for_guests2'])?(int)$chat_bot_options['access_for_guests2']:1; 
                                            if ($access_for_guests2 == 1) {
                                                    $checked = ' checked ';
                                                }
                                            ?>
                                            
                                            <input type="checkbox" id="oc3daig_chatbot_access_for_guests2" 
                                                   name="oc3daig_chatbot_access_for_guests2" 
                                                       <?php echo esc_html($checked); ?>  >

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Check box if you want to make chatbot accessible for anonimous visitors', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_context2"><?php esc_html_e('Context', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    <?php $context2 = isset($chat_bot_options['context2']) ? $chat_bot_options['context2'] : ''; ?>
                                                    <textarea id="oc3daig_chatbot_config_context2" 
                                                              name="oc3daig_chatbot_config_context2"><?php echo esc_html($context2); ?></textarea>
                                                    
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('The text that you will write in the Context field will be added to the beginning of the prompt. Note, in case you want to use the default message, you will need to leave the field blank.', 'oc3d-ai-aiassistant'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                </div>
                                
                                       
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_chat_model2"><?php esc_html_e('Model', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position:relative;">
                                                    <select id="oc3daig_chatbot_config_chat_model2" name="oc3daig_chatbot_config_chat_model2">
                                                        <?php
                                                        $model2 = isset($chat_bot_options['chat_model2']) ? esc_html($chat_bot_options['chat_model2']) : 'gpt-3.5-turbo-16k';

                                                        foreach ($models as $value) {
                                                            if ($model2 == $value) {
                                                                $sel_opt = 'selected';
                                                            } else {
                                                                $sel_opt = '';
                                                            }
                                                            ?>
                                                            <option value="<?php echo esc_html($value); ?>" <?php echo esc_html($sel_opt); ?>><?php echo esc_html($value); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;"><?php esc_html_e('Select model', 'oc3d-ai-aiassistant'); ?></span>
                                                </p>
                                            </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_chat_temperature2">
                                            <?php esc_html_e('Temperature', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $chat_temperature2 = isset($chat_bot_options['chat_temperature2'])?$chat_bot_options['chat_temperature2']:0.8; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_chat_temperature2"  
                                                   id="oc3daig_chatbot_config_chat_temperature2" type="number" 
                                                   step="0.1" min="0" max="2" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Enter number pixels or percent', 'oc3d-ai-aiassistant'); ?>"
                                                   value="<?php echo esc_html($chat_temperature2); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Input temperature from 0 to 2.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_chat_top_p2">
                                            <?php esc_html_e('Top P', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $chat_top_p2 = isset($chat_bot_options['chat_top_p2'])?$chat_bot_options['chat_top_p2']:1; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_chat_top_p2"  
                                                   id="oc3daig_chatbot_config_chat_top_p2" type="number" 
                                                   step="0.1" min="0" max="1" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($chat_top_p2); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('With this option, the model considers only the most probable tokens, based on a specified probability threshold. For example, if the top_p value is set to 0.1, only the tokens with the highest probability mass that make up the top 10% of the distribution will be considered for output. This can help generate more focused and coherent responses, while still allowing for some level of randomness and creativity in the generated text.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_max_tokens2">
                                            <?php esc_html_e('Maximum tokens', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $max_tokens2 = isset($chat_bot_options['max_tokens2'])?$chat_bot_options['max_tokens2']:2048; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_max_tokens2"  
                                                   id="oc3daig_chatbot_config_max_tokens2" type="number" 
                                                   step="1" min="0" max="256000" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($max_tokens2); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Specifies the maximum number of tokens (words or word-like units) that the assistant will generate in response to a prompt. This can be used to control the length of the generated text.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_frequency_penalty2">
                                            <?php esc_html_e('Frequency penalty', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $frequency_penalty2 = isset($chat_bot_options['frequency_penalty2'])?$chat_bot_options['frequency_penalty2']:0; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_frequency_penalty2"  
                                                   id="oc3daig_chatbot_config_frequency_penalty2" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($frequency_penalty2); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the assistant to generate text with a more diverse vocabulary. A higher frequency penalty value will reduce the likelihood of the chatbot repeating words that have already been used in the generated text. Number between -2.0 and 2.0.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_presence_penalty2">
                                            <?php esc_html_e('Presence penalty', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $presence_penalty2 = isset($chat_bot_options['presence_penalty2'])?$chat_bot_options['presence_penalty2']:0; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_presence_penalty2"  
                                                   id="oc3daig_chatbot_config_presence_penalty2" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($presence_penalty2); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the assistant to generate text that includes specific phrases or concepts. A higher presence penalty value will reduce the likelihood of the chatbot repeating the same phrases or concepts multiple times in the generated text. Number between -2.0 and 2.0.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                

                                
                                
                                

                                
                                


                                

                            </div>
                        </div>   
                              
                    </div>
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Chatbot behavior', 'oc3d-ai-aiassistant'); ?></h3>
                                </div>
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_access_for_guests">
                                            <?php esc_html_e('Access for guests', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php 
                                            $checked = '';
                                            $access_for_guests = isset($chat_bot_options['access_for_guests'])?(int)$chat_bot_options['access_for_guests']:1; 
                                            if ($access_for_guests == 1) {
                                                    $checked = ' checked ';
                                                }
                                            ?>
                                            
                                            <input type="checkbox" id="oc3daig_chatbot_access_for_guests" 
                                                   name="oc3daig_chatbot_access_for_guests" 
                                                       <?php echo esc_html($checked); ?>  >

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Check box if you want to make chatbot accessible for anonimous visitors', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_context"><?php esc_html_e('Context', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    <?php $context = isset($chat_bot_options['context']) ? $chat_bot_options['context'] : ''; ?>
                                                    <textarea id="oc3daig_chatbot_config_context" 
                                                              name="oc3daig_chatbot_config_context"><?php echo esc_html($context); ?></textarea>
                                                    
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('The text that you will write in the Context field will be added to the beginning of the prompt. Note, in case you want to use the default message, you will need to leave the field blank.', 'oc3d-ai-aiassistant'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                </div>
                                <?php 
                                if(false){
                                ?>
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_greeting_message"><?php esc_html_e('Greeting message', 'oc3d-ai-aiassistant'); ?>:</label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            $greeting_message = isset($chat_bot_options['greeting_message'])?(int)$chat_bot_options['greeting_message']:1; 
                                            $checked = '';
                                                if ($greeting_message == 1) {
                                                    $checked = ' checked ';
                                                }
                                                
                                            ?>
                                           <input type="checkbox" id="oc3daig_chatbot_config_greeting_message" 
                                                  name="oc3daig_chatbot_config_greeting_message" 
                                                      <?php echo esc_html($checked); ?>  >
                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Select box if you want display greeting message', 'oc3d-ai-aiassistant'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                    
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_greeting_message_text">
                                            <?php esc_html_e('Greetin message text', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php 
                                            $greeting_message_text = isset($chat_bot_options['greeting_message_text'])?$chat_bot_options['greeting_message_text']:''; 
                                              ?>
                                           <input type="text" name="oc3daig_chatbot_config_greeting_message_text" 
                                                  id="oc3daig_chatbot_config_greeting_message_text" 
                                                  value="<?php echo esc_html($greeting_message_text); ?>">
                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Enter greeting message', 'oc3d-ai-aiassistant'); ?> 
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <?php 
                                }
                                ?>
                                
                                <?php
                                if(false){
                                ?>
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_language"><?php esc_html_e('Language', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    <?php $language = isset($chat_bot_options['language']) ? $chat_bot_options['language'] : 'english'; ?>
                                                    <input type="text" id="oc3daig_chatbot_config_language" 
                                                           name="oc3daig_chatbot_config_language" 
                                                           value="<?php echo esc_html($language); ?>" />
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('Language setting for the chatbot', 'oc3d-ai-aiassistant'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                </div>
                                <?php
                                }
                                ?>        
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_chat_model"><?php esc_html_e('Model', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position:relative;">
                                                    <select id="oc3daig_chatbot_config_chat_model" name="oc3daig_chatbot_config_chat_model">
                                                        <?php
                                                        $model = isset($chat_bot_options['chat_model']) ? esc_html($chat_bot_options['chat_model']) : 'gpt-3.5-turbo-16k';

                                                        foreach ($models as $value) {
                                                            if ($model == $value) {
                                                                $sel_opt = 'selected';
                                                            } else {
                                                                $sel_opt = '';
                                                            }
                                                            ?>
                                                            <option value="<?php echo esc_html($value); ?>" <?php echo esc_html($sel_opt); ?>><?php echo esc_html($value); ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;"><?php esc_html_e('Select model', 'oc3d-ai-aiassistant'); ?></span>
                                                </p>
                                            </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_chat_temperature">
                                            <?php esc_html_e('Temperature', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $chat_temperature = isset($chat_bot_options['chat_temperature'])?$chat_bot_options['chat_temperature']:0.8; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_chat_temperature"  
                                                   id="oc3daig_chatbot_config_chat_temperature" type="number" 
                                                   step="0.1" min="0" max="2" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Enter number pixels or percent', 'oc3d-ai-aiassistant'); ?>"
                                                   value="<?php echo esc_html($chat_temperature); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Input temperature from 0 to 2.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_chat_top_p">
                                            <?php esc_html_e('Top P', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $chat_top_p = isset($chat_bot_options['chat_top_p'])?$chat_bot_options['chat_top_p']:1; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_chat_top_p"  
                                                   id="oc3daig_chatbot_config_chat_top_p" type="number" 
                                                   step="0.1" min="0" max="1" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($chat_top_p); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('With this option, the model considers only the most probable tokens, based on a specified probability threshold. For example, if the top_p value is set to 0.1, only the tokens with the highest probability mass that make up the top 10% of the distribution will be considered for output. This can help generate more focused and coherent responses, while still allowing for some level of randomness and creativity in the generated text.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_max_tokens">
                                            <?php esc_html_e('Maximum tokens', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $max_tokens = isset($chat_bot_options['max_tokens'])?$chat_bot_options['max_tokens']:2048; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_max_tokens"  
                                                   id="oc3daig_chatbot_config_max_tokens" type="number" 
                                                   step="1" min="0" max="256000" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($max_tokens); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Specifies the maximum number of tokens (words or word-like units) that the chatbot will generate in response to a prompt. This can be used to control the length of the generated text.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_frequency_penalty">
                                            <?php esc_html_e('Frequency penalty', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $frequency_penalty = isset($chat_bot_options['frequency_penalty'])?$chat_bot_options['frequency_penalty']:0; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_frequency_penalty"  
                                                   id="oc3daig_chatbot_config_frequency_penalty" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($frequency_penalty); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the chatbot to generate text with a more diverse vocabulary. A higher frequency penalty value will reduce the likelihood of the chatbot repeating words that have already been used in the generated text. Number between -2.0 and 2.0.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_presence_penalty">
                                            <?php esc_html_e('Presence penalty', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php $presence_penalty = isset($chat_bot_options['presence_penalty'])?$chat_bot_options['presence_penalty']:0; ?>
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_chatbot_config_presence_penalty"  
                                                   id="oc3daig_chatbot_config_presence_penalty" type="number" 
                                                   step="0.01" min="-2" max="2" maxlength="4" autocomplete="off"  
                                                   value="<?php echo esc_html($presence_penalty); ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Encourages the chatbot to generate text that includes specific phrases or concepts. A higher presence penalty value will reduce the likelihood of the chatbot repeating the same phrases or concepts multiple times in the generated text. Number between -2.0 and 2.0.', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                
                            </div>
                        </div> 
                    </div>

                </div>
<?php
}
?>
            </form>
        </div>

    </div>
</div>

<script>
    let oc3daig_message_config_general_error = '<?php esc_html_e('There were errors during store configuration.', 'oc3d-ai-aiassistant'); ?>';
    let oc3daig_message_config_general_succes1 = '<?php esc_html_e('Configuration stored successfully.', 'oc3d-ai-aiassistant'); ?>';

</script>
<?php
}
if(false){
?>

<div id="oc3daia-tabs-1" class="oc3daig_tab_panel" data-oc3daia="1">
<h2>Tab 1</h2>
</div>

<?php
}
?>