<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$wp_nonce = wp_create_nonce(OC3DAIG_PREFIX_SHORT . 'chatbot_assistant_nonce');
$uploaded_f = get_option(OC3DAIG_PREFIX_LOW . 'assistant_file');
$uploaded_default_file = ['code'=>0,'error_msg'=>'','id'=>'','filename' =>''];
if(is_string($uploaded_f)){
	$uploaded_f_arr = unserialize($uploaded_f);
	if(is_array($uploaded_f_arr)){
		$uploaded_file =  $uploaded_f_arr;
	}
	else{
		$uploaded_file = $uploaded_default_file;
	}
}else{
	$uploaded_file = $uploaded_default_file;
}
if ($uploaded_f == FALSE) {
    $uploaded_file = $uploaded_default_file;
}
$file_id = isset($uploaded_file['id'])?$uploaded_file['id']:'';
$file_path = isset($uploaded_file['filename'])?$uploaded_file['filename']:'';
$models = Oc3dAig_ChatBotUtils::getModels();
$default_provider = get_option(OC3DAIG_PREFIX_LOW . 'chat_bot_provider');
$chat_bot_providers = Oc3dAig_ChatBotUtils::getProviders();
$assistant_opts = get_option(OC3DAIG_PREFIX_LOW . 'assistant_options');
$assistant_opts_default = ['code'=>0,'error_msg'=>'','id'=>'','model' =>''
    ,'created_at' =>'','instruction' =>'','name' =>'','description'=>'','assistant_timeout'=>3];
if(is_string($assistant_opts)){
	$assistant_opts_arr = unserialize($assistant_opts);
	if(is_array($assistant_opts_arr)){
		$assistant =  $assistant_opts_arr;
	}
	else{
		$assistant = $assistant_opts_default;
	}
}else{
	$assistant = $assistant_opts_default;
}
if ($assistant_opts == FALSE) {
    $assistant = $assistant_opts_default;
}
$assistant_id = isset($assistant['id'])?$assistant['id']:'';

