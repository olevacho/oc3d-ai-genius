<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="oc3daig_container">
    <div id="oc3daig_metatabs">


            <ul>
                <li><a href="#oc3daig-tabs-1"><?php echo esc_html__('Image Generation', 'oc3d-ai-genius') ?></a></li>

               
            </ul>
            <?php
            include OC3DAIG_PATH . '/views/backend/image/generation.php';
            
            ?>

    </div>
</div>

<script>

    jQuery(function () {
        jQuery("#oc3daig_metatabs").tabs();
    });
</script>
<script>

    jQuery(document).ready(function () {
        
        
            
    }
    
    );
    
  
</script>

<style>
    .oc3daig_mt10{
        margin-top:10px;
    }
    #oc3daig_surpriseme, #oc3daig_submit, #oc3daig_set_default_setting{
        margin-top: 10px;
    }
    
.oc3daig-overlay {
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: 9999;
  background: rgb(0 0 0 / 20%);
  top: 0;
  direction: ltr;
  left:0;
  }



.oc3daig_modal {
  width: 900px;
  min-height: 100px;
  position: absolute;
  top: 30%;
  background: #fff;
  left: calc(50% - 450px);
  border-radius: 5px;
}

.oc3daig_modal {
  top: 5%;
  width: 90%;
  max-width: 900px;
  left: 50%;
  transform: translateX(-50%);
  height: 90%;
  overflow-y: auto;
}


.oc3daig_modal_head {
  min-height: 30px;
  border-bottom: 1px solid #ccc;
  display: flex;
  align-items: center;
    padding: 6px 12px;
}

.oc3daig_modal_content {
  height: calc(100% - 50px);
  overflow-y: auto;
}

.oc3daig_modal_content {
  padding: 10px;
}

.oc3daig_grid_form {
  grid-template-columns: repeat(3,1fr);
  grid-column-gap: 20px;
  grid-row-gap: 20px;
  display: grid;
  grid-template-rows: auto auto;
  margin-top: 20px;
}

.oc3daig_grid_form_2 {
  grid-column: span 2/span 1;
}

.oc3daig_grid_form_1 {
  grid-column: span 1/span 1;
}


.oc3daig_modal_close {
  position: absolute;
  top: 10px;
  right: 10px;
  font-size: 30px;
  font-weight: bold;
  cursor: pointer;
}



</style>