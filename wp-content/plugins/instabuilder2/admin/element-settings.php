<?php
function ib2_element_settings() {
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
<div class="settings-code editor-panel-content" style="display:none">
	<h3>Code Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="code-content">Code (HTML/CSS/JavaScript)</label>
    	<textarea class="form-control" id="code-content" rows="5"></textarea>
    	<p><strong>Warning: </strong>Please do NOT insert PHP Code and any type of Shortcodes here. To enter a shortcode, please use the "Shortcode Placeholder" element.</p>
  	</div>
</div>

<div class="settings-shortcode editor-panel-content" style="display:none">
	<h3>Shortcode Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="shortcode-content">Shortcode</label>
    	<textarea class="form-control" id="shortcode-content" rows="3"></textarea>
  	</div>
</div>

<div class="settings-tabs editor-panel-content" style="display:none">
	<h3>Tabs Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="tabs-title-edits">
		
	</div>
</div>

<div class="settings-comment editor-panel-content" style="display:none">
	<h3>Comment Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<p><strong>Important:</strong> In order to display a Facebook or Disqus comment, you MUST integrate IB 2.0 with both of the system. You can integrate IB 2.0 with Disqus and Facebook by <a href="<?php echo admin_url('admin.php?page=ib2-settings'); ?>" target="_blank">clicking this link.</a></p> 
	<hr>
	<div class="form-group">
    	<label for="comment-system">Comment System</label>
    	<select class="form-control" id="comment-system">
    		<option value="facebook">Facebook</option>
    		<option value="disqus">Disqus</option>
		</select>
		<p class="help-block"><strong>Note:</strong> Both Facebook or Disqus comment box that you see in this editor are dummies. To view the actual comment box, please save this page first and then click on the "Preview" button (eye icon) at the top bar.</p>
  	</div>
  	<hr>
</div>

<div class="settings-text editor-panel-content" style="display:none">
	<h3>Text Effect Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
		<input type="checkbox" id="text-el-shadow" value="yes" data-label="Enable Text Shadow" /> 
	</div>
	<div class="text-el-shadow-group">
		<hr>
		<div class="form-group">
	    	<label for="text-el-shadow-color">Text Shadow Color</label><br />
	    	<input type="text" class="form-control ib2-pick-color" id="text-el-shadow-color" value="#808080" data-default-color="#808080">
	  	</div>
	  	<hr>
	  	<div class="form-group">
			<div class="panel-col col-sm-8">
		    	<label for="text-el-shadow-blur">Text Shadow Blur</label>
		    	<div id="text-el-shadow-blur-slider" class="ib2-slider"></div>
	    	</div>
	    	<div class="panel-col col-sm-4">
	    		<input type="text" class="form-control ib2-slider-val" id="text-el-shadow-blur" value="0" style="width:40px"> px
	  		</div>
	  		<div class="clearfix"></div>
	  	</div>
	</div>
	<hr>
	<div class="form-group">
    	<label for="text-animation">Display Animation</label>
    	<select class="form-control" id="text-animation">
    		<option value="none" selected="selected">None</option>
			<option value="blind">Blind</option>
			<option value="bounce">Bounce</option>
			<option value="bounceInLeft">Bounce In Left</option>
			<option value="bounceInRight">Bounce In Right</option>
			<option value="bounceInUp">Bounce In Up</option>
			<option value="clip">Clip</option>
			<option value="drop">Drop</option>
			<option value="explode">Explode</option>
			<option value="fold">Fold</option>
			<option value="highlight">Highlight</option>
			<option value="puff">Puff</option>
			<option value="pulsate">Pulsate</option>
			<option value="scale">Scale</option>
			<option value="shake">Shake</option>
			<option value="slide">Slide</option>
			<option value="flipInX">Flip In X</option>
			<option value="flipInY">Flip In Y</option>
			<option value="rotateIn">Rotate</option>
			<option value="rotateInDownLeft">Rotate Down Left</option>
			<option value="rotateInDownRight">Rotate Down Right</option>
			<option value="rotateInUpLeft">Rotate Up Left</option>
			<option value="rotateInUpRight">Rotate Up Right</option>
			<option value="fadeInDownBig">Fade In Down</option>
			<option value="fadeInUpBig">Fade In Up</option>
			<option value="fadeInLeftBig">Fade In Left</option>
			<option value="fadeInRightBig">Fade In Right</option>
		</select>
		<p class="help-block">The selected animation above will be triggered only the first time this element is shown.</p>
  	</div>
  	<hr>
</div>

<div class="settings-date editor-panel-content" style="display:none">
	<h3>Date Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="date-format">Date Format</label>
    	<select class="form-control" id="date-format">
    		<option value="dddd, MMMM Do, YYYY"><?php echo date_i18n('l, F jS, Y'); ?></option>
    		<option value="MMMM Do, YYYY"><?php echo date_i18n('F jS, Y'); ?></option>
    		<option value="MMM Do, YYYY"><?php echo date_i18n('M jS, Y'); ?></option>
    		<option value="dddd, DD MMMM, YYYY"><?php echo date_i18n('l, d F, Y'); ?></option>
    		<option value="DD MMMM, YYYY"><?php echo date_i18n('d F, Y'); ?></option>
    		<option value="DD MMM, YYYY"><?php echo date_i18n('d M, Y'); ?></option>
		</select>
  	</div>
  	<hr>
  	<div class="form-group">
    	<label for="date-timezone">Timezone</label>
    	<?php ib2_timezone_select('date-timezone'); ?>
    </div>
    <hr>
    <div class="form-group">
    	<label for="date-font-face">Font Face</label>
    	<?php
    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
    	ib2_font_selectors('date-font-face', $current_font);
    	?>
  	</div>
		  	
  	<div class="form-group">
    	<label for="date-font-color">Font Color</label><br />
    	<input type="text" class="form-control ib2-pick-color" id="date-font-color" value="#CC0000" data-default-color="#CC0000">
  	</div>
	<hr>	  	
	<div class="form-group">
		<div class="panel-col col-sm-8">
	    	<label for="date-font-size">Font Size</label>
	    	<div id="date-font-size-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="date-font-size" value="14" style="width:40px"> px
  		</div>
  		<div class="clearfix"></div>
  	</div>
  	<hr>
</div>

<div class="settings-share editor-panel-content" style="display:none">
	<h3>Social Share Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="social-share-style">Share Button Style</label>
    	<select class="form-control" id="social-share-style">
			<option value="big" selected="selected">Big</option>
			<option value="small-counter">Small</option>
			<option value="small">Small (no counter)</option>
		</select>
  	</div>
  	<hr>
  	<div class="form-group">
		<input type="checkbox" id="share-facebook-btn" class="ib2-share-chkbox" value="facebook" data-label="Facebook" /> 
	</div>
	<hr>
  	<div class="form-group">
		<input type="checkbox" id="share-twitter-btn" class="ib2-share-chkbox" value="twitter" data-label="Twitter" /> 
	</div>
	<hr>
  	<div class="form-group">
		<input type="checkbox" id="share-linkedin-btn" class="ib2-share-chkbox" value="linkedin" data-label="LinkedIn" /> 
	</div>
	<hr>
  	<div class="form-group">
		<input type="checkbox" id="share-google-btn" class="ib2-share-chkbox" value="google" data-label="Google+" /> 
	</div>
	<hr>
	<div class="form-group">
    	<label for="share-custom-url">Custom URL</label><br />
    	<input type="text" class="form-control" id="share-custom-url" placeholder="e.g. http://mydomain.com/myotherpage">
    	<p>If you want to share a URL other than this page, then you can enter the URL here. Otherwise, leave this empty.</p>
  	</div>
</div>

<div class="settings-hline editor-panel-content" style="display:none">
	<h3>Line/Divider Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="hline-color">Line Color</label><br />
    	<input type="text" class="form-control ib2-pick-color" id="hline-color" value="#A7A7A7" data-default-color="#A7A7A7">
  	</div>
  	<hr>
  	<div class="form-group">
    	<label for="hline-type">Line Style</label>
    	<select class="form-control" id="hline-type">
			<option value="solid" selected="selected">Solid</option>
			<option value="dashed">Dashed</option>
			<option value="dotted">Dotted</option>
			<option value="double">Double Line</option>
			<option value="none">None</option>
		</select>
  	</div>
  	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="hline-thick">Thickness</label>
    		<div id="hline-thick-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hline-thick" value="1" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="hline-space">Spacing</label>
    		<div id="hline-space-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hline-space" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
</div>

<div class="settings-spacer editor-panel-content" style="display:none">
	<h3>Spacer Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="spacer-space">Space Size</label>
    		<div id="spacer-space-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="spacer-space" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
</div>

<?php
	ib2_video_element_settings();
	ib2_image_element_settings();
	ib2_menu_element_settings();
	ib2_countdown_element_settings();
	ib2_optin_element_settings();
	ib2_button_element_settings();
	ib2_box_element_settings();
	ib2_slides_element_settings();
	ib2_hotspot_settings();
}

