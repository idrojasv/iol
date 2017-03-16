<?php
function ib2_page_settings() {
	$post_id = (int) $_GET['post'];
	$post = get_post($post_id);
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}

	$post_title = ( isset($post->post_title) ) ? esc_attr($post->post_title) : '';
	$meta_desc = ( is_array($data) && isset($meta['meta_desc']) ) ? esc_textarea($meta['meta_desc']) : '';
	$meta_keys = ( is_array($data) && isset($meta['meta_keys']) ) ? esc_attr($meta['meta_keys']) : '';
?>
<div class="settings-page editor-panel-content" style="display:none">
	<h3>Page Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="page-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#page-settings-title" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Page Info &amp; SEO</a></li>
			<li><a href="#page-settings-advseo" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Advanced SEO</a></li>
			<li><a href="#page-settings-width" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Page Width</a></li>
			<li><a href="#page-settings-typo" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;General Typography</a></li>
			<li><a href="#page-settings-background" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Color</a></li>
			<li><a href="#page-settings-bgimage" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Image</a></li>
			<li><a href="#page-settings-bgvideo" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Video</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="page-settings-title" class="tab-settings-content">
			<div class="form-group">
		    	<label for="page-title">Page Title</label>
		    	<input type="text" class="form-control " id="page-title" value="<?php echo $post_title; ?>" placeholder="Enter page title here">
		  	</div> 	
			<hr>
			<div class="form-group">
		    	<label for="page-desc">Meta Description</label>
		    	<textarea rows="3" class="form-control " id="page-desc" placeholder="Enter your meta description here" maxlength="160"><?php echo $meta_desc; ?></textarea>
		    	<span class="help-block" id="desc_limit"></span>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="page-keywords">Meta Keywords</label>
		    	<input type="text" class="form-control " id="page-keywords" value="<?php echo $meta_keys; ?>" placeholder="Enter your keywords here">
		  	</div>
		  	<hr>
		</div>
			
		<div id="page-settings-advseo" class="tab-settings-content">
	  		<?php $checked = ( is_array($meta) && isset($meta['noindex']) && $meta['noindex'] == 'true' ) ? ' checked="checked"' : ''; ?>
		  	<div class="form-group">
				<input type="checkbox" id="meta_noindex" class="seo-checks" value="noindex" data-label="Use noindex"<?php echo $checked; ?> /> 
			</div>
		  	<hr>
		  	<?php $checked = ( is_array($meta) && isset($meta['nofollow']) && $meta['nofollow'] == 'true' ) ? ' checked="checked"' : ''; ?>
		  	<div class="form-group">
				<input type="checkbox" id="meta_nofollow" class="seo-checks" value="nofollow" data-label="Use nofollow"<?php echo $checked; ?> /> 
			</div>
		  	<hr>
		  	<?php $checked = ( is_array($meta) && isset($meta['noodp']) && $meta['noodp'] == 'true' ) ? ' checked="checked"' : ''; ?>
		  	<div class="form-group">
				<input type="checkbox" id="meta_noodp" class="seo-checks" value="noodp" data-label="Use noodp"<?php echo $checked; ?> /> 
			</div>
		  	<hr>
		  	<?php $checked = ( is_array($meta) && isset($meta['noydir']) && $meta['noydir'] == 'true' ) ? ' checked="checked"' : ''; ?>
		  	<div class="form-group">
				<input type="checkbox" id="meta_noydir" class="seo-checks" value="noydir" data-label="Use noydir"<?php echo $checked; ?> /> 
			</div>
			<hr>
			<?php $checked = ( is_array($meta) && isset($meta['noarchive']) && $meta['noarchive'] == 'true' ) ? ' checked="checked"' : ''; ?>
			<div class="form-group">
				<input type="checkbox" id="meta_noarchive" class="seo-checks" value="noarchive" data-label="Use noarchive"<?php echo $checked; ?> /> 
			</div>
			<hr>
		</div>
			
		<div id="page-settings-width" class="tab-settings-content">
			<?php $page_width = ( is_array($meta) && isset($meta['page_width']) ) ? esc_attr($meta['page_width']) : '960'; ?>
			<div class="form-group">
				<div class="panel-col col-sm-8">
		    		<label for="page-width">Page Width</label>
		    		<div id="page-width-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control  ib2-slider-val" id="page-width" value="<?php echo $page_width; ?>" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		</div>
			
		<div id="page-settings-typo" class="tab-settings-content">
		    <div class="form-group">
		    	<label for="body-text-font">Font Face</label>
		    	<?php
		    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
		    	ib2_font_selectors('body-text-font', $current_font);
		    	?>
		  	</div>
		  	<hr>
		  	<?php $font_size = ( is_array($meta) && isset($meta['font_size']) ) ? esc_attr($meta['font_size']) : '14'; ?>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
			    	<label for="body-text-size">Font Size</label>
			    	<div id="body-text-size-slider" class="ib2-slider"></div>
				</div>
				<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="body-text-size" value="<?php echo $font_size; ?>" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<?php $line_height = ( is_array($meta) && isset($meta['line_height']) ) ? esc_attr($meta['line_height']) : '1.4'; ?>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
			    	<label for="body-line-height">Line Height</label>
			    	<div id="body-line-height-slider" class="ib2-slider"></div>
			   </div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control  ib2-slider-val" id="body-line-height" value="<?php echo $line_height; ?>" style="width:40px">
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
			<hr>
			<?php $white_space = ( is_array($meta) && isset($meta['white_space']) ) ? esc_attr($meta['white_space']) : '18'; ?>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
			    	<label for="body-white-space">White Space Between Paragraph</label>
			    	<div id="body-white-space-slider" class="ib2-slider"></div>
			   </div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control  ib2-slider-val" id="body-white-space" value="<?php echo $white_space; ?>" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
			<hr>
		  	<?php $font_color = ( is_array($meta) && isset($meta['font_color']) ) ? esc_attr($meta['font_color']) : '#333333'; ?>
		  	<div class="form-group">
		    	<label for="body-text-color">Font Color</label><br />
		    	<input type="text" class="form-control  ib2-pick-color" id="body-text-color" value="<?php echo $font_color; ?>" data-default-color="<?php echo $font_color; ?>">
		  	</div>
		  	<hr>
		  	
			<?php $link_color = ( is_array($meta) && isset($meta['link_color']) ) ? esc_attr($meta['link_color']) : '#428bca'; ?>
			<div class="form-group">
		    	<label for="body-link-color">Link Color</label><br />
		    	<input type="text" class="form-control  ib2-pick-color" id="body-link-color" value="<?php echo $link_color; ?>" data-default-color="<?php echo $link_color; ?>">
		  	</div>
		  	<hr>
		  	<?php $link_hover_color = ( is_array($meta) && isset($meta['link_hover_color']) ) ? esc_attr($meta['link_hover_color']) : '#2a6496'; ?>
		  	<div class="form-group">
		    	<label for="body-link-hover-color">Link Hover Color</label><br />
		    	<input type="text" class="form-control  ib2-pick-color" id="body-link-hover-color" value="<?php echo $link_hover_color; ?>" data-default-color="<?php echo $link_hover_color; ?>">
		  	</div>
		  	<hr>
		</div>

		<div id="page-settings-advtypo" class="tab-settings-content">
		    <div class="form-group">
		    	
		    </div>
		</div>
		
		<div id="page-settings-background" class="tab-settings-content">
			<?php $background_color = ( is_array($meta) && isset($meta['background_color']) ) ? esc_attr($meta['background_color']) : '#E5E5E5'; ?>
			<div class="form-group">
		    	<label for="background-color">Background Color</label><br />
		    	<input type="text" class="form-control  ib2-pick-color" id="background-color" value="<?php echo $background_color; ?>" data-default-color="<?php echo $background_color; ?>">
		  	</div>
		  	<hr>
		</div>
		
		<div id="page-settings-bgimage" class="tab-settings-content">
		  	<?php
		  		$background_img = ( is_array($meta) && isset($meta['background_img']) ) ? esc_attr($meta['background_img']) : '';
				$background_img = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $background_img);
		  		$preview = ( !empty($background_img) ) ? '<img src="' . $background_img . '" border="0" class="img-thumbnail img-responsive" />' : '';
		  		$background_mode = ( is_array($meta) && isset($meta['background_img_mode']) ) ? esc_attr($meta['background_img_mode']) : 'upload';
		  		$remove_display = ( !empty($background_img) ) ? '' : ' style="display:none"';
		  	?>
		  	<div class="form-group">
		  		<label for="background-image">Background Image</label>
		  		<input type="text" class="form-control " id="body-bg-url" value="<?php echo $background_img; ?>" placeholder="e.g. http://mydomain.com/image.png" />
		      	<p class="help-block">Enter an image URL into the field above, or click the "Action" button below to use a premade pattern/background, to search the web for an image, or to upload your own image. The background can be in any size, depending on how you want to setup the background image, but the recommended size for a full cover background is: <strong>1920x1280</strong> pixels</p>
		      	
		      	<div id="background-image-prev"><?php echo $preview; ?></div>
		  		<input type="hidden" id="body-bg-mode" value="<?php echo $background_mode; ?>" />
		        <p style="margin-top:10px">
		        	<div class="btn-group">
		        		<button id="background-image-chooser" class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#" id="background-image-premade" class="open-panel" data-settings="premade" data-element="screen-container">Pre-Made Pattern</a></li>
							<li class="divider"></li>
							<li><a href="#" class="background-image-search" data-type="background" data-element="screen-container">Search Image</a></li>
							<li><a href="#" id="background-image-upload">Upload Image</a></li>
						</ul>
		        	</div>
		        	<span id="background-img-rmv"<?php echo $remove_display; ?>><a href='#' class="btn btn-warning btn-sm open-image-editor" data-element="screen-container" data-img-type="background">Edit</a>&nbsp;&nbsp;<a href='#' class="btn btn-danger btn-sm remove-background-img">Remove</a></span>
		        </p>
		    </div>
		    <hr>
		    <?php $bg_repeat = ( is_array($meta) && isset($meta['background_repeat']) ) ? esc_attr($meta['background_repeat']) : 'tile'; ?>
		    <div class="form-group">
		    	<label for="background-repeat">Background Image Style</label>
		    	<select class="form-control " id="background-repeat">
					<option value="no-repeat"<?php echo ( ($bg_repeat == 'no-repeat') ? ' selected="selected"' : '' ); ?>>Normal</option>
					<option value="repeat"<?php echo ( ($bg_repeat == 'repeat') ? ' selected="selected"' : '' ); ?>>Tile</option>
					<option value="repeat-x"<?php echo ( ($bg_repeat == 'repeat-x') ? ' selected="selected"' : '' ); ?>>Tile Horizontally</option>
					<option value="repeat-y"<?php echo ( ($bg_repeat == 'repeat-y') ? ' selected="selected"' : '' ); ?>>Tile Vertically</option>
				</select>
		  	</div>
		  	<hr>
		  	<?php $bg_pos = ( is_array($meta) && isset($meta['background_pos']) ) ? esc_attr($meta['background_pos']) : 'left top'; ?>
		  	<div class="form-group">
		    	<label for="background-pos">Background Image Position</label>
		    	<select class="form-control " id="background-pos">
					<option value="left top"<?php echo ( ($bg_pos == 'left top') ? ' selected="selected"' : '' ); ?>>Left Top</option>
					<option value="left center"<?php echo ( ($bg_pos == 'left center') ? ' selected="selected"' : '' ); ?>>Left Center</option>
					<option value="left bottom"<?php echo ( ($bg_pos == 'left bottom') ? ' selected="selected"' : '' ); ?>>Left Bottom</option>
					<option value="right top"<?php echo ( ($bg_pos == 'right top') ? ' selected="selected"' : '' ); ?>>Right Top</option>
					<option value="right center"<?php echo ( ($bg_pos == 'right center') ? ' selected="selected"' : '' ); ?>>Right Center</option>
					<option value="right bottom"<?php echo ( ($bg_pos == 'right bottom') ? ' selected="selected"' : '' ); ?>>Right Bottom</option>
					<option value="center top"<?php echo ( ($bg_pos == 'center top') ? ' selected="selected"' : '' ); ?>>Center Top</option>
					<option value="center center"<?php echo ( ($bg_pos == 'center center') ? ' selected="selected"' : '' ); ?>>Center Center</option>
					<option value="center bottom"<?php echo ( ($bg_pos == 'center bottom') ? ' selected="selected"' : '' ); ?>>Center Bottom</option>
				</select>
		  	</div>
		  	<hr>
		  	<?php $bg_attach = ( is_array($meta) && isset($meta['background_attach']) ) ? esc_attr($meta['background_attach']) : 'scroll'; ?>
		  	<div class="form-group">
		    	<label for="background-attach">Background Image Attachment</label>
		    	<select class="form-control " id="background-attach">
					<option value="scroll"<?php echo ( ($bg_attach == 'scroll') ? ' selected="selected"' : '' ); ?>>Scroll</option>
					<option value="fixed"<?php echo ( ($bg_attach == 'fixed') ? ' selected="selected"' : '' ); ?>>Fixed</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
			    	<label for="background-size">Background Image Size</label>
			    	<div id="background-size-slider" class="ib2-slider"></div>
			   </div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control  ib2-slider-val" id="background-size" value="100" style="width:40px"> %
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		</div>
		
		<div id="page-settings-bgvideo" class="tab-settings-content">
			<?php $background_video = ( is_array($meta) && isset($meta['background_video']) ) ? esc_url($meta['background_video']) : ''; ?>
			<div class="form-group">
		    	<label for="background-video">YouTube Video URL</label><br />
		    	<input type="text" class="form-control" id="background-video" value="<?php echo $background_video; ?>" placeholder="e.g. http://youtu.be/Tx7DwS8OiIM" />
		    	<p class="help-block">Please insert a YouTube video URL that you want to use as a background.</p>
		
		  	</div>
		  	<hr>
		  	<?php 
		  		$mute_checked = ( isset($meta['background_video_mute']) && $meta['background_video_mute'] == 1 ) ? ' checked="checked"' : '';
				if ( !isset($meta['background_video_mute']) )
					$mute_checked = ' checked="checked"';
			?>
		  	<div class="form-group">
				<input type="checkbox" id="background-video-mute" value="yes" data-label="Mute Video Sound"<?php echo $mute_checked; ?> /> 
			</div>
			<hr>
			<?php 
		  		$loop_checked = ( isset($meta['background_video_loop']) && $meta['background_video_loop'] == 1 ) ? ' checked="checked"' : '';
				if ( !isset($meta['background_video_loop']) )
					$loop_checked = ' checked="checked"';
			?>
		  	<div class="form-group">
				<input type="checkbox" id="background-video-loop" value="yes" data-label="Loop Video"<?php echo $loop_checked; ?> /> 
			</div>
			<hr>
			<?php 
		  		$ctrl_checked = ( isset($meta['background_video_ctrl']) && $meta['background_video_ctrl'] == 1 ) ? ' checked="checked"' : '';
			?>
		  	<div class="form-group">
				<input type="checkbox" id="background-video-ctrl" value="yes" data-label="Display Video Control"<?php echo $ctrl_checked; ?> /> 
			</div>
		  	<hr>
		  	<p><strong>Important: </strong> The video background will NOT be displayed in editor mode, but will be viewable in the actual page.</p>
		</div>
		
	</div><!-- ./tab-settings-main -->
