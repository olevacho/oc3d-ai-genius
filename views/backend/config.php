<?php ?>
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
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
    const oc3daajaxAction = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';

    let oc3d_gpt_confnonce = "<?php echo esc_html(wp_create_nonce('oc3d_gpt_confnonce')); ?>";
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


    });





</script>