function ib2_slides_element_settings() {
?>
<div class="settings-slides editor-panel-content" style="display:none">
	<h3>Slides/Carousel Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<hr>
	<p>Recommended Image Width: <code id="carousel-width"></code><br />
	Recommended Image Height: <code>any</code></p>
	<hr>
	<div id="slide-images-settings">
		<div class="form-group slide-settings slide-setting-0">
	  		<label for="box-image">Slide Image #<span class="slider-num">1</span></label>
	  		<input type="text" class="form-control slider-image-field" id="slide-el-url-0" value="" placeholder="e.g. http://mydomain.com/image.jpg" data-slide-num="0" />
	  		<p class="help-block">Enter an image URL into the field above, or click the "Upload" button below to upload an image.</p>
	        <p style="margin-top:10px">
	        	<button class="btn btn-primary btn-sm slide-image-upload-btn" data-slide-num="0" type="button">Upload Image</button>
	        </p>

	        <label for="box-image">Slide #<span class="slider-num">1</span> Title</label>
	  		<input type="text" class="form-control slider-title-field" id="slide-el-title-0" value="" placeholder="e.g. My Slide Title" data-slide-num="0" />
	  		<p class="help-block">You can add a title for this slide (optional).</p>
	  		
	  		<label for="box-image">Slide #<span class="slider-num">1</span> Destination URL</label>
	  		<input type="text" class="form-control slider-url-field" id="slide-el-desturl-0" value="" placeholder="e.g. http://mydomain.com/mypage" data-slide-num="0" />
	  		<p class="help-block">You can make the slide image clickable by entering the destination URL (optional).</p>
	        <hr>
	    </div>
    </div>
    <p style="margin-top:10px; text-align:right">
    	<button class="btn btn-default btn-sm add-slide-btn" type="button" data-next-slidenum="1">+ Add Slide</button>
    </p>
</div>
<?php
}

function ib2_video_element_settings() {
?>
<div class="settings-video editor-panel-content" style="display:none">
	<h3>Video Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="video-type">Video Type</label>
    	<select class="form-control" id="video-type">
			<option value="hosted" selected="selected">Hosted</option>
			<option value="youtube">YouTube</option>
			<option value="vimeo">Vimeo</option>
			<option value="embed">Embed Code</option>
		</select>
  	</div>
  	
  	<div class="hosted-property">
  		<hr>
	  	<div class="form-group"> 
	    	<label for="video-mp4">MP4 Video URL</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-mp4" value="" placeholder="e.g. http://mysite.com/video.mp4" />
	  	</div>
	  	<hr>
	  	<div class="form-group">
	    	<label for="video-ogg">OGG Video URL (optional)</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-ogg" value="" placeholder="e.g. http://mysite.com/video.ogv" />
	  	</div>
	  	<hr>
	  	<div class="form-group">
	    	<label for="video-webm">WebM Video URL (optional)</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-webm" value="" placeholder="e.g. http://mysite.com/video.webm" />
	  	</div>
	</div>
	
	<div class="embed-property" style="display:none">
  		<hr>
	  	<div class="form-group"> 
	    	<label for="video-embed">Embed Code</label><br />
	    	<textarea class="form-control video-url-field" id="video-embed" rows="3"></textarea>
	    	<p class="help-block">Using <strong>iframe embed code is recommended.</strong></p>
	  	</div>
	</div>
	
  	<div class="youtube-property" style="display:none">
  		<hr>
	  	<div class="form-group">
	    	<label for="video-youtube">YouTube Video URL</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-youtube" value="" placeholder="e.g. http://youtu.be/Tx7DwS8OiIM" />
	  	</div>
	</div>
  	<div class="vimeo-property" style="display:none">
  		<hr>
	  	<div class="form-group">
	    	<label for="video-vimeo">Vimeo Video URL</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-vimeo" value="" placeholder="e.g. http://vimeo.com/102962788" />
	  	</div>
	</div>

	<div class="hosted-property"> 
		<hr>
		<div class="form-group">
	    	<label for="video-splash">Image Splash URL (optional)</label><br />
	    	<input type="text" class="form-control video-url-field" id="video-splash" value="" placeholder="e.g. http://mysite.com/splash.jpg" />
	  	</div>
	  	<p><button id="upload-vid-splash" type="button" class="btn btn-default">Upload</button></p>
  	</div>
  	<div class="vimeo-property youtube-property hosted-property">
		<hr>
		<div class="form-group">
			<input type="checkbox" id="video-autoplay" value="yes" data-label="Autoplay" />
		</div>
	</div>
	<div class="youtube-property hosted-property">
		<hr>
		<div class="form-group">
			<input type="checkbox" id="video-no-control" value="yes" data-label="Disable Controls" />
		</div>
	</div>
	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="video-tm">Top Margin</label>
    		<div id="video-tm-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="video-tm" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="video-bm">Bottom Margin</label>
    		<div id="video-bm-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="video-bm" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
</div>
<?php
}

function ib2_image_element_settings() {
?>
<div class="settings-image editor-panel-content" style="display:none">
	<h3>Image Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
  		<label for="box-image">Replace/Upload/Search Image</label>
  		<input type="text" class="form-control" id="image-el-url" value="" placeholder="e.g. http://mydomain.com/image.jpg" />
  		<p class="help-block">Enter an image URL into the field above, or click the "Action" button below to upload or to search the web for an image.</p>
  		<div id="image-el-prev"></div>
        <p style="margin-top:10px">
        	<div class="btn-group">
        		<button id="image-chooser" class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></button>
				<ul class="dropdown-menu" role="menu">
					<li><a href="#" class="background-image-search" data-type="image" data-element="image">Search Image</a></li>
					<li><a href="#" id="image-upload">Upload Image</a></li>
				</ul>
        	</div>
        	<span id="image-el-rmv" style="display:none"><a href='#' class="btn btn-warning btn-sm open-image-editor" data-element="image" data-img-type="image">Edit</a>&nbsp;&nbsp;<a href='#' class="btn btn-danger btn-sm remove-img-el">Remove</a></span>
        </p>
    </div>
    <!--
    <hr>
	<div class="form-group">
    	<label for="cur-img-width">Width</label><br />
    	<input type="text" class="form-control" id="cur-img-width" value="0" style="width:80px" /> px
  	</div>
  	<div class="form-group">
    	<label for="cur-img-height">Height</label><br />
    	<input type="text" class="form-control" id="cur-img-height" value="0" style="width:80px" /> px
  	</div>
  	-->
	<hr>
	<div class="form-group">
		<input type="checkbox" id="img-aspect-ratio" value="yes" data-label="Keep Aspect Ratio on Resizing" /> 
	</div>
	<hr>
	<div class="form-group">
		<label for="image-style">Image Style</label>
    	<select class="form-control" id="image-style">
    		<option value="none">None</option>
    		<option value="rounded">Rounded Edge</option>
			<option value="circle">Circle</option>
			<option value="thumbnail">Thumbnail</option>
		</select>
	</div>
	<hr>
	<div class="form-group">
    	<label for="image-link-type">Target Link Type</label>
    	<select class="form-control" id="image-link-type">
    		<option value="none">None</option>
    		<option value="url">URL</option>
			<option value="popup">PopUp</option>
			<!-- <option value="alert">Alert Message Box</option>-->
		</select>
  	</div>
  	
  	<div class="img-url-target-group">
  		<hr >
		<div class="form-group">
	    	<label for="img-link-url">Image Link URL </label><br />
	    	<input type="text" class="form-control" id="img-link-url" value="" placeholder="e.g. http://myothersite.com/" />
	  	</div>
	  	<div class="form-group">
			<input type="checkbox" id="img-link-new" value="yes" data-label="Open Link In New Tab/Window" /> 
		</div>
	</div>
	<p style="text-align:right" class="img-popup-target-group"><button type="button" class="btn btn-primary edit-target-popup" data-popup-from="image">Edit PopUp</button></p>
	
	<hr>
	<div class="form-group">
    	<div class="panel-col col-sm-8">
    		<label for="img-tm">Top Margin</label>
    		<div id="img-tm-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="img-tm" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="img-bm">Bottom Margin</label>
    		<div id="img-bm-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="img-bm" value="0" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
	<div class="form-group">
    	<label for="image-animation">Display Animation</label>
    	<select class="form-control" id="image-animation">
    		<option value="none" selected="selected">None</option>
			<option value="blind">Blind</option>
			<option value="bounce">Bounce</option>
			<option value="bounceInLeft">Bounce In Left</option>
			<option value="bounceInRight">Bounce In Right</option>
			<option value="bounceInUp">Bounce In Up</option>
			<option value="clip">Clip</option>
			<option value="drop">Drop</option>
			<option value="explode">Explode</option>
			<option value="fold">Fold</option>
			<option value="highlight">Highlight</option>
			<option value="puff">Puff</option>
			<option value="pulsate">Pulsate</option>
			<option value="scale">Scale</option>
			<option value="shake">Shake</option>
			<option value="slide">Slide</option>
			<option value="flipInX">Flip In X</option>
			<option value="flipInY">Flip In Y</option>
			<option value="rotateIn">Rotate</option>
			<option value="rotateInDownLeft">Rotate Down Left</option>
			<option value="rotateInDownRight">Rotate Down Right</option>
			<option value="rotateInUpLeft">Rotate Up Left</option>
			<option value="rotateInUpRight">Rotate Up Right</option>
			<option value="fadeInDownBig">Fade In Down</option>
			<option value="fadeInUpBig">Fade In Up</option>
			<option value="fadeInLeftBig">Fade In Left</option>
			<option value="fadeInRightBig">Fade In Right</option>
		</select>
		<p class="help-block">The selected animation above will be triggered only the first time this element is shown.</p>
  	</div>
  	<hr>
	<div class="form-group">
    	<label for="image-caption">Caption</label><br />
    	<input type="text" class="form-control" id="image-caption" placeholder="e.g. My Image Caption">
  	</div>
  	<hr>
  	<div class="form-group">
    	<label for="image-caption-color">Caption Font Color</label><br />
    	<input type="text" class="form-control ib2-pick-color" id="image-caption-color" value="#808080" data-default-color="#808080">
  	</div>
  	<hr>
  	<div class="form-group">
    	<label for="image-caption-background">Caption Background Color</label><br />
    	<input type="text" class="form-control ib2-pick-color" id="image-caption-background" value="#FFFFFF" data-default-color="#FFFFFF">
  	</div>
</div>
<?php
}