</div>
<?php
}

function ib2_scripts_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$head = ( isset($meta['head_scripts']) ) ? stripslashes(addslashes($meta['head_scripts'])) : '';
	$body = ( isset($meta['body_scripts']) ) ? stripslashes(addslashes($meta['body_scripts'])) : '';
	$footer = ( isset($meta['footer_scripts']) ) ? stripslashes(addslashes($meta['footer_scripts'])) : '';
?>
<div class="settings-scripts editor-panel-content" style="display:none">
	<h3>Scripts Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<p>You can insert JavaScript and CSS code here (e.g. tracking code, etc).</p>
	<hr />
	<div class="form-group">
    	<label for="head-scripts">Head Scripts</label><br />
    	<textarea class="form-control" id="head-scripts" rows="3"><?php echo $head; ?></textarea>
    	<p class="help-block">The scripts/codes will be placed inside the <code>&lt;head&gt;&lt;/head&gt;</code> tag.</p>
  	</div>
  	<hr />
  	<div class="form-group">
    	<label for="body-scripts">Body Scripts</label><br />
    	<textarea class="form-control" id="body-scripts" rows="3"><?php echo $body; ?></textarea>
    	<p class="help-block">The scripts/codes will be placed right after the <code>&lt;body&gt;</code> tag.</p>
  	</div>
  	<hr />
  	<div class="form-group">
    	<label for="footer-scripts">Footer Scripts</label><br />
    	<textarea class="form-control" id="footer-scripts" rows="3"><?php echo $footer; ?></textarea>
    	<p class="help-block">The scripts/codes will be placed right before the closing <code>&lt;/body&gt;</code> tag.</p>
  	</div>
