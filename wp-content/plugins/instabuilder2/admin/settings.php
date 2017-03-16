<?php $options = get_option('ib2_options'); 
if ( !ib2_chkdsk() ) :
?>
<form id="ib2-license-form" class="form-horizontal" method="post" role="form" action="<?php echo admin_url('admin.php?page=ib2-settings'); ?>">
	<input type="hidden" name="ib2action" value="buka_kunci" />
	<div class="ib2-admin-box">
		<div class="ib2-admin-box-title">License Activation</div>
		<div class="ib2-admin-box-inside">
			<div class="form-group">
	    		<label for="disqus" class="col-sm-3 control-label">Your License Key</label>
	    		<div class="col-sm-5">
	      			<input type="text" class="form-control" name="ib2_kata_kunci" id="ib2_kata_kunci" placeholder="e.g. 17su-i982nfisug-a928">
	    		</div>
	    		<div class="col-sm-4">
	    			<p class="help-block">Please enter your InstaBuilder 2.0 license key to unlock all features.</p>
	    		</div>
	  		</div>
		</div>
	</div>
	<p style="text-align:right"><button type="submit" class="btn btn-success btn-lg"><i class="fa fa-key"></i> Activate Now</button></p>
</form>
<?php endif; ?>
<?php if ( ib2_chkdsk() ) : ?>
<?php if ( isset($_GET['saved']) && $_GET['saved'] == 'true' ) : ?><div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-check"></i> Settings Saved.</div><?php endif; ?>
<?php if ( isset($_GET['gtw']) && $_GET['gtw'] == 'connected' ) : ?><div class="alert alert-success alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><i class="fa fa-check"></i> You have successfully connect your GTW account with IB 2.0.</div><?php endif; ?>
<?php if ( isset($_GET['gtw']) && $_GET['gtw'] == 'failed' ) : ?><div class="alert alert-danger alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button> ERROR: GTW Authentication Failed. Please check your settings and try again.</div><?php endif; ?>
				
<form id="ib2-settings-form" class="form-horizontal" method="post" role="form" enctype="multipart/form-data">
<input type="hidden" name="action" value="ib2_options_save" />

