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
?>
<div id="oc3daia-tabs-2" class="oc3daig_tab_panel" data-oc3daia="2">
<div class="inside">
    <div class="oc3daig_config_items_wrapper">
        <!--<form action="" method="post" id="oc3daig_chatbot_gen_form">  -->  
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_assistant_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>chatbot_hash" value="<?php echo esc_html($chatbot_hash); ?>"/>
                <input type="hidden" name="action" value="<?php echo esc_html(OC3DAIG_PREFIX_SHORT); ?>store_chatbot_general_tab"/>
                <div class="oc3daig_block_content">

                    <div class="oc3daig_row_content oc3daig_pr">

                        <div class="oc3daig_bloader oc3daig_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-aiassistant') ?>" 
                                       name="oc3daig_submit" 
                                       id="oc3daig_submit" class="button button-primary button-large" 
                                        >

                            </div>

                            <div class="oc3daia-custom-loader oc3daia-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                
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
                                    <h3><?php esc_html_e('Appearance', 'oc3d-ai-aiassistant'); ?></h3>
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
        <!--</form>   -->     
        

    </div>
</div>
<script>
    //onclick="oc3daiaSaveChatbotAssistantUploadFile(event);
    function oc3daiaSaveChatbotAssistantUploadFile(e){
        e.preventDefault;
        //store_chatbot_assistant_upload
        document.getElementById("oc3daig_chatbot_upload_form").submit();//oc3daig_chatbot_upload_form
   

    }
</script>