</div>
<?php
}

function ib2_split_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	if ( !is_array($data) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
		}
	}
	
	$type = ( isset($data['conversion_type']) ) ? $data['conversion_type'] : 'wp';
	$c_id = ( isset($data['conversion_id']) ) ? $data['conversion_id'] : '';
	
	$variants = ib2_get_variants($post_id);
?>
<div class="settings-split editor-panel-content" style="display:none">
	<h3>Split Test Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<label>Traffic Weight</label>
	</div>
	<div class="var-weight-group">
	<p class="text-danger" id="weight-diff"></p>
	<?php
	if ( $variants ) {
		foreach ( $variants as $var ) {
			echo '<div class="form-group" style="margin-bottom:10px">' . "\n";
			echo '<div class="panel-col col-sm-8">Variation <strong>' . strtoupper($var->variant) . '</strong></div>' . "\n";
			echo '<div class="panel-col col-sm-4"><input type="text" class="form-control ib2-variant-weight" value="' . $var->weight . '" data-variant="' . $var->variant . '" style="width:40px" /> %</div>' . "\n";
			echo '<div class="clearfix"></div>';
			echo '</div>' . "\n\n";
		}
	}
	?>
	</div>
	<hr>
	<h3>Conversion Settings</h3>
	<div class="form-group">
    	<label for="conversion-page-type">Your Thank You Page Location</label>
    	<select class="form-control" id="conversion-page-type">
			<option value="wp"<?php echo ( $type == 'wp' ? ' selected="selected"' : '' ); ?>>WordPress Post/Page</option>
			<option value="external"<?php echo ( $type == 'external' ? ' selected="selected"' : '' ); ?>>External Page</option>
		</select>
  	</div>
	<div class="wp-thank-pages">
		<hr>
	  	<div class="form-group">
	    	<label for="conversion-page">Thank You Page</label>
	    	<select class="form-control" id="conversion-page">
	    		<option value=""> -- Select Thank You Page -- </option>
	    		<optgroup label="Posts">
	    			<?php 
	    				$posts = get_posts( array('posts_per_page' => 9999, 'post_status' => 'publish') );
						if ( $posts ) {
							foreach ( $posts as $ps ) {
								if ( $ps->ID == $post_id ) continue;
								$selected = ( $ps->ID == $c_id ) ? ' selected="selected"' : '';
								echo '<option value="' . $ps->ID . '"' . $selected . '>' . esc_attr($ps->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
				<optgroup label="Pages">
	    			<?php 
	    				$pages = get_pages( array('post_status' => 'publish') );
						if ( $pages ) {
							foreach ( $pages as $pg ) {
								if ( $pg->ID == $post_id ) continue;
								$selected = ( $pg->ID == $c_id ) ? ' selected="selected"' : '';
								echo '<option value="' . $pg->ID . '"' . $selected . '>' . esc_attr($pg->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
			</select>
	  	</div>
  	</div>
  	
  	<?php
  	$conversion_url = esc_url_raw(add_query_arg(array('ib2script' => 'conversion_js', 'post_id' => $_GET['post']), get_permalink($_GET['post'])));
  	$trackscript = '&lt;script type="text/javascript"&gt;' . "\n";
	$trackscript .= '(function() {' . "\n";
	$trackscript .= 'var ib2scr = document.createElement(\'script\');' . "\n";
	$trackscript .= 'ib2scr.type = \'text/javascript\';' . "\n";
	$trackscript .= 'ib2scr.async = true;' . "\n";
	$trackscript .= 'ib2scr.src = \'' . $conversion_url . '\';' . "\n";
	$trackscript .= 'document.getElementsByTagName(\'head\')[0].appendChild(ib2scr);' . "\n";
	$trackscript .= '})();' . "\n";
  	$trackscript .= '&lt;/script&gt;' . "\n";
  	?>
  	<div class="ex-thank-pages" style="display:none">
  		<hr>
	  	<div class="form-group">
	    	<label for="conversion-script">Conversion Tracking Code</label>
	    	<p class="help-block"><strong>Instruction</strong> Simply put the tracking code below at the bottom of your thank you page. Just before the closing <code>&lt;/body&gt;</code> tag.</p>
	    	<textarea class="form-control" rows="5" readonly style="cursor:pointer" onfocus="this.select();" onclick="this.select();"><?php echo $trackscript; ?></textarea>
	  		<p><strong>Important:</strong> Even though IB 2.0 lets you track external conversion page, however, you must ensure that your conversion page are on the same domain as this landing page so the cookie can be read. You could have your conversion page on a different sub-domain as long as the top level domain are the same.</p>
	  	</div>
  	</div>
</div>
<?php
}

function ib2_attentionbar_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$display = ( isset($meta['attention_bar']) && $meta['attention_bar'] == 1 ) ? '' : ' style="display:none"'; 
	$checked = ( isset($meta['attention_bar']) && $meta['attention_bar'] == 1 ) ? ' checked="checked"' : ''; 
	$poptime = ( !empty($meta['attention_bar_time']) ) ? $meta['attention_bar_time'] : 'pageload';
	$msg = ( !empty($meta['attention_bar_text']) ) ? stripslashes($meta['attention_bar_text']) : 'Your attention grabbing message here';
?>
<div class="settings-attention editor-panel-content">
	<h3>Attention Bar Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="attention-bar-enable" value="yes" data-label="Enable Attention Bar"<?php echo $checked; ?> /> 
	</div>
	<div class="attention-bar-group"<?php echo $display; ?>>
		<hr>
		<div class="form-group">
	    	<label for="attention-bar-time">When to Load The Attention Bar</label>
	    	<select class="form-control" id="attention-bar-time">
				<option value="pageload"<?php echo ( $poptime == 'pageload' ? ' selected="selected"' : ''); ?>>Every time the page is loaded</option>
				<option value="firsttime"<?php echo ( $poptime == 'firsttime' ? ' selected="selected"' : ''); ?>>First time visit only</option>
				<option value="daily"<?php echo ( $poptime == 'daily' ? ' selected="selected"' : ''); ?>>Once a day</option>
				<option value="session"<?php echo ( $poptime == 'session' ? ' selected="selected"' : ''); ?>>Once per browser session</option>
			</select>
			<p class="help-block">Please note that if this feature is enabled, the attention bar will always appear in the editor mode regardless the option the you choose above.</p>
	  	</div>
	  	<hr>
	  	
	  	<div class="form-group">
			<label for="attention-bar-text">Message</label><br />
			<input type="text" class="form-control" id="attention-bar-text" value="<?php echo $msg; ?>" placeholder="e.g. Your attention grabbing message here" />
			<p class="help-block">HTML tags are allowed.</p>
		</div>
		<hr>
		<?php $link_text = ( !empty($meta['attention_bar_anchor']) ) ? esc_attr($meta['attention_bar_anchor']) : 'Click Here'; ?>
		<div class="form-group">
			<label for="attention-bar-anchor">Link Text</label><br />
			<input type="text" class="form-control" id="attention-bar-anchor" value="<?php echo $link_text; ?>" placeholder="e.g. Click Here" />
		</div>
		<hr>
		<?php $link_url = ( !empty($meta['attention_bar_url']) ) ? esc_url($meta['attention_bar_anchor']) : ''; ?>
		<div class="form-group">
			<label for="attention-bar-url">Link URL</label><br />
			<input type="text" class="form-control" id="attention-bar-url" value="<?php echo $link_url; ?>" placeholder="e.g. http://domain.com/mylink.html" />
		</div>
		<hr>
		<?php $att_bg = ( !empty($meta['attention_bar_background']) ) ? esc_attr($meta['attention_bar_background']) : '#5bc0de'; ?>
		<div class="form-group">
	    	<label for="attention-bar-background">Background Color</label><br />
	    	<input type="text" class="form-control ib2-pick-color" id="attention-bar-background" value="<?php echo $att_bg; ?>" data-default-color="#5bc0de">
	  	</div>
		<hr>
		<?php $att_border = ( !empty($meta['attention_bar_border']) ) ? esc_attr($meta['attention_bar_border']) : '#FFFFFF'; ?>
		<div class="form-group">
	    	<label for="attention-bar-border">Border Color</label><br />
	    	<input type="text" class="form-control ib2-pick-color" id="attention-bar-border" value="<?php echo $att_border; ?>" data-default-color="#FFFFFF">
	  	</div>
		<hr>
		<?php  ?>
	  	<div class="form-group">
	    	<label for="attention-bar-font">Font Face</label>
	    	<?php
	    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif";
			$att_font = ( !empty($meta['attention_bar_font']) ) ? stripslashes($meta['attention_bar_font']) : $current_font;
	    	ib2_font_selectors('attention-bar-font', $att_font);
	    	?>
	  	</div>
	  	<hr>
	  	<?php $att_fontcolor = ( !empty($meta['attention_bar_fontcolor']) ) ? esc_attr($meta['attention_bar_fontcolor']) : '#FFFFFF'; ?>
	  	<div class="form-group">
	    	<label for="attention-bar-fontcolor">Font Color</label><br />
	    	<input type="text" class="form-control ib2-pick-color" id="attention-bar-fontcolor" value="<?php echo $att_fontcolor; ?>" data-default-color="#FFFFFF">
	  	</div>
		<hr>
  	</div>
</div>
<?php
}

function ib2_popup_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$checked = ( isset($meta['popup']) && $meta['popup'] == 1 ) ? ' checked="checked"' : ''; 
	$poptime = ( !empty($meta['popup_time']) ) ? $meta['popup_time'] : 'pageload'; 
?>
<div class="settings-popup editor-panel-content" style="display:none">
	<h3>Pop Up Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="main-popup-enable" value="yes" data-label="Enable PopUp"<?php echo $checked; ?> /> 
	</div>
	<div class="main-popup-group">
		<hr>
		<div class="form-group">
	    	<label for="main-popup-time">When to Load The PopUp</label>
	    	<select class="form-control" id="main-popup-time">
				<option value="pageload"<?php echo ( $poptime == 'pageload' ? ' selected="selected"' : ''); ?>>Every time the page is loaded</option>
				<option value="firsttime"<?php echo ( $poptime == 'firsttime' ? ' selected="selected"' : ''); ?>>First time visit only</option>
				<option value="daily"<?php echo ( $poptime == 'daily' ? ' selected="selected"' : ''); ?>>Once a day</option>
				<option value="weekly"<?php echo ( $poptime == 'weekly' ? ' selected="selected"' : ''); ?>>Once a week</option>
				<option value="monthly"<?php echo ( $poptime == 'monthly' ? ' selected="selected"' : ''); ?>>Once a month</option>
				<option value="session"<?php echo ( $poptime == 'session' ? ' selected="selected"' : ''); ?>>Once per browser session</option>
				<option value="unfocus"<?php echo ( $poptime == 'unfocus' ? ' selected="selected"' : ''); ?>>Page unfocus (about to leave the page)</option>
			</select>
	  	</div>
	  	<hr>
	  	<p style="text-align:right"><button type="button" class="btn btn-primary edit-target-popup" data-popup-from="popup">Edit PopUp</button></p>
  	</div>
</div>
<?php
}

function ib2_slider_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$checked = ( isset($meta['slider']) && $meta['slider'] == 1 ) ? ' checked="checked"' : ''; 
	$slidertime = ( !empty($meta['slider_time']) ) ? $meta['slider_time'] : 'pageload'; 
	$settings_display = ( isset($meta['slider']) && $meta['slider'] == 1 ) ? '' : ' style="display:none"';
	$close_checked = ( isset($meta['slider_close']) && $meta['slider_close'] == 1 ) ? ' checked="checked"' : '';
?>
<div class="settings-slider editor-panel-content" style="display:none">
	<h3>Bottom Slider Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="main-slider-enable" value="yes" data-label="Enable Bottom Slider"<?php echo $checked; ?> /> 
	</div>
	<div class="main-slider-group"<?php echo $settings_display;?>> 
		<hr>
		<div class="form-group">
	    	<label for="main-slider-time">When to Load The Bottom Slider</label>
	    	<select class="form-control" id="main-slider-time">
				<option value="pageload"<?php echo ( $slidertime == 'pageload' ? ' selected="selected"' : ''); ?>>Every time the page is loaded</option>
				<option value="firsttime"<?php echo ( $slidertime == 'firsttime' ? ' selected="selected"' : ''); ?>>First time visit only</option>
				<option value="daily"<?php echo ( $slidertime == 'daily' ? ' selected="selected"' : ''); ?>>Once a day</option>
				<option value="weekly"<?php echo ( $slidertime == 'weekly' ? ' selected="selected"' : ''); ?>>Once a week</option>
				<option value="monthly"<?php echo ( $slidertime == 'monthly' ? ' selected="selected"' : ''); ?>>Once a month</option>
				<option value="session"<?php echo ( $slidertime == 'session' ? ' selected="selected"' : ''); ?>>Once per browser session</option>
			</select>
	  	</div>
	  	<hr>
	  	<div class="form-group">
			<input type="checkbox" id="main-slider-close" value="yes" data-label="User Cannot Close The Slider"<?php echo $close_checked; ?> /> 
		</div>
	  	<hr>
	  	<p>The bottom slider is located at the bottom. To <strong>edit the slider content</strong>, simply scroll down into the bottom of this page.</p>
  	</div>
</div>
<?php
}

function ib2_exitsplash_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}

	$checked = ( isset($meta['exit_splash']) && $meta['exit_splash'] == 1 ) ? ' checked="checked"' : '';
	$msg = ( isset($meta['exit_msg']) ) ? esc_textarea($meta['exit_msg']) : '';
	$url = ( isset($meta['exit_url']) ) ? esc_url($meta['exit_url']) : '';
?>
<div class="settings-exitsplash editor-panel-content" style="display:none">
	<h3>Exit Splash Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="exit-splash-enable" value="yes" data-label="Enable Exit Splash"<?php echo $checked; ?>/> 
	</div>
	<div class="exit-splash-group">
		<hr>
		<div class="form-group">
	    	<label for="exit-splash-msg">Exit Splash Message </label><br />
	    	<textarea class="form-control" id="exit-splash-msg" rows="4"><?php echo $msg; ?></textarea>
	  	</div>
	  	<hr>
		<div class="form-group">
			<label for="exit-splash-url">Exit Splash URL </label><br />
			<input type="text" class="form-control" id="exit-splash-url" value="<?php echo $url; ?>" placeholder="e.g. http://myothersite.com/" />
		</div>
  	</div>
</div>
<?php
}

