<?php ?>
<script>
    let oc3daig_copy_clipboard_sucess = '<?php esc_html_e('Copying to clipboard was successful!', 'oc3d-ai-genius'); ?>';
    let oc3daig_copy_clipboard_fail = '<?php esc_html_e('Could not copy text', 'oc3d-ai-genius'); ?>';
    
    jQuery(function () {
        jQuery("#oc3daig_metatabs").tabs();
    });
</script>


<?php ?>

<div class="oc3daig_container">
    <div id="oc3daig_metatabs">
        <form action="" method="post" id="oc3daig_post">
            <?php
//wp_nonce_field('oc3daig_setting_save');
            ?>
            <ul>
                <li><a href="#oc3daig-tabs-1"><?php echo esc_html__('Edit & Extend', 'oc3d-ai-genius') ?></a></li>

                <li><a href="#oc3daig-tabs-3"><?php echo esc_html__('Expert', 'oc3d-ai-genius') ?></a></li>
            </ul>
            <?php
            include OC3DAIG_PATH . '/views/backend/correct.php';
            include OC3DAIG_PATH . '/views/backend/generate.php';
            ?>

        </form>
    </div>
</div>
<script>
    const oc3daajaxAction = '<?php echo admin_url('admin-ajax.php'); ?>';
    let oc3daig_jquery_is_not_installed = '<?php esc_html_e('Error. jQuery library is not installed!', 'oc3d-ai-genius'); ?>';
    (function ($) {
    $(document).ready(function () {
        
        $('.oc3daig_submit_meta').click(function (event) {
            console.log('Send GPT request ');
            event.preventDefault();
            console.log($("#oc3daig_metatabs").tabs('option', 'selected'));
            console.log($("#oc3daig_metatabs .oc3daig_tab_panel:visible").attr("id"));
            console.log($("#oc3daig_metatabs .oc3daig_tab_panel:visible").attr("data-oc3daig"));
            let oc3d_active_panel = $("#oc3daig_metatabs .oc3daig_tab_panel:visible").attr("data-oc3daig");
            if (oc3d_active_panel <= 0) {
                console.log('error = can not find active panel ');
                return;
            }
            let oc3ddata = {'oc3d_gpt_nonce': "<?php echo wp_create_nonce('oc3d_gpt_nonce'); ?>"};
            switch (oc3d_active_panel) {
                case '1':
                    oc3ddata['action'] = 'oc3d_gpt_correct';
                    oc3ddata['instructiontype'] = $('input[name="oc3daig_instructiontype"]:checked').val();
                    oc3ddata['model'] = $('#oc3daig_model_c').find(":selected").val();
                    oc3ddata['temperature'] = $('#oc3daig_temperature_c').val();

                    oc3ddata['max_tokens'] = $('#oc3daig_max_tokens_c').val();
                    oc3ddata['instruction'] = $('#oc3daig_instruction').val();
                    oc3ddata['text'] = $('#oc3daig_text_c').val();
                    
                    $('#oc3daig_result_c').val('');
                    oc3d_performAjax.call(oc3d_correct_result_dynamic, oc3ddata);
                    break;
                case '2':
                    oc3ddata['action'] = 'oc3d_gpt_generate';
                    oc3ddata['model'] = $('#oc3daig_model_e').find(":selected").val();
                    oc3ddata['temperature'] = $('#oc3daig_temperature_e').val();
                    oc3ddata['max_tokens'] = $('#oc3daig_max_tokens_e').val();
                    oc3ddata['top_p'] = $('#oc3daig_top_p_edit').val();
                    oc3ddata['presence_penalty'] = $('#oc3daig_presence_penalty_e').val();
                    oc3ddata['frequency_penalty'] = $('#oc3daig_frequency_penalty_e').val();
                    oc3ddata['system'] = $('#oc3daig_system_e').val();
                    let cnt_els = oc3daig_radion_lst2.length;
                    let oc3daig_actors = [];
                    let oc3daig_msgs = [];

                    for (let i = 1; i < cnt_els; i++) {
                        if (oc3daig_radion_lst2[i] === 'Deleted') {
                            continue;
                        }
                        oc3daig_actors.push(oc3daig_radion_lst2[i]);
                        let oc3daig_textarea = document.querySelector("#oc3daig_message_e_" + i);
                        if (oc3daig_textarea) {
                            oc3daig_msgs.push(oc3daig_textarea.value);
                        } else {
                            oc3daig_msgs.push('');
                        }
                        oc3ddata['actors'] = oc3daig_actors;
                        oc3ddata['msgs'] = oc3daig_msgs;
                    }
                    $('#oc3daig_response_e').val('');
                    oc3d_performAjax.call(oc3d_generate_result_dynamic, oc3ddata);

                    break;
                default:
                    console.log('Sorry, we are out of ' + oc3d_active_panel);
                    return;
            }

        });
        
        let oc3daig_temperature_c_prevent = document.getElementById("oc3daig_temperature_c");
        oc3daig_temperature_c_prevent.addEventListener("keypress", oc3daig_preventDefault);
        
        let oc3daig_max_tokens_c_prevent = document.getElementById("oc3daig_max_tokens_c");
        oc3daig_max_tokens_c_prevent.addEventListener("keypress", oc3daig_preventDefault);
        
        let oc3daig_search_prevent = document.getElementById("oc3daig_search");
        oc3daig_search_prevent.addEventListener("keypress", oc3daig_preventDefault);
        
        let oc3daig_temperature_e_prevent = document.getElementById("oc3daig_temperature_e");
        oc3daig_temperature_e_prevent.addEventListener("keypress", oc3daig_preventDefault);
        
        let oc3daig_max_tokens_e_prevent = document.getElementById("oc3daig_max_tokens_e");
        oc3daig_max_tokens_e_prevent.addEventListener("keypress", oc3daig_preventDefault);

        let oc3daig_top_p_edit_prevent = document.getElementById("oc3daig_top_p_edit");
        oc3daig_top_p_edit_prevent.addEventListener("keypress", oc3daig_preventDefault);

        let oc3daig_presence_penalty_e_prevent = document.getElementById("oc3daig_presence_penalty_e");
        oc3daig_presence_penalty_e_prevent.addEventListener("keypress", oc3daig_preventDefault);

        let oc3daig_frequency_penalty_e_prevent = document.getElementById("oc3daig_frequency_penalty_e");
        oc3daig_frequency_penalty_e_prevent.addEventListener("keypress", oc3daig_preventDefault);
        
        if(!navigator || !navigator.clipboard){
            let oc3daig_cp_clipb_links = document.querySelectorAll('.oc3daig_copy_link');
            for (let ii in oc3daig_cp_clipb_links){
				let oc3daig_cp_clipb_link = oc3daig_cp_clipb_links[ii];
				if(oc3daig_cp_clipb_link.remove){
					oc3daig_cp_clipb_link.remove();
				}
            }
        }
        
    });
})(jQuery);

    
    

</script>
