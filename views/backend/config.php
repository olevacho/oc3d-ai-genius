<?php ?>

<script>
    
</script>
<div class="oc3daig_container">
    <div id="oc3daig_configtabs">


        <ul>
            <li><a href="#oc3daig-tabs-1"><?php echo esc_html__('ChatGPT', 'oc3d-ai-genius') ?></a></li>
            
        </ul>
        <?php
        include OC3DAIG_PATH . '/views/backend/config_gpt_general.php';
        ?>
        <div class="oc3daig_bloader">
            <div style="padding: 1em 1.4em;"></div>


        </div>

    </div>
</div>



<script>
    const oc3daajaxAction = '<?php echo admin_url('admin-ajax.php'); ?>';

    let oc3d_gpt_confnonce = "<?php echo wp_create_nonce('oc3d_gpt_confnonce'); ?>";
    let oc3daig_instruction_table_height = 0;

    jQuery(function () {
        jQuery("#oc3daig_configtabs").tabs({activate: function (event, ui) {
                let oc3daig_active_tab = jQuery("#oc3daig_configtabs .oc3daig_tab_panel:visible").attr("id");
                if (oc3daig_active_tab === 'oc3daig-tabs-3') {

                    oc3daig_instruction_table_height = oc3daigSetTableContainerHeight();
                    let  tbl_div = document.querySelector('#oc3daig_container');
                    if (tbl_div) {
                        tbl_div.style.height = oc3daig_instruction_table_height + 'px';
                    }

                }
            }});
    });
    
    
    jQuery(document).ready(function () {
        let oc3daig_instruction = document.querySelector("#oc3daig_instruction");
        oc3daig_instruction.addEventListener("paste", (event) => {
            //event.preventDefault();
            let paste = (event.clipboardData || window.clipboardData).getData("text");
            let len = event.target.value.length;
            if (len > 0 || paste.length > 0) {
                document.querySelector('#oc3daig_submit_edit_instruction').disabled = false;//oc3daig_submit_edit_instruction
            } else {
                document.querySelector('#oc3daig_submit_edit_instruction').disabled = true;
            }
        });


    });





</script>