function ib2_favicon_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}

	$favicon = ( isset($meta['favicon']) ) ? esc_url(stripslashes($meta['favicon'])) : '';
?>
<div class="settings-favicon editor-panel-content" style="display:none">
	<h3>Favicon Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<label for="favicon-url">Favicon URL</label>
  		<input type="text" class="form-control" id="favicon-url" value="<?php echo $favicon; ?>" placeholder="e.g. http://mydomain.com/favicon.ico" />
  		<p class="help-block">Enter a favicon URL into the field above, or click the "Action" button below to upload a new favicon. Accepted format: <code>*.ico</code> or <code>*.png</code></p>
  		<div id="favicon-prev"></div>
        <p style="margin-top:10px">
        	<div class="btn-group">
        		<button id="favicon-chooser" class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#" id="favicon-upload">Upload</a></li>
				</ul>
        	</div>
        	<span id="favicon-el-rmv"<?php if ( empty($favicon) ) : ?> style="display:none"<?php endif; ?>><a href='#' class="btn btn-danger btn-sm remove-favicon-el">Remove</a></span>
        </p>
	</div>
</div>
<?php
}

function ib2_rclick_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}

	$checked = ( isset($meta['right_click']) && $meta['right_click'] == 1 ) ? ' checked="checked"' : '';
	$checked2 = ( isset($meta['right_click_img']) && $meta['right_click_img'] == 1 ) ? ' checked="checked"' : '';
	$msg = ( isset($meta['right_click_msg']) ) ? esc_textarea($meta['right_click_msg']) : '';
