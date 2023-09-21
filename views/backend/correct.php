<?php
$display_pagination = true;

$instructions_per_page = (int) get_option(OC3DAIG_PREFIX_LOW . 'count_of_instructions', 10);
$current_page = 1;
$search_string = '';
$arr_instructions = Oc3dAig_InstructionsModel::searchInstructions(0, $search_string, $current_page, $instructions_per_page, true);
$instructions = $arr_instructions['rows'];
$total_instructions = $arr_instructions['cnt'];
?>
<div class="oc3daigwrap"> 
<div id="oc3daig-tabs-1" class="oc3daig_tab_panel" data-oc3daig="1">
    <h3><?php esc_html_e('Edit & Extend', 'oc3d-ai-genius'); ?></h3>
    <p class="oc3daig_instruction_title">
        <?php esc_html_e('How to use:', 'oc3d-ai-genius'); ?>
    </p>
    <p class="oc3daig_instruction"><b>1.</b><?php esc_html_e('Input text into "Text to be changed" field.', 'oc3d-ai-genius'); ?>
        <b>2.</b><?php esc_html_e('Enter instruction with description of what ChatGPT needs to do with the text, or select stored instruction from list below.', 'oc3d-ai-genius'); ?>
        <b>3.</b><?php esc_html_e('Select other parameters (optionally). '); ?>
        <b>4.</b><?php esc_html_e('Click Send button.', 'oc3d-ai-genius'); ?>
    </p>
    <div class="oc3daig_form_row">
        <?php
        $first_instruction_text = '';
        $first_instruction = is_array($edit_instructions) && count($edit_instructions) > 0 ? $edit_instructions[0] : false;
        if (is_object($first_instruction) && $first_instruction !== false && isset($first_instruction->instruction)) {
            $first_instruction_text = $first_instruction->instruction;
        }
        //var_dump($first_instruction);
        ?>

        
                       <div class="oc3daigwrap"> 
                        <table class="oc3daig_edit_tbl">
                            <tbody>
                            <tr>
                    <td id="oc3daig_model_td" class="oc3daig_td" style="">
                        <label for="oc3daig_model" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Model', 'oc3d-ai-genius'); ?></label>
                        <select id="oc3daig_model_c" name="oc3daig_model_c" class="oc3daig_selection">
                            <?php
                            foreach ($edit_models as $ei => $em) {
                                $em_esc = wp_kses($em, Oc3dAig_Utils::getInstructionAllowedTags());
                                ?>
                                <option value="<?php echo $em_esc; ?>"><?php echo $em_esc; ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_temperature_c" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Temperature', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_temperature_c" name="oc3daig_temperature_c" type="number" step="0.1" min="0" max="2" maxlength="3" autocomplete="off"  placeholder="temperature" value="0.8">

                    </td>
                    <td id="newmetaleft" class="oc3daig_td">
                        <label for="oc3daig_max_tokens_c" class="oc3daig_lbl oc3daig_block"><?php esc_html_e('Maximum length', 'oc3d-ai-genius'); ?></label>
                        <input class="oc3daig_input oc3daig_select_ch" id="oc3daig_max_tokens_c" name="oc3daig_max_tokens_c" type="number" step="1"  value="<?php echo $mx_tokens; ?>" maxlength="3" autocomplete="off" >

                    </td>
                            </tr>
                            </tbody>
                    </table>
                       </div>  
        <div class="oc3daigwrap"> 
            <table class="oc3daig_edit_tbl">
            <tbody>

                <tr>
                    <td colspan="3">
                        <label for="oc3daig_text_c" class="oc3daig_lbl"><?php esc_html_e('Text to be changed', 'oc3d-ai-genius'); ?></label>
                        <textarea id="oc3daig_text_c" name="oc3daig_text_c" rows="3" cols="25"></textarea>
                        <a href="#" onclick="oc3daigMetaClearText(event,'oc3daig_text_c');" class="oc3daig_clear_link"><?php esc_html_e('Clear text', 'oc3d-ai-genius'); ?></a>
                    </td>    
                </tr>
            </tbody>
            </table>
        </div>   
        <div class="oc3daigwrap"> 
            <table class="oc3daig_edit_tbl">
            <tbody>
                <tr>
                    <td colspan="3">
                        <label for="oc3daig_result_c" class="oc3daig_lbl"><?php esc_html_e('Result', 'oc3d-ai-genius'); ?></label>
                        
                        <textarea id="oc3daig_result_c" name="oc3daig_result_c" rows="3" cols="25"></textarea>
                        <a href="#" onclick="oc3daigMetaCopyToClipboard(event,'oc3daig_result_c');" class="oc3daig_copy_link"><?php esc_html_e('Copy to clipboard', 'oc3d-ai-genius'); ?></a>
                        <a href="#" onclick="oc3daigMetaClearText(event,'oc3daig_result_c');" class="oc3daig_clear_link"><?php esc_html_e('Clear text', 'oc3d-ai-genius'); ?></a>
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
                        <label for="oc3daig_instruction" class="oc3daig_lbl"><?php esc_html_e('Instruction', 'oc3d-ai-genius'); ?></label>
                        <textarea id="oc3daig_instruction" name="oc3daig_instruction" rows="3" cols="55" class="oc3dmt30"><?php
                            echo $first_instruction_text;
                            ?>
                        </textarea>

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
                        <div class="oc3daig_bloader">
                            <div style="padding: 1em 1.4em;"><button   name="oc3daig_submit2" id="oc3daig_submit2" class="oc3daig_submit_meta button button-primary button-large"><?php echo esc_html__('Send', 'oc3d-ai-genius') ?></button></div>

                            <div class="oc3daig-custom-loader"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <h4 class="oc3daig_block_header">
                            <?php echo esc_html__('Click on any instruction from the list to use in the prompt.', 'oc3d-ai-genius') ?>
                        </h4>
                        
                    </td>
                </tr>    
                <tr>
                    <td colspan="3">

                        <div class="tablenav-pages">
                            <?php
                            if ($display_pagination) {
                                ?>

                                <input type="hidden" id="oc3daig_page" name="oc3daig_page" value="1"/>
                                <input type="hidden" id="instructions_per_page" name="instructions_per_page" value="<?php echo $instructions_per_page ?>"/>
                                <div class="oc3daig_pagination">
                                    <?php
                                    echo '<span class="oc3daig_page_lbl" style=""> ' . esc_html__('Page', 'oc3d-ai-genius') . ':</span> ';

                                    echo '<span aria-current="page" class="page-numbers current" style="padding-left:10px;">' . $current_page . '</span>';
                                    echo '<a class="oc3dprevious page-numbers" href="#" onclick="oc3daigMetaPrevPageIn(event);" style="display:none;" >&lt;&lt;</a>';
                                    if ($current_page * $instructions_per_page < $total_instructions) {
                                        echo '<a class="oc3dnext page-numbers" href="#" style="" onclick="oc3daigMetaNextPageIn(event);" >&gt;&gt;</a>';
                                    }
                                    echo '<span class="oc3daig_total_instructions" style="padding-left:20px;"> ' . esc_html__('Total', 'oc3d-ai-genius') . ': ' . $total_instructions . ' ' . esc_html__('items', 'oc3d-ai-genius') . '</span>   ';
                                    echo '';
                                    echo '';
                                    ?>    
                                </div>
                                <?php
                            }
                            ?>
                            <p class="search-box">

                                <span title="clear" id="oc3daigclear" class="dashicons dashicons-no" onclick="oc3daigMetaClearSearch(event);"></span>
                                <input type="text" id="oc3daig_search" name="oc3daig_search" value="<?php echo $search_string; ?>" onkeyup="oc3daigMetaSearchKeyUp(event);" >
                                <input type="submit" id="oc3daig_search_submit" class="button" value="<?php echo esc_html__('Search instructions', 'oc3d-ai-genius') ?>" onclick="oc3daigMetaLoadInstructionsSearch(event);"></p>
                        </div>   

                        <table id="oc3daig_instructions" class="wp-list-table widefat fixed striped pages">
                            <thead>

                            <th class="manage-column id_column" style="width: 5%;"><?php echo esc_html__('ID', 'oc3d-ai-genius'); ?></th>

                            <th class="manage-column"  style="width: 55%;"><?php echo esc_html__('Instruction', 'oc3d-ai-genius'); ?></th>
                            <th class="manage-column mvertical"  style="width: 10%;"><?php echo esc_html__('Type', 'oc3d-ai-genius'); ?></th>


                            </thead>
                            <tbody id="oc3daig-the-list">
                                <?php
                                $js_instructions = [];

                                foreach ($instructions as $row) {
                                    $row_id = (int) $row->id;
                                    $author = (int) Oc3dAig_Utils::getUsername($row->user_id);
                                    $row->user_id = $author;
                                    $row_instruction = wp_kses($row->instruction, Oc3dAig_Utils::getInstructionAllowedTags());
                                    $js_instructions[$row_id] = $row;
                                    $js_instructions[$row_id]->instruction = $row_instruction;
                                    $oc3daig_disabled_text = '';
                                    if ($row->disabled) {
                                        $oc3daig_disabled_text = 'oc3daig_disabled_text';
                                    }
                                    ?>
                                    <tr class="<?php echo $oc3daig_disabled_text; ?>">
                                        <td class="id_column">
                                            <?php
                                            $displayed_id = $row_id;
                                            ?>
                                            <a href="<?php echo '#'; ?>" onclick="oc3daigMetaSelectInstruction(event,<?php echo $row_id; ?>)" >
                                                <?php
                                                echo $displayed_id;
                                                ?>
                                            </a>
                                        </td>
                                        <?php ?> 
                                        <td>
                                            <a href="<?php echo '#'; ?>" onclick="oc3daigMetaSelectInstruction(event,<?php echo $row_id; ?>)" id="oc3daig_instr_href_<?php echo $row_id; ?>">
                                                <?php
                                                echo $row_instruction;
                                                ?>
                                            </a>


                                        </td>
                                        <td class="mvertical">
                                            <a href="<?php echo '#'; ?>" onclick="oc3daigMetaSelectInstruction(event,<?php echo $row_id; ?>)" >

                                                <?php
                                                switch ($row->typeof_instruction) {
                                                    case 2:
                                                        echo esc_html__('code-edit', 'oc3d-ai-genius');
                                                        break;
                                                    default:
                                                        echo esc_html__('text-edit', 'oc3d-ai-genius');
                                                }
                                                ?>
                                            </a>              
                                        </td>



                                    </tr>
                                    <?php
                                }
                                ?>

                            </tbody>
                        </table>


                    </td>    
                </tr>


            </tbody>
        </table>
    </div>

</div>
</div>
    <script>
    let oc3daig_instructions = <?php echo json_encode($js_instructions); ?>;
    let oc3daig_edited_instruction_id = 0;
    let oc3d_gpt_loadnoncec = "<?php echo wp_create_nonce('oc3d_gpt_loadnoncec'); ?>";
    let oc3daig_typeofinstr_text = '<?php esc_html_e('text-edit', 'oc3d-ai-genius'); ?>';
    let oc3daig_typeofinstr_code = '<?php esc_html_e('code-edit', 'oc3d-ai-genius'); ?>';
    




</script>


