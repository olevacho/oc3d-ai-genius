<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$dropdown_images = Oc3dAig_ImageUtils::getSettings();
?>
                    <div class="oc3daig_block_header">
                                    <h3><?php esc_html_e('Settings', 'oc3d-ai-genius'); ?></h3>
                    </div>


				<?php
				foreach ( $dropdown_images as $label => $image_options ) :
					?>
				<div class="oc3daig_block_content oc3daig_mt10">

                                            <div class="oc3daig_row_header">
						<label for="<?php echo esc_attr( $image_options['id'] ); ?>" class="oc3daig_lbl oc3daig_block"><?php echo esc_html( $label ); ?>:</label>
                                            </div>
                                            <div class="oc3daig_row_content oc3daig_pr">
                                                <div style="position:relative;">
                                                <select class="oc3daig_selection" id="<?php echo esc_attr( $image_options['id'] ); ?>" name="<?php echo esc_attr( $image_options['id'] ); ?>">
						<?php
						foreach ( $image_options['option'] as $option_key => $option ) :
							$selected = '';
							if ( $option_key === $image_options['option_selected'] ) {
								$selected = ' selected';
							}
							?>
							<option value="<?php echo esc_html( $option ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
						</select>
                                            </div>
                                            </div>
                                        
                                            </div>
					<?php
				endforeach;
				?>

			<div class="oc3daig_block_content">
                                    
                                    <div class="oc3daig_row_content oc3daig_pr">
                                        <div style="position:relative;">
				<button type="button" id="oc3daig_set_default_setting" class="button button-primary button-large"><?php echo esc_html__( 'Save Settings', 'oc3d-ai-genius' ); ?></button>
			</div>
                                        </div>
                            </div>
			

<script>
<?php
    if(is_admin()):
    ?>
    let oc3daigSetDefault = document.getElementById('oc3daig_set_default_setting');
    let oc3daigImageForm = document.getElementById('oc3daig_img_gen_form');
    if(oc3daigSetDefault) {
        oc3daigSetDefault.addEventListener('click', function () {

            oc3daigImageLoadingEffect(oc3daigSetDefault);
            let queryString = new URLSearchParams(new FormData(oc3daigImageForm)).toString();
            queryString += '&action=oc3daig_img_default_settings';
            const xhttp = new XMLHttpRequest();
            xhttp.open('POST', oc3daigParams.ajax_url);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(queryString);
            xhttp.onreadystatechange = function (ev) {
                if (xhttp.readyState === 4) {
                    if (xhttp.status === 200) {
                        let oc3daigParentCol = oc3daigSetDefault.parentElement;
                        let successMessage = document.createElement('div');
                        successMessage.style.color = '#AE1F00';
                        successMessage.style.fontWeight = 'bold';
                        successMessage.innerHTML = '<?php echo esc_html__('Settings updated successfully','gpt3-ai-content-generator')?>';
                        oc3daigParentCol.appendChild(successMessage);
                        setTimeout(function (){
                            successMessage.remove();
                        },4000);
                        oc3daigImageRmLoading(oc3daigSetDefault);
                    }
                }
            }
        });
    }
    <?php
    endif;
    ?>
</script>
