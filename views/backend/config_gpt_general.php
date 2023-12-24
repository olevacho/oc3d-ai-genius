<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$wp_nonce = wp_create_nonce(OC3DAIG_PREFIX_SHORT . 'config_nonce');
$menu_page = OC3DAIG_PREFIX_LOW . 'settings';
$p_types = get_post_types();
$stored_selected_p_types = get_option(OC3DAIG_PREFIX_LOW . 'selected_types');

if(is_string($stored_selected_p_types)){
	$stored_selected_p_types_arr = unserialize($stored_selected_p_types);
	if(is_array($stored_selected_p_types_arr)){
		$selected_p_types = array_map('wp_kses', $stored_selected_p_types_arr, []);
	}
	else{
		$selected_p_types = [];
	}
}else{
	$selected_p_types = [];
}
if ($stored_selected_p_types == FALSE) {
    $selected_p_types = ['post', 'page'];
}

$response_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'response_timeout', 120);
$connection_timeout = (int) get_option(OC3DAIG_PREFIX_LOW . 'connection_timeout', 10);
$max_tokens = (int) get_option(OC3DAIG_PREFIX_LOW . 'max_tokens', 1024);
$count_of_instructions = (int) get_option(OC3DAIG_PREFIX_LOW . 'count_of_instructions', 10);
$models = [];
?>
<div id="oc3daig-tabs-1" class="oc3daig_tab_panel" data-oc3daig="1">
    <div class="inside">
        <div class="oc3daig_config_items_wrapper">
            <form action="" method="post" id="oc3daig_gen_form">    
                <input type="hidden" name="<?php echo OC3DAIG_PREFIX_SHORT ?>config_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="action" value="oc3d_store_general_tab"/>
                <div class="oc3daig_block_content">

                    <div class="oc3daig_row_content oc3daig_pr">

                        <div class="oc3daig_bloader oc3daig_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Save', 'oc3d-ai-genius') ?>" name="oc3daig_submit" id="oc3daig_submit" class="button button-primary button-large" onclick="oc3daigSaveGeneral(event);" >

                            </div>

                            <div class="oc3daig-custom-loader oc3daig-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>

                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('ChatGPT general', 'oc3d-ai-genius'); ?></h3>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_open_ai_gpt_key"><?php esc_html_e('Open AI Key', 'oc3d-ai-genius'); ?>:</label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">

                                            <input type="text"  name="oc3daig_open_ai_gpt_key"   id="oc3daig_open_ai_gpt_key"  value="<?php echo esc_html($oc3daig_open_ai_gpt_key); ?>">
                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('You can get your API Keys in your', 'oc3d-ai-genius'); ?> <a href="https://beta.openai.com/account/api-keys" target="_blank"><?php esc_html_e('OpenAI Account', 'oc3d-ai-genius'); ?></a>.
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_connection_timeout"><?php esc_html_e('Connection Timeout (sec)', 'oc3d-ai-genius'); ?>:</label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_connection_timeout"  id="oc3daig_connection_timeout"  type="number" 
                                                   step="1" min="1" max="100" maxlength="3" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Connection Timeout', 'oc3d-ai-genius'); ?>" value="<?php echo (int) $connection_timeout; ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Make this value higher for bad internet connection.', 'oc3d-ai-genius'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_response_timeout">
                                            <?php esc_html_e('Response Timeout (sec)', 'oc3d-ai-genius'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_response_timeout"  id="oc3daig_response_timeout" type="number" 
                                                   step="1"  max="200" maxlength="3" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Response Timeout', 'oc3d-ai-genius'); ?>" value="<?php echo (int)$response_timeout; ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Make this value higher for bad internet connection.', 'oc3d-ai-genius'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_response_timeout">
                                            <?php esc_html_e('Request text length (tokens)', 'oc3d-ai-genius'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_max_tokens"  id="oc3daig_max_tokens" type="number" 
                                                   step="1" maxlength="4" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Max tokens', 'oc3d-ai-genius'); ?>" value="<?php echo (int)$max_tokens; ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Make this value higher for larger text.', 'oc3d-ai-genius'); ?>
                                            </span>
                                        </p>
                                    </div>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <div class="oc3daig_row_header">
                                        <label for="oc3daig_response_timeout">
                                            <?php esc_html_e('Count of instructions per portion', 'oc3d-ai-genius'); ?>:
                                        </label>
                                    </div>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">

                                            <input class="oc3daig_input oc3daig_20pc"  name="oc3daig_count_of_instructions"  
                                                   id="oc3daig_count_of_instructions" type="number" 
                                                   step="1" min="5" maxlength="3" autocomplete="off"  
                                                   placeholder="<?php esc_html_e('Max tokens', 'oc3d-ai-genius'); ?>" value="<?php echo (int)$count_of_instructions; ?>">

                                        </div>
                                        <p class="oc3daig_input_description">
                                            <span style="display: inline;">
                                                <?php esc_html_e('Count of instruction displayed in correction tab in meta box.', 'oc3d-ai-genius'); ?>"
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
                                    <h3><?php esc_html_e('Post Types', 'oc3d-ai-genius'); ?></h3>
                                </div>

                                <div class="oc3daig_block_content" >

                                    <div  class="oc3daig_row_content ">
                                        <div  class="oc3daig_block_header">
                                            <h4><?php esc_html_e('Select post type where you want to display AI assistant meta box.', 'oc3d-ai-genius'); ?></h4>
                                        </div> 
                                        <div class="oc3daig_data_column_container oc3daig_pl20">
                                            <?php
                                            foreach ($p_types as $p_t) {
                                                //var_dump($p_t);
                                                $checked = '';
                                                if (in_array($p_t, $selected_p_types)) {
                                                    $checked = ' checked ';
                                                }
                                                ?>
                                                <div class="oc3daig_c_opt">
                                                    <input type="checkbox" id="<?php echo esc_html($p_t); ?>" name="<?php echo "oc3daig_ptypes[" . esc_html($p_t); ?>]" <?php echo esc_html($checked); ?>  >
                                                    <label for="<?php echo esc_html($p_t); ?>"><?php echo esc_html($p_t); ?></label>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>

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
    let oc3daig_message_config_general_error = '<?php esc_html_e('There were errors during store configuration.', 'oc3d-ai-genius'); ?>';
    let oc3daig_message_config_general_succes1 = '<?php esc_html_e('Configuration stored successfully.', 'oc3d-ai-genius'); ?>';

</script>