function ib2_menu_element_settings() {
	$menus = get_terms('nav_menu', array( 'hide_empty' => false ));
?>
	<div class="settings-menu editor-panel-content" style="display:none">
	<h3>Menu Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="menu-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#menu-settings-setup" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Menu Setup</a></li>
			<li><a href="#menu-settings-style" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Menu Style</a></li>
			<li><a href="#menu-settings-substyle" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Sub-Menu Style</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="menu-settings-setup" class="tab-settings-content">
			<div class="form-group">
		    	<label for="menu-list">Menu</label>
		    	<select class="form-control" id="menu-list">
		    		<option value="none"> -- Select menu -- </option>
					<?php 
					if ( $menus ) {
						foreach ( $menus as $menu ) {
							echo "<option value='{$menu->name}'>{$menu->name}</option>\n";
						}
					}
					?>
				</select>
				<p class="help-block">Select a menu that you want to display from the list above. If you haven't created a menu, you can create your first menu by <a href="<?php echo admin_url('nav-menus.php'); ?>" target="_blank">clicking here</a>. Once you're done, go back and reload this page (make sure you save any changes first).</p>
		  	</div>
		  	
		</div>
		
		<div id="menu-settings-style" class="tab-settings-content">
			<div class="form-group">
		    	<label for="menu-style">Menu Style</label>
		    	<select class="form-control" id="menu-style">
					<option value="plain" selected="selected">Plain</option>
					<option value="plain-pipe">Plain (/w pipe)</option>
					<option value="flat-box">Flat-Box</option>
					<option value="glossy">Glossy</option>
				</select>
		  	</div>
		  	<div class="non-plain-menu-group">
			  	<hr>
			  	<div class="form-group">
			  		<div class="panel-col col-sm-8">
			    		<label for="menu-corners">Rounded Corners</label>
			    		<div id="menu-corners-slider" class="ib2-slider"></div>
			    	</div>
			    	<div class="panel-col col-sm-4">
			    		<input type="text" class="form-control ib2-slider-val" id="menu-corners" value="0" style="width:40px"> px
			  		</div>
			  		<div class="clearfix"></div>
			  	</div>
		  	</div>
		  	<hr>

	  		<div class="form-group">
	    		<label for="menu-text-color">Text Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="menu-text-color" value="#428bca" data-default-color="#428bca">
	  		</div>
	  		<div class="non-plain-menu-group">
			  	<hr>
		  		<div class="form-group">
		    		<label for="menu-background">Background Color</label><br />
		    		<input type="text" class="form-control ib2-pick-color" id="menu-background" value="#E5E5E5" data-default-color="#E5E5E5">
		  		</div>
		  	</div>
	  		<hr>
	  		<div class="form-group">
	    		<label for="menu-hover-text-color">Hover Text Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="menu-hover-text-color" value="#2a6496" data-default-color="#2a6496">
	  		</div>
	  		<div class="non-plain-menu-group">
			  	<hr>
		  		<div class="form-group">
		    		<label for="menu-hover-background">Hover Background Color</label><br />
		    		<input type="text" class="form-control ib2-pick-color" id="menu-hover-background" value="#CCC" data-default-color="#CCC">
		  		</div>
		  	</div>
	  		<hr>
		</div>
		
		<div id="menu-settings-substyle" class="tab-settings-content">
	  		<div class="form-group">
	    		<label for="sub-menu-text-color">Text Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="sub-menu-text-color" value="#666" data-default-color="#666">
	  		</div>
	  		<hr>
	  		<div class="form-group">
	    		<label for="sub-menu-background">Background Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="sub-menu-background" value="#F5F5F5" data-default-color="#F5F5F5">
	  		</div>
	  		<hr>
	  		<div class="form-group">
	    		<label for="sub-menu-hover-text-color">Hover Text Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="sub-menu-hover-text-color" value="#666" data-default-color="#666">
	  		</div>
	  		<hr>
			<div class="form-group">
	    		<label for="sub-menu-hover-background">Hover Background Color</label><br />
	    		<input type="text" class="form-control ib2-pick-color" id="sub-menu-hover-background" value="#E5E5E5" data-default-color="#E5E5E5">
	  		</div>
	  		<hr>
		</div>
	</div>
</div>
<?php
}

function ib2_countdown_element_settings() {
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
<div class="settings-countdown editor-panel-content" style="display:none">
	<h3>Countdown Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="countdown-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#countdown-settings-setup" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Countdown Setup</a></li>
			<li><a href="#countdown-settings-design" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Design</a></li>
			<li class="countdown-non-evergreen" data-hide-when="countdown-type:evergreen"><a href="#countdown-settings-expiry" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Expiry Action</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="countdown-settings-setup" class="tab-settings-content">
			<p><strong>Important: </strong>Don't worry if the countdown didn't run, or if your changes does not take effect. It's totally normal. All countdown elements will run normally in live page mode.</p>
			<div class="form-group">
		    	<label for="countdown-type">Countdown Type</label>
		    	<select class="form-control" id="countdown-type">
		    		<option value="date">Exact Date &amp; Time</option>
					<option value="evergreen">Evergreen</option>
					<option value="cookie">Cookie Based</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group c-date-type">
			    <label for="countdown-date">Text Label</label><br />
			    <input type="text" class="form-control " id="countdown-date" value="2016-01-01" style="width:100px" />
			    <p class="help-block">Format: yyyy-mm-dd</p>
			</div>
		  	
		  	<div class="alert alert-info c-non-date-type" role="alert">The remaining time is...</div>
		  	
		  	<div class="form-group c-non-date-type">
		  		<div class="panel-col col-sm-8">
		  			<label for="countdown-day">Day</label>
		    		<div id="countdown-day-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val " id="countdown-day" value="1" style="width:40px">
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	
		  	<div class="alert alert-info c-date-type" role="alert">Time is in 24 hours format</div>
		  	
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		  			<label for="countdown-hour">Hour</label>
		    		<div id="countdown-hour-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val " id="countdown-hour" value="12" style="width:40px">
		  		</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<div class="panel-col col-sm-8">
		    		<label for="countdown-min">Minute</label>
		    		<div id="countdown-min-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val " id="countdown-min" value="0" style="width:40px">
		  		</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<div class="c-date-type">
			  	<hr>
			  	<div class="form-group">
			    	<label for="countdown-tz">Timezone</label>
			    	<?php ib2_timezone_select('countdown-tz'); ?>
			  	</div>
		  	</div>
  			<hr>
		</div>
		
		<div id="countdown-settings-design" class="tab-settings-content">
			<div class="form-group">
		    	<label for="countdown-style">Countdown Style</label>
		    	<select class="form-control " id="countdown-style">
		    		<option value="text">Normal Text</option>
					<option value="fancy-text">Fancy Text</option>
					<option value="flat-box">Flat Box</option>
					<option value="glossy-box">Glossy Box</option>
					<option value="circular">Circular</option>
				</select>
		  	</div>
		  	<div class="countdown-text-group">
		  		<div class="form-group">
				    <label for="countdown-text-before">Text Before (Optional)</label><br />
				    <input type="text" class="form-control " id="countdown-text-before" value="" placeholder="e.g. Remaining Time: " />
				</div>
				<div class="form-group">
				    <label for="countdown-text-after">Text After (Optional)</label><br />
				    <input type="text" class="form-control " id="countdown-text-after" value="" placeholder="e.g. Until Midnight" />
				</div>
		  	</div>
		  	<hr>
		  	<div class="countdown-box-style">
			  	<div class="form-group">
			    	<label for="countdown-color">Background Color</label><br />
			    	<input type="text" class="form-control ib2-pick-color" id="countdown-color" value="#76a8bb" data-default-color="#76a8bb">
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<label for="countdown-border-color">Border Color</label><br />
			    	<input type="text" class="form-control ib2-pick-color" id="countdown-border-color" value="#76a8bb" data-default-color="#76a8bb">
			  	</div>
		  		<hr>
		  		<div class="form-group">
			    	<label for="countdown-border-style">Border Style</label>
			    	<select class="form-control " id="countdown-border-style">
			    		<option value="solid">Solid</option>
						<option value="dashed">Dashed</option>
						<option value="dotted">Dotted</option>
						<option value="double">Double</option>
					</select>
			  	</div>
			  	<hr>
		  		<div class="form-group">
			  		<div class="panel-col col-sm-8">
						<label for="countdown-border-thick">Border Thickness</label>
						<div id="countdown-border-thick-slider" class="ib2-slider"></div>
					</div>
					<div class="panel-col col-sm-4">
						<input type="text" class="form-control ib2-slider-val" id="countdown-border-thick" value="1" style="width:40px"> px
					</div>
					<div class="clearfix"></div>
				</div>
		  	</div>
		  	
		  	<div class="form-group">
		    	<label for="countdown-text-font">Font Face</label>
		    	<?php
		    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
		    	ib2_font_selectors('countdown-text-font', $current_font);
		    	?>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="countdown-font-color">Font Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color " id="countdown-font-color" value="#CC0000" data-default-color="#CC0000">
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
					<label for="countdown-font-size">Font Size</label>
					<div id="countdown-font-size-slider" class="ib2-slider"></div>
				</div>
				<div class="panel-col col-sm-4">
					<input type="text" class="form-control ib2-slider-val" id="countdown-font-size" value="24" style="width:40px"> px
				</div>
				<div class="clearfix"></div>
			</div>
			<hr>
		  	<div class="form-group">
		    	<label for="countdown-text-shadow-color">Text Shadow Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color " id="countdown-text-shadow-color" value="#A7A7A7" data-default-color="#A7A7A7">
		  	</div>
		  	<hr>
		</div>
		
		<div id="countdown-settings-expiry" class="tab-settings-content">
			<p class="help-block">Please note that expiry action will NOT be executed in editor mode.</p>
			<div class="form-group">
		    	<label for="countdown-action-type">Action Type</label>
		    	<select class="form-control " id="countdown-action-type">
		    		<option value="none">Do Nothing</option>
					<option value="hide">Hide Countdown Timer</option>
					<option value="redirect">Redirect to URL</option>
					<option value="reveal">Reveal Hidden Content</option>
				</select>
		  	</div>
		  	
		  	<div class="expiry-action-redirect">
		  		<hr>
			  	<div class="form-group">
				    <label for="countdown-url">Redirect URL</label><br />
				    <input type="text" class="form-control " id="countdown-url" value="" placeholder="e.g. http://domain.com" />
				</div>
			</div>
			<hr>
		</div>
	</div>
</div>
<?php
}

