<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$wp_nonce = wp_create_nonce(OC3DAIG_PREFIX_SHORT.'imggen_nonce');
$menu_page = OC3DAIG_PREFIX_LOW . 'settings';
$p_types = get_post_types();
$stored_selected_p_types = get_option(OC3DAIG_PREFIX_LOW . 'selected_types');
$oc3daig_image_save_media_text = get_option(OC3DAIG_PREFIX_LOW.'img_save_text',esc_html__('Save to Media','gpt3-ai-content-generator'));
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





$models = [];
?>
<div id="oc3daig-tabs-1" class="oc3daig_tab_panel" data-oc3daig="1">
    <div class="inside">
        <div class="oc3daig_config_items_wrapper">
            <form action="" method="post" id="oc3daig_img_gen_form">    
                <input type="hidden" name="<?php echo OC3DAIG_PREFIX_SHORT ?>imggen_nonce" value="<?php echo esc_html($wp_nonce); ?>"/>
                <input type="hidden" name="action" value="oc3daig_image_generate"/>

                <div class="oc3daig_data_column_container">
                    <div class="oc3daig_data_column oc3daig_left_column">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Image Generation', 'oc3d-ai-genius'); ?></h3>
                                </div>

                                <div class="oc3daig_block_content" >
                                    <?php
                                    $prompt_examples = Oc3dAig_ImageUtils::getPromptExamples();
                                    $r_prompt = esc_html($prompt_examples[array_rand($prompt_examples)]);
                                    
                                    ?>
                                    <div  class="oc3daig_row_content oc3daig_pr">
                                        <div  style="position:relative;">
                                            <label for="oc3daig_open_ai_gpt_key" class="oc3daig_lbl"><?php esc_html_e('Prompt', 'oc3d-ai-genius'); ?>:</label>
                                            <textarea id="oc3daig_text_c" name="oc3daig_text_c" rows="3" style="width: 100%;"><?php echo esc_textarea($r_prompt); ?></textarea>
                                        </div>

                                    </div>
                                </div>
                                <div class="oc3daig_block_content" >
                                    <div  class="oc3daig_row_content oc3daig_pr oc3daig_mt10">
                                        <div  style="position:relative;">
                                            <label for="oc3daig_open_ai_gpt_key" class="oc3daig_lbl"><?php esc_html_e('The number of images that are being generated', 'oc3d-ai-genius'); ?>:</label>
                                            
                                            <select id="<?php echo OC3DAIG_PREFIX_LOW; ?>images_count" name="<?php echo OC3DAIG_PREFIX_LOW; ?>images_count">
                                            <?php
                                            $oc3daig_images_count = get_option(OC3DAIG_PREFIX_LOW . 'image_generator_cnt', '1');
                                            $oc3daig_images_variations = Oc3dAig_ImageUtils::getImageCountVars();
                                            
                                            foreach($oc3daig_images_variations as $oc3daig_images_variation){
                                                if($oc3daig_images_variation == $oc3daig_images_count){
                                                    $sel_opt = 'selected';
                                                }else{
                                                    $sel_opt = '';
                                                }
                                                ?>
                                                <option value="<?php echo esc_html($oc3daig_images_variation); ?>" <?php echo esc_html($sel_opt);  ?>> <?php echo esc_html($oc3daig_images_variation); ?> </option>
                                                <?php
                                            }
                                            ?>
                                            </select>

                                        </div>

                                    </div>
                        </div>
                <div class="oc3daig_block_content">

                    <div class="oc3daig_row_content oc3daig_pr">

                        <div class="oc3daig_bloader oc3daig_gbutton_container">
                            <div style="padding: 1em 1.4em;">
                                <input type="submit" value="<?php echo esc_html__('Surprise Me', 'oc3d-ai-genius') ?>" name="oc3daig_surpriseme" id="oc3daig_surpriseme" class="button button-primary button-large" onclick="oc3daigGetRandPrompt(event);" >
                                <input type="submit" value="<?php echo esc_html__('Generate', 'oc3d-ai-genius') ?>" name="oc3daig_submit" id="oc3daig_submit" class="button button-primary button-large"  >
                                <div class="oc3daig-custom-loader  oc3daig-img-loader"></div>
                            </div>

                            <div class="oc3daig-custom-loader oc3daig-general-loader" style="display: none;"></div>
                        </div>
                    </div>
                </div>
                                
                <div class="image-generated">
                            <div class="image-generate-loading" id="image-generate-loading"><div class="lds-dual-ring"></div></div>
                            <div class="image-grid oc3daig-mb-5" id="image-grid">
                            </div>
                            <div style="<?php echo is_user_logged_in()? '' : 'display:none'?>">
                            <br><br>
                            <div id="oc3daig_message" class="oc3daig_message" style="text-align: center;margin-top: 10px;"></div>
                            <div class="oc3daig-convert-progress oc3daig-convert-bar" id="oc3daig-convert-bar" style="display:none;">
                                <span></span>
                                <small>0%</small>
                            </div>
                            <div class="oc3daig_bloader">
                            <button type="button" id="image-generator-save" class="button button-primary oc3daig-button image-generator-save" style="width: 100%;display: none"><?php echo esc_html($oc3daig_image_save_media_text)?></button>
                            <div class="oc3daig-custom-loader  oc3daig-img-loader2"></div>
                            </div>
                            </div>
                </div>                

                           


                            </div>
                        </div>    
                    </div>
                    <div class="oc3daig_data_column oc3daig_right_column ">
                        <div class="oc3daig_block ">
                            <div style="position:relative;">
                                 <?php
                                    include OC3DAIG_PATH . '/views/backend/image/settings.php';
                                    ?>
                            </div>
                        </div> 
                    </div>

                </div>

            </form>
        </div>

    </div>
