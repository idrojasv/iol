
<table class="form-table">

	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'From Name', 'mailster' ) ?> *</th>
		<td><input type="text" name="mailster_options[from_name]" value="<?php echo esc_attr( mailster_option( 'from_name' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender name which is displayed in the from field', 'mailster' ) ?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'From Email', 'mailster' ) ?> *</th>
		<td><input type="text" name="mailster_options[from]" value="<?php echo esc_attr( mailster_option( 'from' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The sender email address. Force your receivers to whitelabel this email address.', 'mailster' ) ?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Reply-to Email', 'mailster' ) ?> *</th>
		<td><input type="text" name="mailster_options[reply_to]" value="<?php echo esc_attr( mailster_option( 'reply_to' ) ); ?>" class="regular-text"> <span class="description"><?php esc_html_e( 'The address users can reply to', 'mailster' ) ?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e( 'Send delay', 'mailster' ) ?> *</th>
		<td><input type="text" name="mailster_options[send_offset]" value="<?php echo esc_attr( mailster_option( 'send_offset' ) ); ?>" class="small-text"> <span class="description"><?php _e( 'The default delay in minutes for sending campaigns.', 'mailster' ) ?></span></td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Delivery by Time Zone', 'mailster' ) ?> *</th>
		<td><label><input type="hidden" class="wasabi" name="mailster_options[timezone]" value=""><input type="checkbox" name="mailster_options[timezone]" value="1" <?php checked( mailster_option( 'timezone' ) );?>> <?php esc_html_e( 'Send Campaigns based on the subscribers timezone if known', 'mailster' ) ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Embed Images', 'mailster' ) ?> *</th>
		<td><label><input type="hidden" class="wasabi" name="mailster_options[embed_images]" value=""><input type="checkbox" name="mailster_options[embed_images]" value="1" <?php checked( mailster_option( 'embed_images' ) );?>> <?php esc_html_e( 'Embed images in the mail', 'mailster' ) ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Module Thumbnails', 'mailster' ) ?></th>
		<td><label><input type="hidden" class="wasabi" name="mailster_options[module_thumbnails]" value=""><input type="checkbox" name="mailster_options[module_thumbnails]" value="1" <?php checked( mailster_option( 'module_thumbnails' ) );?>> <?php esc_html_e( 'Show thumbnails of modules in the editor if available', 'mailster' ) ?></label>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Post List Count', 'mailster' ) ?></th>
		<td><input type="text" name="mailster_options[post_count]" value="<?php echo esc_attr( mailster_option( 'post_count' ) ); ?>" class="small-text"> <span class="description"><?php esc_html_e( 'Number of posts or images displayed at once in the editbar.', 'mailster' ) ?></span></td>
	</tr>
	<?php if ( function_exists( 'find_core_auto_update' ) ) : // only 3.7+ ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'Auto Update', 'mailster' ) ?></th>
					<td>
					<?php
					$is = mailster_option( 'autoupdate', 'minor' );
					$types = array(
					'1' => __( 'enabled', 'mailster' ),
					'0' => __( 'disabled', 'mailster' ),
					'minor' => __( 'only minor updates', 'mailster' ),
					);
?>
					<select name="mailster_options[autoupdate]">
						<?php foreach ( $types as $value => $name ) {?>
						<option value="<?php echo $value; ?>" <?php selected( $is == $value ) ?>><?php echo $name; ?></option>
						<?php }?>
					</select>
					<p class="description"><?php esc_html_e( 'auto updates are recommended for important fixes.', 'mailster' );?></p>
					</td>
				</tr>
				<?php endif; ?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'System Mails', 'mailster' ) ?>
		<p class="description"><?php esc_html_e( 'decide how Mailster uses the wp_mail function', 'mailster' );?></p></th>
		<td>
		<p><label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="0" <?php checked( ! mailster_option( 'system_mail' ) ) ?>> <?php esc_html_e( 'Do not use Mailster for outgoing WordPress mails', 'mailster' ) ?></label></p>
		<p><label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="1" <?php checked( mailster_option( 'system_mail' ) == 1 ) ?>> <?php esc_html_e( 'Use Mailster for all outgoing WordPress mails', 'mailster' ) ?></label><br>
			<label><input type="radio" name="mailster_options[system_mail]" class="system_mail" value="template" <?php checked( mailster_option( 'system_mail' ) == 'template' ) ?>> <?php esc_html_e( 'Use only the template for all outgoing WordPress mails', 'mailster' ) ?></label></p>
		<p>&nbsp;&nbsp;<?php esc_html_e( 'use', 'mailster' );?><select name="mailster_options[system_mail_template]" class="system_mail_template" <?php echo ! mailster_option( 'system_mail' ) ? 'disabled' : '' ?>>
		<?php
		$selected = mailster_option( 'system_mail_template', 'notification.html' );
		foreach ( $templatefiles as $slug => $filedata ) {
			if ( $slug == 'index.html' ) {
				continue;
			}

				?>
					<option value="<?php echo $slug ?>"<?php selected( $slug == $selected ) ?>><?php echo esc_attr( $filedata['label'] ) ?> (<?php echo $slug ?>)</option>
		<?php
		}