function ib2_optin_element_settings() {
	$options = get_option('ib2_options');
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
	
	$gtw_token = get_option('ib2_gotowebinar');
	$webinars = array();
	$allow_webinar = false;
	if ( is_array($gtw_token) && !empty($gtw_token['organizer_key']) && !empty($gtw_token['access_token']) ) {
		$allow_webinar = true;
		$organizer_key = stripslashes($gtw_token['organizer_key']);
		$access_token = stripslashes($gtw_token['access_token']);
		$api_url = "https://api.citrixonline.com/G2W/rest/organizers/{$organizer_key}/upcomingWebinars";
		$result = wp_remote_get($api_url, array(
				'timeout' => 120,
				'httpversion' => '1.1',
				'sslverify' => false,
				'headers' => array(
					'Authorization' => 'OAuth oauth_token=' . $access_token
				)
		    )
		);
			
		if ( isset($result['response']['code']) ) {
			switch ( $result['response']['code'] ) {
				case 200:
				case 201:
				case 204:
					if ( version_compare(PHP_VERSION, '5.4.0', '<') )
						$_webinars = json_decode($result['body'], false, 512);
					else
						$_webinars = json_decode($result['body'], false, 512, JSON_BIGINT_AS_STRING);
						
					foreach ( $_webinars as $wbr ) {
						if ( isset($wbr->webinarKey) && isset($wbr->subject) )
							$webinars[$wbr->webinarKey] = urldecode($wbr->subject);
					}
					break;
			}
		}
	}

	$webjam_key = ( isset($options['webinarjam']) ? trim($options['webinarjam']) : '');
	$webjams = array();
	if ( !empty($webjam_key) && function_exists('curl_init') ) {
		$allow_webinar = true;
		$webjam_url = 'https://app.webinarjam.com/api/v2/webinars';
		$cert = IB2_PATH . 'inc/certs/cacert.pem';
		$fields = 'api_key=' . $webjam_key;
		$error = false;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $webjam_url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "/cacert.pem");
		curl_setopt($ch, CURLOPT_TIMEOUT, 90);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

		$result = curl_exec($ch);

		if ( curl_errno($ch) ) {
			curl_close($ch);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $webjam_url);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($ch, CURLOPT_TIMEOUT, 90);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

			$result = curl_exec($ch);

			if ( curl_errno($ch) ) {
			    $error = true;
			}
		}

		if ( !$error ) {
			$json = json_decode($result);
			$webjams = ( isset($json->webinars) ? $json->webinars : array()); 
		}

	}
?>
<div class="settings-optin editor-panel-content" style="display:none">
	<h3>Opt-In Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="optin-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#optin-settings-code" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Opt-In Form HTML Code</a></li>
			<li><a href="#optin-settings-fields" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Opt-In Form Fields</a></li>
			<li><a href="#optin-settings-button" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Opt-In Form Button</a></li>
			<li><a href="#optin-settings-webinar" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Webinar Integration</a></li>
			<li><a href="#optin-settings-facebook" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Facebook Opt-In</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="optin-settings-code" class="tab-settings-content">
			<div class="form-group optin-unprocess-group">
		    	<p>Please insert the HTML version of the opt-in form code that you can obtain from your autoresponder/email marketing service provider (e.g. Aweber, GetResponse, etc).</p>
		    	<hr>
		    	<p><strong>How To Obtain HTML Code From GetResponse:</strong><br />Instead of using the new form builder, please create the form using the "Legacy" Form builder so that you can still grab the HTML opt-in code. You can access this in your GR account by navigate to <code>Forms >> Manage Forms Legacy</code></p>
		  		<hr>
		    	<textarea class="form-control" id="optin-html-code" rows="4"></textarea>
		    	
		  	</div>
		  	<div class="alert alert-success optin-process-msg" style="display:none">Opt-In form code has been processed.</div>
		  	<p class="help-block optin-unprocess-group">Once you have inserted the <strong>HTML version</strong> of your opt-in form code, please click the process button below so IB can populate and display the actual form fields.</p>
		  	<p id="optin-process" style="text-align:right"><button type="button" class="btn btn-primary btn-sm" id="optin-process-btn" data-loading-text="Processing...">Process</button></p>
		  	<p id="change-optin" style="text-align:right; display:none"><button type="button" class="btn btn-danger btn-sm change-optin-code">Change Opt-In Code</button></p>
		</div>
		
		<div id="optin-settings-webinar" class="tab-settings-content">
			<?php if ( $allow_webinar ) { ?>
				<div class="form-group">
			    	<label for="optin-webinar">Integrate Webinar</label>
			    	<select class="form-control" id="optin-webinar">
			    		<option value=""> -- No Integration -- </option>
			    		<?php if ( !empty($webinars) ) {
			    			echo '<optgroup label="GoToWebinar">';
			    			foreach ( $webinars as $k => $v ) {
			    				echo "<option value='{$k}'>{$v}</option>";
			    			}
			    			echo '</optgroup>';
			    		} 
			    		if ( count($webjams) > 0 ) {
			    			echo '<optgroup label="WebinarJam">';
			    			foreach ( $webjams as $wj ) {
			    				echo "<option value='wj-{$wj->webinar_id}'>{$wj->name}</option>";
			    			}
			    			echo '</optgroup>';
			    		} ?>
					</select>
					<p class="help-block"><strong>Important:</strong> Please process your opt-in form or autoresponder code first before you integrate this opt-in form with GTW. IB 2.0 will reset your webinar integration if you integrate webinar first and then opt-in form code.</p>
			  	</div>
			  	<div class="form-group webinar-signup-group" style="display:none">
				  	<hr>
				  	<div class="form-group">
				    	<label for="optin-webinar-redirect">Webinar Thank You Page URL</label><br />
				    	<input type="text" class="form-control" id="optin-webinar-redirect" placeholder="e.g. http://www.mydomain.com/webinarthankyoupage">
				  		<p class="help-block"><strong>Important:</strong> Visitors who registered for the webinar will be redirected here afterwards. However, this webinar thank you page url will be used ONLY if you did NOT integrate this opt-in form with an autoresponder (e.g. Aweber). This setting will be ignored if you already integrated this opt-in form with an autoresponder.</p>
				  	</div>
				</div>
			<?php } else { ?>
				<div class"form-group">
					<p>In order to integrate IB 2.0 Opt-In Form with webinar, you have to connect IB 2.0 with either GoToWebinar or WebinarJam first. You can do so by <a href="<?php echo admin_url('admin.php?page=ib2-settings'); ?>" target="_blank">clicking here</a>.</p>
					<p>Once your GoToWebinar or WebinarJam account has been connected to IB 2.0, please save your work and reload this editor.</p>
				</div>
			<?php } ?>
		</div>
		
		<div id="optin-settings-fields" class="tab-settings-content">
			<div class="form-group">
		    	<label for="optin-form-mode">Form Mode</label>
		    	<select class="form-control" id="optin-form-mode">
		    		<option value="vertical">Vertical</option>
		    		<option value="horizontal">Horizontal</option>
					<option value="semi-horizontal">Semi-Horizontal</option>
				</select>
		  	</div>
		  	<hr>
			<div class="form-group">
		    	<label for="field-size">Field Size</label>
		    	<select class="form-control" id="field-size">
		    		<option value="normal">Normal</option>
					<option value="small">Small</option>
					<option value="big">Big</option>
					<option value="normallong">Normal (longer)</option>
					<option value="smalllong">Small (longer)</option>
					<option value="biglong">Big (longer)</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="field-style">Field Style</label>
		    	<select class="form-control" id="field-style">
		    		<option value="field-normal">Style #1</option>
		    		<option value="field-normal-thick">Style #2</option>
					<option value="field-sharp">Style #3</option>
					<option value="field-sharp-thick">Style #4</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="field-background-color">Field Background Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="field-background-color" value="#FFFFFF" data-default-color="#FFFFFF">
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="field-border-color">Field Border Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="field-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
		  	</div>
		  	
			<hr />
			<p class="help-block">Click the button below to edit the fields properties and/or to sort the fields position.</p>
			<button id="optin-manage-fields" type="button" class="btn btn-primary btn-sm">Manage Fields</button>
		</div>
		
		<div id="optin-settings-button" class="tab-settings-content">
			<div class="form-group">
		    	<label for="optin-button-type">Button type</label>
		    	<select class="form-control" id="optin-button-type">
		    		<option value="css" selected="selected">CSS Button</option>
		    		<option value="image">Image Button</option>
				</select>
		  	</div>
		  	
		  	<hr>
		  	
		  	<div class="form-group image-button-group">
		  		<label for="image-button-url">Button Image</label>
		  		<input type="text" class="form-control" id="image-button-url" value="" placeholder="e.g. http://mydomain.com/button.png" />
		      	<p class="help-block">Enter an image URL into the field above, or click the "Action" button below to use a pre-made button or to upload your own button image.</p>
		      	
		      	<div id="button-image-prev"></div>
		        <p style="margin-top:10px">
		        	<div class="btn-group dropup">
		        		<button id="button-image-chooser" class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></button>
						<ul class="dropdown-menu" role="menu">
							<li><a href="#" id="button-image-premade" class="open-panel" data-settings="premade-button" data-element="optin">Pre-Made Button</a></li>
							<li><a href="#" id="button-image-upload">Upload Image</a></li>
						</ul>
		        	</div>
		        	<span id="button-img-rmv" style="display:none"><a href='#' class="btn btn-danger btn-sm remove-button-img">Remove</a></span>
		        </p>
		        <hr>
		    </div>
		  	
		  	<div class="css-button-group">
				<p class="help-block">Click the button below to edit the style of the opt-in form button.</p>
				<button id="optin-edit-submit" type="button" class="btn btn-primary btn-sm">Edit Button</button>
			</div>
		</div>
		
		<div id="optin-settings-facebook" class="tab-settings-content">
			<div class="form-group">
				<input type="checkbox" id="facebook-opt-enable" value="yes" data-label="Enable Facebook Opt-In" />
			</div>
			<hr>
			<div class="facebook-opt-group">
				<div class="form-group">
			    	<label for="facebook-optin-label">Facebook Opt-In Button Label</label><br />
			    	<input type="text" class="form-control" id="facebook-optin-label" value="Subscribe with Facebook" placeholder="e.g. Subscribe with Facebook" />
				</div>
				<hr>
				<div class="form-group">
					<input type="checkbox" id="facebook-opt-only" value="yes" data-label="Show Facebook Opt-In Only" />
				</div>
				<hr>
				<div class="form-group">
			    	<label for="facebook-optin-text">Facebook Opt-In Pre-Text</label><br />
			    	<input type="text" class="form-control" id="facebook-optin-text" value="Have a Facebook account?" placeholder="e.g. Have a Facebook account?" />
				</div>
				<hr>
				<div class="form-group">
			    	<label for="facebook-optin-font">Pre-Text Font Face</label>
			    	<?php
			    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
			    	ib2_font_selectors('facebook-optin-font', $current_font);
			    	?>	
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<label for="facebook-optin-color">Pre-Text Color</label><br />
			    	<input type="text" class="form-control ib2-pick-color" id="facebook-optin-color" value="#3a3a3a" data-default-color="#3a3a3a">
			  	</div>
		  		<hr>
			  	<div class="form-group">
			  		<div class="panel-col col-sm-8">
						<label for="facebook-optin-size">Pre-Text Font Size</label>
						<div id="facebook-optin-size-slider" class="ib2-slider"></div>
					</div>
					<div class="panel-col col-sm-4">
						<input type="text" class="form-control ib2-slider-val" id="facebook-optin-size" value="14" style="width:40px"> px
					</div>
					<div class="clearfix"></div>
				</div>
				<hr>
			</div>
		</div>
	</div>
