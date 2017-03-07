<?php
global $feature_list_array;
global $edit_id;
global $moving_array;
?>

<div class="col-md-12 add-estate profile-page profile-onprofile row"> 
<div class="submit_container ">  
    <div class="col-md-4 profile_label">
        <!--<div class="submit_container_header"><?php _e('Amenities and Features','wpestate');?></div>-->
        <div class="user_details_row"><?php _e('Amenities and Features','wpestate');?></div> 
        <div class="user_profile_explain"><?php _e('Select what features and amenities apply for your property. ','wpestate')?></div>
    </div>

<div class="col-md-8">
<?php

foreach($feature_list_array as $key => $value){
    $post_var_name =   str_replace(' ','_', trim($value) );
    $post_var_name =   wpestate_limit45(sanitize_title( $post_var_name ));
    $post_var_name =   sanitize_key($post_var_name);

    $value_label=$value;
    if (function_exists('icl_translate') ){
        $value_label    =   icl_translate('wpestate','wp_estate_property_custom_amm_'.$value, $value ) ;                                      
    }

    print '<p class="featurescol">
           <input type="hidden"    name="'.$post_var_name.'" value="" style="display:block;">
           <input type="checkbox"   id="'.$post_var_name.'" name="'.$post_var_name.'" value="1" ';

    if (esc_html(get_post_meta($edit_id, $post_var_name, true)) == 1) {
        print' checked="checked" ';
    }else{
        if(is_array($moving_array) ){                      
            if( in_array($post_var_name,$moving_array) ){
                print' checked="checked" ';
            }
        }
    }
    print' /><label for="'.$post_var_name.'">'.stripslashes($value_label).'</label></p>';  
}
?>
</div>
</div>
</div>