<!-- Powered By -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">General Settings</div>
	<div class="ib2-admin-box-inside">
		<input type="hidden" name="ib2post[enable_powered]" value="0" />
		<div class="form-group">
    		<div class="col-sm-offset-3 col-sm-9">
      			<div class="checkbox">
        			<label>
          				<input type="checkbox" name="ib2post[enable_powered]" value="1" <?php echo ( (isset($options['enable_powered']) && $options['enable_powered'] == 1 ) ? 'checked="checked" ' : ''); ?>/> Enable "Powered By InstaBuilder" 2.0 link
        			</label>
      			</div>
    		</div>
  		</div>
  		
		<div class="form-group">
    		<label for="ib2affurl" class="col-sm-3 control-label">InstaBuilder 2.0 Affiliate URL</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[ib2affurl]" id="ib2affurl" placeholder="Enter your affiliate link here (must use http:// or https://)" value="<?php echo ( isset($options['ib2affurl']) ? esc_attr($options['ib2affurl']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">If you enter your own IB 2.0 affiliate link, then it will be use as the "Powered By InstaBuilder 2.0" link so that you can earn the commissions.</p>
    		</div>
  		</div>
  		
  		<div class="form-group">
    		<label for="ib2historyage" class="col-sm-3 control-label">Automatic Page Histories Deletion</label>
    		<div class="col-sm-5">
      			Delete page histories older than <input type="text" class="form-control" name="ib2post[ib2historyage]" id="ib2historyage" placeholder="e.g. 3" value="<?php echo ( isset($options['ib2historyage']) ? esc_attr($options['ib2historyage']) : '3' ); ?>" style="display: inline; width:80px"> days
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">IB2 keep all page histories in database so that you can use the "Restore Previous Save" feature. However, some data may be too old and you may not need it anymore. IB2 can automatically delete the old data for every <code>x</code> days to keep your database clean.</p>
    		</div>
  		</div>
  		
  		<input type="hidden" name="ib2post[autosave]" value="0" />
		<div class="form-group">
			<label class="col-sm-3 control-label">Editor Auto-Save</label>
    		<div class="col-sm-5">
      			<div class="checkbox">
        			<label>
          				<input type="checkbox" name="ib2post[autosave]" value="1" <?php echo ( (isset($options['autosave']) && $options['autosave'] == 1 ) ? 'checked="checked" ' : ''); ?>/> Enable Auto-Save
        			</label>
      			</div>
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Note: When enabled, this auto-save feature will run ONLY if you actively editing the page.</p>
    		</div>
  		</div>
  		<?php
  		$license = get_option('ib2_kunci');
		if ( !empty($license['content']) ) {
			$length = strlen($license['content']) - 4;
			$letter_x = '';
			for ($i = 0; $i < $length; $i++ ) {
				$letter_x .= 'x';
			}
			$license['content'] = substr_replace($license['content'], $letter_x, 0, $length);
		}
		$lic_nonce = wp_create_nonce('ib2-license');
  		?>
  		<div class="form-group">
    		<label for="fb_appid" class="col-sm-3 control-label">License Key</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" id="ib2_license" value="<?php echo ( isset($license['content']) ? stripslashes($license['content']) : '' ); ?>" readonly>
    		</div>
    		<div class="col-sm-4">
    			<a href="<?php echo admin_url('admin.php?page=ib2-settings&ib2action=remove_license&_ib2nonce=' . $lic_nonce); ?>" role="button" class="btn btn-danger btn-sm" onclick="return confirm('Removing a license key will prevent you from using IB 2.0.\nAre you sure you want to remove IB2.0 license Key?')">Remove License Key</a>
    		</div> 
  		</div>
  		<?php
  		$license = get_option('ib2f_checker');
		if ( !empty($license['content']) ) :
			$length = strlen($license['content']) - 4;
			$letter_x = '';
			for ($i = 0; $i < $length; $i++ ) {
				$letter_x .= 'x';
			}
			$license['content'] = substr_replace($license['content'], $letter_x, 0, $length);
			$lic_pro_nonce = wp_create_nonce('ib2-pro-license');
  		?>
  		<div class="form-group">
    		<label for="fb_appid" class="col-sm-3 control-label">PRO License Key</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" id="ib2_license" value="<?php echo ( isset($license['content']) ? stripslashes($license['content']) : '' ); ?>" readonly>
    		</div>
    		<div class="col-sm-4">
    			<a href="<?php echo admin_url('admin.php?page=ib2-settings&ib2action=remove_pro_license&_ib2prononce=' . $lic_pro_nonce); ?>" role="button" class="btn btn-danger btn-sm" onclick="return confirm('Removing PRO license key will prevent you from using IB 2.0 PRO.\nAre you sure you want to remove IB2.0 PRO license Key?')">Remove PRO License Key</a>
    		</div> 
  		</div>
  		<?php endif; ?>
	</div>
</div>
<!-- ./Powered By -->

<!-- Facebook Settings -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Facebook</div>
	<div class="ib2-admin-box-inside">
		<div class="alert alert-info" role="alert">In order to obtain both <em>Facebook App ID</em> and <em>Facebook App Secret</em>, please <a href="https://developers.facebook.com/apps" class="alert-link" target="_blank">create a Facebook App</a>. Both of the keys will be needed by some IB 2.0 features, such as Facebook Connect for Opt-In, FB Comment, FB Like and FB Page integration.</div>
		<div class="form-group">
    		<label for="fb_appid" class="col-sm-3 control-label">Facebook App ID</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[fb_appid]" id="fb_appid" placeholder="e.g. 340297652710877" value="<?php echo ( isset($options['fb_appid']) ? stripslashes($options['fb_appid']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Please enter your Facebook App ID here.</p>
    		</div>
  		</div>
  		<div class="form-group">
    		<label for="fb_secret" class="col-sm-3 control-label">Facebook App Secret</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[fb_secret]" id="fb_secret" placeholder="e.g. 02c170d85320de892539f735edj75d2c" value="<?php echo ( isset($options['fb_secret']) ? stripslashes($options['fb_secret']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Please enter your Facebook App Secret here.</p>
    		</div>
  		</div>
  		<?php if ( !empty($options['fb_secret']) && !empty($options['fb_appid']) ) : ?>
  		<hr>
  		<div class="alert alert-warning" role="alert">
  			<p><strong>If you want to use IB 2.0 landing pages also as a Facebook page tab, then you have to <a href="https://developers.facebook.com/apps" class="alert-link" target="_blank">create/edit Facebook app</a> and do the following: </strong></p>
  			<ul>
  				<li>&bull; In your Facebook app interface, go to <code>Settings >> Add Platform >> Page Tab</code> </li>
  				<li>&bull; Enter the Page Tab URL below into the <code>Page Tab URL</code> field in your Facebook app.</li>
  				<li>&bull; Enter the Secure Page Tab URL below into the <code>Secure Page Tab URL</code> field in your Facebook app.</li>
  				<li>&bull; Enable the "Wide Page Tab" option and save your Facebook app.</li>
  				<li>&bull; To publish one of your landing pages as a Facebook page tab, go edit one of your pages and choose the "Publish To Facebook" option from the top bar.</li>
  			</ul>
  		</div>
  		<?php
  		$pagetab_url = esc_url(add_query_arg('ib2mode', 'fbpage', trailingslashit(get_bloginfo('url'))));
		$pagetab_url = str_replace('https://', 'http://', $pagetab_url);
		?>
  		<div class="form-group">
    		<label for="fb_pagetab_url" class="col-sm-3 control-label">Page Tab URL</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" id="fb_pagetab_url" value="<?php echo $pagetab_url; ?>" readonly>
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Your Facebook page tab URL.</p>
    		</div>
  		</div>
  		
  		<?php
		$secure_pagetab_url = str_replace('http://', 'https://', $pagetab_url);
		?>
  		<div class="form-group">
    		<label for="fb_pagetab_url" class="col-sm-3 control-label">Secure Page Tab URL</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" id="fb_pagetab_url" value="<?php echo $secure_pagetab_url; ?>" readonly>
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Your secure Facebook page tab URL. Make sure you already installed an valid SSL certificate on your server, and visitors must be able to visits your site using <code>https://</code> protocol.</p>
    		</div>
  		</div>
  		<?php endif; ?>
	</div>
</div>
<!-- ./Facebook Settings -->

<!-- Disqus Settings -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Disqus</div>
	<div class="ib2-admin-box-inside">
		<div class="form-group">
    		<label for="disqus" class="col-sm-3 control-label">Disqus Shortname</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[disqus]" id="disqus" placeholder="e.g. mysitename" value="<?php echo ( isset($options['disqus']) ? esc_attr($options['disqus']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">In order to integrate IB 2.0 with Disqus, please register at <a href="http://disqus.com" target="_blank">Disqus</a>. The <code>Shortname</code> can be obtained after you register this site in your Disqus account.</p>
    		</div>
  		</div>
	</div>
</div>
<!-- ./Disqus Settings -->

<!-- GTW Settings -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">GoToWebinar</div>
	<div class="ib2-admin-box-inside">
		<div class="alert alert-info" role="alert">In order to integrate IB 2.0 with GoToWebinar, you have to <a href="https://developer.citrixonline.com/user/register" target="_blank" class="alert-link">create a developer account at Citrix</a>. After you create the account, please login to your Citrix's developer account and create a new app so that you can obtain the <em>Consumer Key</em>. Upon creating a new app, you can give your app any good name and description. In the "Product API" options, please choose <code>GoToWebinar</code>, and enter <code>http://<?php echo $_SERVER['HTTP_HOST']; ?>/</code> into the "Application URL" field.</div>
		<div class="form-group">
    		<label for="gtw" class="col-sm-3 control-label">Consumer Key</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[gtw]" id="gtw" placeholder="e.g. Dzz5rPWH6RiBB4HuO8g856KnB4ENZ925" value="<?php echo ( isset($options['gtw']) ? stripslashes($options['gtw']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Please enter the Consumer Key of your Citrix's application, and click on the "Save Settings" button below. Once this setting has been saved, a button labeled "CONNECT ..." will appear. Please click that button to connect IB 2.0 with your GoToWebinar account.</p>
    		</div>
  		</div>
  		<?php
  		$gtw_token = get_option('ib2_gotowebinar');
		$show = false;
		$reconnect = false;
		if ( empty($gtw_token) ) $show = true;
		if ( isset($gtw_token['date']) ) {
			$oneyear = strtotime("+1 Year", $gtw_token['date']);
			if ( time() > $oneyear ) {
				$show = true;
				$reconnect = true;
			}
		}
		
		$gtw_status = ( is_array($gtw_token) && !empty($gtw_token['access_token']) ) ? '<p class="form-control-static text-success"><strong>Connected</strong></p>' : '<p class="form-control-static text-danger"><strong>Disconnected</strong></p>';
  		if ( !empty($options['gtw']) && $show ) {
		$label = ( $reconnect ) ? 'RECONNECT IB 2.0 With GoToWebinar' : 'CONNECT IB 2.0 With GotoWebinar';
		$redirect_uri = admin_url('admin.php?page=ib2-settings&ib2action=gtw_token');
		$help = '';
		if ( $reconnect ) 
			$help = 'It\'s been a year since you connect IB 2.0 with your GTW account. It\'s time to renew the connection. Please click the reconnect button to renew the connection.';
  		?>
  		<div class="form-group">
    		<div class="col-sm-offset-3 col-sm-5">
      			<a href="https://api.citrixonline.com/oauth/authorize?client_id=<?php echo stripslashes($options['gtw']); ?>&redirect_uri=<?php echo urlencode($redirect_uri); ?>" role="button" class="btn btn-info"><?php echo $label; ?></a>
    		</div>
    		<div class="col-sm-4">
				<p class="help-block"><?php echo $help; ?></p>
			</div>
  		</div>
  		<?php } ?>
  		<div class="form-group">
    		<label for="gtw" class="col-sm-3 control-label">Status</label>
    		<div class="col-sm-9">
      			<?php echo $gtw_status; ?>
    		</div>
  		</div>
	</div>
</div>
<!-- ./GTW Settings -->

<!-- WebinarJam Settings -->
<div class="ib2-admin-box">
  <div class="ib2-admin-box-title">WebinarJam</div>
  <div class="ib2-admin-box-inside">
    <div class="form-group">
      <label for="webinarjam" class="col-sm-3 control-label">API Key</label>
      <div class="col-sm-5">
        <input type="text" class="form-control" name="ib2post[webinarjam]" id="webinarjam" placeholder="e.g. d40d300fa2bcd0e5fec1ae3c985acabaa09b398096702e8a6be931fb384929384
" value="<?php echo ( isset($options['webinarjam']) ? stripslashes($options['webinarjam']) : '' ); ?>">
      </div>
      <div class="col-sm-4">
        <p class="help-block">In order to integrate InstaBuilder 2.0 with WebinarJam, please enter your WebinarJam API Key. To obtain the key, simply edit one of your webinars or create a new one if you have none. The API is under the <code>Integration</code> tab. Copy the API Key and make sure the status is <code>Active</code>.</p>
      </div>
    </div>
  </div>
</div>
<!-- ./WebinarJam Settings -->

<!-- Image Search Settings -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Image Search API</div>
	<div class="ib2-admin-box-inside">
		<div class="alert alert-info" role="alert">This settings are optional, but can be useful if you want to enhance the results of the IB 2.0 Image Search feature.</div>
		<div class="form-group">
    		<label for="flickr" class="col-sm-3 control-label">Flickr API Key</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[flickr]" id="flickr" placeholder="e.g. 98as26e2155694cd7a6g235" value="<?php echo ( isset($options['flickr']) ? stripslashes($options['flickr']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">You can obtain the Flickr API Key by creating an account at <a href="https://www.flickr.com" target="_blank">Flickr</a>. Once you are registered, please <a href="https://www.flickr.com/services/apps/create/" target="_blank">create a Flickr App</a> to obtain the API Key.</p>
    		</div>
    		<div class="clearfix"></div>
  		</div>
  		<hr>
		<div class="form-group">
    		<label for="pixabay_key" class="col-sm-3 control-label">Pixabay API Key</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[pixabay_key]" id="pixabay_key" placeholder="e.g. asd6e215094cdf34ra3s25" value="<?php echo ( isset($options['pixabay_key']) ? stripslashes($options['pixabay_key']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">You can obtain the Pixabay API Key by creating an account at <a href="http://pixabay.com/" target="_blank">Pixabay</a>. Once you are registered, please visit the <a href="http://pixabay.com/api/docs/" target="_blank">Pixabay API Documentation</a> and scroll down to find a link labeled <code>Show API key</code> (under the "Request Parameter" section). Click that link to obtain the API Key.</p>
    		</div>
    		<div class="clearfix"></div>
  		</div>
  		<div class="form-group">
    		<label for="pixabay_id" class="col-sm-3 control-label">Pixabay Username</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[pixabay_id]" id="pixabay_id" placeholder="e.g. john123, iamuser456, etc" value="<?php echo ( isset($options['pixabay_id']) ? stripslashes($options['pixabay_id']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Please enter your Pixabay username.</p>
    		</div>
    		<div class="clearfix"></div>
  		</div>
	</div>
</div>
<!-- ./Image Search Settings -->

<!-- Aviary Settings -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Image Editor</div>
	<div class="ib2-admin-box-inside">
		<div class="form-group">
    		<label for="aviary" class="col-sm-3 control-label">API Key/Client ID</label>
    		<div class="col-sm-5">
      			<input type="text" class="form-control" name="ib2post[aviary]" id="aviary" placeholder="e.g. 626e215094cd7235" value="<?php echo ( isset($options['aviary']) ? stripslashes($options['aviary']) : '' ); ?>">
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">IB 2.0 allows you to edit and customize images right on the landing page editor. If you want to enable this feature, please signup at <a href="https://creativesdk.adobe.com/" target="_blank">Adobe Creative SDK</a> as a developer (it's FREE). Once you signed up, please login to your <a href="https://creativesdk.adobe.com/" target="_blank">Adobe Creative SDK account</a>, and create an app. The API Key or Client ID can be obtained after you create an app in your Adobe Creative SDK account.</p>
    		</div>
  		</div>
	</div>
</div>
<!-- ./Aviary Settings -->

<!-- Upload Templates -->
<div class="ib2-admin-box">
	<div class="ib2-admin-box-title">Upload New Template(s)</div>
	<div class="ib2-admin-box-inside">
		<div class="form-group">
    		<label for="upload-zip-template" class="col-sm-3 control-label">Template Zip File</label>
    		<div class="col-sm-5">
      			<input type="file" class="form-control" name="upload-zip-template" id="upload-zip-template" >
    		</div>
    		<div class="col-sm-4">
    			<p class="help-block">Simply click the "browse" button to upload IB 2.0 new template zip file, and then click the "Save Settings" button below to start the upload and import process.</p>
    		</div>
  		</div>
	</div>
</div>
<!-- ./Aviary Settings -->

<p style="text-align:right"><button type="submit" class="btn btn-success btn-lg"><i class="fa fa-save"></i> Save Settings</button></p>
</form>
<?php endif; ?>