</div>
<?php

}
function ib2_button_element_settings() {
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
<div class="settings-button editor-panel-content" style="display:none">
	<h3>Button Settings <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="button-settings-tab" class="settings-tab">
		<ul>
			<li><a href="#button-settings-text" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Button Text</a></li>
			<li><a href="#button-settings-style" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Button Style</a></li>
			<li><a href="#button-settings-normal" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Normal State Colors</a></li>
			<li><a href="#button-settings-hover" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Hover State Colors</a></li>
			<li><a href="#button-settings-icon" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Icon</a></li>
			<li><a href="#button-settings-corners" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Rounded Corners</a></li>
			<li class="btn-url-setting-group button-el-only"><a href="#button-settings-target" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Target Link</a></li>
			<li><a href="#button-settings-effect" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Display Effect</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="button-settings-text" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-text">Text Label</label><br />
		    	<input type="text" class="form-control" id="button-text" value="Click Here" placeholder="e.g. Click Here" />
		  	</div>
		  	<hr>
			<div class="form-group">
		    	<label for="button-text-font">Font Face</label>
		    	<?php
		    	$current_font = ( is_array($meta) && isset($meta['font_face']) ) ? stripslashes($meta['font_face']) : "'Open Sans',sans-serif"; 
		    	ib2_font_selectors('button-text-font', $current_font);
		    	?>
		  	</div>
		  	
		  	<hr />
			<div class="form-group" data-toggle="buttons">
				<label>Font Style</label><br />
				<div class="btn-group">
					<label class="btn btn-default button-bold-btn">
    					<input type="checkbox" id="button-style-bold" style="display:none"><i class="fa fa-bold"></i>
  					</label>
					<label class="btn btn-default button-bold-btn">
    					<input type="checkbox" id="button-style-italic" style="display:none"><i class="fa fa-italic"></i>
  					</label>
  					<label class="btn btn-default button-bold-btn">
    					<input type="checkbox" id="button-style-underline" style="display:none"><i class="fa fa-underline"></i>
  					</label>
				</div>
			</div>
			<hr />
			
			<div class="form-group">
				<div class="panel-col col-sm-8">
					<label for="button-text-size">Font Size</label>
					<div id="button-text-size-slider" class="ib2-slider"></div>
				</div>
				<div class="panel-col col-sm-4">
					<input type="text" class="form-control ib2-slider-val" id="button-text-size" value="14" style="width:40px"> px
				</div>
				<div class="clearfix"></div>
			</div>
			<hr>
			
			<div class="form-group">
				<div class="panel-col col-sm-8">
					<label for="button-text-spacing">Letter Spacing</label>
					<div id="button-text-spacing-slider" class="ib2-slider"></div>
				</div>
				<div class="panel-col col-sm-4">
					<input type="text" class="form-control ib2-slider-val" id="button-text-spacing" value="0" style="width:40px"> px
				</div>
				<div class="clearfix"></div>
			</div>
			<hr>
		</div>
		
		<div id="button-settings-style" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-style">Button Style</label>
		    	<select class="form-control" id="button-style">
		    		<option value="flat">Flat</option>
					<option value="glossy">Glossy</option>
				</select>
		  	</div>
		  	<hr>
		</div>
		
		<div id="button-settings-normal" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-color">Button Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-color" value="#428BCA" data-default-color="#428BCA">
		  	</div>
			<hr>
			<div class="form-group">
		    	<label for="button-text-color">Text Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-text-color" value="#FFFFFF" data-default-color="#FFFFFF">
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="button-tshadow-color">Text Shadow Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-tshadow-color" value="#FFFFFF" data-default-color="#FFFFFF">
		  	</div>
		  	<hr>
		</div>
		
		<div id="button-settings-hover" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-hover-color">Button Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-hover-color" value="#428BCA" data-default-color="#428BCA">
		  	</div>
			<hr>
			<div class="form-group">
		    	<label for="button-text-hover-color">Text Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-text-hover-color" value="#FFFFFF" data-default-color="#FFFFFF">
		  	</div>
			<hr>	
			<div class="form-group">
		    	<label for="button-tshadow-hover-color">Text Shadow Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="button-tshadow-hover-color" value="#FFFFFF" data-default-color="#FFFFFF">
		  	</div>
		  	<hr>
		</div>
		
		<div id="button-settings-icon" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-icon-position">Icon Position</label>
		    	<select class="form-control" id="button-icon-position">
		    		<option value="before">Before Text</option>
					<option value="after">After Text</option>
				</select>
		  	</div>
		  	<hr>
			<p>Simply click on one of the icons below to add:</p>
			<hr>
			<?php ib2_icon_html('ib2-button-icon'); ?>
			<hr>
		</div>
		
		<div id="button-settings-corners" class="tab-settings-content">
			<div class="form-group">
				<div class="panel-col col-sm-8">
					<label for="button-corners">Rounded Corners</label>
					<div id="button-corners-slider" class="ib2-slider"></div>
				</div>
				<div class="panel-col col-sm-4">
					<input type="text" class="form-control ib2-slider-val" id="button-corners" value="5" style="width:40px"> px
				</div>
				<div class="clearfix"></div>
			</div>
			<hr>
		</div>
		
		<div id="button-settings-target" class="tab-settings-content">
			<div class="form-group">
		    	<label for="target-link-type">Target Link Type</label>
		    	<select class="form-control" id="target-link-type">
		    		<option value="url">URL</option>
					<option value="popup">PopUp</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="url-target-group">
				<div class="form-group">
			    	<label for="button-link-url">Button Link URL</label><br />
			    	<input type="text" class="form-control" id="button-link-url" value="" placeholder="e.g. http://mysite.com/page" />
			  	</div>
			  	<hr>
			  	<div class="form-group">
					<input type="checkbox" id="button-link-new" value="yes" data-label="Open Link In New Tab/Window" /> 
				</div>
				<hr>
			</div>
			<p style="text-align:right" class="popup-target-group"><button type="button" class="btn btn-primary edit-target-popup" data-popup-from="button">Edit PopUp</button></p>
		</div>
		
		<div id="button-settings-effect" class="tab-settings-content">
			<div class="form-group">
		    	<label for="button-animation">Display Animation</label>
		    	<select class="form-control" id="button-animation">
		    		<option value="none" selected="selected">None</option>
					<option value="blind">Blind</option>
					<option value="bounce">Bounce</option>
					<option value="bounceInLeft">Bounce In Left</option>
					<option value="bounceInRight">Bounce In Right</option>
					<option value="bounceInUp">Bounce In Up</option>
					<option value="clip">Clip</option>
					<option value="drop">Drop</option>
					<option value="explode">Explode</option>
					<option value="fold">Fold</option>
					<option value="highlight">Highlight</option>
					<option value="puff">Puff</option>
					<option value="pulsate">Pulsate</option>
					<option value="scale">Scale</option>
					<option value="shake">Shake</option>
					<option value="slide">Slide</option>
					<option value="flipInX">Flip In X</option>
					<option value="flipInY">Flip In Y</option>
					<option value="rotateIn">Rotate</option>
					<option value="rotateInDownLeft">Rotate Down Left</option>
					<option value="rotateInDownRight">Rotate Down Right</option>
					<option value="rotateInUpLeft">Rotate Up Left</option>
					<option value="rotateInUpRight">Rotate Up Right</option>
					<option value="fadeInDownBig">Fade In Down</option>
					<option value="fadeInUpBig">Fade In Up</option>
					<option value="fadeInLeftBig">Fade In Left</option>
					<option value="fadeInRightBig">Fade In Right</option>
				</select>
				<p class="help-block">The selected animation above will be triggered only the first time this element is shown.</p>
		  	</div>
		</div>
		
	</div>
</div>
<?php
}

