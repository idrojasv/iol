<?php
global $prop_action_category;
global $prop_action_category_selected;
global $prop_category_selected;
?>
<div class="col-md-12 add-estate profile-page profile-onprofile row"> 
<div class="submit_container"> 
    <div class="col-md-4 profile_label">
        <!--<div class="submit_container_header"><?php _e('Select Categories', 'wpestate'); ?></div>-->
        <div class="user_details_row"><?php _e('Select Categories','wpestate');?></div> 
        <div class="user_profile_explain"><?php _e('Selecting a category will make it easier for users to find you property in search results.','wpestate')?></div>
    </div> 
       
    <p class="col-md-4"><label for="prop_category"><?php _e('Category','wpestate');?></label>
        <?php 
            $args=array(
                    'class'       => 'select-submit2',
                    'hide_empty'  => false,
                    'selected'    => $prop_category_selected,
                    'name'        => 'prop_category',
                    'id'          => 'prop_category_submit',
                    'orderby'     => 'NAME',
                    'order'       => 'ASC',
                    'show_option_none'   => __('None','wpestate'),
                    'taxonomy'    => 'property_category',
                    'hierarchical'=> true
                );
            wp_dropdown_categories( $args ); ?>
        </p>

        <p class="col-md-4"><label for="prop_action_category"> <?php _e('Listed In ','wpestate'); $prop_action_category;?></label>
            <?php 
            $args=array(
                    'class'       => 'select-submit2',
                    'hide_empty'  => false,
                    'selected'    => $prop_action_category_selected,
                    'name'        => 'prop_action_category',
                    'id'          => 'prop_action_category_submit',
                    'orderby'     => 'NAME',
                    'order'       => 'ASC',
                    'show_option_none'   => __('None','wpestate'),
                    'taxonomy'    => 'property_action_category',
                    'hierarchical'=> true
                );

               wp_dropdown_categories( $args );  ?>
        </p>       
</div>
</div>