?>
<div class="settings-rclick editor-panel-content" style="display:none">
	<h3>Disable Right-Click Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="right-click-enable" value="yes" data-label="Enable This Feature"<?php echo $checked; ?>/> 
	</div>
	<div class="rclick-group">
		<hr>
		<div class="form-group">
	    	<label for="right-click-msg">Warning Message </label><br />
	    	<textarea class="form-control" id="right-click-msg" rows="4"><?php echo $msg; ?></textarea>
	    	<p class="help-block">Enter some text if you want to display a warning message. Otherwise, leave this blank.</p>
	  	</div>
	  	<hr>
	  	<div class="form-group">
			<input type="checkbox" id="right-click-img" value="yes" data-label="Disable Right-Click on Image Only"<?php echo $checked2; ?>/> 
		</div>
  	</div>
</div>
<?php
}

function ib2_welcomegate_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}

	if ( isset($data['gate_type']) ) {
		$checked = ( isset($data['welcome_gate']) && $data['welcome_gate'] == 1 ) ? ' checked="checked"' : '';
		$gate_type = ( isset($data['gate_type']) ) ? $data['gate_type'] : 'welcome';
		$thanks_id = ( isset($data['gate_thanks_id']) ) ? $data['gate_thanks_id'] : '';
		$gate_code = ( !empty($data['gate_code']) ) ? $data['gate_code'] : ib2_string_enc($post_id, 'instabuilder2');
	} else {
		$variants = ib2_get_variants($post_id);
		if ( count($variants) > 1 && empty($meta['welcome_gate']) ) {
			foreach ( $variants as $value ) {
				$var = 'variation' . $value->variant;
				$dat = ( isset($data[$var]) ) ? $data[$var] : array();
				if ( isset($dat['welcome_gate']) && $dat['welcome_gate'] == 1 ) {
					$meta = $dat;
					break;
				}
			}
		}
		
		$checked = ( isset($meta['welcome_gate']) && $meta['welcome_gate'] == 1 ) ? ' checked="checked"' : '';
		$gate_type = ( isset($meta['gate_type']) ) ? $meta['gate_type'] : 'welcome';
		$c_id = ( isset($meta['locked_page']) ) ? $meta['locked_page'] : '';
		$thanks_id = ( isset($meta['gate_thanks_id']) ) ? $meta['gate_thanks_id'] : '';
		$gate_code = ( !empty($meta['gate_code']) ) ? $meta['gate_code'] : ib2_string_enc($post_id, 'instabuilder2');
	}

	$gate_meta = get_post_meta($post_id, 'ib2_gate_master', true);
	$lock_meta = get_post_meta($post_id, 'ib2_welcome_gate', true);
	$parent_lock_id = ( !empty($lock_meta['gate_id']) ) ? (int) $lock_meta['gate_id'] : 0;
	
	$gatevalues = array();
	if ( isset($gate_meta['lock_ids']) ) {
		$gatevalues = $gate_meta['lock_ids'];
	} else {
		if ( isset($c_id) )
			$gatevalues = array($c_id);
	}
	
	$gate_thanks_url = ( !empty($thanks_id) ) ? add_query_arg('ib2uc', $gate_code, get_permalink($thanks_id)) : 'Please select a thank you page first';