function ib2_hotspot_settings() {
?>
<div class="settings-hotspot editor-panel-content" style="display:none">
	<h3><span id="hotspot-settings-title">Hotspot Settings</span> <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div class="form-group">
    	<label for="hotspot-type">Hotspot Type</label>
    	<select class="form-control" id="hotspot-type">
			<option value="tooltip" selected="selected">Tooltip</option>
			<option value="popover">Pop-Over</option>
			<option value="popup">Pop Up</option>
		</select>
  	</div>
  	<hr>
  	<div class="tooltip-prop hotspot-type-prop">
  		<div class="form-group">
	    	<label for="tooltip-text">Tooltip Text</label><br />
	    	<input type="text" class="form-control" id="tooltip-text" placeholder="e.g. This is a tooltip">
	  	</div>
	  	<hr>
	</div>
	<div class="popover-prop hotspot-type-prop">
  		<div class="form-group">
	    	<label for="popover-title">Pop-Over Title</label><br />
	    	<input type="text" class="form-control" id="popover-title" placeholder="e.g. My Pop-Over">
	  	</div>
	  	<hr>
	  	<div class="form-group">
	    	<label for="popover-text">Pop-Over Content</label><br />
	    	<input type="text" class="form-control" id="popover-text" placeholder="e.g. My Pop-Over">
	  	</div>
	  	<hr>
	</div>
	<div class="popup-prop hotspot-type-prop">
		<p style="text-align:right"><button type="button" class="btn btn-primary edit-target-popup" data-popup-from="hotspot">Edit PopUp</button></p>
		<hr>
	</div>
	<div class="form-group">
    	<label for="hotspot-trigger">Display Trigger</label>
    	<select class="form-control" id="hotspot-trigger">
			<option value="hover" selected="selected">On-Hover</option>
			<option value="click">On-Click</option>
		</select>
  	</div>
  	<hr>
  	<div class="form-group">
		<input type="checkbox" id="hotspot-blink" value="yes" data-label="Enable Blinking" /> 
	</div>
	<hr>
	<div class="form-group">
    	<div class="panel-col col-sm-8">
    		<label for="hotspot-width">Width</label>
    		<div id="hotspot-width-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hotspot-width" value="30" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<div class="form-group">
    	<div class="panel-col col-sm-8">
    		<label for="hotspot-height">Height</label>
    		<div id="hotspot-height-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hotspot-height" value="30" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
	<div class="form-group">
		<label for="hotspot-color">Background Color</label><br />
		<input type="text" class="form-control ib2-pick-color" id="hotspot-color" value="#212121" data-default-color="#212121">
	</div>
	<hr>
	<div class="form-group">
    	<div class="panel-col col-sm-8">
    		<label for="hotspot-opac">Background Opacity</label>
    		<div id="hotspot-opac-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hotspot-opac" value="0.8" style="width:40px">
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<div class="form-group">
    	<label for="hotspot-border-color">Border Color</label><br />
    	<input type="text" class="form-control ib2-pick-color" id="hotspot-border-color" value="#009900" data-default-color="#009900">
  	</div>
  	<hr>
	<div class="form-group">
    	<label for="hotspot-border-type">Border Style</label>
    	<select class="form-control" id="hotspot-border-type">
    		<option value="none">None</option>
			<option value="solid" selected="selected">Solid</option>
			<option value="double">Double Line</option>
		</select>
  	</div>
  	<hr>
  	<div class="form-group">
  		<div class="panel-col col-sm-8">
    		<label for="hotspot-border-thick">Border Thickness</label>
    		<div id="hotspot-border-thick-slider" class="ib2-slider"></div>
    	</div>
    	<div class="panel-col col-sm-4">
    		<input type="text" class="form-control ib2-slider-val" id="hotspot-border-thick" value="1" style="width:40px"> px
    	</div>
    	<div class="clearfix"></div>
  	</div>
  	<hr>
  	<p class="text-center">
  		<button class="btn btn-danger btn-lg delete-hotspot" type="button">Delete This HotSpot</button>
  	</p>
</div>
<?php
}	