$instruction = isset($assistant['instruction'])?$assistant['instruction']:'';
$model = isset($assistant['model'])?$assistant['model']:'';
$assistant_name = isset($assistant['name'])?$assistant['name']:'';
$assistant_timeout = isset($assistant['assistant_timeout'])?(int)$assistant['assistant_timeout']:1;
?>
<div id="oc3daia-tabs-2" class="oc3daig_tab_panel" data-oc3daia="2">
<div class="inside">
    <div class="oc3daig_config_items_wrapper">

                
                
                <?php
                $adm_url = admin_url( 'admin-post.php' );
                ?>
                <form action="<?php echo esc_url($adm_url); ?>" method="post" enctype="multipart/form-data" id="oc3daig_chatbot_upload_form"> 
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_assistant_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_hash" value="<?php echo esc_html($chatbot_hash); ?>"/>
                <input type="hidden" name="action" value="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>store_chatbot_upload"/>
                    
                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Step 1. Upload file', 'oc3d-ai-aiassistant'); ?></h3>
                                </div>
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_chatbot_config_position"><?php esc_html_e('Step1. Select file', 'oc3d-ai-aiassistant'); ?>:</label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <?php
                                            if(strlen($file_id) > 0){
                                            ?>
                                            <p id="oc3daig_uploaded_file"><?php echo esc_html($file_id); ?></p>
                                            <p id="oc3daig_uploaded_filepath"><?php echo esc_html($file_path); ?></p>
                                            <?php
                                            }else{
                                            ?>
                                            
                                            <input type="file" id="oc3daig_chatbot_config_database" 
                                                   name="oc3daig_chatbot_config_database" accept=".doc,.docx,.txt" />
                                            <?php
                                            }
                                            ?>
                                            
                                        </div>
                                        <?php
                                        if(strlen($file_id) <= 0){
                                        ?>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e(' Select file as database for assistant. By now only doc, docx and txt fileas are accepted.', 'oc3d-ai-aiassistant'); ?> 
                                            </span>
                                        </p>
                                        <p class="oc3daig_input_description">
                                            <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-aiassistant') ?>" 
                                       name="oc3daig_submit" 
                                       id="oc3daig_submit" class="button button-primary button-large" 
                                       " >
                                        </p>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>                 

        
        <form action="<?php echo esc_url($adm_url); ?>" method="post" enctype="multipart/form-data" id="oc3daig_assistant_manage_form"> 
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_assistant_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_hash" value="<?php echo esc_html($chatbot_hash); ?>"/>
                <input type="hidden" id="oc3daig_assistant_manage_action" name="action" value="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>create_assistant"/>
                    
                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Step 2. Create Assistant', 'oc3d-ai-aiassistant'); ?></h3>
                                </div>
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_assistant_id"><?php esc_html_e('Assistant ID', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    
                                                    <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php echo esc_html($assistant_id); ?>
                                                    </span>
                                                </p>
                                                <?php
                                                if(strlen($assistant_id) > 1){
                                                ?>
                                                <p class="oc3daig_input_description">
                                                    <input type="submit" value="<?php echo esc_html__('Remove', 'oc3d-ai-aiassistant') ?>" 
                                                        name="oc3daig_submit" 
                                                        id="oc3daig_submit" class="button button-primary button-large" 
                                                        onclick="oc3daig_removeAssistant(event);"
                                                        " >
                                                </p>
                                                <?php
                                                }
                                                ?>
                                                </div>
                                                
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
                                
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_assistant_name"><?php esc_html_e('Assistant name', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    
                                                    <input type="text"  name="oc3daig_assistant_name"   id="oc3daig_assistant_name"  value="<?php echo esc_html($assistant_name); ?>">
                                                    
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('Enter name of assistant.', 'oc3d-ai-aiassistant'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                </div>
                                
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_assistant_instructions"><?php esc_html_e('Instructions', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position: relative;">
                                                    
                                                    <input type="text"  name="oc3daig_assistant_instructions"   id="oc3daig_assistant_instructions"  value="<?php echo esc_html($instruction); ?>">
                                                    
                                                </div>
                                                <p class="oc3daig_input_description">
                                                    <span style="display: inline;">
                                                        <?php esc_html_e('Enter instructions.', 'oc3d-ai-aiassistant'); ?>
                                                    </span>
                                                </p>
                                            </div>
                                </div>
                                
                                
                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_">
                                            <?php esc_html_e('Timeout', 'oc3d-ai-aiassistant'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            
                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_assistant_timeout"  
                                                   id="oc3daig_assistant_timeout" type="number" 
                                                   step="1" min="1" max="1000" maxlength="4" autocomplete="off"  
                                                   value="<?php echo (int)$assistant_timeout; ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Timeout in seconds is time which bot will wait for generating answer by GPT Assistant. Its value depends on many factors including your internet connection. Try to play with this value to find best results. Minimal value = 1', 'oc3d-ai-aiassistant'); ?>
                                            </span>
                                        </p>
                                        <p class="oc3daig_input_description">
                                            <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-aiassistant') ?>" 
                                                name="oc3daig_submit" 
                                                id="oc3daig_submit" class="button button-primary button-large" 
                                                " >
                                        </p>
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        </div>
                    </div>
                </div>
                </form>
        
        
        <form action="<?php echo esc_url($adm_url); ?>" method="post" enctype="multipart/form-data" id="oc3daig_chatbot_upload_form"> 
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_assistant_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_hash" value="<?php echo esc_html($chatbot_hash); ?>"/>
                <input type="hidden" name="action" value="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_provider"/>
                    
                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Step 3. Select Chatbot provider', 'oc3d-ai-aiassistant'); ?></h3>
                                </div>
                                <div class="oc3daig_block_content">
                                            <div class="oc3daig_row_header">
                                                <label for="oc3daig_chatbot_config_chatbot_provider"><?php esc_html_e('Chat Bot Provider', 'oc3d-ai-aiassistant'); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position:relative;">
                                                    <select id="oc3daig_chatbot_config_chatbot_provider" name="oc3daig_chatbot_config_chatbot_provider">
                                                        <?php
                                                        
                                                        foreach ($chat_bot_providers as $value) {
                                                            if ($default_provider == $value) {
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
                                                    <span style="display: inline;"><?php esc_html_e('Select provider', 'oc3d-ai-aiassistant'); ?></span>
                                                </p>
                                                <p class="oc3daig_input_description">
                                            <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-aiassistant') ?>" 
                                       name="oc3daig_submit3" 
                                       id="oc3daig_submit3" class="button button-primary button-large" 
                                       " >
                                        </p>
                                            </div>
                                </div>
                                
                                
                               
                            </div>
                        </div>
                    </div>
                </div>
                
                </form>

    </div>
</div>
</div>
<script>
    //onclick="oc3daiaSaveChatbotAssistantUploadFile(event);
    function oc3daiaSaveChatbotAssistantUploadFile(e){
        e.preventDefault;
        //store_chatbot_assistant_upload
        document.getElementById("oc3daig_chatbot_upload_form").submit();//oc3daig_chatbot_upload_form
   

    }
    
    function oc3daig_removeAssistant(e){
        e.preventDefault;
        let form = document.querySelector('#oc3daig_assistant_manage_form');
        let action = document.querySelector('#oc3daig_assistant_manage_action');
        action.value = 'oc3d_remove_assistant';
        form.submit();
    }
</script>