<?php
global $embed_video_id;
global $option_video;
?>
<div class="col-md-12 add-estate profile-page profile-onprofile row"> 
<div class="submit_container "> 
     <div class="col-md-4 profile_label">
        <!--<div class="submit_container_header"><?php _e('Video Option','wpestate');?></div>-->
        <div class="user_details_row"><?php _e('Video Option','wpestate');?></div> 
        <div class="user_profile_explain"><?php _e('Add just the video ID from the vimeo or youtube url.','wpestate')?></div>
    </div>

    <div class="col-md-4">
        <p class="full_form">
           <label for="embed_video_type"><?php _e('Video from','wpestate');?></label>
           <select id="embed_video_type" name="embed_video_type" class="select-submit2">
               <?php print $option_video;?>
           </select>
        </p>
    </div>
    <div class="col-md-4">
       <p class="full_form sidebar_full_form">     
           <label for="embed_video_id"><?php _e('Embed Video id: ','wpestate');?></label>
           <input type="text" id="embed_video_id" class="form-control"  name="embed_video_id" size="40" value="<?php print $embed_video_id;?>">
       </p>
   </div>
</div> 
</div>