function ib2_box_element_settings() {
?>
<div class="settings-box editor-panel-content" style="display:none">
	<h3><span id="box-settings-title">Box Settings</span> <button type="button" class="close hide-side-panel"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></h3>
	<div id="box-settings-tab" class="settings-tab">
		<ul class="box-settings-menu">
			<li><a href="#box-settings-margin" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Margin &amp; Padding</a></li>
			<li><a href="#box-settings-background" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Color</a></li>
			<li><a href="#box-settings-bgimg" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Image</a></li>
			<li><a href="#box-settings-bgvid" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Background Video</a></li>
			<li><a href="#box-settings-borders" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Borders</a></li>
			<li><a href="#box-settings-corners" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Rounded Corners</a></li>
			<li><a href="#box-settings-opacity" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Opacity &amp; Shadow</a></li>
			<li class="not-wbox not-popup"><a href="#box-settings-delay" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Display Delay</a></li>
			<li class="not-wbox"><a href="#box-settings-effect" title="Click to open settings" class="open-tab-settings"><i class="fa fa-chevron-right"></i>&nbsp;&nbsp;Effect</a></li>
		</ul>
	</div>
	
	<div class="tab-settings-main">
		<div id="box-settings-margin" class="tab-settings-content">
		  	<div class="form-group">
		    	<div class="panel-col col-sm-8">
		    		<label for="box-tm">Top Margin</label>
		    		<div id="box-tm-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-tm" value="0" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-bm">Bottom Margin</label>
		    		<div id="box-bm-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-bm" value="0" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-vp">Top Padding</label>
		    		<div id="box-vp-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-vp" value="15" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-vp2">Bottom Padding</label>
		    		<div id="box-vp2-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-vp2" value="15" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-hp">Left Padding</label>
		    		<div id="box-hp-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-hp" value="35" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-hp2">Right Padding</label>
		    		<div id="box-hp2-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-hp2" value="35" style="width:40px"> px
		    	</div>
		    	<div class="clearfix"></div>
		  	</div>
		</div>
		
		<div id="box-settings-background" class="tab-settings-content">
			<div class="form-group">
				<input type="checkbox" id="box-transparent" value="yes" data-label="Transparent Background Color" />
			</div>
			<hr>
			<div class="box-background-form">
				<div class="form-group">
			    	<label for="box-color">Background Color</label><br />
			    	<input type="text" class="form-control ib2-pick-color" id="box-color" value="#F5F5F5" data-default-color="#F5F5F5">
			  	</div>
			  	<hr>
			  	<div class="form-group">
					<input type="checkbox" id="box-color-glossy" value="yes" data-label="Enable Glossy Effect" />
				</div>
				<hr>
		  	</div>
		</div>
		
		<div id="box-settings-bgimg" class="tab-settings-content">
		  	<div class="form-group">
		  		<label for="box-image">Background Image</label>
		  		<input type="text" class="form-control" id="box-bg-url" value="" placeholder="e.g. http://mydomain.com/image.gif" />
		      	<p class="help-block">Enter an image URL into the field above, or click the "Action" button below to use a pre-made pattern/background, to search the web for an image or to upload your own image.</p>
		      	
		      	<div id="box-image-prev"></div>
		        <div style="margin-top:10px">
		        	<div class="btn-group">
		        		<button id="box-image-chooser" class="btn btn-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">Action <span class="caret"></span></button>
  						<ul class="dropdown-menu" role="menu">
    						<li><a href="#" id="box-image-premade" class="open-panel" data-settings="premade" data-element="box">Pre-Made Pattern</a></li>
    						<li class="divider"></li>
    						<li><a href="#" class="background-image-search" data-type="background" data-element="box">Search Image</a></li>
    						<li><a href="#" id="box-image-upload">Upload Image</a></li>
  						</ul>
		        	</div>
		        	<span id="box-img-rmv" style="display:none"><a href='#' class="btn btn-warning btn-sm open-image-editor" data-element="box" data-img-type="background">Edit</a>&nbsp;&nbsp;<a href='#' class="btn btn-danger btn-sm remove-box-img">Remove</a></span>
		        </div>
		    </div>
		    <hr>
		    <div class="form-group">
		    	<label for="box-bgrepeat">Background Image Style</label>
		    	<select class="form-control" id="box-bgrepeat">
					<option value="no-repeat">Normal</option>
					<option value="repeat" selected="selected">Tile</option>
					<option value="repeat-x">Tile Horizontally</option>
					<option value="repeat-y">Tile Vertically</option>
					<option value="cover">Stretch</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="box-bgpos">Background Image Position</label>
		    	<select class="form-control" id="box-bgpos">
					<option value="left top" selected="selected">Left Top</option>
					<option value="left center">Left Center</option>
					<option value="left bottom">Left Bottom</option>
					<option value="right top">Right Top</option>
					<option value="right center">Right Center</option>
					<option value="right bottom">Right Bottom</option>
					<option value="center top">Center Top</option>
					<option value="center center">Center Center</option>
					<option value="center bottom">Center Bottom</option>
				</select>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<label for="box-bgattach">Background Image Attachment</label>
		    	<select class="form-control" id="box-bgattach">
					<option value="scroll" selected="selected">Scroll</option>
					<option value="fixed">Fixed</option>
				</select>
		  	</div>
		  	<hr>
		</div>
		
		<div id="box-settings-bgvid" class="tab-settings-content">
			<div class="form-group">
				<input type="checkbox" id="box-bgvid" value="yes" data-label="Enable Background Video" />
			</div>
			<div id="bgvideo-set">
				<hr>
				<p class="help-block">Convert your video into 3 different files (mp4, ogv, webm) so that the background video can be displayed in various web browsers. <strong>Important:</strong> The background video feature is NOT yet supported on smartphones.</p>
				<hr>
				<div class="form-group"> 
		    		<label for="bgvideo-mp4">MP4 Video URL</label><br />
		    		<input type="text" class="form-control" id="bgvideo-mp4" value="" placeholder="e.g. http://mysite.com/bgvideo.mp4" />
		  		</div>
			  	<hr>
			  	<div class="form-group">
			    	<label for="bgvideo-ogg">OGG Video URL</label><br />
			    	<input type="text" class="form-control" id="bgvideo-ogg" value="" placeholder="e.g. http://mysite.com/bgvideo.ogv" />
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<label for="bgvideo-webm">WebM Video URL</label><br />
			    	<input type="text" class="form-control" id="bgvideo-webm" value="" placeholder="e.g. http://mysite.com/bgvideo.webm" />
			  	</div>
			</div>
		</div>

		<div id="box-settings-borders" class="tab-settings-content">
			<div id="border-single-set">
				<div class="form-group">
			    	<label for="box-border-color">Border Color</label><br />
			    	<input type="text" class="form-control ib2-pick-color" id="box-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
			  	</div>
			  	<hr>
				<div class="form-group">
			    	<label for="box-border-type">Style</label>
			    	<select class="form-control" id="box-border-type">
			    		<option value="none">None</option>
						<option value="solid" selected="selected">Solid</option>
						<option value="dashed">Dashed</option>
						<option value="dotted">Dotted</option>
						<option value="double">Double Line</option>
					</select>
			  	</div>
			  	<hr>
			  	<div class="form-group">
			  		<div class="panel-col col-sm-8">
			    		<label for="box-border-thick">Thickness</label>
			    		<div id="box-border-thick-slider" class="ib2-slider"></div>
			    	</div>
			    	<div class="panel-col col-sm-4">
			    		<input type="text" class="form-control ib2-slider-val" id="box-border-thick" value="1" style="width:40px"> px
			    	</div>
			    	<div class="clearfix"></div>
			  	</div>
		  	</div>
		  
		  	<div id="border-multi-set" style="display:none">
		  		<div id="border-accordion">
					<h3>Left Border</h3>
					<div>
				  		<div class="form-group">
					    	<label for="box-left-border-color">Left Border Color</label><br />
					    	<input type="text" class="form-control ib2-pick-color" id="box-left-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
					  	</div>
					  	<hr>
						<div class="form-group">
					    	<label for="box-left-border-type">Left Border Style</label>
					    	<select class="form-control" id="box-left-border-type">
					    		<option value="none">None</option>
								<option value="solid" selected="selected">Solid</option>
								<option value="dashed">Dashed</option>
								<option value="dotted">Dotted</option>
								<option value="double">Double Line</option>
							</select>
					  	</div>
					  	<hr>
					  	<div class="form-group">
					  		<div class="panel-col col-sm-8">
					    		<label for="box-left-border-thick">Left Border Thickness</label>
					    		<div id="box-left-border-thick-slider" class="ib2-slider"></div>
					    	</div>
			    			<div class="panel-col col-sm-4">
					    		<input type="text" class="form-control ib2-slider-val" id="box-left-border-thick" value="1" style="width:40px"> px
					    	</div>
			    			<div class="clearfix"></div>
					  	</div>
			  		</div>
			  		
			  		<h3>Right Border</h3>
					<div>
					  	<div class="form-group">
					    	<label for="box-right-border-color">Right Border Color</label><br />
					    	<input type="text" class="form-control ib2-pick-color" id="box-right-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
					  	</div>
					  	<hr>
						<div class="form-group">
					    	<label for="box-right-border-type">Right Border Style</label>
					    	<select class="form-control" id="box-right-border-type">
					    		<option value="none">None</option>
								<option value="solid" selected="selected">Solid</option>
								<option value="dashed">Dashed</option>
								<option value="dotted">Dotted</option>
								<option value="double">Double Line</option>
							</select>
					  	</div>
					  	<hr>
					  	<div class="form-group">
					  		<div class="panel-col col-sm-8">
					    		<label for="box-right-border-thick">Right Border Thickness</label>
					    		<div id="box-right-border-thick-slider" class="ib2-slider"></div>
					    	</div>
			    			<div class="panel-col col-sm-4">
					    		<input type="text" class="form-control ib2-slider-val" id="box-right-border-thick" value="1" style="width:40px"> px
					  		</div>
			    			<div class="clearfix"></div>
					  	</div>
			  		</div>
			  		
			  		<h3>Top Border</h3>
					<div>
					  	<div class="form-group">
					    	<label for="box-top-border-color">Top Border Color</label><br />
					    	<input type="text" class="form-control ib2-pick-color" id="box-top-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
					  	</div>
					  	<hr>
						<div class="form-group">
					    	<label for="box-top-border-type">Top Border Style</label>
					    	<select class="form-control" id="box-top-border-type">
					    		<option value="none">None</option>
								<option value="solid" selected="selected">Solid</option>
								<option value="dashed">Dashed</option>
								<option value="dotted">Dotted</option>
								<option value="double">Double Line</option>
							</select>
					  	</div>
					  	<hr>
					  	<div class="form-group">
					  		<div class="panel-col col-sm-8">
					    		<label for="box-top-border-thick">Top Border Thickness</label>
					    		<div id="box-top-border-thick-slider" class="ib2-slider"></div>
					    	</div>
			    			<div class="panel-col col-sm-4">
					    		<input type="text" class="form-control ib2-slider-val" id="box-top-border-thick" value="1" style="width:40px"> px
					  		</div>
			    			<div class="clearfix"></div>
					  	</div>
			  		</div>
			  		<h3>Bottom Border</h3>
					<div>
						
					  	<div class="form-group">
					    	<label for="box-bottom-border-color">Bottom Border Color</label><br />
					    	<input type="text" class="form-control ib2-pick-color" id="box-bottom-border-color" value="#CCCCCC" data-default-color="#CCCCCC">
					  	</div>
					  	<hr>
						<div class="form-group">
					    	<label for="box-bottom-border-type">Bottom Border Style</label>
					    	<select class="form-control" id="box-bottom-border-type">
					    		<option value="none">None</option>
								<option value="solid" selected="selected">Solid</option>
								<option value="dashed">Dashed</option>
								<option value="dotted">Dotted</option>
								<option value="double">Double Line</option>
							</select>
					  	</div>
					  	<hr>
					  	<div class="form-group">
					  		<div class="panel-col col-sm-8">
					    		<label for="box-bottom-border-thick">Bottom Border Thickness</label>
					    		<div id="box-bottom-border-thick-slider" class="ib2-slider"></div>
					    	</div>
			    			<div class="panel-col col-sm-4">
					    		<input type="text" class="form-control ib2-slider-val" id="box-bottom-border-thick" value="1" style="width:40px"> px
					  		</div>
			    			<div class="clearfix"></div>
					  	</div>
					 </div>
				</div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
				<input type="checkbox" id="indiv-box-border" value="yes" data-label="Setup Border Individually" /> 
			</div>
		  	<hr>
		</div>
		
		<div id="box-settings-corners" class="tab-settings-content">
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
			    	<label for="box-corners">Top Corners</label>
			    	<div id="box-corners-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-corners" value="0" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		    	<div class="panel-col col-sm-8">
			    	<label for="box-corners-bot">Bottom Corners</label>
			    	<div id="box-corners-bot-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-corners-bot" value="0" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		</div>
		
		<div id="box-settings-opacity" class="tab-settings-content">
			<div class="form-group">
		    	<label for="box-shadow-type">Shadow Type</label>
		    	<select class="form-control" id="box-shadow-type">
		    		<option value="none" selected="selected">None</option>
					<option value="outset">Outset</option>
					<option value="inset">Inset</option>
				</select>
		  	</div>
		  	<hr>
			<div class="form-group">
		    	<label for="box-shadow-color">Shadow Color</label><br />
		    	<input type="text" class="form-control ib2-pick-color" id="box-shadow-color" value="#CCCCCC" data-default-color="#CCCCCC">
		  	</div>
		  	<hr>
			<div class="form-group">
				<div class="panel-col col-sm-8">
		    		<label for="box-hshadow">Horizontal Shadow</label>
		    		<div id="box-hshadow-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-hshadow" value="0" style="width:40px"> px
		  		</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-vshadow">Vertical Shadow</label>
		    		<div id="box-vshadow-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-vshadow" value="0" style="width:40px"> px
		    	</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	
		  	<hr>
		  	
		  	<div class="form-group">
		  		<div class="panel-col col-sm-8">
		    		<label for="box-opacity">Box Opacity</label>
		    		<div id="box-opacity-slider" class="ib2-slider"></div>
		    	</div>
		    	<div class="panel-col col-sm-4">
		    		<input type="text" class="form-control ib2-slider-val" id="box-opacity" value="100" style="width:40px"> %
		    	</div>
		  		<div class="clearfix"></div>
		  	</div>
		  	<hr>
		</div>
		
		<div id="box-settings-delay" class="tab-settings-content">
			<p>If you want to hide this element and display it after a certain amount of time, then you can enable this option.</p>
			<hr />
			<div class="form-group">
				<input type="checkbox" id="enable_box_delay" value="noindex" data-label="Enable Display Delay" /> 
			</div>
			<div class="delay-group" style="display:none">
				<hr />
				<div class="form-group">
			    	<div class="panel-col col-sm-8">
				    	<label for="box-delay-hour">Delay Hour</label>
				    	<div id="box-delay-hour-slider" class="ib2-slider"></div>
			    	</div>
			    	<div class="panel-col col-sm-4">
			    		<input type="text" class="form-control ib2-slider-val" id="box-delay-hour" value="0" style="width:40px">
			  		</div>
			  		<div class="clearfix"></div>
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<div class="panel-col col-sm-8">
				    	<label for="box-delay-min">Delay Minute</label>
				    	<div id="box-delay-min-slider" class="ib2-slider"></div>
			    	</div>
			    	<div class="panel-col col-sm-4">
			    		<input type="text" class="form-control ib2-slider-val" id="box-delay-min" value="0" style="width:40px">
			  		</div>
			  		<div class="clearfix"></div>
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<div class="panel-col col-sm-8">
				    	<label for="box-delay-secs">Delay Seconds</label>
				    	<div id="box-delay-secs-slider" class="ib2-slider"></div>
			    	</div>
			    	<div class="panel-col col-sm-4">
			    		<input type="text" class="form-control ib2-slider-val" id="box-delay-secs" value="0" style="width:40px">
			  		</div>
			  		<div class="clearfix"></div>
			  	</div>
			  	<hr>
		  	</div>
		</div>
		
		<div id="box-settings-effect" class="tab-settings-content">
			<div class="form-group">
		    	<label for="box-animation">Display Animation</label>
		    	<select class="form-control" id="box-animation">
		    		<option value="none" selected="selected">None</option>
					<option value="blind">Blind</option>
					<option value="bounce">Bounce</option>
					<option value="bounceInLeft">Bounce In Left</option>
					<option value="bounceInRight">Bounce In Right</option>
					<option value="bounceInUp">Bounce In Up</option>
					<option value="clip">Clip</option>
					<option value="drop">Drop</option>
					<option value="explode">Explode</option>
					<option value="fold">Fold</option>
					<option value="highlight">Highlight</option>
					<option value="puff">Puff</option>
					<option value="pulsate">Pulsate</option>
					<option value="scale">Scale</option>
					<option value="shake">Shake</option>
					<option value="slide">Slide</option>
					<option value="flipInX">Flip In X</option>
					<option value="flipInY">Flip In Y</option>
					<option value="rotateIn">Rotate</option>
					<option value="rotateInDownLeft">Rotate Down Left</option>
					<option value="rotateInDownRight">Rotate Down Right</option>
					<option value="rotateInUpLeft">Rotate Up Left</option>
					<option value="rotateInUpRight">Rotate Up Right</option>
					<option value="fadeInDownBig">Fade In Down</option>
					<option value="fadeInUpBig">Fade In Up</option>
					<option value="fadeInLeftBig">Fade In Left</option>
					<option value="fadeInRightBig">Fade In Right</option>
				</select>
				<p class="help-block">The selected animation above will be shown the first time the page is loaded.</p>
		  	</div>
		</div>
	</div>
</div>
<?php	
}