</div>

<script>
    let oc3daigImageNonce = '<?php echo esc_html(wp_create_nonce( 'oc3daig-imagelog' ))?>';
    let oc3daigImageSaveNonce = '<?php echo esc_html(wp_create_nonce('oc3daig-ajax-nonce'))?>';
    let oc3daigSelectAllText = '<?php echo esc_html('Select All')?>';
    
    let oc3daig_message_config_general_error = '<?php esc_html_e('There were errors during store configuration.', 'oc3d-ai-genius'); ?>';
    let oc3daig_message_config_general_succes1 = '<?php esc_html_e('Configuration stored successfully.', 'oc3d-ai-genius'); ?>';
    
    jQuery(document).ready(function () {
        
        
            
    }
    
    );
    
    function oc3daigImageLoadingEffect(btn, loader_selector){
        console.log('btn.id='+btn.id);
        let l_selector = '.oc3daig-img-loader';
        if(loader_selector){
            l_selector = loader_selector;
        }
        let loader = document.querySelector(l_selector);
        loader.style.left = '200' +  'px';
        loader.style.top =  '-30px';
        oc3daigShowLoader(l_selector);
        btn.setAttribute('disabled','disabled');
        btn.innerHTML += '<span class="oc3daig-loader"></span>';
    }
    
    function oc3daigImageRmLoading(btn, hide_loader_selector){
        if(hide_loader_selector){
            oc3daigHideLoader(hide_loader_selector);
        }else{
            oc3daigHideLoader('.oc3daig-img-loader');
        }
        btn.removeAttribute('disabled');
        if(oc3daigHasChildElement(btn)){
            btn.removeChild(btn.getElementsByTagName('span')[0]);
        }
        let mtop = window.getComputedStyle(document.querySelector('.oc3daig_right_column')).marginTop;
        if(mtop === '300px'){
            let rcolumn = document.querySelector(".oc3daig_right_column");
            rcolumn.classList.add("oc3daig_right_column2");   
        }
        //console.log('margin-top:'+window.getComputedStyle(document.querySelector('.oc3daig_right_column')).marginTop);
        
    }
  
    function oc3daigHasChildElement(elm) {
        
        let child, rv;

        if (elm.children) {

            rv = elm.children.length !== 0;
        } else {
            // The hard way...
            rv = false;
            for (child = element.firstChild; !rv && child; child = child.nextSibling) {
                if (child.nodeType == 1) { // 1 == Element
                    rv = true;
                }
            }
        }
        return rv;
    }
    function oc3daigGetRandPrompt(e){
        e.preventDefault();
        let randomIndex = Math.floor(Math.random() * <?php echo esc_html(count($prompt_examples)); ?>);
        document.getElementById("oc3daig_text_c").value = <?php echo json_encode($prompt_examples); ?> [randomIndex];
    }
    
    function oc3daigImageCloseModal() {
        document.querySelectorAll('.oc3daig_modal_close')[0].addEventListener('click', event => {
            document.querySelectorAll('.oc3daig_modal_content')[0].innerHTML = '';
            document.querySelectorAll('.oc3daig-overlay')[0].style.display = 'none';
            document.querySelectorAll('.oc3daig_modal')[0].style.display = 'none';
        })
    }
    
    function oc3daigSaveImageData(id){
        var item = document.getElementById('oc3daig-image-item-'+id);
        item.querySelectorAll('.oc3daig-image-item-alt')[0].value = document.querySelectorAll('.oc3daig_edit_item_alt')[0].value;
        item.querySelectorAll('.oc3daig-image-item-title')[0].value = document.querySelectorAll('.oc3daig_edit_item_title')[0].value;
        item.querySelectorAll('.oc3daig-image-item-caption')[0].value = document.querySelectorAll('.oc3daig_edit_item_caption')[0].value;
        item.querySelectorAll('.oc3daig-image-item-description')[0].value = document.querySelectorAll('.oc3daig_edit_item_description')[0].value;
        let l_selector = '.oc3daig-modal-loader';
        
        let loader = document.querySelector(l_selector);
        loader.style.left = '75%';
        loader.style.top =  '50%';
        
        oc3daigShowLoader('.oc3daig-modal-loader');
        if(oc3daigImage){
            oc3daigImage.save_image([id],0);
        }
    }
    
</script>
<style>
.image-grid {
        grid-template-columns: repeat(3,1fr);
        grid-column-gap: 20px;
        grid-row-gap: 20px;
        display: grid;
        grid-template-rows: auto auto;
    }
    
    .oc3daig-image-item {
        background-size: cover;
        box-shadow: 0px 0px 10px #ccc;
        position: relative;
        cursor: pointer;
    }
    .oc3daig-image-item img{
        width: 100%; /* instead of max-width */
        height: auto;
    }
    .oc3daig-image-item label{
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .oc3daig_left_column{
        min-width:45%;
    }
    /*oc3daig_right_column*/
    @media only screen and (max-width: 768px) {
        .oc3daig_right_column {
            margin-top: 150px; /* make it one column on small screens */
        }
    }
    
    
    
    
    @media only screen and (max-width: 600px) {
        .image-grid {
            grid-template-columns: 1fr; /* make it one column on small screens */
        }
    }
    
    @media only screen and (min-width: 601px) and (max-width: 900px) {
        .image-grid {
            grid-template-columns: repeat(2,1fr); /* make it two columns on medium screens */
        }
    }
    
    @media only screen and (max-width: 480px) {
        .oc3daig_right_column {
            margin-top: 300px; /* make it one column on small screens */
        }
        
        .oc3daig_right_column2 {
            margin-top: 650px; /* make it one column on small screens */
        }
    }
</style>