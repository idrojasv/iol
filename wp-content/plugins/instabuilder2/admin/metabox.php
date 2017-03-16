<?php
add_action('admin_init', 'ib2_add_meta_box');
function ib2_add_meta_box() {
	if ( !function_exists( 'add_meta_box' ) ) return;
	
	if ( ib2_chkdsk() ) :
		add_meta_box( 'ib2-meta-box-post', 'InstaBuilder 2.0', 'ib2_metabox', 'post', 'side', 'high' );
		add_meta_box( 'ib2-meta-box-page', 'InstaBuilder 2.0', 'ib2_metabox', 'page', 'side', 'high' );
	endif;
}

function ib2_metabox () {
	global $post;
	$alert = ( $post->post_status == 'auto-draft' ) ? ' data-alert="1"' : '';
	$meta = get_post_meta($post->ID, 'ib2_settings', true);
	
	$is_new = false;
	if ( $meta === FALSE || !is_array($meta) ) $is_new = true;
	if ( is_array($meta) && !isset($meta['variationa']) ) $is_new = true;
	
	$checked = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? ' checked="checked"' : '';
	$hide = ( isset($meta['enable']) && $meta['enable'] == 'yes' ) ? '' : ' style="display:none"';
	
	echo '<input type="hidden" name="ib2_meta_nonce" id="ib2_meta_nonce" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
?>

<input name="ib2_enable" value="no" type="hidden" />
<div class="ib2-switch-label">Enable InstaBuilder</div>
<div class="ib2-switch">
	<input id="ib2-enable" name="ib2_enable" value="yes" type="checkbox"<?php echo $checked; ?> />
	<label for="ib2-enable"><span>Enable</span></label>
	<div style="clear:left"></div>
</div>
<?php
if ( $is_new )
	echo '<div id="ib2-launch-btn"' . $hide . '><a href="#" id="ib2-create-new" title="InstaBuilder 2.0 - Select Template" class="ib2-btn ib2-btn-primary ib2-btn-lg"' . $alert . '>Launch IB 2.0 Editor</a></div>';
else
	echo '<div id="ib2-launch-btn"' . $hide . '><a href="' . admin_url('post.php?post=' . $post->ID . '&action=edit&ib2editor=true') . '" class="ib2-btn ib2-btn-primary ib2-btn-lg"' . $alert . '>Launch IB 2.0 Editor</a></div>';
}

add_action('save_post', 'ib2_save_meta', 9, 3);
function ib2_save_meta( $post_id, $post, $update ) {
	$ib2_meta_nonce = isset($_POST['ib2_meta_nonce']) ? $_POST['ib2_meta_nonce'] : '';
	if ( !wp_verify_nonce( $ib2_meta_nonce, plugin_basename(__FILE__) ) )
		return $post_id;
 
	if ( isset($post) ) {
		if ( 'page' == $post->post_type  ) {
	    	if ( !current_user_can( 'edit_page', $post->ID ) ) return $post->ID;
	    } else {
	      	if ( !current_user_can( 'edit_post', $post->ID ) ) return $post->ID;
	    }
		
		$data = get_post_meta($post->ID, 'ib2_settings', true);
		if ( !is_array($data) || empty($data) ) {
			$data = array();
		}
		
		if ( isset($data['enable']) || ( isset($_POST['original_post_status']) && $_POST['original_post_status'] == 'auto-draft' )  ) {
			if ( isset($_POST['ib2_enable']) && $_POST['ib2_enable'] == 'yes' )
				$data['enable'] = 'yes';
			else		
				$data['enable'] = 'no';
		
			update_post_meta($post_id, 'ib2_settings', $data);
		}
	}
}