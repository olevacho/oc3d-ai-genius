<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$default_chat_bot = $this->view_vars['default_bot'];
$chat_bot_options = isset($default_chat_bot->bot_options) && is_array($default_chat_bot->bot_options) && count($default_chat_bot->bot_options) > 0 ? $default_chat_bot->bot_options : [];
$chatbot_hash = isset($default_chat_bot->hash_code)  && strlen($default_chat_bot->hash_code) > 0 ? $default_chat_bot->hash_code : 'default';//hash_code
?>



<div class="oc3daig_container">
    <div id="oc3daig_configtabs">


        <ul>
            <li><a href="#oc3daia-tabs-1"><?php echo esc_html__('General', 'oc3d-ai-aiassistant') ?></a></li>
            <li><a href="#oc3daia-tabs-2"><?php echo esc_html__('CPT Assistant', 'oc3d-ai-aiassistant') ?></a></li>
        </ul>
        <?php
        include OC3DAIG_PATH . '/views/backend/chatbot/chatbot_general.php';
        include OC3DAIG_PATH . '/views/backend/chatbot/chatbot_gptassistant.php';
        ?>
        <div class="oc3daig_bloader">
            <div style="padding: 1em 1.4em;"></div>


        </div>

    </div>
</div>



<script>
    const oc3daajaxAction = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';

    let oc3d_gpt_confnonce = "<?php echo esc_html(wp_create_nonce('oc3d_gpt_confnonce')); ?>";
    let oc3daig_instruction_table_height = 0;

    jQuery(function () {
        jQuery("#oc3daig_configtabs").tabs({activate: function (event, ui) {
                let oc3daig_active_tab = jQuery("#oc3daig_configtabs .oc3daig_tab_panel:visible").attr("id");
                if (oc3daig_active_tab === 'oc3daia-tabs-3') {
                    oc3daig_instruction_table_height = oc3daiaSetTableContainerHeight();
                    let  tbl_div = document.querySelector('#oc3daig_container');
                    if (tbl_div) {
                        tbl_div.style.height = oc3daig_instruction_table_height + 'px';
                    }


                }
            }});
    });
    
    
    jQuery(document).ready(function () {


    });





</script>