?>
		</select></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'CharSet', 'mailster' ) ?> / <?php esc_html_e( 'Encoding', 'mailster' ) ?></th>
		<td>
		<?php
		$is = mailster_option( 'charset', 'UTF-8' );
		$charsets = array(
		'UTF-8' => 'Unicode 8',
		'ISO-8859-1' => 'Western European',
		'ISO-8859-2' => 'Central European',
		'ISO-8859-3' => 'South European',
		'ISO-8859-4' => 'North European',
		'ISO-8859-5' => 'Latin/Cyrillic',
		'ISO-8859-6' => 'Latin/Arabic',
		'ISO-8859-7' => 'Latin/Greek',
		'ISO-8859-8' => 'Latin/Hebrew',
		'ISO-8859-9' => 'Turkish',
		'ISO-8859-10' => 'Nordic',
		'ISO-8859-11' => 'Latin/Thai',
		'ISO-8859-13' => 'Baltic Rim',
		'ISO-8859-14' => 'Celtic',
		'ISO-8859-15' => 'Western European revision',
		'ISO-8859-16' => 'South-Eastern European',
		) ?>
		<select name="mailster_options[charset]">
			<?php foreach ( $charsets as $code => $region ) {?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ) ?>><?php echo $code; ?> - <?php echo $region; ?></option>
			<?php }?>
		</select>
		<?php
		$is = mailster_option( 'encoding', '8bit' );
		$encoding = array(
		'8bit' => '8bit',
		'7bit' => '7bit',
		'binary' => 'binary',
		'base64' => 'base64',
		'quoted-printable' => 'quoted-printable',
		) ?> /
		<select name="mailster_options[encoding]">
			<?php foreach ( $encoding as $code ) {?>
			<option value="<?php echo $code; ?>" <?php selected( $is == $code ) ?>><?php echo $code; ?></option>
			<?php }?>
		</select>
		<p class="description"><?php esc_html_e( 'change Charset and encoding of your mails if you have problems with some characters', 'mailster' );?></p>
		</td>
	</tr>
	<?php
	$geoip = mailster_option( 'trackcountries' );
	$geoipcity = mailster_option( 'trackcities' );
	if ( isset( $_GET['nogeo'] ) ) {
		$geoip = $geoipcity = false;
	}

