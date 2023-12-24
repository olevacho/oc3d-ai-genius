<?php 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="oc3daig-tabs-3" class="oc3daig_tab_panel"  data-oc3daig="2">
    <h3><?php esc_html_e('Expert', 'oc3d-ai-genius'); ?></h3>
    <div class="oc3daig_form_row">
<?php
?>

<div class="oc3daigwrap">     
            
        <table  class="oc3daig_edit_tbl">
            <tbody >
                <tr>
                    <td id="oc3daig_model_td_expert" class="oc3daig_td" style="">
                        <label for="oc3daig_model" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Model', 'oc3d-ai-genius'); ?></label>
                        <select id="oc3daig_model_e" name="oc3daig_model_e" class="oc3daig_selection">
<?php
foreach ($expert_models as $ei => $em) {

    ?>
                                <option value="<?php echo wp_kses($em, Oc3dAig_Utils::getInstructionAllowedTags()); ?>"><?php echo wp_kses($em, Oc3dAig_Utils::getInstructionAllowedTags()); ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_temperature_e" class="oc3daig_lbl oc3daig_block "><?php esc_html_e('Temperature', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_temperature_e" name="oc3daig_temperature_e" type="number" step="0.1" min="0" max="2" maxlength="3" autocomplete="off"  placeholder="temperature" value="0.8">

                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_max_tokens_e" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Maximum length', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_max_tokens_e" name="oc3daig_max_tokens_e" type="number" step="1"  value="<?php echo (int)$mx_tokens; ?>" maxlength="3" autocomplete="off"  >

                    </td>

                </tr>
</tbody>
        </table>
    </div>
<div class="oc3daigwrap">     
            <table  class="oc3daig_edit_tbl">
            <tbody>   
                <tr>
                    <td id="oc3daig_model_td_expert" class="oc3daig_td" style="">
                        <label for="oc3daig_top_p_edit" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Top P', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_top_p_edit" name="oc3daig_top_p_edit" type="number" step="0.1" min="0" max="1" maxlength="3" autocomplete="off"  placeholder="temperature" value="1">

                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_presence_penalty_e" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Presence penalty (from -2 to 2)', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_presence_penalty_e" name="oc3daig_presence_penalty_e" type="number" step="0.1" min="-2" max="2" maxlength="3" autocomplete="off"  placeholder="temperature" value="0">

                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_frequency_penalty_e" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Frequency penalty (from -2 to 2)', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_frequency_penalty_e" name="oc3daig_frequency_penalty_e" type="number" step="0.1" min="-2" max="2" maxlength="3" autocomplete="off"  placeholder="temperature" value="0">
                    </td>

                </tr> 
                </tbody>
        </table>
    </div>
<div class="oc3daigwrap">     
            <table class="oc3daig_edit_tbl">
            <tbody>    
                <tr>
                    <td colspan="3" class="">
                        <label for="oc3daig_system_e" class="oc3daig_lbl"><?php esc_html_e('System', 'oc3d-ai-genius'); ?></label>

                        <textarea id="oc3daig_system_e" name="oc3daig_system_e" rows="2" cols="25"  class=""></textarea>

                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
            <div class="oc3daigwrap">     
            <table id="oc3daig_expert_tbl"  class="oc3daig_edit_tbl">
            <tbody id="oc3daig_expert_tbody">    
                <tr class="oc3daig_field">
                    <td colspan="3" class="">
                        <div class="oc3daig_halfscreen">
                            <input type="radio" id="oc3daig_actor_ae_1" name="oc3daig_actor[]" value="<?php esc_html_e('Assistant', 'oc3d-ai-genius'); ?>"
                                   onchange="oc3daigRadioChange(1, 'ae');" >
                            <label for="oc3daig_message_e_1"><?php esc_html_e('Assistant', 'oc3d-ai-genius'); ?></label>
                        </div>
                        <div class="oc3daig_halfscreen">
                            <input type="radio" id="oc3daig_actor_ue_1" name="oc3daig_actor[]" value="<?php esc_html_e('User', 'oc3d-ai-genius'); ?>"
                                   checked onchange="oc3daigRadioChange(1, 'ue');">
                            <label for="oc3daig_message_ue_1"><?php esc_html_e('User', 'oc3d-ai-genius'); ?></label>
                        </div>

                        <div class="oc3daig_2actor">
                            <textarea id="oc3daig_message_e_1" name="oc3daig_message_e_1" rows="2" cols="55"  class=""></textarea>
                        </div>
                        <div class="oc3daig_actor">
                            <span onclick="oc3daigAddField(this)">+</span>
                            <span onclick="oc3daigRemoveField(this)">-</span>
                        </div>

                    </td>
                </tr>
                
                <tr id="oc3daig_response_td">
                    <td colspan="3">
                        <label for="oc3daig_response_e" class="oc3daig_lbl"><?php esc_html_e('Response', 'oc3d-ai-genius'); ?></label>
                        <textarea id="oc3daig_response_e" name="oc3daig_response_e" rows="10" cols="25"></textarea>
                        <a href="#" onclick="oc3daigMetaCopyToClipboard(event,'oc3daig_response_e');"  class="oc3daig_copy_link"><?php esc_html_e('Copy to clipboard', 'oc3d-ai-genius'); ?></a>
                        <a href="#" onclick="oc3daigMetaClearText(event,'oc3daig_response_e');" class="oc3daig_clear_link"><?php esc_html_e('Clear text', 'oc3d-ai-genius'); ?></a>
                    </td>    
                </tr>
            </tbody>
        </table>
</div>    
        <div class="oc3daig_bloader">
            <div style="padding: 1em 1.4em;"><button   name="oc3daig_submit" id="oc3daig_submit" class="oc3daig_submit_meta button button-primary button-large"><?php echo esc_html__('Send', 'oc3d-ai-genius') ?></button></div>

            <div class="oc3daig-custom-loader"></div>
        </div>
    </div>

</div>



<script>
    let oc3daig_generate_assistant = '<?php esc_html_e('Assistant', 'oc3d-ai-genius'); ?>';
    let oc3daig_generate_user = '<?php esc_html_e('User', 'oc3d-ai-genius'); ?>';
    

</script>