function ib2_elements() {
?>
<div id="element-pallete" style="position:fixed">
	<div class="element-handle">:::</div>
	<div class="element-area normal-elements">
		<div class="pull-left ib2-element ib2-el-section" data-element="section" data-tooltip="Section">
			<i class="fa fa-square"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-wsection" data-element="wsection" data-tooltip="Wide Section">
			<i class="fa fa-square-o"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-wbox" data-element="wbox" data-tooltip="Wide Background Box">
			<i class="glyphicon glyphicon-sound-stereo"></i>
		</div>
		<div class="clearfix"></div>
		<hr>
	</div>
	<div class="element-area normal-elements">
		<div class="pull-left ib2-element ib2-el-title" data-element="title" data-tooltip="Title Element">
			<i class="fa fa-font"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-text" data-element="text" data-tooltip="Text Element">
			<i class="fa fa-align-justify"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-image" data-element="image" data-tooltip="Image Element">
			<i class="fa fa-photo"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-video" data-element="video" data-tooltip="Video Element">
			<i class="fa fa-video-camera"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-box" data-element="box" data-tooltip="Box Element">
			<i class="fa fa-cube"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-hline" data-element="hline" data-tooltip="Line/Divider">
			<i class="fa fa-ellipsis-h"></i>
		</div>
		
		<div class="pull-left ib2-element ib2-el-countdown" data-element="countdown" data-tooltip="Countdown">
			<i class="fa fa-tachometer"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-button" data-element="button" data-tooltip="Button Element">
			<i class="fa fa-hand-o-up"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-optin" data-element="optin" data-tooltip="Opt-In Form">
			<i class="fa fa-envelope"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-optin3" data-element="optin3" data-tooltip="3 Steps Opt-In">
			<i class="fa fa-envelope-square"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-comment" data-element="comment" data-tooltip="Comment">
			<i class="fa fa-comment-o"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-share" data-element="share" data-tooltip="Social Share">
			<i class="fa fa-share-alt"></i>
		</div>
		<div class="pull-left ib2-element" data-element="columns" data-tooltip="Columns">
			<i class="fa fa-columns"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-tabs" data-element="tabs" data-tooltip="Tabbed Content">
			<i class="glyphicon glyphicon-folder-close"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-menu" data-element="menu" data-tooltip="Navigation">
			<i class="fa fa-th-list"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-date" data-element="date" data-tooltip="Today's Date">
			<i class="fa fa-calendar-o"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-quiz" data-element="quiz" data-tooltip="Questions">
			<i class="fa fa-dot-circle-o"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-code" data-element="code" data-tooltip="HTML/CSS/JavaScript">
			<i class="fa fa-code"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-slides" data-element="slides" data-tooltip="Slides/Carousel">
			<i class="fa fa-toggle-right"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-spacer" data-element="spacer" data-tooltip="Spacer">
			<i class="fa fa-arrows-v"></i>
		</div>
		<div class="pull-left ib2-element ib2-el-shortcode" data-element="shortcode" data-tooltip="Shortcode Placeholder">
			[]
		</div>
		<div class="clearfix"></div>
		<hr>
	</div>
	<div class="element-area normal-elements" style="text-align:center">
		<button type="button" class="btn btn-default btn-sm show-combo-els">Combo Elements</button>
		<hr>
	</div>
	<div class="element-area combo-elements">
		<div class="ib2-element ib2-element-full" data-element="text_image" data-tooltip="Text + Image #1">
			Text + Image #1
		</div>
		<div class="ib2-element ib2-element-full" data-element="text_image2" data-tooltip="Text + Image #2">
			Text + Image #2
		</div>
		<div class="ib2-element ib2-element-full" data-element="box_video" data-tooltip="Box + Video">
			Box + Video
		</div>
		<div class="ib2-element ib2-element-full" data-element="product_box" data-tooltip="Product Box #1">
			Product Box #1
		</div>
		<div class="ib2-element ib2-element-full" data-element="product_box2" data-tooltip="Product Box #2">
			Product Box #2
		</div>
		<div class="ib2-element ib2-element-full" data-element="order_scarcity" data-tooltip="Order Scarcity #1">
			Order Scarcity #1
		</div>
		<div class="ib2-element ib2-element-full" data-element="order_scarcity2" data-tooltip="Order Scarcity #2">
			Order Scarcity #2
		</div>
		<div class="ib2-element ib2-element-full" data-element="fancy_optin1" data-tooltip="Fancy Opt-In #1">
			Fancy Opt-In #1
		</div>
		<div class="ib2-element ib2-element-full" data-element="fancy_optin2" data-tooltip="Fancy Opt-In #2">
			Fancy Opt-In #2
		</div>
		<div class="clearfix"></div>
		<hr>
	</div>
	<div class="element-area combo-elements" style="text-align:center">
		<button type="button" class="btn btn-default btn-sm show-basic-els">Basic Elements</button>
		<hr>
	</div>
	<div class="element-area">
		<div style="margin-top:10px">
			<button class="pull-left ib2-set-box btn btn-default btn-xs open-panel" type="button" data-settings="page"><i class="fa fa-gear"></i></button>
			<button class="pull-left ib2-set-box btn btn-primary btn-xs open-panel" type="button" data-settings="graphics"><i class="fa fa-file-picture-o"></i></button>
			
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="clearfix"></div>
	<div class="element-sizer els-maximize" style="display:none"><a href="#" class="ib2-maximize-elements"><i class="fa fa-caret-down"></i></a></div>
	<div class="element-sizer els-minimize"><a href="#" class="ib2-minimize-elements"><i class="fa fa-caret-up"></i></a></div>
</div>
<?php
}