?>
<div class="settings-wgate editor-panel-content" style="display:none">
	<h3>Page Gate Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="wgate-enable" value="yes" data-label="Enable Page Gate"<?php echo $checked; ?>/> 
	</div>
	<div class="wgate-splash-group">
		<hr>
		<div class="form-group">
			<p><strong>How It Works: </strong> You can select other page(s) below to be locked by this landing page. So, when someone visit one of the locked page, they will be redirected to this landing page instead, which mean this page act like as a gateway to the locked page(s).</p>
			<p>You can choose whether to unlock the locked page(s) after first time visit or force visitors to subscribe to unlock.</p>
		</div>
		<hr>
		<div class="form-group">
	    	<label for="locked-page">Posts/Pages To Be Locked:</label>
	    	<select class="form-control" id="locked-page" multiple>
	    		<optgroup label="Posts">
	    			<?php
	    				$posts = get_posts( array('posts_per_page' => 9999, 'post_status' => 'publish') );
						if ( $posts ) {
							foreach ( $posts as $ps ) {
								if ( $ps->ID == $parent_lock_id ) continue;
								if ( $ps->ID == $post_id ) continue;
								$selected = ( in_array($ps->ID, $gatevalues) ) ? ' selected="selected"' : '';
								echo '<option value="' . $ps->ID . '"' . $selected . '>' . esc_attr($ps->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
				<optgroup label="Pages">
	    			<?php
	    				$pages = get_pages( array('post_status' => 'publish') );
						if ( $pages ) {
							foreach ( $pages as $pg ) {
								if ( $pg->ID == $parent_lock_id ) continue;
								if ( $pg->ID == $post_id ) continue;
								$selected = ( in_array($pg->ID, $gatevalues) ) ? ' selected="selected"' : '';
								echo '<option value="' . $pg->ID . '"' . $selected . '>' . esc_attr($pg->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
			</select>
			<p class="help-block">Use 'ctrl+click' to choose multiple pages to be locked.</p>
			<p class="help-block"><strong>Important: </strong> The page gate feature will work if both this and the locked pages has been published. Also, this feature will work ONLY if you're not logging in as WP admin/user.</p>
	  	</div>
	  	<hr>
		<div class="form-group">
	    	<label for="gate-type">Unlock Mode</label>
	    	<select class="form-control" id="gate-type">
				<option value="welcome"<?php echo ( $gate_type == 'welcome' ? ' selected="selected"' : '' ); ?>>Auto-Unlock After First Time Visit</option>
				<option value="launch"<?php echo ( $gate_type == 'launch' ? ' selected="selected"' : '' ); ?>>Unlock Only After Subscribed</option>
			</select>
	  	</div>
	  	
	  	<hr>
		<div class="form-group launch-gate-group">
	    	<label for="gate-thanks-page">Your Opt-In Thank You Page:</label>
	    	<select class="form-control" id="gate-thanks-page">
	    		<option value=""> -- Select Thank You Page -- </option>
	    		<optgroup label="Posts">
	    			<?php 
						if ( $posts ) {
							foreach ( $posts as $post ) {
								if ( $post->ID == $parent_lock_id ) continue;
								if ( $post->ID == $post_id ) continue;
								if ( in_array($post->ID, $gatevalues) ) continue;
								$selected = ( $post->ID == $thanks_id ) ? ' selected="selected"' : '';
								echo '<option value="' . $post->ID . '"' . $selected . '>' . esc_attr($post->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
				<optgroup label="Pages">
	    			<?php 
						if ( $pages ) {
							foreach ( $pages as $page ) {
								if ( $page->ID == $parent_lock_id ) continue;
								if ( $page->ID == $post_id ) continue;
								if ( in_array($page->ID, $gatevalues) ) continue;
								$selected = ( $page->ID == $thanks_id ) ? ' selected="selected"' : '';
								echo '<option value="' . $page->ID . '"' . $selected . '>' . esc_attr($page->post_title) . '</option>';
							}
						}
	    			?>
	    		</optgroup>
			</select>
			<p class="help-block">Please choose the thank you page that you use for your opt-in form. After that, IB2 will generate a custom URL for your thank you page below. Please use the custom thank page URL in your autoresponder account.</p>
	  	</div>
	  	<div class="form-group launch-gate-group">
	  		<label for="gate-custom-thank-page">Custom Thank You Page URL</label><br />
			<input type="text" class="form-control" id="gate-custom-thank-page" value="<?php echo esc_url($gate_thanks_url); ?>" readonly />
			<input type="hidden" id="gate-code" value="<?php echo $gate_code; ?>" />
			<p class="help-block">Please copy the generated thank you page url above and use it as the thank you page url in your autoresponder form settings.</p>
	  	</div>
	  	<script>
	  		jQuery(document).ready(function($){
	  			$('#gate-type').change(function(){
	  				var opt = $("option:selected", this).val();
	  				if ( opt == 'launch' )
	  					$('.launch-gate-group').show();
	  				else
	  					$('.launch-gate-group').hide();
	  			}).change();
	  			
	  			$('#gate-thanks-page').change(function(){
	  				var opt = $("option:selected", this).val();
	  				$('#gate-custom-thank-page').val('Generating URL ...');
	  				$.post(ajaxurl, {
	  					action: 'ib2_gate_url',
	  					thanks_id: opt,
	  					code: $('#gate-code').val()
	  				}, function(response){
	  					$('#gate-custom-thank-page').val(response);
	  				});
	  			});
	  		});
	  	</script>
  	</div>
</div>
<?php
}

function ib2_quiz_settings() {
	$post_id = (int) $_GET['post'];
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
?>
<div class="settings-quiz editor-panel-content" style="display:none">
	<h3>Questions Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="quiz-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#quiz-settings-setup" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Survey Setup</a></li>
			<li><a href="#quiz-settings-qfont" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Question Font Style</a></li>
			<li><a href="#quiz-settings-afont" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Answers Font Style</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="quiz-settings-setup" class="tab-settings-content">
			<div class="form-group">
				<div class="panel-col col-sm-8">
					<label for="quiz-questions">Number Of Questions</label>
	    			<div id="quiz-questions-slider" class="ib2-slider"></div>
	    		</div>
				<div class="panel-col col-sm-4">
	    			<input type="text" class="form-control ib2-slider-val" id="quiz-questions" value="1" style="width:40px">
	    		</div>
				<div class="clearfix"></div>
    		</div>
    		<hr>
		</div>
		
		<div id="quiz-settings-qfont" class="tab-settings-content">
			<div class="form-group">
		    	<label for="quiz-question-font">Font Face</label>
		    	<?php
		    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
		    	ib2_font_selectors('quiz-question-font', $current_font);
		    	?>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="quiz-question-color">Font Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="quiz-question-color" value=#333333" data-default-color="#333333">
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
					<label for="quiz-question-size">Font Size</label>
	    			<div id="quiz-question-size-slider" class="ib2-slider"></div>
	    		</div>
				<div class="panel-col col-sm-4">
	    			<input type="text" class="form-control ib2-slider-val" id="quiz-question-size" value="26" style="width:40px">
    			</div>
				<div class="clearfix"></div>
    		</div>
    		<hr>
		</div>
		
		<div id="quiz-settings-afont" class="tab-settings-content">
			<div class="form-group">
		    	<label for="quiz-answer-font">Font Face</label>
		    	<?php
		    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
		    	ib2_font_selectors('quiz-answer-font', $current_font);
		    	?>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="quiz-answer-color">Font Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="quiz-answer-color" value="#333333" data-default-color="#333333">
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
					<label for="quiz-answer-size">Font Size</label>
	    			<div id="quiz-answer-size-slider" class="ib2-slider"></div>
	    		</div>
				<div class="panel-col col-sm-4">
	    			<input type="text" class="form-control ib2-slider-val" id="quiz-answer-size" value="16" style="width:40px">
    			</div>
				<div class="clearfix"></div>
    		</div>
    		<hr>
		</div>
  	</div>
</div>
<?php
}

function ib2_graphics_settings() {
	$folders = ib2_graphic_folders();
	$default = 'bullets';
?>
<div class="settings-graphics editor-panel-content" style="display:none">
	<h3>Insert Graphics <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<hr>
	<div class="form-group">
    	<label for="graphic-type">Graphic Type</label>
    	<select class="form-control" id="graphic-type">
			<?php if ( !empty($folders) ) {
				foreach ( $folders as $folder ) {
					$selected = ( $folder == $default ) ? ' selected="selected"' : '';
					echo '<option value="' . $folder . '"' . $selected . '>' . ucwords(str_replace("_", ' ', $folder)) . '</option>' . "\n";
				}
			} ?>
		</select>
  	</div>
  	<hr>
  	<p class="help-block">Drag and drop the graphic into the content area on the left.</p>
  	<div id="graphics-container">
  		<p id="graphic-loader" style="display: none; text-align:center"><img src="<?php echo IB2_IMG; ?>ajax-loader.gif" border="0" /></p>
  		<?php ib2_graphics_html('bullets'); ?>
  	</div>
</div> 
<?php	
}

function ib2_media_settings( $post_id ) {
	global $content_width, $wpdb;

	// We're going to pass the old thickbox media tabs to `media_upload_tabs`
	// to ensure plugins will work. We will then unset those tabs.
	$tabs = array(
		// handler action suffix => tab label
		'type'     => '',
		'type_url' => '',
		'gallery'  => '',
		'library'  => '',
	);

	/** This filter is documented in wp-admin/includes/media.php */
	$tabs = apply_filters( 'media_upload_tabs', $tabs );
	unset( $tabs['type'], $tabs['type_url'], $tabs['gallery'], $tabs['library'] );

	$props = array(
		'link'  => get_option( 'image_default_link_type' ), // db default is 'file'
		'align' => get_option( 'image_default_align' ), // empty default
		'size'  => get_option( 'image_default_size' ),  // empty default
	);

	$exts = array_merge( wp_get_audio_extensions(), wp_get_video_extensions() );
	$mimes = get_allowed_mime_types();
	$ext_mimes = array();
	foreach ( $exts as $ext ) {
		foreach ( $mimes as $ext_preg => $mime_match ) {
			if ( preg_match( '#' . $ext . '#i', $ext_preg ) ) {
				$ext_mimes[ $ext ] = $mime_match;
				break;
			}
		}
	}

	$has_audio = $wpdb->get_var( "
		SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'attachment'
		AND post_mime_type LIKE 'audio%'
		LIMIT 1
	" );
	$has_video = $wpdb->get_var( "
		SELECT ID
		FROM $wpdb->posts
		WHERE post_type = 'attachment'
		AND post_mime_type LIKE 'video%'
		LIMIT 1
	" );

	$settings = array(
		'tabs'      => $tabs,
		'tabUrl'    => esc_url(add_query_arg( array( 'chromeless' => true ), admin_url('media-upload.php') )),
		'mimeTypes' => wp_list_pluck( get_post_mime_types(), 0 ),
		/** This filter is documented in wp-admin/includes/media.php */
		'captions'  => ! apply_filters( 'disable_captions', '' ),
		'nonce'     => array(
			'sendToEditor' => wp_create_nonce( 'media-send-to-editor' ),
		),
		'post'    => array(
			'id' => 0,
		),
		'defaultProps' => $props,
		'attachmentCounts' => array(
			'audio' => (int) $has_audio,
			'video' => (int) $has_video,
		),
		'embedExts'    => $exts,
		'embedMimes'   => $ext_mimes,
		'contentWidth' => $content_width,
	);

	$post = null;
	if ( is_numeric($post_id) ) {
		$post = get_post( $post_id );
		$settings['post'] = array(
			'id' => $post->ID,
			'nonce' => wp_create_nonce( 'update-post_' . $post->ID ),
		);

		$thumbnail_support = current_theme_supports( 'post-thumbnails', $post->post_type ) && post_type_supports( $post->post_type, 'thumbnail' );
		if ( ! $thumbnail_support && 'attachment' === $post->post_type && $post->post_mime_type ) {
			if ( 0 === strpos( $post->post_mime_type, 'audio/' ) ) {
				$thumbnail_support = post_type_supports( 'attachment:audio', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:audio' );
			} elseif ( 0 === strpos( $post->post_mime_type, 'video/' ) ) {
				$thumbnail_support = post_type_supports( 'attachment:video', 'thumbnail' ) || current_theme_supports( 'post-thumbnails', 'attachment:video' );
			}
		}

		if ( $thumbnail_support ) {
			$featured_image_id = get_post_meta( $post->ID, '_thumbnail_id', true );
			$settings['post']['featuredImageId'] = $featured_image_id ? $featured_image_id : -1;
		}
	}

	$hier = $post && is_post_type_hierarchical( $post->post_type );

	$strings = array(
		// Generic
		'url'         => __( 'URL' ),
		'addMedia'    => __( 'Add Media' ),
		'search'      => __( 'Search' ),
		'select'      => __( 'Select' ),
		'cancel'      => __( 'Cancel' ),
		'update'      => __( 'Update' ),
		'replace'     => __( 'Replace' ),
		'remove'      => __( 'Remove' ),
		'back'        => __( 'Back' ),
		/* translators: This is a would-be plural string used in the media manager.
		   If there is not a word you can use in your language to avoid issues with the
		   lack of plural support here, turn it into "selected: %d" then translate it.
		 */
		'selected'    => __( '%d selected' ),
		'dragInfo'    => __( 'Drag and drop to reorder images.' ),

		// Upload
		'uploadFilesTitle'  => __( 'Upload Files' ),
		'uploadImagesTitle' => __( 'Upload Images' ),

		// Library
		'mediaLibraryTitle'  => __( 'Media Library' ),
		'insertMediaTitle'   => __( 'Insert Media' ),
		'createNewGallery'   => __( 'Create a new gallery' ),
		'createNewPlaylist'   => __( 'Create a new playlist' ),
		'createNewVideoPlaylist'   => __( 'Create a new video playlist' ),
		'returnToLibrary'    => __( '&#8592; Return to library' ),
		'allMediaItems'      => __( 'All media items' ),
		'noItemsFound'       => __( 'No items found.' ),
		'insertIntoPost'     => $hier ? __( 'Insert into page' ) : __( 'Insert into post' ),
		'uploadedToThisPost' => $hier ? __( 'Uploaded to this page' ) : __( 'Uploaded to this post' ),
		'warnDelete' =>      __( "You are about to permanently delete this item.\n  'Cancel' to stop, 'OK' to delete." ),

		// From URL
		'insertFromUrlTitle' => __( 'Insert from URL' ),

		// Featured Images
		'setFeaturedImageTitle' => __( 'Set Featured Image' ),
		'setFeaturedImage'    => __( 'Set featured image' ),

		// Gallery
		'createGalleryTitle' => __( 'Create Gallery' ),
		'editGalleryTitle'   => __( 'Edit Gallery' ),
		'cancelGalleryTitle' => __( '&#8592; Cancel Gallery' ),
		'insertGallery'      => __( 'Insert gallery' ),
		'updateGallery'      => __( 'Update gallery' ),
		'addToGallery'       => __( 'Add to gallery' ),
		'addToGalleryTitle'  => __( 'Add to Gallery' ),
		'reverseOrder'       => __( 'Reverse order' ),

		// Edit Image
		'imageDetailsTitle'     => __( 'Image Details' ),
		'imageReplaceTitle'     => __( 'Replace Image' ),
		'imageDetailsCancel'    => __( 'Cancel Edit' ),
		'editImage'             => __( 'Edit Image' ),

		// Crop Image
		'chooseImage' => __( 'Choose Image' ),
		'selectAndCrop' => __( 'Select and Crop' ),
		'skipCropping' => __( 'Skip Cropping' ),
		'cropImage' => __( 'Crop Image' ),
		'cropYourImage' => __( 'Crop your image' ),
		'cropping' => __( 'Cropping&hellip;' ),
		'suggestedDimensions' => __( 'Suggested image dimensions:' ),
		'cropError' => __( 'There has been an error cropping your image.' ),

		// Edit Audio
		'audioDetailsTitle'     => __( 'Audio Details' ),
		'audioReplaceTitle'     => __( 'Replace Audio' ),
		'audioAddSourceTitle'   => __( 'Add Audio Source' ),
		'audioDetailsCancel'    => __( 'Cancel Edit' ),

		// Edit Video
		'videoDetailsTitle'     => __( 'Video Details' ),
		'videoReplaceTitle'     => __( 'Replace Video' ),
		'videoAddSourceTitle'   => __( 'Add Video Source' ),
		'videoDetailsCancel'    => __( 'Cancel Edit' ),
		'videoSelectPosterImageTitle' => __( 'Select Poster Image' ),
		'videoAddTrackTitle'	=> __( 'Add Subtitles' ),

 		// Playlist
 		'playlistDragInfo'    => __( 'Drag and drop to reorder tracks.' ),
 		'createPlaylistTitle' => __( 'Create Audio Playlist' ),
 		'editPlaylistTitle'   => __( 'Edit Audio Playlist' ),
 		'cancelPlaylistTitle' => __( '&#8592; Cancel Audio Playlist' ),
 		'insertPlaylist'      => __( 'Insert audio playlist' ),
 		'updatePlaylist'      => __( 'Update audio playlist' ),
 		'addToPlaylist'       => __( 'Add to audio playlist' ),
 		'addToPlaylistTitle'  => __( 'Add to Audio Playlist' ),

 		// Video Playlist
 		'videoPlaylistDragInfo'    => __( 'Drag and drop to reorder videos.' ),
 		'createVideoPlaylistTitle' => __( 'Create Video Playlist' ),
 		'editVideoPlaylistTitle'   => __( 'Edit Video Playlist' ),
 		'cancelVideoPlaylistTitle' => __( '&#8592; Cancel Video Playlist' ),
 		'insertVideoPlaylist'      => __( 'Insert video playlist' ),
 		'updateVideoPlaylist'      => __( 'Update video playlist' ),
 		'addToVideoPlaylist'       => __( 'Add to video playlist' ),
 		'addToVideoPlaylistTitle'  => __( 'Add to Video Playlist' ),
	);

	/**
	 * Filter the media view settings.
	 *
	 * @since 3.5.0
	 *
	 * @param array   $settings List of media view settings.
	 * @param WP_Post $post     Post object.
	 */
	$settings = apply_filters( 'media_view_settings', $settings, $post );

	/**
	 * Filter the media view strings.
	 *
	 * @since 3.5.0
	 *
	 * @param array   $strings List of media view strings.
	 * @param WP_Post $post    Post object.
	 */
	$strings = apply_filters( 'media_view_strings', $strings,  $post );

	$strings['settings'] = $settings;

	echo ib2_script_localize( '_wpMediaViewsL10n', $strings );
}