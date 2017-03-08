<?php
/**
 * /tmp/phptidy-sublime-buffer.php
 *
 * @author Xaver Birsak (https://revaxarts.com)
 * @package
 */


?>
<table class="form-table">
	<tr valign="top">
		<th scope="row"><?php _e( 'Export', 'mailster' ) ?>
		<p class="description">
		<?php _e( 'Use this data to copy your settings between Mailster installations. This data contains sensitive information like passwords so don\'t share them. Capabilities are not included.', 'mailster' );?>
		</p>
		</th>
		<td><textarea rows="10" cols="40" class="large-text code"><?php echo $this->export_settings(); ?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e( 'Import', 'mailster' ) ?>
		<p class="description">
		<?php _e( 'Import your settings by pasting the exported data. Make sure you check the data after import.', 'mailster' );?>
		</p>
		</th>
		<td><textarea rows="10" cols="40" class="large-text code" name="mailster_settings_data"></textarea>
		<p class="alignright"><input type="submit" class="button button-primary" value="<?php _e( 'Import Data', 'mailster' ) ?>" name="mailster_import_data" id="mailster_import_data" /></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><?php _e( 'Reset Settings', 'mailster' ) ?>
		</th>
		<td><a href="edit.php?post_type=newsletter&page=mailster_settings&reset-settings=1&_wpnonce=<?php echo wp_create_nonce( 'mailster-reset-settings' ) ?>" class="button" id="mailster_reset_data"><?php _e( 'Reset all settings', 'mailster' );?></a>
		<p class="description">
		<?php _e( 'Use this options to reset the data to Mailster default values.', 'mailster' );?>
		</p>
		</td>
	</tr>
</table>