?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Track Geolocation', 'mailster' ) ?>
		<div class="loading geo-ajax-loading"></div></th>
		<td>
		<p><label><input type="hidden" class="wasabi" name="mailster_options[trackcountries]" value=""><input type="checkbox" id="mailster_geoip" name="mailster_options[trackcountries]" value="1" <?php checked( $geoip );?>> <?php esc_html_e( 'Track Countries in Campaigns', 'mailster' ) ?></label></p>
		<p><button id="load_country_db" class="button-primary" data-type="country" <?php disabled( ! $geoip );?>><?php ( is_file( mailster_option( 'countries_db' ) ) ) ? esc_html_e( 'Update Country Database', 'mailster' ) : esc_html_e( 'Load Country Database', 'mailster' );?></button> <?php esc_html_e( 'or', 'mailster' );?> <a id="upload_country_db_btn" href="#"><?php esc_html_e( 'upload file', 'mailster' );?></a>
		</p>
		<p id="upload_country_db" class="hidden">
			<input type="file" name="country_db_file"> <input type="submit" class="button" value="<?php esc_html_e( 'Upload', 'mailster' ) ?>" />
			<br><span class="description"><?php esc_html_e( 'upload the GeoIPv6.dat you can find in the package here:', 'mailster' );?> <a href="http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz">http://geolite.maxmind.com/download/geoip/database/GeoIPv6.dat.gz</a></span>
		</p>

		<input id="country_db_path" type="text" name="mailster_options[countries_db]" class="widefat" value="<?php echo mailster_option( 'countries_db' ) ?>" placeholder="<?php echo MAILSTER_UPLOAD_DIR . '/GeoIPv6.dat' ?>">
		<p><label><input type="hidden" class="wasabi" name="mailster_options[trackcities]" value=""><input type="checkbox" id="mailster_geoipcity" name="mailster_options[trackcities]" value="1" <?php checked( $geoipcity );?><?php disabled( ! $geoip );?>> <?php esc_html_e( 'Track Cities in Campaigns', 'mailster' ) ?></label></p>
		<p><button id="load_city_db" class="button-primary" data-type="city" <?php disabled( ! $geoipcity );?>><?php ( is_file( mailster_option( 'cities_db' ) ) ) ? esc_html_e( 'Update City Database', 'mailster' ) : esc_html_e( 'Load City Database', 'mailster' );?></button> <?php esc_html_e( 'or', 'mailster' );?> <a id="upload_city_db_btn" href="#"><?php esc_html_e( 'upload file', 'mailster' );?></a>
		</p>
		<p id="upload_city_db" class="hidden">
			<input type="file" name="city_db_file"> <input type="submit" class="button" value="<?php esc_html_e( 'Upload', 'mailster' ) ?>" />
			<br><span class="description"><?php esc_html_e( 'upload the GeoLiteCity.dat you can find in the package here:', 'mailster' );?> <a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz">http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz</a></span>
		</p>
		<p class="description"><?php esc_html_e( 'The city DB is about 12 MB. It can take a while to load it', 'mailster' );?></p>
		<input id="city_db_path" type="text" name="mailster_options[cities_db]" class="widefat" value="<?php echo mailster_option( 'cities_db' ) ?>" placeholder="<?php echo MAILSTER_UPLOAD_DIR . '/GeoIPCity.dat' ?>">

		</td>
	</tr>
	<?php if ( $geoip && is_file( mailster_option( 'countries_db' ) ) ) : ?>
	<tr valign="top">
		<th scope="row"></th>
		<td>
	<?php if ( mailster_is_local() ) : ?>
	<div class="error inline"><p><strong><?php esc_html_e( 'Geolocation is not available on localhost!', 'mailster' ) ?></strong></p></div>
	<?php endif; ?>
		<p class="description"><?php esc_html_e( 'If you don\'t find your country down below the geo database is missing or corrupt', 'mailster' ) ?></p>
		<p>
		<strong><?php esc_html_e( 'Your IP', 'mailster' ) ?>:</strong> <?php echo mailster_get_ip() ?><br>
		<strong><?php esc_html_e( 'Your country', 'mailster' ) ?>:</strong> <?php echo mailster_ip2Country( '', 'name' ) ?><br>&nbsp;&nbsp;<strong><?php esc_html_e( 'Last update', 'mailster' ) ?>: <?php echo date( $timeformat, filemtime( mailster_option( 'countries_db' ) ) + $timeoffset ) ?> </strong><br>
	<?php if ( $geoipcity && is_file( mailster_option( 'cities_db' ) ) ) : ?>
		<strong><?php esc_html_e( 'Your city', 'mailster' ) ?>:</strong> <?php echo mailster_ip2City( '', 'city' ) ?><br>&nbsp;&nbsp;<strong><?php esc_html_e( 'Last update', 'mailster' ) ?>: <?php echo date( $timeformat, filemtime( mailster_option( 'cities_db' ) ) + $timeoffset ) ?></strong>
	<?php endif; ?>
		</p>
		<p class="description">This product includes GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com" class="external">http://www.maxmind.com</a></p>
		</td>
	</tr>
	<?php
	endif;
	?>
	<tr valign="top">
		<th scope="row"><?php esc_html_e( 'Google API Key', 'mailster' ) ?>
		</th>
		<td><input type="password" name="mailster_options[google_api_key]" value="<?php echo esc_attr( mailster_option( 'google_api_key' ) ); ?>" class="regular-text" autocomplete="new-password">
		<p class="description">
		<?php esc_html_e( 'The Google API key is used to display Maps for Mailster on the back end.', 'mailster' ); ?><br>
		<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" class="external"><?php esc_html_e( 'Get your Google API Key.', 'mailster' ); ?></a></p>
		</td>
	</tr>
</table>
<p class="description">* <?php esc_html_e( 'can be changed in each campaign', 'mailster' ) ?></p>
