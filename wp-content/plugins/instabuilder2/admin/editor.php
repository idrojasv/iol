<?php
add_action('admin_footer', 'ib2_template_chooser', 11);
function ib2_template_chooser() {
	global $post;
	$show = false;
	if ( isset($_GET['page']) ) {
		if ( $_GET['page'] == 'ib2-dashboard' )
			$show = true;
		
		if ( $_GET['page'] == 'ib2-funnel' )
			$show = true;
	}
	
	$screen = get_current_screen();
	if ( $screen && $screen->base == 'post' && $screen->action == 'add' ) $show = true;
	if ( $screen && $screen->base == 'post' && isset($_GET['post']) ) $show = true;
	
	if ( !$show ) return;
?>
<div id="ib2-templates" style="display:none;">
	<div class="ib2-template-header">Choose a Template</div>
	<div class="ib2-template-content">
		<!-- <div class="ib2-alert"><strong>Want more templates?</strong> Visit our marketplace to <a href="http://marketplace.instabuilder.com" target="_blank" class="ib2-alert-link">download</a> new and fresh templates</div> //-->
		<p> 
			<label>Template Type:</label>
			<select id="ib2-tmpl-type">
				<option value="all">All</option>
				<option value="sales">Sales Pages</option>
				<option value="optin">Squeeze Pages</option>
				<option value="launch">Launch Pages</option>
				<option value="webinar">Webinar Pages</option>
				<option value="coming">Coming Soon Pages</option>
				<option value="others">Others</option>
			</select>
			&nbsp;<select id="ib2-tmpl-subtype">
				<option value="">-- Sub-Type --</option>
				<option value="textsqueeze">Text Squeeze</option>
				<option value="videosqueeze">Video Squeeze</option>
				<option value="minisqueeze">Mini Squeeze</option>
				<option value="2stepsoptin">2 Steps Opt-In</option>
				<option value="3stepsoptin">3 Steps Opt-In</option>
				<option value="textsales">Text Sales Page</option>
				<option value="videosales">Video Sales Page</option>
				<option value="hybridsales">Hybrid Sales Page</option>
				<option value="otosales">OTO Sales Page</option>
				<option value="webinarsignup">Webinar Sign-Up</option>
				<option value="webinarthanks">Webinar Thank You</option>
				<option value="download">Download Page</option>
				<option value="confirmation">Confimation Page</option>
				<option value="thankyou">Thank You Page</option>
			</select>
			&nbsp;<input type="text" id="ib2-tmpl-tags" placeholder="e.g. enter keywords here" />&nbsp;<button type="button" class="ib2-btn ib2-btn-default ib2-btn-sm" id="find-templates">GO</button>
		</p>
		<div id="ib2-templates-area">
			<h3>All Templates</h3>
			<p style="display:none; margin-top:40px; text-align:center" id="ib2-template-loader">
				<img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" /><br />
				<em>Loading...</em>
			</p>
			<div id="ib2-templates-content">
				<?php ib2_get_templates_html('type=all'); ?>
			</div>
			<div style="clear:left"></div>
		</div>
	</div>
	<div class="ib2-template-footer">
		<?php $post_id = ( isset($_GET['post']) ? $_GET['post'] : 0 ) ?>
		<a href="<?php echo admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true'); ?>" role="button" id="ib2-new-scratch" class="ib2-btn ib2-btn-primary">Create From Scratch</a>&nbsp;&nbsp;<a href="#" class="ib2-template-close ib2-btn ib2-btn-default" role="button">Close</a>
	</div>
</div>
<?php
}

add_action('admin_footer', 'ib2_launch_editor_script');
function ib2_launch_editor_script() {
	global $post;

	$show = false;
	
	$screen = get_current_screen();
	if ( $screen && $screen->base == 'post' && $screen->action == 'add' ) $show = true;
	if ( $screen && $screen->base == 'post' && isset($_GET['post']) ) $show = true;
	
	if ( !$show ) return;
?>
<script type="text/javascript">
var template_open = 0;
jQuery(document).ready(function($){

	$('#ib2-enable').change(function(){
		if ( $(this).is(":checked") ) {
			$('#ib2-launch-btn').show();
		} else {
			$('#ib2-launch-btn').hide();
		}
	});
	
	var subtype = {};
	subtype.all = '<option value="textsqueeze">Text Squeeze</option>'
				+ '<option value="videosqueeze">Video Squeeze</option>'
				+ '<option value="minisqueeze">Mini Squeeze</option>'
				+ '<option value="2stepsoptin">2 Steps Opt-In</option>'
				+ '<option value="3stepsoptin">3 Steps Opt-In</option>'
				+ '<option value="surveyoptin">Survey Opt-In</option>'
				+ '<option value="textsales">Text Sales Page</option>'
				+ '<option value="videosales" >Video Sales Page</option>'
				+ '<option value="hybridsales">Hybrid Sales Page</option>'
				+ '<option value="otosales">OTO Sales Page</option>'
				+ '<option value="webinarsignup">Webinar Sign-Up</option>'
				+ '<option value="webinarthanks">Webinar Thank You</option>'
				+ '<option value="download">Download Page</option>'
				+ '<option value="confirmation">Confimation Page</option>'
				+ '<option value="thankyou">Thank You Page</option>';
	
	subtype.optin = '<option value="textsqueeze">Text Squeeze</option>'
				+ '<option value="videosqueeze">Video Squeeze</option>'
				+ '<option value="minisqueeze">Mini Squeeze</option>'
				+ '<option value="2stepsoptin">2 Steps Opt-In</option>'
				+ '<option value="3stepsoptin">3 Steps Opt-In</option>'
				+ '<option value="surveyoptin">Survey Opt-In</option>';
				
	subtype.sales = '<option value="textsales">Text Sales Page</option>'
				+ '<option value="videosales" >Video Sales Page</option>'
				+ '<option value="hybridsales">Hybrid Sales Page</option>'
				+ '<option value="otosales">OTO Sales Page</option>';
				
	subtype.webinar = '<option value="webinarsignup">Webinar Sign-Up</option>'
				+ '<option value="webinarthanks">Webinar Thank You</option>';
				
	subtype.launch = '';
	subtype.coming = '';
	subtype.others = '<option value="download">Download Page</option>'
				+ '<option value="confirmation">Confimation Page</option>'
				+ '<option value="thankyou">Thank You Page</option>';
	
	$('body').on('change', '#ib2-tmpl-type', function(){
		var $this = $("option:selected", this), type = $this.val(),
		text = $this.text(), keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-templates-area').find('h3').text(text + ' Templates');
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		var newoptions = '<option value=""> -- Sub-Type -- </option>';
		newoptions += subtype[type];
		
		$('#ib2-tmpl-subtype').html(newoptions);
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: '',
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('body').on('change', '#ib2-tmpl-subtype', function(){
		var $this = $("option:selected", this), subtype = $this.val(),
		type = $('#ib2-tmpl-type').val(), text = $this.text(),
		keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('body').on('click', '#find-templates', function(){
		var $this = $(this), subtype = $('#ib2-tmpl-subtype').val(),
		type = $('#ib2-tmpl-type').val(),
		keyword = $('#ib2-tmpl-tags').val();
		
		$('#ib2-template-loader').show();
		$('#ib2-templates-content').html('');
		
		$.post(ajaxurl, {
			action: 'ib2_show_templates',
			type: type,
			subtype: subtype,
			keyword: keyword
		}, function(response){
			$('#ib2-templates-content').html(response);
			$('#ib2-template-loader').hide();
		});
	});
	
	$('body').on('mouseenter', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		
		if ( !$this.find('.ib2-choose-tmpl').length && !$this.find('.ib2-choose-bg').length ) {
			var url = '<?php echo admin_url('post.php?post=' . $post->ID . '&action=edit&ib2editor=true'); ?>';
			url += '&template_id=' + $this.data('id');
			
			var preview_url = '<?php echo admin_url('post.php?post=' . $post->ID . '&action=preview&ib2editor=true'); ?>';
			preview_url += '&template_id=' + $this.data('id');
			
			$this.append('<div class="ib2-choose-bg"></div>');
			$this.append('<a href="' + url + '" class="ib2-btn ib2-btn-primary ib2-choose-tmpl">Choose</a>');
			$this.append('<a href="' + preview_url + '" target="_blank" class="ib2-btn ib2-btn-default ib2-preview-tmpl">Preview</a>');
			
			var top  = (($this.height() / 2) - ($this.find('.ib2-choose-tmpl').outerHeight() / 2));
			var left = (($this.width() / 2) - ($this.find('.ib2-choose-tmpl').outerWidth() / 2));

			if ( top < 0 ) top = 0;
			if ( left < 0 ) left = 0;
		
			$this.find('.ib2-choose-bg').css({
				'position': 'absolute',
				'background-color': '#FFF',
				'opacity': '0.6',
				'width': $this.width() + 'px',
				'height': $this.height() + 'px',
				'left': 0,
				'top': 0,
				'z-index': '8'
			});
			
			$this.find('.ib2-choose-tmpl').css({
				'position': 'absolute',
				'display': 'block',
				'left': left,
				'top': (top - 20),
				'z-index': '10'
			});
			
			$this.find('.ib2-preview-tmpl').css({
				'position': 'absolute',
				'display': 'block',
				'left': left,
				'top': (top + 20),
				'z-index': '10'
			});
		} else {
			$this.find('.ib2-choose-bg').show();
			$this.find('.ib2-choose-tmpl').show();
			$this.find('.ib2-preview-tmpl').show();
		}
	});
	
	$('body').on('mouseleave', '.ib2-tmpl-thumb', function(){
		var $this = $(this);
		$this.find('.ib2-choose-tmpl').hide();
		$this.find('.ib2-preview-tmpl').hide();
		$this.find('.ib2-choose-bg').hide();
	});
	
	$('#ib2-create-new').click(function(e){
		var $this = $(this);
		if ( adminpage == 'post-new-php' ) {
			if ( $this.data('alert') == 1 ) {
				alert('ERROR: Please save this page at least as a draft first.');
				return false;
			}
		}
		
		if ( template_open == 1 ) return false;
		
		if ( !$('#ib2-templates-bg').length ) {
			$('body').append('<div id="ib2-templates-bg"></div>');
		}
		
		var pos  = 'absolute';
		var top  = (($(window).height() / 2) - ($('#ib2-templates').outerHeight() / 2));
		var left = (($(window).width() / 2) - ($('#ib2-templates').outerWidth() / 2));

		if ( top < 0 ) top = 0;
		if ( left < 0 ) left = 0;
	
		// IE6 fix
		if ( $.browser.msie && parseInt($.browser.version) <= 6 ) {
			top = top + $(window).scrollTop();
			pos = 'absolute';
		}

		$('#ib2-templates').css({
	    	'position' : pos,
	    	'top' : top,
	    	'left' : left
		});
		
		$('#ib2-templates-bg').fadeIn("fast");
		$('#ib2-templates').fadeIn("medium");
		
		template_open = 1;
		
		e.preventDefault();
	});
	
	$('body').on('click', '#ib2-templates-bg', function(e){
		$('body').trigger("close-ib2-templates");
		e.preventDefault();
	});
	
	$(document).keyup(function(e) {
		if ( e.keyCode == 27 && template_open == 1 ) {
			$('body').trigger('close-ib2-templates');
		}
	});
	$('.ib2-template-close').click(function(e){
		$('body').trigger("close-ib2-templates");
		e.preventDefault();
	});
	$('body').on('close-ib2-templates', function(){
		$('#ib2-templates').fadeOut("medium");
		$('#ib2-templates-bg').fadeOut("slow");
		
		template_open = 0;
	});
});
</script>
<?php	
}

function ib2_get_templates_html( $args ) {
	$templates = ib2_get_templates($args);
	if ( !$templates ) {
		echo '<p style="text-align:center"><em>No template available.</em></p>';
	} else {
		foreach ( $templates as $t ) {
			$thumb = ( empty($t->screenshot) ) ? IB2_IMG . 'no_thumb.jpg' : $t->screenshot;
			$thumb = str_replace("{%IB2_PLUGIN_URL%}", IB2_URL, $thumb);
			echo '<div class="ib2-tmpl-thumb" data-id="' . $t->ID . '"><img src="' . $thumb . '" border="0" class="ib2-img-thumbnail ib2-img-responsive" /></div>' . "\n";
		}
	}
}

add_action('admin_init', 'ib2_editor', 20);
function ib2_editor() {
	global $wpdb;
	if ( isset($_GET['post']) && isset($_GET['action']) ) {
		if ( isset($_GET['ib2editor']) && $_GET['ib2editor'] == 'true' ) {
			
			if ( !ib2_chkdsk() ) return;
			
			$preview = ( $_GET['action'] == 'preview' ) ? TRUE : FALSE;
			
			// Define the editor constant...	
			define('IB2EDITOR', true);
			
			$post_id = (int) $_GET['post'];
			$meta = get_post_meta($post_id, 'ib2_settings', true);
			if ( !is_array($meta) )
				$meta = array();
			
			$all_variants = ib2_get_variants($post_id);
			if ( !$preview && !isset($meta['variationa']) && !$all_variants ) {
				// add/update into IB 2.0 pages table
				$page = ib2_page_entry($post_id);
			
				// register the new variant into IB2.0 database
				$variant = ib2_variant_entry("post_id={$post_id}&variant=a");
				
				if ( $page && $variant && isset($_GET['template_id']) && ($template = ib2_get_template($_GET['template_id'])) ) {
					if ( empty($template->metadata) ) {
						// Process template content from file
						$file = IB2_PATH . 'templates/' . $template->name . '.txt';
						$content = '';
						if ( !file_exists($file) )
							$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';

						if ( file_exists($file) ) {
							$content = @file_get_contents($file);
							$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
						}
						$data = maybe_unserialize($content);
					} else {
						$data = maybe_unserialize($template->metadata);
					}

					// Save The New Data
					$meta = array_merge($meta, $data);
					if ( !isset($meta['enable']) )
						$meta['enable'] = 'yes';
						
					update_post_meta($post_id, 'ib2_settings', $meta);
					$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true');
					wp_redirect($url);
					exit;
				}
			}
	
			// Populate data for preview to wp transient
			if ( $preview ) {
				$meta = array();
				$meta['enable'] = 'yes';
				if ( isset($_GET['template_id']) && ($template = ib2_get_template($_GET['template_id'])) ) {
					if ( empty($template->metadata) ) {
						// Process template content from file
						$file = IB2_PATH . 'templates/' . $template->name . '.txt';
						if ( !file_exists($file) )
							$file = WP_CONTENT_DIR . '/ib2-templates/' . $template->name . '.txt';
						
						$content = '';
						if ( file_exists($file) ) {
							$content = @file_get_contents($file);
							$wpdb->update("{$wpdb->prefix}ib2_templates", array('metadata' => $content), array('ID' => $template->ID));
						}
						$data = maybe_unserialize($content);
					} else {
						$data = maybe_unserialize($template->metadata);
					}
					
					$meta = array_merge($meta, $data);
						
					set_transient('ib2_preview_settings', $meta, 1800);
				}
			}
			
			// Clean up old history data ...
			delete_post_meta($post_id, 'ib2_history');
			
			// IB2 Editor Action Hook
			do_action('ib2editor', $preview);
			exit;
		}
	}
}

add_action('ib2editor', 'ib2_editor_document', 2, 1);
function ib2_editor_document( $preview = false ) {

@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
if ( !defined( 'WP_ADMIN' ) )
	require_once( dirname( __FILE__ ) . '/admin.php' );

// In case admin-header.php is included in a function.
global $title, $hook_suffix, $current_screen, $wp_locale, $pagenow, $wp_version,
	$update_title, $total_update_count, $parent_file;

// Catch plugins that include admin-header.php before admin.php completes.
if ( empty( $current_screen ) )
	set_current_screen();

get_admin_page_title();
$title = esc_html( strip_tags( $title ) );

if ( is_network_admin() )
	$admin_title = sprintf( __( 'Network Admin: %s' ), esc_html( get_current_site()->site_name ) );
elseif ( is_user_admin() )
	$admin_title = sprintf( __( 'Global Dashboard: %s' ), esc_html( get_current_site()->site_name ) );
else
	$admin_title = get_bloginfo( 'name' );

if ( $admin_title == $title )
	$admin_title = sprintf( __( '%1$s &#8212; WordPress' ), $title );
else
	$admin_title = sprintf( __( '%1$s &lsaquo; %2$s &#8212; WordPress' ), $title, $admin_title );

/**
 * Filter the <title> content for an admin page.
 *
 * @since 3.1.0
 *
 * @param string $admin_title The page title, with extra context added.
 * @param string $title       The original page title.
 */
$admin_title = apply_filters( 'admin_title', $admin_title, $title );

wp_user_settings();

_wp_admin_html_begin();

$options = get_option('ib2_options');
$gfonts_url = ib2_googlefonts_url();
$post_id = (int) $_GET['post'];
if ( $preview ) {
	$data = get_transient('ib2_preview_settings');
	$def_variant = 'a';
	$variant = 'variation' . $def_variant;
} else {
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$def_variant = ib2_get_default_variant($post_id, false);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
}
$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
	$t_id = (int) $_GET['template_id'];
	if ( $template = ib2_get_template($t_id) ) {
		$data = maybe_unserialize($template->metadata);
		$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	}
}

$create_history = false;
$history = get_post_meta($post_id, 'ib2_history', true);
$letter_ver = ( isset($_GET['variant']) ) ? $_GET['variant'] : $def_variant;
if ( empty($history) || $history === FALSE || !is_array($history) ) {
	$create_history = true;
	$history = array();
}

if ( !isset($history[$letter_ver]) ) {
	$create_history = true;
	$history[$letter_ver] = array();
	$new_history = array(
				'content' => $meta,
				'date' => date_i18n("Y-m-d H:i:s")
			);
				
	array_push($history[$letter_ver], $new_history);
}

if ( $create_history ) {
	update_post_meta($post_id, 'ib2_history', $history);
}

$default_css  = '#screen-container { font-family: "Open Sans", sans-serif; font-size: 14px; color:#333; }' . "\n";
$default_css .= '#screen-container a { color: #428bca; }' . "\n";
$default_css .= '#screen-container a:hover, #screen-container a:focus { color: #2a6496; }' . "\n";

$line_height = ( is_array($data) && !empty($meta['line_height']) ) ? $meta['line_height'] : 1.4;
$white_space = ( is_array($data) && !empty($meta['white_space']) ) ? $meta['white_space'] : 18;
$paragraph_css = '.ib2-section-content p { line-height: ' . $line_height . ' !important; margin-bottom: ' . $white_space . 'px !important; }';

$body_css = ( is_array($data) && !empty($meta['css']) ) ? stripslashes($meta['css']) : $default_css;
?>
<title><?php echo $admin_title; ?></title>
<?php
$admin_body_class = preg_replace('/[^a-z0-9_-]+/i', '-', $hook_suffix);
?>
<script type="text/javascript">
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
	pagenow = '<?php echo $current_screen->id; ?>',
	typenow = '<?php echo $current_screen->post_type; ?>',
	adminpage = '<?php echo $admin_body_class; ?>',
	thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
	decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
	isRtl = <?php echo (int) is_rtl(); ?>,
	autosave = <?php echo ( isset($options['autosave']) ? (int) $options['autosave'] : 0); ?>,
	autosave_interval = 180,
	videoData = {},
	carouselData = {};
</script>
<meta name="viewport" id="screen-viewport" content="width=device-width,initial-scale=1.0">

<link rel='stylesheet' id='buttons-css'  href='<?php echo includes_url('css/buttons.min.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='dashicons-css'  href='<?php echo includes_url('css/dashicons.min.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='mediaelement-css'  href='<?php echo includes_url('js/mediaelement/mediaelementplayer.min.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='wp-mediaelement-css'  href='<?php echo includes_url('js/mediaelement/wp-mediaelement.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='media-views-css'  href='<?php echo includes_url('css/media-views.min.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='imgareaselect-css'  href='<?php echo includes_url('js/imgareaselect/imgareaselect.css'); ?>' type='text/css' media='all' />
<link rel='stylesheet' id='editor-buttons-css'  href='<?php echo includes_url('css/editor.min.css'); ?>' type='text/css' media='all' />

<link href="<?php echo $gfonts_url; ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_JS; ?>prettyCheckable/dist/prettyCheckable.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>jquery-ui-1.10.3.custom.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>perfect-scrollbar.css" rel="stylesheet" type="text/css" />
<link href="<?php echo IB2_CSS; ?>ib2editor.css" rel="stylesheet" type="text/css" />

<?php if ( $preview ) : ?>
<style type="text/css">
body {
  padding-top: 0 !important;
}	
</style>
<?php endif; ?>

<style type="text/css" id="editor-body-typo">
<?php echo $body_css; ?>
</style>

<style type="text/css" id="advtyposet">
<?php echo $paragraph_css; ?>
</style>

<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php echo IB2_CSS; ?>jquery.ui.1.10.3.ie.css">
<![endif]-->

<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery-migrate.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>jquery.tubular.1.0.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>prettyCheckable/dist/prettyCheckable.min.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>base64.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>tinymce4/tinymce.min.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>kinetic-v5.1.0.min.js'></script>
<script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/js/feather.js"></script>

<?php echo ib2_script_localize( 'userSettings', array(
		'url' => (string) SITECOOKIEPATH,
		'uid' => (string) get_current_user_id(),
		'time' => (string) time(),
	) ); 
?>

<?php if ( $preview ) : ?>
<script type="text/javascript">
var ib2_popup = 0,
ib2_poptime = '', 
ib2_popid = '',
ib2_slider = 0,
ib2_slider_close = 0,
ib2_attbar = 0,
post_id = <?php echo $post_id; ?>,
webinar_url = '',
powered_by = '<?php echo ( isset($options['enable_powered']) && $options['enable_powered'] == 1 ? 'yes' : 'no' ); ?>',
powered_by_link = '<?php echo ( !empty($options['ib2affurl']) ? esc_url($options['ib2affurl']) : '' ); ?>',
powered_img = '<?php echo IB2_IMG ?>sprites/instabuilder2-poweredby.png';

jQuery(document).ready(function($){
	if ( ib2_attbar == 0 ) {
		jQuery('.ib2-notification-bar').remove();
	}
});
</script>
<?php endif; ?>

<?php if ( !$preview ) : ?>
<script type='text/javascript' src='<?php echo includes_url('js/utils.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/plupload/plupload.full.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/json2.min.js'); ?>'></script>
<?php endif; ?>

<!--[if lt IE 9]>
	<script src="<?php echo IB2_JS; ?>html5shiv.js" type="text/javascript"></script>
	<script src="<?php echo IB2_JS; ?>respond.min.js" type="text/javascript"></script>
<![endif]-->

<?php 
if ( !empty($meta['allcss']) && count($meta['allcss']) > 0 ) {
	foreach ( $meta['allcss'] as $k => $v ) {
?>
<style type="text/css" id="<?php echo stripslashes($k); ?>" class="ib2-element-css">
<?php echo stripslashes($v); ?>
</style>
<?php
	}
}
?>

<?php if ( $preview ) : ?>
<script>
jQuery(document).ready(function($){
	$('a').each(function(){
		$(this).click(function(e){
			alert('Link is disabled on preview.');
			e.preventDefault();
		});
	});
	
	$('form').each(function(){
		$(this).submit(function(e){
			alert('Form submission is disabled on preview.');
			e.preventDefault();
		});
	});	
});
</script>
<?php endif; ?>
</head>

<Body>
<?php if ( !empty($options['fb_appid']) ) : ?>
	
<div id="fb-root"></div>
<script>
	window.fbAsyncInit = function() {
		FB.init({
			appId      : '<?php echo esc_attr($options['fb_appid']); ?>',
			cookie     : true,
			xfbml      : true,
			version    : 'v2.2'
		});
		
		(function($) {
			$('.fb-publish-btn').click(function(e){
				FB.getLoginStatus(function(response) {
					if ( response.status == 'connected' ) {
						publish_to_facebook();
					} else {
						facebook_login();
					}
	
					e.preventDefault();
				});
				e.preventDefault();
			});
			
			var facebook_login = function() {
				FB.login(function(response) {
					if ( response.authResponse ) {
						if ( response.status == 'connected' ) {
							publish_to_facebook();
						}
					}
				}, {scope: 'email,public_profile,manage_pages,publish_pages'});
			};
			
			var publish_to_facebook = function() {
				FB.ui({
  					method: 'pagetab'
				}, function( response ){
					if ( response != null && response.tabs_added != null ) {
    					var fbIds = new Array();
    					var length = response.tabs_added.length;
    					length = (typeof length === 'undefined') ? true : false;
    					
    					if ( length ) {
    						$.each(response.tabs_added, function(id) {
	    						fbIds.push(id);
	        				});
	        		
	        				var data = {
								action: 'ib2_publish_facebook',
								fb_ids: fbIds,
								post_id: $('#ib2-post-id').val()
							};
							
							$.post(ajaxurl, data, function(response){
								if ( response.success ) {
									window.location.href = response.fburl;
								}
							});
    					} else {
    						console.log(response);
    					}
    				} else {
    					console.log(response);
    				}
				});
			}
		})(jQuery);
	};

	(function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) {return;}
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
    
<?php endif; ?>

<?php do_action('ib2editor_main', $preview); ?>

<?php if ( !$preview ) ib2_elements(); ?>
<?php if ( !$preview ) ib2_footer_scripts(); ?>
<?php if ( $preview ) ib2_preview_footer_scripts(); ?>
</Body>
</html>

<?php
}

function ib2_preview_footer_scripts() {
?>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/bootstrap.min.js?ver=3.2.0'></script>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/moment.js?ver=2.8.3'></script>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/moment-timezone-with-data.min.js?ver=2.8.3'></script>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/jquery.countdown.min.js?ver=2.0.4'></script>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/prettyCheckable/dist/prettyCheckable.min.js?ver=4.1.1'></script>
<script type='text/javascript' src='http://localhost/wordpress/wp-content/plugins/instabuilder2/assets/js/instabuilder2.js?ver=1.0.0'></script>
<?php
}

function ib2_footer_scripts() {
	$post_id = ( isset($_GET['post']) ) ? (int) $_GET['post'] : 0;
	$post = get_post($post_id);
	$data = get_post_meta($post_id, 'ib2_settings', true);
	$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variationa';
	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();

	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$main_popup_id = ( isset($meta['popup_id']) ) ? stripslashes($meta['popup_id']) : '';
	require_once ABSPATH . WPINC . '/media-template.php';
	wp_print_media_templates();
	
	$tzstring = get_option('timezone_string');
	$d_timezone = ( $tzstring == '' ) ? 'UTC' : $tzstring;
	
	$def_variant = ib2_get_default_variant($post_id, false);
	$letter = ( isset($_GET['variant']) ) ? $_GET['variant'] : $def_variant;
	$options = get_option('ib2_options');
	$fbappid = ( $options && !empty($options['fb_appid']) ) ? esc_attr($options['fb_appid']) : '';
	
	$webinar_action_url = get_permalink($post_id);
	if ( $post->post_status != 'publish' ) {
		list($permalink, $post_name) = get_sample_permalink($post_id);

		$webinar_action_url = str_replace(array('%pagename%','%postname%'), $post_name, $permalink);
	}
	
	$webinar_action_url = esc_url(add_query_arg('ib2mode', 'webinar_signup', $webinar_action_url));
?>

<img id="ib2-img-src" src="<?php echo IB2_IMG; ?>img-editor.png" style="display:none" />
<img id="ib2-vid-src" src="<?php echo IB2_IMG; ?>vid-placeholder.png" style="display:none" />

<input type="hidden" id="ib2-post-id" value="<?php echo $_GET['post']; ?>" />
<input type="hidden" id="ib2-current-variation" value="<?php echo $letter; ?>" />
<input type="hidden" id="ib2-current-resize" value="" />
<input type="hidden" id="ib2-current-box" value="" />
<input type="hidden" id="ib2-current-text" value="" />
<input type="hidden" id="ib2-current-image" value="" />
<input type="hidden" id="ib2-current-hline" value="" />
<input type="hidden" id="ib2-current-vline" value="" />
<input type="hidden" id="ib2-current-spacer" value="" />
<input type="hidden" id="ib2-current-button" value="" />
<input type="hidden" id="ib2-current-video" value="" />
<input type="hidden" id="ib2-current-optin" value="" />
<input type="hidden" id="ib2-current-share" value="" />
<input type="hidden" id="ib2-current-countdown" value="" />
<input type="hidden" id="ib2-current-panel" value="" />
<input type="hidden" id="ib2-current-popup" value="" />
<input type="hidden" id="ib2-current-quiz" value="" />
<input type="hidden" id="ib2-current-menu" value="" />
<input type="hidden" id="ib2-current-code" value="" />
<input type="hidden" id="ib2-current-comment" value="" />
<input type="hidden" id="ib2-current-tabs" value="" />
<input type="hidden" id="ib2-current-hotspot" value="" />
<input type="hidden" id="ib2-current-date" value="" />
<input type="hidden" id="ib2-current-slider" value="" />
<input type="hidden" id="ib2-current-question" value="1" />
<input type="hidden" id="ib2-current-screen" value="desktop" />
<input type="hidden" id="ib2-imed-id" value="" />
<input type="hidden" id="ib2-imed-type" value="" />
<input type="hidden" id="ib2-selected-element" value="" />
<input type="hidden" id="ib2-aviary-key" value="<?php echo ( !empty($options['aviary']) ? trim(esc_attr($options['aviary'])) : '' ); ?>" />
<input type="hidden" id="ib2-admin-url" value="<?php echo admin_url('post.php'); ?>" />
<input type="hidden" id="ib2-main-popup-id" value="<?php echo $main_popup_id; ?>" />
<input type="hidden" id="ib2-img-folder" value="<?php echo IB2_IMG; ?>" />
<input type="hidden" id="ib2-player" value="<?php echo esc_url(add_query_arg('mode', 'ib2player', get_permalink($post_id))); ?>" />
<input type="hidden" id="ib2-video-resize" value="" />
<input type="hidden" id="ib2-button-mode" value="normal" />
<input type="hidden" id="ib2-background-element" value="" />
<input type="hidden" id="ib2-isearch-type" value="" />
<input type="hidden" id="ib2-isearch-term" value="" />
<input type="hidden" id="ib2-isearch-page" value="0" />
<input type="hidden" id="ib2-isearch-pages" value="0" />
<input type="hidden" id="ib2-powered-enable" value="<?php echo ( (isset($options['enable_powered']) && $options['enable_powered'] == 1) ? 'yes' : 'no'); ?>" />
<input type="hidden" id="ib2-powered-link" value="<?php echo ( !empty($options['ib2affurl']) ? esc_url($options['ib2affurl']) : 'http://instabuilder.com'); ?>" />
<input type="hidden" id="ib2-permalink" value="<?php echo get_permalink($_GET['post']); ?>" />
<input type="hidden" id="ib2-webinar-action" value="<?php echo $webinar_action_url; ?>" />
<input type="hidden" id="ib2-fbappid" value="<?php echo $fbappid; ?>" />
<input type="hidden" id="default-page-width" value="<?php echo ( is_array($data) && !empty($meta['page_width']) ? esc_attr($meta['page_width']) : '960' ); ?>" />
<input type="hidden" id="default-font-size" value="<?php echo ( is_array($data) && !empty($meta['font_size']) ? esc_attr($meta['font_size']) : '14' ); ?>" />
<input type="hidden" id="default-time-zone" value="<?php echo $d_timezone; ?>" />

<div id="remote-copy-temp" style="display: none"></div>

<div id="editor-panel-inside" style="display:none">
	<div id="editor-panel-inside-content">
		<?php
		ib2_page_settings();
		ib2_exitsplash_settings();
		ib2_popup_settings();
		ib2_quiz_settings();
		ib2_element_settings();
		ib2_graphics_settings();
		ib2_premade_background();
		ib2_premade_buttons();
		ib2_rclick_settings();
		ib2_scripts_settings();
		ib2_split_settings();
		ib2_attentionbar_settings();
		ib2_welcomegate_settings();
		ib2_slider_settings();
		ib2_favicon_settings();
		?>
		<p style="margin:16px 0"><span id="back-panel" style="display:none"><button type="button" class="btn btn-danger open-panel" data-settings="none">&larr; Back</button>&nbsp;&nbsp;</span><button type="button" class="btn btn-warning hide-side-panel">&rarr; Hide This Panel</button></p>
	</div>
</div>

<?php wp_nonce_field('ib2editornonce','_ib2editornonce'); ?>

<link rel='stylesheet' id='thickbox-css'  href='<?php echo includes_url('js/thickbox/thickbox.css'); ?>' type='text/css' media='all' />

<script src="<?php echo IB2_JS; ?>jquery-ui.min.js"></script>
<script src="<?php echo IB2_JS; ?>bootstrap.min.js" type="text/javascript"></script>

<script type="text/javascript">
<?php 
if ( !empty($meta['video_data']) ) {
	foreach ( $meta['video_data'] as $k => $v ) {
		$v_embed = ( isset($v['embed']) ? stripslashes($v['embed']) : '');
		$v_mp4 = stripslashes($v['hosted']['mp4']);
		$v_ogg = stripslashes($v['hosted']['ogg']);
		$v_webm = stripslashes($v['hosted']['webm']);
		$v_splash = stripslashes($v['hosted']['splash']);
		$v_hostedcode = stripslashes($v['hosted']['code']);
		$v_youtubeurl = stripslashes($v['youtube']['url']);
		$v_youtubecode = stripslashes($v['youtube']['code']);
		$v_vimeourl = stripslashes($v['vimeo']['url']);
		$v_vimeocode = stripslashes($v['vimeo']['code']);
		
		$v_embed = is_base64($v_embed) ? $v_embed : base64_encode($v_embed);
		$v_mp4 = is_base64($v_mp4) ? $v_mp4 : base64_encode(esc_url($v_mp4));
		$v_ogg = is_base64($v_ogg) ? $v_ogg : base64_encode(esc_url($v_ogg));
		$v_webm = is_base64($v_webm) ? $v_webm : base64_encode(esc_url($v_webm));
		$v_splash = is_base64($v_splash) ? $v_splash : base64_encode(esc_url($v_splash));
		$v_hostedcode = is_base64($v_hostedcode) ? $v_hostedcode : base64_encode($v_hostedcode);
		$v_youtubeurl = is_base64($v_youtubeurl) ? $v_youtubeurl : base64_encode(esc_url($v_youtubeurl));
		$v_youtubecode = is_base64($v_youtubecode) ? $v_youtubecode : base64_encode($v_youtubecode);
		$v_vimeourl = is_base64($v_vimeourl) ? $v_vimeourl : base64_encode(esc_url($v_vimeourl));
		$v_vimeocode = is_base64($v_vimeocode) ? $v_vimeocode : base64_encode($v_vimeocode);
?>

videoData["<?php echo $k; ?>"] = {};
videoData["<?php echo $k; ?>"].hosted = {};
videoData["<?php echo $k; ?>"].youtube = {};
videoData["<?php echo $k; ?>"].vimeo = {};
videoData["<?php echo $k; ?>"].type = '<?php echo stripslashes($v['type']); ?>';
videoData["<?php echo $k; ?>"].embed = '<?php echo $v_embed; ?>';
videoData["<?php echo $k; ?>"].autoplay = '<?php echo $v['autoplay']; ?>';
videoData["<?php echo $k; ?>"].controls = '<?php echo $v['controls']; ?>';
videoData["<?php echo $k; ?>"].hosted.mp4 = '<?php echo $v_mp4; ?>';
videoData["<?php echo $k; ?>"].hosted.ogg = '<?php echo $v_ogg; ?>';
videoData["<?php echo $k; ?>"].hosted.webm = '<?php echo $v_webm; ?>';
videoData["<?php echo $k; ?>"].hosted.splash = '<?php echo $v_splash; ?>';
videoData["<?php echo $k; ?>"].hosted.code = '<?php echo $v_hostedcode; ?>';
videoData["<?php echo $k; ?>"].youtube.url = '<?php echo $v_youtubeurl; ?>';
videoData["<?php echo $k; ?>"].youtube.id = '<?php echo stripslashes($v['youtube']['id']); ?>';
videoData["<?php echo $k; ?>"].youtube.code = '<?php echo $v_youtubecode; ?>';
videoData["<?php echo $k; ?>"].vimeo.url = '<?php echo $v_vimeourl; ?>';
videoData["<?php echo $k; ?>"].vimeo.id = '<?php echo stripslashes($v['vimeo']['id']); ?>';
videoData["<?php echo $k; ?>"].vimeo.code = '<?php echo $v_vimeocode; ?>';

<?php
	}
}

if ( !empty($meta['carousel_data']) ) {
	foreach ( $meta['carousel_data'] as $k => $v ) {
?>
		carouselData["<?php echo $k; ?>"] = {};
<?php
		foreach ( $v as $j => $z ) {
?>
		carouselData["<?php echo $k; ?>"]["<?php echo $j; ?>"] = {};
		carouselData["<?php echo $k; ?>"]["<?php echo $j; ?>"].imageurl = '<?php echo stripslashes($z['imageurl']); ?>';
		carouselData["<?php echo $k; ?>"]["<?php echo $j; ?>"].title = '<?php echo stripslashes($z['title']); ?>';
		carouselData["<?php echo $k; ?>"]["<?php echo $j; ?>"].desturl = '<?php echo stripslashes($z['desturl']); ?>';
<?php
		}
	}
}
?>
</script>

<script type='text/javascript' src='<?php echo includes_url('js/underscore.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/shortcode.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/backbone.min.js'); ?>'></script>

<?php echo ib2_script_localize( '_wpUtilSettings', array(
		'ajax' => array(
			'url' => admin_url( 'admin-ajax.php', 'relative' ),
		),
	) ); 
?>
<script type='text/javascript' src='<?php echo includes_url('js/wp-util.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/wp-backbone.min.js'); ?>'></script>
<?php echo ib2_script_localize( '_wpMediaModelsL10n', array(
		'settings' => array(
			'ajaxurl' => admin_url( 'admin-ajax.php', 'relative' ),
			'post' => array( 'id' => $post_id ),
		),
	) ); 
?>
<script type='text/javascript' src='<?php echo includes_url('js/media-models.min.js'); ?>'></script>
<?php
	// error message for both plupload and swfupload
	$uploader_l10n = array(
		'queue_limit_exceeded' => __('You have attempted to queue too many files.'),
		'file_exceeds_size_limit' => __('%s exceeds the maximum upload size for this site.'),
		'zero_byte_file' => __('This file is empty. Please try another.'),
		'invalid_filetype' => __('This file type is not allowed. Please try another.'),
		'not_an_image' => __('This file is not an image. Please try another.'),
		'image_memory_exceeded' => __('Memory exceeded. Please try another smaller file.'),
		'image_dimensions_exceeded' => __('This is larger than the maximum size. Please try another.'),
		'default_error' => __('An error occurred in the upload. Please try again later.'),
		'missing_upload_url' => __('There was a configuration error. Please contact the server administrator.'),
		'upload_limit_exceeded' => __('You may only upload 1 file.'),
		'http_error' => __('HTTP error.'),
		'upload_failed' => __('Upload failed.'),
		'big_upload_failed' => __('Please try uploading this file with the %1$sbrowser uploader%2$s.'),
		'big_upload_queued' => __('%s exceeds the maximum upload size for the multi-file uploader when used in your browser.'),
		'io_error' => __('IO error.'),
		'security_error' => __('Security error.'),
		'file_cancelled' => __('File canceled.'),
		'upload_stopped' => __('Upload stopped.'),
		'dismiss' => __('Dismiss'),
		'crunching' => __('Crunching&hellip;'),
		'deleted' => __('moved to the trash.'),
		'error_uploading' => __('&#8220;%s&#8221; has failed to upload.')
	);
	
	echo ib2_script_localize( 'pluploadL10n', $uploader_l10n );
	
	global $wp_scripts;

	$data = $wp_scripts->get_data( 'wp-plupload', 'data' );
	if ( $data && false !== strpos( $data, '_wpPluploadSettings' ) )
		return;

	$max_upload_size = wp_max_upload_size();

	$defaults = array(
		'runtimes'            => 'html5,flash,silverlight,html4',
		'file_data_name'      => 'async-upload', // key passed to $_FILE.
		'url'                 => admin_url( 'async-upload.php', 'relative' ),
		'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
		'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
		'filters' => array(
			'max_file_size'   => $max_upload_size . 'b',
		),
	);

	// Multi-file uploading doesn't currently work in iOS Safari,
	// single-file allows the built-in camera to be used as source for images
	if ( wp_is_mobile() )
		$defaults['multi_selection'] = false;

	$defaults = apply_filters( 'plupload_default_settings', $defaults );

	$params = array(
		'action' => 'upload-attachment',
	);

	$params = apply_filters( 'plupload_default_params', $params );
	$params['_wpnonce'] = wp_create_nonce( 'media-form' );
	$defaults['multipart_params'] = $params;

	$settings = array(
		'defaults' => $defaults,
		'browser'  => array(
			'mobile'    => wp_is_mobile(),
			'supported' => _device_can_upload(),
		),
		'limitExceeded' => is_multisite() && ! is_upload_space_available()
	);
		
	echo ib2_script_localize( '_wpPluploadSettings', $settings );
?>
<script type='text/javascript' src='<?php echo includes_url('js/plupload/wp-plupload.min.js'); ?>'></script>
<?php
	$mejsL10n = array(
		'language' => get_bloginfo( 'language' ),
		'strings'  => array(
			'Close'               => __( 'Close' ),
			'Fullscreen'          => __( 'Fullscreen' ),
			'Download File'       => __( 'Download File' ),
			'Download Video'      => __( 'Download Video' ),
			'Play/Pause'          => __( 'Play/Pause' ),
			'Mute Toggle'         => __( 'Mute Toggle' ),
			'None'                => __( 'None' ),
			'Turn off Fullscreen' => __( 'Turn off Fullscreen' ),
			'Go Fullscreen'       => __( 'Go Fullscreen' ),
			'Unmute'              => __( 'Unmute' ),
			'Mute'                => __( 'Mute' ),
			'Captions/Subtitles'  => __( 'Captions/Subtitles' )
		),
	);
	
	echo ib2_script_localize( 'mejsL10n', $mejsL10n );
	echo ib2_script_localize( '_wpmejsSettings', array(
		'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
	) );
?>
<script type='text/javascript' src='<?php echo includes_url('js/mediaelement/mediaelement-and-player.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/mediaelement/wp-mediaelement.js'); ?>'></script>
<?php ib2_media_settings( $post_id ); ?>
<script type='text/javascript' src='<?php echo includes_url('js/media-views.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/media-editor.min.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/media-audiovideo.min.js'); ?>'></script>


<script type='text/javascript' src='<?php echo IB2_JS; ?>iris.min.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>insta-color-picker.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>perfect-scrollbar.min.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>moment.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>moment-timezone-with-data.min.js'></script>
<script type='text/javascript' src='<?php echo IB2_JS; ?>jquery.countdown.min.js'></script>

<script type='text/javascript' src='<?php echo IB2_JS; ?>ib2editor.js?ver=<?php echo time(); ?>'></script>

<?php 

do_action('ib2_print_footer_scripts');

}

add_action('ib2editor_main', 'ib2_editor_topbar', 5, 1);
function ib2_editor_topbar( $preview = false ) {
	if ( $preview ) return false;
	
	$post_id = (int) $_GET['post'];
	$post = get_post($post_id);
	$data = get_post_meta($post_id, 'ib2_settings', true);
	if ( ( !is_array($data) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		$template = ib2_get_template($t_id);
		$data = maybe_unserialize($template->metadata);
	}
	
	$def_variant = ib2_get_default_variant($post_id, false);
	$letters = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,1,2,3,4,5,6,7,8,9';
	$letters = explode(",", $letters);
		
	$variant = ( isset($_GET['variant']) ) ? $_GET['variant'] : $def_variant;
	$all_variants = ib2_get_variants($post_id);
	$variants = array();
	if ( is_array($data) ) {
  		foreach ( $letters as $l ) {
  			if ( $l == $variant || !isset($data['variation' . $l]) || !ib2_variant_exists($post_id, $l) ) continue;
			$variants[] = $l;
  		}	
  	}
	
	$options = get_option('ib2_options');
	
	$fb_alert = ( empty($options['fb_appid']) || empty($options['fb_secret']) ) ? ' onclick="alert(\'Please integrate IB 2.0 and Facebook app (both app ID and secret key must be inserted) to use this feature.\nTo do so, please go to WP Dashboard >> InstaBuilder 2.0 >> Settings.\'); return false;"' : ' class="fb-publish-btn"';
?>
<nav class="navbar-inverse navbar-default navbar-fixed-top" role="navigation">
	<div class="container-fluid">
	    <div class="navbar-header">
		      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ib2editor-navbar">
			        <span class="sr-only">Toggle navigation</span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
			        <span class="icon-bar"></span>
		      </button>
		      <a class="navbar-brand" href="#"><img src="<?php echo IB2_IMG; ?>ib-logo-medium.png" border="0" /></a>
	    </div>

	    <div class="collapse navbar-collapse" id="ib2editor-navbar">
	    	<div class="btn-group navbar-left">
	    		<?php if ( $post->post_status == 'publish' ) : ?>
	    		<a role="button" href="<?php echo get_permalink($post->ID); ?>" target="_blank" id="ib2-visit-url" class="btn btn-success btn-sm navbar-btn">Visit Page</a>
	    		<?php endif; ?>
	    		<button type="button" class="ib2-change-permalink btn btn-success btn-sm navbar-btn">Change Permalink</button>
				<button type="button" class="btn btn-success btn-sm navbar-btn">
					<span id="ib2-current-var">Variation <strong><?php echo strtoupper($variant); ?></strong></span>
				</button>
				<button type="button" class="btn btn-success btn-sm navbar-btn dropdown-toggle" data-toggle="dropdown">
    				<span class="caret"></span>
    				<span class="sr-only">Toggle Dropdown</span>
  				</button>
  				<ul class="dropdown-menu" role="menu">
  					<?php if ( count($variants) > 0 ) {
  						foreach ( $variants as $v ) {
  							$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true&variant=' . $v);
  							echo '<li><a href="' . $url . '">Switch To Variation ' . strtoupper($v) . '</a></li>' . "\n";
  						}
						
						echo '<li class="divider"></li>' . "\n";
						
  					} ?>
  					<li><a href="#" class="open-panel" data-settings="split">Split Test Settings</a></li>
    				<li class="divider"></li>
    				<li><a href="#" id="new-split-variant">Create New Variation</a></li>
  				</ul>
			</div>
			<?php if ( count($all_variants) > 1 ) : ?>
			<p class="navbar-text navbar-left">
				<a href="<?php echo admin_url('post.php?post_id=' . $post_id . '&action=ib2_delete_variation&variant=' . $variant); ?>" id="del-current-variant" style="color:#cc0000 !important" title="Delete this variation"><i class="fa fa-times fa-lg"></i></a>
			</p>
			<?php endif; ?>
			<p class="navbar-text navbar-left hidden" id="autosave-status">Saving...</p>
			<ul class="nav navbar-nav navbar-right">
				<li class="disabled"><a href="#" id="ib2-undo"><i class="fa fa-rotate-left fa-2x"></i></a></li>
				<li class="disabled"><a href="#" id="ib2-redo"><i class="fa fa-rotate-right fa-2x"></i></a></li>
				<li><a href="<?php echo admin_url('post.php?ib2preview=' . $post_id . '&variant=' . $variant); ?>" target="_blank" alt="Preview Page" title="Preview Page"><i class="fa fa-eye fa-2x"></i></a></li>
				<li class="dropdown">
			          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-gear fa-2x"></i> <span class="caret"></span></a>
			          <ul class="dropdown-menu" role="menu">
				            <li><a href="#" class="open-panel" data-settings="page">Page Settings</a></li>
				            <li><a href="#" class="open-panel" data-settings="favicon">Favicon</a></li>
				            <li><a href="#" class="open-panel" data-settings="attention">Attention Bar</a></li>
				            <li><a href="#" class="open-panel" data-settings="popup">PopUp Settings</a></li>
				            <li><a href="#" class="open-panel" data-settings="slider">Bottom Slider</a></li>			           
				            <li><a href="#" class="open-panel" data-settings="exitsplash">Exit Splash</a></li>
				            <li><a href="#" class="open-panel" data-settings="wgate">Gateway Page</a></li>
				            <li><a href="#" class="open-panel" data-settings="rclick">Disable Right-Click</a></li>
				            <li><a href="#" class="open-panel" data-settings="scripts">Scripts/Codes</a></li>
				            <li class="divider"></li>
				            <li><a href="#" class="open-panel" data-settings="graphics">Graphics</a></li>
				            <li class="divider"></li>
				            <li><a href="#" class="remote-copy-element">Remote Copy Element</a></li>
				            <li><a href="#" class="remote-paste-element">Remote Paste Element</a></li>
				            <li class="divider"></li>
				            <li><a href="#" class="change-template">Change Template</a></li>
			          </ul>
		        </li>
		        <li><a href="#" class="ib2-page-save" data-status="any"><i class="fa fa-save fa-2x"></i></a></li>
					
		        <?php if ( $post->post_status != 'publish' ) : ?>
		        	<li id="ib2-publish-btn"><a href="#" class="ib2-page-save" data-status="publish"><i class="fa fa-globe fa-2x"></i> PUBLISH</a></li>
		        <?php endif; ?>
			        
		        <li class="dropdown" title="More">
			          <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="padding-top:25px"><span class="caret"></span></a>
			          <ul class="dropdown-menu" role="menu">
			          		<li><a href="<?php echo admin_url('post.php'); ?>?ib2mode=reset_page&variant=<?php echo $variant; ?>&post_id=<?php echo $post_id; ?>" id="restore_prev_save">Restore Previous Save</a></li>
			          		
			          		<li class="divider"></li>
				            
				            <li><a href="#"<?php echo $fb_alert; ?>>Publish To Facebook</a></li>
				            <li><a href="<?php echo esc_url(add_query_arg('ib2mode', 'save_html', get_permalink($post_id))); ?>">Save HTML (single file)</a></li>
				            <li><a href="<?php echo esc_url(add_query_arg('ib2mode', 'save_html_rich', get_permalink($post_id))); ?>">Save HTML + Graphics</a></li>
				            <li class="divider"></li>
				            <li><a href="<?php echo admin_url('admin.php?page=ib2-dashboard&ib2action=export_file&post_id=' . $post_id); ?>">Export</a></li>
				            <li><a href="#" class="import-file-dialog">Import</a></li>
				            <li class="divider"></li>
				            <li><a href="#" class="save-as-tmpl">Save as template</a></li>
				            <li class="divider"></li>
				            <li><a href="<?php echo admin_url('admin.php?page=ib2-dashboard'); ?>" class="ib2-editor-close">Exit Editor</a></li> 
			          </ul>
		        </li>
			</ul>
	    </div><!-- /.navbar-collapse -->
  	</div><!-- /.container-fluid -->
</nav>
<?php
}

function ib2_save_history( $data ) {
	global $wpdb;
	
	if ( !is_array($data) || empty($data) ) return false;
	
	$data_filtered = array();
	$accepted_keys = array('post_id', 'variant', 'content', 'date');
	foreach ( $data as $k => $v ) {
		if ( !in_array($k, $accepted_keys) ) continue;
		$data_filtered[$k] = $v;
	}
	
	if ( !isset($data_filtered['post_id']) || !isset($data_filtered['variant']) ) return false;
	
	$wpdb->insert("{$wpdb->prefix}ib2_histories", $data);
	$history_id = $wpdb->insert_id;
	
	// let's clear some old data...
	$oldtimes = strtotime("7 days ago");
	$post_id = (int) $data_filtered['post_id'];
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->prefix}ib2_histories WHERE `post_id` = %d AND `date` < %d", $post_id, $oldtimes));
	if ( $chk > 5 ) {
		$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ib2_histories WHERE `post_id` = %d AND `date` < %d", $post_id, $oldtimes));
	}
	
	return $history_id;
}

function ib2_get_history( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$history = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}ib2_histories WHERE `post_id` = %d AND `variant` = %s ORDER BY `date` DESC LIMIT 1", $post_id, $variant));
	if ( !$history ) {
		$old_data = get_post_meta($post_id, 'ib2_history', true);
		if ( is_array($old_data) && !empty($old_data[$variant]) && !empty($old_data[$variant]['content']) ) {
			$history = new stdClass;
			$history->post_id = $post_id;
			$history->variant = $variant;
			$history->content = $old_data[$variant]['content'];
			$history->date = strtotime($old_data[$variant]['date']);
		}
	}
	return $history;
}

add_action('admin_init', 'ib2_restore_previous');
function ib2_restore_previous() {
	if ( isset($_GET['ib2mode']) && $_GET['ib2mode'] == 'reset_page' ) {
		global $wpdb;
		
		$post_id = (int) $_GET['post_id'];
		$variant = $_GET['variant'];
		$meta = get_post_meta($post_id, 'ib2_settings', true);
		if ( !is_array($meta) )
			$meta = array();
		
		$history = ib2_get_history($post_id, $variant);
		if ( $history ) {
			$meta['variation' . $variant] = maybe_unserialize($history->content);
			update_post_meta($post_id, 'ib2_settings', $meta);
			
			if ( isset($history->ID) ) {
				$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}ib2_histories WHERE `ID` = %d", $history->ID));
			} else {
				$old_data = get_post_meta($post_id, 'ib2_history', true);
				if ( isset($old_data[$variant]) ) {
					unset($old_data[$variant]);
					update_post_meta($post_id, 'ib2_history', $old_data);
				}
			}
		}
		
		$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true&variant=' . $variant);
		wp_redirect($url);
		exit;
	}
}

add_action('ib2editor_main', 'ib2_editor_body', 6, 1);
function ib2_editor_body( $preview = false ) {
	if ( $preview ) {
		$data = get_transient('ib2_preview_settings');
		$def_variant = 'a';
		$variant = 'variation' . $def_variant;
	} else {
		$post_id = (int) $_GET['post'];
		$data = get_post_meta($post_id, 'ib2_settings', true);
		$def_variant = ib2_get_default_variant($post_id, false);
		$variant = ( isset($_GET['variant']) ) ? 'variation' . $_GET['variant'] : 'variation' . $def_variant;
	}

	$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
	if ( ( !is_array($data) || empty($meta) ) && isset($_GET['template_id']) ) {
		$t_id = (int) $_GET['template_id'];
		if ( $template = ib2_get_template($t_id) ) {
			$data = maybe_unserialize($template->metadata);
			$meta = ( isset($data[$variant]) ) ? $data[$variant] : array();
		}
	}
	
	$slider_display = ( isset($meta['slider']) && $meta['slider'] == 1 ) ? '' : ' style="display:none"'; 
	
	$content = ( is_array($data) && !empty($meta['content']) ) ? do_shortcode(stripslashes($meta['content'])) : '';
	$content_style = ( is_array($data) && !empty($meta['content_style']) ) ? ' style="' . str_replace('"', "'", stripslashes($meta['content_style'])) . '"' : '';
	
	$content = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $content);
	$content = str_replace('{%sc_open%}', '[', $content);
	$content = str_replace('{%sc_close%}', ']', $content);
	$content_style = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $content_style);
	
	// TEMP CODE
	$shav_url = 'http://shavuotkreatifsolusi.com/ibtheme/wp-content/uploads/2016/09/';
	$content = str_replace($shav_url, IB2_URL . 'assets/img/templates/sept-', $content);
	$content_style = str_replace($shav_url, IB2_URL . 'assets/img/templates/sept-', $content_style);
	// END OF TEMP CODE

	$slider_content = ( is_array($data) && !empty($meta['slider_content']) ) ? stripslashes($meta['slider_content']) : '';
	$slider_content = ( is_base64($slider_content) ) ? base64_decode($slider_content) : $slider_content;
	$slider_content = do_shortcode($slider_content);
	$slider_content = str_replace('{%IB2_PLUGIN_DIR%}', IB2_URL, $slider_content);
	$slider_content = str_replace('{%sc_open%}', '[', $slider_content);
	$slider_content = str_replace('{%sc_close%}', ']', $slider_content);
	
	if ( empty($meta) && empty($content) ) {
		$content = '<div id="ib2_el_section1" class="container ib2-section-el" style="width:960px; max-width:100%; margin-left: auto; margin-right:auto;" data-el="section" data-border-type="single" data-img-mode="upload">';
		$content .= '<div class="el-content el-cols" style="background-color:#FFFFFF; padding:15px 35px">';
		$content .= '<div id="ib2_el_section1-box" class="ib2-section-content" style="width:100%; min-height:400px;"></div>';
		$content .= '</div>';
		$content .= '</div>';
	}

	if ( $preview ) {
		$content = str_replace('otopelay', 'autoplay', $content);
		$content = str_replace('ib2-video-responsive-class', 'embed-responsive embed-responsive-16by9', $content);
	}
?>
<div class="container-fluid" id="editor-container">
	<div id="editor-row" class="row-fluid">
		<div id="main-editor" class="col-md-12">
			<div id="screen-container" class="col-md-12 screen-desktop-edit"<?php echo $content_style; ?>>
				<?php echo $content; ?>
				
				<div id="ib2-bottom-slider"<?php echo $slider_display; ?>>
					<?php if ( !empty($slider_content) ) {
						echo $slider_content; 
					} else { ?>
					<div id="ib2-bottom-slider-main" class="ib2-wsection-el ib2-section-el ib2-slider-el" data-el="wsection" data-animation="none" data-delay="none" data-border-type="single" data-img-mode="upload">
						<div class="el-content" style="background-color:#CCC; padding: 20px 0px 10px; opacity: 1; border-color: rgb(168, 157, 157) rgb(51, 51, 51) rgb(51, 51, 51); border-width: 2px 0px 0px; box-shadow: 0px 0px 8px 3px #c2c2c2; border-top: 2px solid rgb(168, 157, 157)">
							<div class="el-content-inner container" style="margin:0 auto;">
								<div class="el-cols" style="max-width:100%; width:100%;">
									<div id="ib2-bottom-slider-main-box" class="ib2-section-content" style="min-height:50px; max-width:100%; margin:0 auto;">
										<div id="ib2_el_slidertext" class="ib2-content-el ib2-text-el" data-el="text" data-shadow="none" data-animation="none" style="position: relative;">
											<p>This is the bottom slider area. You can edit this text and also insert any element here. This is a good place if you want to put an opt-in form or a scarcity countdown.</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
				</div>
				
			</div><!-- ./screen-container -->
		</div><!-- ./main-editor -->
		
		<?php if ( !$preview ) : ?><div id="editor-panel" class="col-md-3"></div><?php endif; ?>
	</div>
</div>
<?php
}

function ib2_premade_background() {
?>
<div class="settings-premade editor-panel-content" style="display:none">
	<h3>Background Pattern</h3>
	
	<p class="help-block">Click on the image to insert</p>
  	<div id="graphics-container">
	<?php ib2_backgrounds_html(); ?>
	</div>
</div>
<?php
}

function ib2_premade_buttons() {
?>
<div class="settings-premade-button editor-panel-content" style="display:none">
	<h3>Pre-Made Buttons</h3>
	
	<p class="help-block">Click on the image to insert</p>
  	<div id="graphics-container">
	<?php ib2_backgrounds_html('button'); ?>
	</div>
</div>
<?php
}

function ib2_font_selectors( $id, $default_value ) {
?>
	<select class="form-control" id="<?php echo $id; ?>">
		<optgroup label="Basic Fonts">
			<?php 
			
			$fonts = ib2_normalfonts();
			foreach ( $fonts as $k => $v ) {
				$selected = ( $v == $default_value ) ? ' selected="selected"' : '';
				$v = str_replace('"', "'", $v);
				echo "<option value=\"{$v}\"{$selected}>{$k}</option>\n";
			}
			?>
		</optgroup>
		<optgroup label="Google Fonts">
			<?php
			$fonts = ib2_googlefonts();
			foreach ( $fonts as $k => $v ) {
				$font = ( preg_match('/\s/', $k) ) ? "'" . $k . "'" : $k;
				$value = "{$font},{$v}";
				$selected = ( $value == $default_value ) ? ' selected="selected"' : '';
				echo "<option value=\"{$value}\"{$selected}>{$k}</option>\n";
			}
			?>
		</optgroup>
	</select>
<?php
}


function ib2_script_localize( $object_name, $l10n ) {
	if ( is_array($l10n) && isset($l10n['l10n_print_after']) ) { // back compat, preserve the code in 'l10n_print_after' if present
		$after = $l10n['l10n_print_after'];
		unset($l10n['l10n_print_after']);
	}

	foreach ( (array) $l10n as $key => $value ) {
		if ( !is_scalar($value) )
			continue;

		$l10n[$key] = html_entity_decode( (string) $value, ENT_QUOTES, 'UTF-8');
	}

	$script = "var {$object_name} = " . json_encode($l10n) . ';';

	if ( !empty($after) )
		$script .= "\n$after;";

	$output  = '<script type="text/javascript">' . "\n";
	$output .= '/* <![CDATA[ */' . "\n";
	$output .= $script . "\n";
	$output .= '/* ]]> */' . "\n";
	$output .= '</script>' . "\n";
	
	return $output;
}

function ib2_tinymce( $content, $editor_id, $settings = array() ) {
	if ( ! class_exists( '_IB_Tinymce' ) )
		require( IB2_ADMIN . 'tinymce.php' );

	_IB_Tinymce::editor($content, $editor_id, $settings);
}

function ib2_timezone_select( $id ) {
?>
	<select class="form-control" id="<?php echo $id; ?>">
		<optgroup label="UTC">
			<option value="UTC">UTC</option>
		</optgroup>
		<?php
		$tzstring = get_option('timezone_string');
		
		$all = timezone_identifiers_list();
		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific');
		
		$i = 0;
		$zonen = array();
	    foreach ( $all as $zone ) {
			$zone = explode('/', $zone);
			
			if ( !in_array( $zone[0], $continents ) ) continue;
			
			$zonen[$i]['continent'] = isset($zone[0]) ? $zone[0] : '';
	      	$zonen[$i]['city'] = isset($zone[1]) ? $zone[1] : '';
	      	$zonen[$i]['subcity'] = isset($zone[2]) ? $zone[2] : '';
	      	$i++;
	    }
	    
		asort($zonen);
		$structure = '';
		foreach ( $zonen as $zone ) {
			extract($zone);
			
			if ( !isset($selectcontinent) ) {
				$structure .= '<optgroup label="' . $continent . '">'; // continent
			} else if( $selectcontinent != $continent ) {
				$structure .= '</optgroup><optgroup label="' . $continent . '">'; // continent
			}
			
	        if ( !empty($city) ) {
	        	if ( !empty($subcity) )
	            	$city = $city . '/' . $subcity;

				$value = "{$continent}/{$city}";
				$selected = ( $tzstring == $value ) ? ' selected="selected"' : '';
	          	$structure .= '<option value="' . $value . '"' . $selected . '>' . str_replace('_', ' ', $city) . '</option>'; //Timezone
	        } else {
	        	$selected = ( $tzstring == $continent ) ? ' selected="selected"' : '';
	          	$structure .= '<option value="' . $continent . '"' . $selected . '>' . $continent . '</option>'; //Timezone
	        }

			$selectcontinent = $continent;

		}
		$structure .= '</optgroup>';
					
		unset($zonen);
		
		echo $structure;
		?>
	</select>
<?php
}

function ib2_icon_lists() {
	return array(
		'fa-arrows',
		'fa-cc',
		'fa-area-chart',
		'fa-copyright',
		'fa-paypal',
		'fa-wifi',
		'fa-pie-chart',
		'fa-trash',
		'fa-birthday-cake',
		'fa-newspaper-o',
		'fa-at',
		'fa-shopping-cart',
		'fa-gift',
		'fa-money',
		'fa-plus',
		'fa-plus-square',
		'fa-bar-chart',
		'fa-bolt',
		'fa-bookmark-o',
		'fa-camera',
		'fa-search',
		'fa-external-link',
		'fa-cloud-download',
		'fa-download',
		'fa-upload',
		'fa-save',
		'fa-reply',
		'fa-share',
		'fa-retweet',
		'fa-refresh',
		'fa-rss-square',
		'fa-check-square',
		'fa-check-circle',
		'fa-envelope-o',
		'fa-envelope',
		'fa-paperclip',
		'fa-inbox',
		'fa-gear',
		'fa-exclamation-triangle',
		'fa-sitemap',
		'fa-tasks',
		'fa-power-off',
		'fa-question',
		'fa-key',
		'fa-unlock',
		'fa-unlock-alt',
		'fa-home',
		'fa-headphones',
		'fa-globe',
		'fa-info',
		'fa-sign-in',
		'fa-sign-out',
		'fa-arrow-left',
		'fa-arrow-right',
		'fa-hand-o-up',
		'fa-hand-o-down',
		'fa-life-buoy',
		'fa-phone',
		'fa-file-archive-o',
		'fa-file-pdf-o',
		'fa-file-excel-o',
		'fa-file-word-o',
		'fa-file-image-o',
		'fa-file-text-o',
		'fa-file-video-o',
		'fa-file-audio-o',
		'fa-usd',
		'fa-gbp',
		'fa-euro',
		'fa-jpy',
		'fa-btc'
	);
}

function ib2_icon_html( $class = '' ) {
	$icons = ib2_icon_lists();
	echo '<div class="icon-lists">';
	echo '<a href="#" class="ib2-icon-holder ' . $class . ' ib2-no-icon selected" title="Remove Icon"><i class="fa fa-ban fa-fw"></i></a>';
	foreach ( $icons as $icon ) {
		echo '<a href="#" class="ib2-icon-holder ' . $class . '" data-icon="' . $icon . '"><i class="fa ' . $icon . ' fa-fw"></i></a>';
	}
	echo '<div class="clearfix"></div>';
	echo '</div>';
}

function ib2_populate_fields( $fields, $classes = '', $styles = '' ) {
	$output = '';
	if ( is_array($fields) && !empty($fields) ) {
		$data = '';
		$styles = !empty($styles) ? ' style="' . $styles . '"' : '';
		foreach ( $fields as $field ) {
			switch ( $field['type'] ) {
				case 'select':
					$data .= '<div class="ib2-field-group form-group">';
					$multiple = ( isset($field['multiple']) && $field['multiple'] == 'yes' ) ? ' multiple' : '';
					if ( !empty($field['label']) )
						$data .= '<label>' . $field['label'] . '</label>';
	
					$data .= '<select name="' . $field['name'] . '" class="form-control ib2-opt-field' . $classes . '"' . $styles . $multiple . '>';

					if ( !empty($field['options']) ) {
						foreach ( $field['options'] as $k => $v ) {
							$data .= '<option value="' . $k . '">' . $v . '</option>';
						}
					}
					$data .= '</select>';
					$data .= '</div>';
					break;
				
				case 'checkbox':
					$data .= '<div class="ib2-field-group checkbox">';
					$data .= '<label><input type="checkbox" name="' . $field['name'] . '" value="' . ( isset($field['value']) ? $field['value'] : 1 ) . '" class="ib2-opt-field" > <span class"label-txt">' . $field['label'] . '</span></label>' ;
					$data .= '</div>';
					break;
					
				case 'radio':
					$data .= '<div class="ib2-field-group radio">';
					$data .= '<label><input type="radio" name="' . $field['name'] . '" value="' . ( isset($field['value']) ? $field['value'] : 1 ) . '" class="ib2-opt-field" > <span class"label-txt">' . $field['label'] . '</span></label>' ;
					$data .= '</div>';
					break;
					
				case 'textarea':
					$data .= '<div class="ib2-field-group form-group">';
					$data .= '<textarea name="' . $field['name'] . '" rows="3" class="form-control ib2-opt-field' . $classes . '" placeholder="' . $field['label'] . '"' . $styles . '></textarea>';
					$data .= '</div>';
					break;
					
				case 'text':
				case 'email':
				default:
				
					$ftype = ( $field['type'] == 'email' ) ? 'email' : 'text';
					$data .= '<div class="ib2-field-group form-group">';
					$data .= '<input type="' . $ftype . '" name="' . $field['name'] . '" class="form-control ib2-opt-field' . $classes . '" placeholder="' . $field['label'] . '"' . $styles . ' />';
					$data .= '</div>';
					break;
			}
		}
		$output = $data;
	}
	return $output;
}
function ib2_extract_optin( &$response, $code ) {
	require_once( IB2_INC . 'simple_html_dom.php');
	$code = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $code );
	$code = strip_tags($code, '<form><input><button><textarea><select><label><option>');
	$code = preg_replace('/[\r\n\t ]+/', ' ', $code);
	
	$code = ( !seems_utf8($code) && function_exists('mb_convert_encoding') ) ? mb_convert_encoding($code, "UTF-8") : $code;
	
	$html = new simple_html_dom();
	$html->load($code);
	
	$f = array('input', 'select', 'textarea', 'option');
	foreach ( $html->find('form') as $form ) {
		$response['action'] = $form->action;
		$response['method'] = ( !empty($form->method) ) ? $form->method : 'get';
		foreach ( $form->children() as $child ) {
			if ( in_array($child->tag, $f) ) {
				$skip = false;
				if ( isset($child->style) ) {
					if ( stristr($child->style, 'display:none') || stristr($child->style, 'display: none') ) {
						if ( $child->tag == 'input' ) {
							$response['hiddens'][$child->name] = $child->value;
						}
						$skip = true;
					}
				}
				
				if ( $child->tag == 'input' && isset($child->type) ) {
					if ( $child->type == 'hidden' 
						|| $child->type == 'submit' 
						|| $child->type == 'button'
						|| $child->type == 'image' 
					) {
						if ( $child->type == 'hidden' ) {
							$response['hiddens'][$child->name] = $child->value;
						}
						$skip = true;
					}
				}
				
				// Skip to the next if the field isn't what we want...
				if ( $skip ) continue;
				
				$type = $child->tag;
				if ( isset($child->type) )
					$type = $child->type;
				
				$tempArray = array('name' => $child->name, 'type' => $type);
				if ( isset($child->value) )
					$tempArray['value'] = $child->value;
				
				if ( $child->tag == 'select' ) {
					if ( isset($child->multiple) )
						$tempArray['multiple'] = 'yes';
					
					foreach ( $child->find('option') as $opt ) {
						$tempArray['options'][$opt->value] = $opt->innertext;
					}
				}
				
				$label = '';
				if ( isset($child->placeholder) )
					$label = $child->placeholder;
				
				if ( empty($label) && $child->prev_sibling() && $child->prev_sibling()->tag == 'label' )
					$label = $child->prev_sibling()->innertext;
				
				if ( empty($label) && isset($child->value) )
					$label = $child->value;
				
				// Labeling the field based on the field's name...
				if ( empty($label) )
					$label = ( stristr( $child->name, 'mail') || stristr( $child->name, 'from') ) ? 'Email Address' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'name') ) ? 'Your Name' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'address') || stristr( $child->name, 'street') ) ? 'Street/Address' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'city') ) ? 'City' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'state') || stristr( $child->name, 'province') ) ? 'State/Province' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'country') ) ? 'Country' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'title') ) ? 'Title' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'phone') || stristr( $child->name, 'mobile') ) ? 'Phone #' : '';
				
				if ( empty($label) )
					$label = ( stristr( $child->name, 'zip') || stristr( $child->name, 'postal') ) ? 'Zip Code' : '';
				
				$tempArray['label'] = str_replace(':', '', $label);
				
				$response['fields'][] = $tempArray;
				
				unset($tempArray);
			}
		}
	}

	$html->clear();
}

add_action('ib2_print_footer_scripts', 'ib2_fields_manager', 45);
function ib2_fields_manager() {
?>
	<div id="ib2-fields-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2FieldsModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Manage Opt-In Fields</h4>
			</div>
		    <div class="modal-body">
		    	<p id="fields-loader" style="display:none; text-align:center; margin-top:40px"><img src="<?php echo IB2_IMG; ?>ajax-loader.gif" border="0" /></p>
		    	<form id="manage-opt-fields" name="manage-opt-fields" method="post">
		    	<table id="fields-sortable" class="table">
		    		<thead>
		    			<tr><th>::</th></th><th>Field Name/ID</th><th>Label</th><th>Icon</th><th>Required</th><th>Is Email?</th><th>Hide</th><th>Delete</th></tr>
		    		</thead>
		    		<tbody>
		    			<tr>
		    				<td class="optin-field-handle" style="cursor:move">::</td>
		    				<td><input type="hidden" class="type-field" value="text" /><div class="form-group"><input type="text" class="form-control name-field" value="optin-name" /></div></td>
		    				<td><div class="form-group"><input type="text" class="form-control label-field" value="First Name" /></div></td>
		    				<td>
		    					<div class="form-group">
		    						<select class="form-control icon-field">
										<option value="none">None</option>
										<option value="mail">Mail icon</option>
										<option value="user">User icon</option>
										<option value="home">Home icon</option>
										<option value="phone">Phone icon</option>
										<option value="mobile">Mobile Phone icon</option>
										<option value="search">Search icon</option>
										<option value="clock">Clock icon</option>
									</select>
		    					</div>
		    				</td>
		    				<td><input type="checkbox" value="1" class="req-check" checked="checked" /></td>
		    				<td><input type="checkbox" value="1" class="email-check" /></td>
		    				<td><input type="checkbox" value="1" class="hide-check" /></td>
		    				<td><button class="btn btn-danger btn-xs remove-optin-field">Del</button></td>
		    			</tr>
		    		</tbody>
		    	</table>
		    	</form>
		    	<button class="btn btn-success btn-sm" id="add-optin-field">+ Add Field</button>&nbsp;
		    </div>
		    <div class="modal-footer">
		    	<button type="button" id="btn-save-opt-fields" class="btn btn-primary">Save</button>
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
}

add_action('ib2_print_footer_scripts', 'ib2_images_search', 50);
function ib2_images_search() {
?>
	<div id="ib2-imgsearch-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2ImgSearchModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Search Image</h4>
			</div>
		    <div class="modal-body">
		    	<form class="form-inline" name="ib2-img-search" id="ib2-img-search">
		    		<div class="form-group">
    					<label class="sr-only" for="image-query">Search image</label>
    					<input type="text" name="i_q" class="form-control" id="image-query" placeholder="Search image...">
  					</div>
  					<button type="submit" class="btn btn-default do-isearch">GO</button>
		    	</form>
		    	<div id="isearch-results">
		    		<div class="clearfix"></div>
		    	</div>
		    	<p id="isearch-loader" style="display:none; text-align:center; margin-top:30px"><img src="<?php echo IB2_IMG; ?>ajax-loader.gif" border="0" /></p>
		    	<button type="button" class="btn btn-default btn-lg" id="isearch-load-more" style="display: none; width:100%">Load More Images</button>
		    	<p id="isearch-downloader" style="display:none; text-align:center; margin-top:30px"><img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" />
		    		<br /><span class="muted">Downloading image... Please do NOT close this pop-up.</span></p>
		    	
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php
}

function ib2_flickr_search( $apikey, $term, $page ) {
	$fl_url = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key={$apikey}&text=" . urlencode($term) . "&license=7&safe_search=2&content_type=4&per_page=5&page={$page}&format=json&nojsoncallback=1";
	$result = wp_remote_get($fl_url, array('timeout' => 300, 'httpversion' => '1.1', 'sslverify' => false));
	if ( is_wp_error($result) || !isset($result['body']) )
		return false;
	
	return json_decode($result['body']);
}

function ib2_flickr_image( $apikey, $photoid ) {
	$fl_url = "https://api.flickr.com/services/rest/?method=flickr.photos.getSizes&api_key={$apikey}&photo_id={$photoid}&format=json&nojsoncallback=1";
	$result = wp_remote_get($fl_url, array('timeout' => 300, 'httpversion' => '1.1', 'sslverify' => false));
	if ( is_wp_error($result) || !isset($result['body']) )
		return false;
	
	return json_decode($result['body']);
}

function ib2_pixabay_images( $username, $apikey, $term, $page ) {
	//$px_url = "http://pixabay.com/api/?username={$username}&key={$apikey}&search_term=" . urlencode($term) . "&image_type=photo&page={$page}&per_page=10&safesearch=true";
	$px_url = "http://pixabay.com/api/?key={$apikey}&q=" . urlencode($term) . "&image_type=photo&page={$page}&per_page=10&safesearch=false";
	$result = wp_remote_get($px_url, array('timeout' => 300, 'httpversion' => '1.1'));
	if ( is_wp_error($result) || !isset($result['body']) )
		return false;
	
	return json_decode($result['body']);
}

function ib2_sample_permalink( $id, $title = null, $name = null ) {
	$post = get_post($id);
	if ( !$post )
		return array( '', '' );

	$ptype = get_post_type_object($post->post_type);

	$original_status = $post->post_status;
	$original_date = $post->post_date;
	$original_name = $post->post_name;

	// Hack: get_permalink() would return ugly permalink for drafts, so we will fake that our post is published.
	if ( in_array( $post->post_status, array( 'draft', 'pending' ) ) ) {
		$post->post_status = 'publish';
		$post->post_name = sanitize_title($post->post_name ? $post->post_name : $post->post_title, $post->ID);
	}

	// If the user wants to set a new name -- override the current one
	// Note: if empty name is supplied -- use the title instead, see #6072
	if ( !is_null($name) )
		$post->post_name = sanitize_title($name ? $name : $title, $post->ID);

	if ( !function_exists('wp_unique_post_slug') )
		require_once ABSPATH . WPINC . '/post.php';
	
	$post->post_name = wp_unique_post_slug($post->post_name, $post->ID, $post->post_status, $post->post_type, $post->post_parent);

	$post->filter = 'sample';

	$permalink = get_permalink($post, true);

	// Replace custom post_type Token with generic pagename token for ease of use.
	$permalink = str_replace("%$post->post_type%", '%pagename%', $permalink);

	// Handle page hierarchy
	if ( $ptype->hierarchical ) {
		$uri = get_page_uri($post);
		$uri = untrailingslashit($uri);
		$uri = strrev( stristr( strrev( $uri ), '/' ) );
		$uri = untrailingslashit($uri);

		/** This filter is documented in wp-admin/edit-tag-form.php */
		$uri = apply_filters( 'editable_slug', $uri );
		if ( !empty($uri) )
			$uri .= '/';
		$permalink = str_replace('%pagename%', "{$uri}%pagename%", $permalink);
	}

	/** This filter is documented in wp-admin/edit-tag-form.php */
	$permalink = array( $permalink, apply_filters( 'editable_slug', $post->post_name ) );
	$post->post_status = $original_status;
	$post->post_date = $original_date;
	$post->post_name = $original_name;
	unset($post->filter);

	return $permalink;
}

function ib2_graphic_folders() {
	$folders = array();
	$folder_path = IB2_PATH . 'assets/img/graphics/';
	if ( is_dir($folder_path) ) :
		if ( $tmp_dir = opendir($folder_path) ) :
			while ( ( $folder = readdir($tmp_dir) ) !== false ) :
				if ( $folder == '.' || $folder == '..' ) continue;
				
				// check if it's a folder
				$temp = $folder_path . $folder . '/';
				if ( !is_dir($temp) ) continue;
				
				$folders[] = $folder;
			endwhile;
		endif;
	endif;
	
	return $folders;
}

function ib2_get_graphics( $folder = 'bullets' ) {
	$graphics = array();
	$graphic_path = IB2_PATH . 'assets/img/graphics/' . $folder . '/';
	if ( is_dir($graphic_path) ) :
		if ( $tmp_dir = opendir($graphic_path) ) :
			while ( ( $graphic = readdir($tmp_dir) ) !== false ) :
				if ( $graphic == '.' || $graphic == '..' ) continue;
				
				// check for extension
				$allowed = array('jpg', 'gif', 'bmp', 'png', 'jpeg');
				$parts = explode(".", $graphic);
				$ext = strtolower(end($parts));
				
				if ( !in_array($ext, $allowed) ) continue;
				
				$graphics[] = IB2_IMG . 'graphics/' . $folder . '/' . $graphic;
			endwhile;
		endif;
	endif;
	
	return $graphics;
}

function ib2_graphics_html( $folder = 'bullets', $type = 'small' ) {
	$graphics = ib2_get_graphics( $folder );
	if ( empty($graphics) ) return '';

	echo '<div id="graphics-list-' . $folder . '" class="graphics-list graphics-list-' . $type . '">' . "\n";
	foreach ( $graphics as $graphic ) {
		echo '<div class="ib2-graphic-item">' . "\n";
		echo '<img class="ib2-img-item img-responsive" src="' . $graphic . '" border="0" data-element="image" />' . "\n";
		echo '</div>' . "\n";
	}
	echo '</div>' . "\n";
}

function ib2_get_backgrounds( $type = 'background' ) {
	$backgrounds = array();
	$background_path = ( $type == 'button' ) ? IB2_PATH . 'assets/img/graphics/buttons/' : IB2_PATH . 'assets/img/backgrounds/';
	if ( is_dir($background_path) ) :
		if ( $tmp_dir = opendir($background_path) ) :
			while ( ( $background = readdir($tmp_dir) ) !== false ) :
				if ( $background == '.' || $background == '..' ) continue;
				
				// check for extension
				$allowed = array('jpg', 'gif', 'bmp', 'png', 'jpeg');
				$parts = explode(".", $background);
				$ext = strtolower(end($parts));
				
				if ( !in_array($ext, $allowed) ) continue;
				
				$backgrounds[] = ( $type == 'button' ) ? IB2_IMG . 'graphics/buttons/' . $background : IB2_IMG . 'backgrounds/' . $background;
			endwhile;
		endif;
	endif;
	
	return $backgrounds;
}

function ib2_backgrounds_html( $type = 'background' ) {
	$backgrounds = ib2_get_backgrounds($type);
	if ( empty($backgrounds) ) return '';
	
	$class = ( $type == 'button' ) ? 'ib2-button-cols' : 'ib2-background-cols';
	$height = ( $type == 'button' ) ? '' : ' style="height:33px"';
	echo '<div class="backgrounds-list">' . "\n";
	echo '<div class="' . $class . '">' . "\n";
	echo '<div class="ib2-background-item ib2-background-none" style="text-align:center;color:#cc0000">' . "\n";
	echo '<i class="fa fa-ban fa-2x"></i>' . "\n";
	echo '</div>' . "\n";
	echo '</div>' . "\n";
	foreach ( $backgrounds as $background ) {
		echo '<div class="' . $class . '">' . "\n";
			echo '<div class="ib2-background-item"' . $height . '>' . "\n";
				echo '<img src="' . $background . '" border="0" />' . "\n";
			echo '</div>' . "\n";
		echo '</div>' . "\n";
	}
	echo '<div class="clearfix"></div>' . "\n";
	echo '</div>' . "\n";
}

add_action('admin_init', 'ib2_preview', 1);
function ib2_preview() {
	if ( isset($_GET['ib2preview']) ) {
		$post_id = (int) $_GET['ib2preview'];
		if ( !$post = get_post($post_id) )
			return;
		
		$def_variant = ib2_get_default_variant($post_id, false);
		$variant = isset($_GET['variant']) ? $_GET['variant'] : $def_variant;
			
?>
<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo esc_attr($post->post_title); ?></title>

<link href="<?php echo IB2_CSS; ?>font-awesome.min.css" rel="stylesheet" type="text/css" />

<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery.js'); ?>'></script>
<script type='text/javascript' src='<?php echo includes_url('js/jquery/jquery-migrate.min.js'); ?>'></script>

<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
<![endif]-->

<style type="text/css">
	html { overflow: auto; }
	body { background-color:#666; }
	html, body, iframe { margin: 0px; padding: 0px; height: 100%; border: none; }
	iframe { display: block; width: 100%; border: none; overflow-y: auto; overflow-x: hidden; margin:0 auto; }
</style>
<style type="text/css">
#preview-bar {
	position:fixed;
	top:50px;
	right:20px;
	display:block;
	min-width:200px;
	background-color:#545454;
	z-index:99;
	padding:8px 15px;
	-webkit-border-radius: 10px;
	border-radius: 10px;
	-webkit-box-shadow: 0 0 10px 4px rgba(0,0,0,0.2);
	box-shadow: 0 0 10px 4px rgba(0,0,0,0.2);
	border:1px solid #3a3a3a;
}
.preview-mobile {
	margin-top:20px;
	border:1px solid #212121;
}
.devices-view {
	padding-right:15px;
	border-right:1px solid #3a3a3a;	
}
.switch-variants {
	padding:0 15px;
	border-left:1px solid #666;	
}
a.switch-view {
	color:#FFF;
	text-decoration: none;
	display: inline-block;
	text-align:center;
	padding:5px 8px;
	font-size:14px;
	background-color:#3a3a3a;
	-webkit-border-radius: 4px;
	border-radius: 4px;
	vertical-align: middle;
}
select.form-control {
	width: auto;
	border:1px solid #3a3a3a;
	background-color:#666;
	color:#F5F5F5;
	padding:5px 3px;
	-webkit-border-radius: 3px;
	border-radius: 3px;
}
</style>
</head>

<body>
<iframe name='previewFrame' id='previewFrame' src="<?php echo esc_url(add_query_arg(array('variant' => $variant, 'preview' => 'true'), get_permalink($post_id))); ?>" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="100%" scrolling="auto">
<p>Your browser does not support iframes.</p>
</iframe>
<div id="preview-bar">
	<div class="devices-view" style="float:left;">
		<a href="#" class="switch-view" data-type="desktop"><i class="fa fa-desktop"></i></a>
		<a href="#" class="switch-view" data-type="tablet"><i class="fa fa-tablet"></i></a>
		<a href="#" class="switch-view" data-type="tablet-landscape"><i class="fa fa-tablet fa-rotate-270"></i></a>
		<a href="#" class="switch-view" data-type="mobile"><i class="fa fa-mobile"></i></a>
		<a href="#" class="switch-view" data-type="mobile-landscape"><i class="fa fa-mobile fa-rotate-270"></i></a>
	</div>
	<div class="switch-variants" style="float:left;">
		<select class="form-control" id="variant-switcher">
			<?php
			if ( $vars = ib2_get_variants($post_id) ) {
				foreach ( $vars as $var ) {
					$selected = ( $_GET['variant'] == $var->variant ) ? ' selected="selected"' : '';
					echo '<option value="' . $var->variant . '"' . $selected . '>Variation ' . strtoupper($var->variant) . '</option>';
				}
			}
			?>
		</select>
	</div>
	<div style="clear:left"></div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#variant-switcher').change(function(e){
			var opt = $("option:selected", this).val(),
			url = '<?php echo admin_url('post.php?ib2preview=' . $post_id ); ?>&variant=' + opt;
			
			window.location.href = url;
		});
		
		$('.switch-view').each(function(){
			$(this).click(function(e){
				var $this = $(this), type = $this.data('type');
				if ( type == 'tablet' ) {
					$('#previewFrame').css('width', '767px');
					$('#previewFrame').css('height', '1024px');
					$('#previewFrame').addClass('preview-mobile');
				} else if ( type == 'tablet-landscape' ) {
					$('#previewFrame').css('width', '1024px');
					$('#previewFrame').css('height', '767px');
					$('#previewFrame').addClass('preview-mobile');
				} else if ( type == 'mobile' ) {
					$('#previewFrame').css('width', '320px');
					$('#previewFrame').css('height', '480px');
					$('#previewFrame').addClass('preview-mobile');
				} else if ( type == 'mobile-landscape' ) {
					$('#previewFrame').css('width', '480px');
					$('#previewFrame').css('height', '320px');
					$('#previewFrame').addClass('preview-mobile');
				} else {
					$('#previewFrame').css('width', '100%');
					$('#previewFrame').css('height', '100%');
					$('#previewFrame').removeClass('preview-mobile');
				}
				e.preventDefault();
			});
		});
	});
</script>
</body>
</html>
<?php
		exit;
	}
}

add_action('ib2_print_footer_scripts', 'ib2_footer_html', 99);
function ib2_footer_html() {
	$post_id = (int) $_GET['post'];
	$post= get_post($post_id);
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
	
	$optincodes = ( isset($meta['optincodes']) ) ? stripslashes($meta['optincodes']) : '';
	
	$post_type = $post->post_type;
	$post_type_object = get_post_type_object($post_type);
	
	$sample_permalink = ib2_sample_permalink($post->ID);
	
	$new_title = null;
	$new_slug = null;
	
	list($permalink, $post_name) = get_sample_permalink($post->ID, $new_title, $new_slug);
	
	if ( 'publish' == get_post_status( $post ) ) {
		$title = 'Click to edit this part of the permalink';
	} else {
		$title = 'Temporary permalink. Click to edit this part.';
	}
	
	$edit_permalink_btn = '';
	if ( false === strpos($permalink, '%postname%') && false === strpos($permalink, '%pagename%') ) {
		$display_link = $permalink;
		if ( '' == get_option( 'permalink_structure' ) && current_user_can( 'manage_options' ) && !( 'page' == get_option('show_on_front') && $id == get_option('page_on_front') ) )
			$edit_permalink_btn = '<a href="options-permalink.php" class="btn btn-primary btn-sm" target="_blank">Change Permalinks</a>';
			
		$return = apply_filters( 'get_sample_permalink_html', $return, $id, $new_title, $new_slug );
	} else {
		if ( function_exists('mb_strlen') ) {
			if ( mb_strlen($post_name) > 30 ) {
				$post_name_abridged = mb_substr($post_name, 0, 14). '&hellip;' . mb_substr($post_name, -14);
			} else {
				$post_name_abridged = $post_name;
			}
		} else {
			if ( strlen($post_name) > 30 ) {
				$post_name_abridged = substr($post_name, 0, 14). '&hellip;' . substr($post_name, -14);
			} else {
				$post_name_abridged = $post_name;
			}
		}
		
		$post_name_html = '<span id="editable-post-name">' . $post_name_abridged . '</span>';
		$display_link = str_replace(array('%pagename%','%postname%'), $post_name_html, $permalink);
		
		$edit_permalink_btn = '<button type="button" class="btn btn-primary btn-xs ib2-edit-permalink">Edit Permalink</button>';
	}
	
	$options = get_option('ib2_options');
?>
<!-- Default Menu -->
<?php wp_page_menu('menu_class=ib2-default-nav&show_home=1&include=2,3,4,5,6'); ?>

<!-- Permalink HTML -->
<div id="ib2-generator-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2GeneratorModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
				<h4 class="modal-title">Generate Disclaimer, Privacy Policy &amp; TOS Content</h4>
			</div>
		    <div class="modal-body">
		    	<div class="alert alert-danger" role="alert">
		    		<strong>WARNING:</strong> The current text content will be replaced by the generated content.
				</div>
				<div class="form-group">
			    	<label for="gen-content-type">Content Type</label>
			    	<select class="form-control" id="gen-content-type">
						<option value="disclaimer">Disclaimer</option>
						<option value="policy">Privacy Policy</option>
						<option value="tos">Terms Of Service</option>
					</select>
			  	</div>
			  	<hr>
			  	<div class="form-group">
			    	<label for="gen-business-name">Your Business/Company Name (required)</label>
			    	<input type="text" class="form-control" id="gen-business-name" placeholder="e.g. mycompanyname" value="<?php echo get_bloginfo('name'); ?>">
			  	</div>
			  	<div class="policy-only-field">
			  		<hr>
				  	<div class="form-group">
				    	<label for="gen-business-email">Business Email Address (required)</label>
				    	<input type="text" class="form-control" id="gen-business-email" value="<?php echo get_bloginfo('admin_email'); ?>">
				  	</div>
				</div>
			  	<div class="tos-only-field policy-only-field">
			  		<hr>
				  	<div class="form-group">
				    	<label for="gen-business-addr">Business/Company Address (including street, city, state &amp; zip - required)</label>
				    	<input type="text" class="form-control" id="gen-business-addr">
				  	</div>
				</div>
				<div class="tos-only-field">
					<hr>
				  	<div class="form-group">
				    	<label for="gen-business-country">Country</label>
				    	<select class="form-control" id="gen-business-country">
				    		<?php foreach ( ib2_countries() as $k => $v ) {
				    			$selected = ( $k == 'US' ) ? ' selected="selected"' : '';
								echo '<option value="' . $v . '"' . $selected . '>' . $v . '</option>';
							} ?>
						</select>
				  	</div>
			  	</div>
			  	<hr>
			  	<p style="text-align:right"><button id="generate-content" type="button" class="btn btn-primary btn-lg" data-loading-text="Generating...">Generate Now</button></p>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default generate-close-button" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Permalink HTML -->
<div id="ib2-permalink-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2PermalinkModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Change Permalink</h4>
			</div>
		    <div class="modal-body">
		    	<div id="permalink-alert" class="alert alert-success alert-dismissible" role="alert" style="display:none">
					<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
					Success! Please don't forget to <strong>save the page</strong> to permanently change the permalink.
				</div>
			
				<div class="form-group permalink-display">
					<p class="form-control-static"><?php echo $display_link; ?></p>
				</div>
				<input type="hidden" id="current-slug" value="<?php echo $post_name; ?>">
				<div class="form-group permalink-form" style="display:none">
					<div class="input-group">
						<span id="slug_url" class="input-group-addon"><?php echo str_replace(array('%pagename%/','%postname%/','%pagename%','%postname%'), '', $permalink); ?></span>
  						<input type="text" class="form-control" id="ib2-new-slug" value="<?php echo $post_name; ?>" >
  						<span class="input-group-btn">
    						<button class="btn btn-default" id="save-new-permalink" type="button">OK</button>
  						</span>
					</div><!-- /input-group -->
				</div>
				<?php echo $edit_permalink_btn; ?>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Save as Template HTML -->
<div id="ib2-template-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2TemplateModal" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Save as Template</h4>
			</div>
		    <div class="modal-body">
		    	<div class="form-group">
			    	<label for="template-name">Template Name</label>
			    	<input type="text" class="form-control" id="template-name" placeholder="e.g. mytemplate (Letters and numbers only. No spaces.)">
			  		<br /><span class="text-danger"></span>
			  	</div>
		    	<div class="form-group">
			    	<label for="template-type">Page Type</label>
			    	<select class="form-control" id="template-type">
						<option value="sales">Sales Page</option>
						<option value="optin">Squeeze Page</option>
						<option value="launch">Launch Page</option>
						<option value="webinar">Webinar</option>
						<option value="coming">Coming Soon</option>
						<option value="others">Others</option>
					</select>
			  	</div>
			  	<div class="form-group">
			  		<label for="template-subtype">Page Sub-Type</label>
			  		<select id="template-subtype" class="form-control">
						<option value="">-- Sub-Type --</option>
						<option value="textsales">Text Sales Page</option>
						<option value="videosales">Video Sales Page</option>
						<option value="hybridsales">Hybrid Sales Page</option>
						<option value="otosales">OTO Sales Page</option>
					</select>
			  	</div>
			  	<div class="form-group">
			    	<label for="template-tags">Tags (optional)</label>
			    	<input type="text" class="form-control" id="template-tags" placeholder="e.g. yellow, corporate, simple, clean">
			  	</div>
			  	<div class="form-group">
			    	<label for="template-thumb">Thumbnail</label><br />
			    	<div id="template-thumb-preview" style="width:120px"></div>
			    	<input type="hidden" id="template-thumb-url" value="" />
			    	<div style="margin-top:10px"><button id="template-thumb" type="button" class="btn btn-default btn-sm">Upload</button>&nbsp;&nbsp;<button id="remove-template-thumb" type="button" class="btn btn-danger btn-sm" style="display:none">Remove</button></div>
			  	</div>
			  	<p id="tmpl-duplicate-msg" style="display:none; color:#cc0000">Template with the same name and type is already exists. Do you want to overwrite?</p>
			  	<p style="text-align:right"><button id="template-save" type="button" class="btn btn-primary btn-lg" data-loading-text="Saving..." data-status="template">Save Template</button></p>
		    	<input type="hidden" id="save-template-id" value="0" />
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- New Variation -->
<div id="ib2-variation-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2VariationModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Create New Variation</h4>
			</div>
		    <div class="modal-body">
		    	<div class="form-group">
			    	<label for="variant-type">Creation Mode</label>
			    	<select class="form-control" id="variant-type">
						<option value="duplicate">Duplicate Current Variation</option>
						<option value="template">Use New Template</option>
						<option value="scratch">Create From Scratch</option>
					</select>
			  	</div>
				
				<div id="ib2-templates" class="ib2-tmpls" style="display:none;">
					<hr />
					<ul class="nav nav-pills new-variant-type">
  						<li class="active"><a href="#" class="ib2-tmpl-type" data-type="sales">Sales Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="optin">Squeeze Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="launch">Launch Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="webinar">Webinar Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="coming">Coming Soon Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="others">Others</a></li>
					</ul>
					
					<hr>
					<form id="template-search-form" method="post" class="form-inline">
						<div class="form-group">
						<select id="ib2-tmpl-subtype" class="form-control">
							<option value="">-- Sub-Type --</option>
							<option value="textsqueeze">Text Squeeze</option>
							<option value="videosqueeze">Video Squeeze</option>
							<option value="minisqueeze">Mini Squeeze</option>
							<option value="2stepsoptin">2 Steps Opt-In</option>
							<option value="3stepsoptin">3 Steps Opt-In</option>
							<option value="textsales">Text Sales Page</option>
							<option value="videosales">Video Sales Page</option>
							<option value="hybridsales">Hybrid Sales Page</option>
							<option value="otosales">OTO Sales Page</option>
							<option value="webinarsignup">Webinar Sign-Up</option>
							<option value="webinarthanks">Webinar Thank You</option>
							<option value="download">Download Page</option>
							<option value="confirmation">Confimation Page</option>
							<option value="thankyou">Thank You Page</option>
						</select>
						</div>
						<div class="form-group">
							<input type="text" id="ib2-tmpl-tags" class="form-control" placeholder="e.g. enter keywords here" />
						</div>
						<button type="submit" class="btn btn-default" id="search-templates">GO</button>
					</form>
					
					<div class="ib2-templates-area">
						<h3>Sales Page Templates</h3>
						<p style="display:none; margin-top:40px; text-align:center" class="ib2-template-loader">
							<img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" /><br />
							<em>Loading...</em>
						</p>
						<div class="ib2-templates-content">
							<?php ib2_get_templates_html('type=sales'); ?>
						</div>
						<div style="clear:left"></div>
					</div>
				</div>

			  	<p style="text-align:right" id="non-templates-area"><button id="variant-create" type="button" class="btn btn-primary btn-lg" data-status="variant" data-mode="duplicate">Create Now</button></p>
		    	
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Import Modal -->
<div id="ib2-import-file" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2ImportFile" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Import Page From File</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-danger"><strong>Warning!</strong> Importing a page will override the current content &amp; settings.</div>
		    	<form method="post" action="<?php admin_url('admin.php?page=ib2-dashboard'); ?>" enctype="multipart/form-data">
					<input type="hidden" name="ib2action" value="import_file" />
					<input type="hidden" name="import_post_id" value="<?php echo $post_id; ?>" />
					<div class="form-group">
					    <label for="import_from_file">Upload IB 2.0 File To Import</label>
					    <input type="file" name="import" id="import_from_file">
					    <p class="help-block">Click "Browse" to select IB 2.0 landing page file.</p>
					</div>
					<button type="submit" id="import_now" class="btn btn-lg btn-success">Import Now</button>
				</form>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Change Template -->
<div id="ib2-changetemplate-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="ib2ChangeTemplateModal" aria-hidden="true">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
				<h4 class="modal-title">Change Template</h4>
			</div>
		    <div class="modal-body">
		    	<div class="alert alert-danger" role="alert">
		    		<strong>WARNING:</strong> The current page content and configuration will be lost if you change this page template, and this action CANNOT be undone.
				</div>
				<div class="ib2-tmpls">
					<hr />
					<ul class="nav nav-pills change-template-type">
  						<li class="active"><a href="#" class="ib2-tmpl-type" data-type="sales">Sales Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="optin">Squeeze Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="launch">Launch Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="webinar">Webinar Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="coming">Coming Soon Pages</a></li>
  						<li><a href="#" class="ib2-tmpl-type" data-type="others">Others</a></li>
					</ul>
					
					<hr>
					<!-- <div class="alert alert-info"><strong>Want more templates?</strong> Visit our marketplace to <a href="http://marketplace.instabuilder.com" target="_blank" class="alert-link alert-info-link">download</a> new and fresh templates</div> //-->
					<form id="template-search-form2" method="post" class="form-inline">
						<div class="form-group">
						<select id="ib2-tmpl-subtype2" class="form-control">
							<option value="">-- Sub-Type --</option>
							<option value="textsqueeze">Text Squeeze</option>
							<option value="videosqueeze">Video Squeeze</option>
							<option value="minisqueeze">Mini Squeeze</option>
							<option value="2stepsoptin">2 Steps Opt-In</option>
							<option value="3stepsoptin">3 Steps Opt-In</option>
							<option value="textsales">Text Sales Page</option>
							<option value="videosales">Video Sales Page</option>
							<option value="hybridsales">Hybrid Sales Page</option>
							<option value="otosales">OTO Sales Page</option>
							<option value="webinarsignup">Webinar Sign-Up</option>
							<option value="webinarthanks">Webinar Thank You</option>
							<option value="download">Download Page</option>
							<option value="confirmation">Confimation Page</option>
							<option value="thankyou">Thank You Page</option>
						</select>
						</div>
						<div class="form-group">
							<input type="text" id="ib2-tmpl-tags2" class="form-control" placeholder="e.g. enter keywords here" />
						</div>
						<button type="submit" class="btn btn-default" id="search-templates2">GO</button>
					</form>
					
					<div class="ib2-templates-area">
						<h3>Sales Page Templates</h3>
						<p style="display:none; margin-top:40px; text-align:center" class="ib2-template-loader">
							<img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" /><br />
							<em>Loading...</em>
						</p>
						<div class="ib2-templates-content">
							<?php ib2_get_templates_html('type=sales'); ?>
						</div>
						<div style="clear:left"></div>
					</div>
				</div>
		    </div>
		    <div class="modal-footer">
			    <button type="button" class="btn btn-default" data-dismiss="modal" aria-hidden="true">Close</button>
		    </div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="ib2-content-placeholder" style="display:none"></div>
<div id="optin-code-placeholder" style="display:none"><?php echo html_entity_decode($optincodes); ?></div>

<div id="ib2-image-editor" style="display:none">
	<h3>Image Editor</h3>
	<hr>
	<?php 
	if ( empty($options['aviary']) ) {
		echo '<p>In order to use the image editor feature, you have to integrate IB 2.0 with <a href="https://developers.aviary.com" target="_blank">Aviary</a> first. You can do so by <a href="' . admin_url('admin.php?page=ib2-settings') . '" target="_blank">clicking here</a>.</p>';
		echo '<p>After you integrate IB 2.0 with <a href="https://developers.aviary.com" target="_blank">Aviary</a> in the settings page, please reload this editor so the changes can take effect.</p>';
	} else {
		echo '<div id="ib2-aviary"></div>' . "\n";
	}
	?>
	<hr>
	<p style="text-align:right">
		<button type="button" class="btn btn-default close-image-editor">Close</button>
	</p>
</div>

<!-- Page Save Loader -->
<div id="save-background-ui"></div>
<div id="save-loader-ui">
	<img src="<?php echo IB2_IMG; ?>preload-bar.gif" border="0" id="save-loader-img" /><br />
	<strong>Please wait while saving ...</strong>
</div>
<?php
}

add_action('admin_init', 'ib2_delete_variant');
function ib2_delete_variant() {
	if ( isset($_GET['action']) && $_GET['action'] == 'ib2_delete_variation' ) {
		$post_id = (int) $_GET['post_id'];
		$l = $_GET['variant'];
		
		$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true');
		$all_variants = ib2_get_variants($post_id);
		if ( !ib2_variant_exists($post_id, $l) || count($all_variants) <= 1 ) {
			wp_redirect($url);
			exit;
		}
		
		$meta = get_post_meta($post_id, 'ib2_settings', true);
		if ( !is_array($meta) ) {
			$meta = array();
			$meta['enable'] = 'yes';
		}
		
		if ( isset($meta['variation' . $l]) ) {
			unset($meta['variation' . $l]);
		}
		
		ib2_delete_variant_entry($post_id, $l);
		update_post_meta($post_id, 'ib2_settings', $meta);

		wp_redirect($url);
		exit;
	}
}

function ib2_variant_entry( $args ) {
	global $wpdb;
	$defaults = array(
		'post_id' => 0,
		'variant' => 'a',
		'weight' => -1,
		'status' => 'none',
		'quota' => -1
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);
	
	$post_id = (int) $post_id;
	
	if ( empty($post_id) )
		return false;
	
	$update = false;
	if ( ib2_variant_exists($post_id, $variant) )
		$update = true;
	
	$data = array();
	
	if ( $update ) {
		if ( $weight > -1 ) $data['weight'] = $weight;
		if ( $quota > -1 ) $data['quota'] = $quota;
		if ( $status != 'none' ) $data['status'] = $status;
		
		if ( !empty($data) )
			$wpdb->update("{$wpdb->prefix}ib2_variants", $data, array('post_id' => $post_id, 'variant' => $variant));
	} else {
		if ( $weight == -1 ) {
			// let's calculate weight
			$weight = 100;
			if ( $vars = ib2_get_variants($post_id) ) {
				if ( count($vars) == 1 && $vars[0]->weight == 100 ) {
					$weight = 50;

					// update the other entry
					ib2_variant_entry( "post_id={$post_id}&variant={$vars[0]->variant}&weight=50" );
				} else {
					foreach ( $vars as $var ) {
						$weight -= $var->weight;
					}
				}
			}
		}
		
		if ( $quota == -1 ) $quota = 0;
		
		$data['post_id'] = $post_id;
		$data['variant'] = $variant;
		$data['weight'] = $weight;
		$data['status'] = 'active';
		$data['quota'] = $quota;
		
		$wpdb->insert("{$wpdb->prefix}ib2_variants", $data);
	}
	return true;
}

function ib2_delete_variant_entry( $post_id, $variant ) {
	global $wpdb;
	
	$meta = get_post_meta($post_id, 'ib2_settings', true);
	if ( !is_array($meta) )
		$meta = array();
	
	if ( isset($meta['variation' . $variant]) )
		unset($meta['variation' . $variant]);
	
	// delete from meta...
	update_post_meta($post_id, 'ib2_settings', $meta);
	
	// delete from stats...
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_stats", array('post_id' => $post_id, 'variant' => $variant));
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_questions", array('post_id' => $post_id, 'variant' => $variant));
	$wpdb->delete("{$wpdb->prefix}ib2_quizzes", array('post_id' => $post_id, 'variant' => $variant));
	$wpdb->delete("{$wpdb->prefix}ib2_conversions", array('post_id' => $post_id, 'variant' => $variant));
	$wpdb->delete("{$wpdb->prefix}ib2_hits", array('post_id' => $post_id, 'variant' => $variant));
	
	$post_id = (int) $post_id;
	$wpdb->delete("{$wpdb->prefix}ib2_variants", array('post_id' => $post_id, 'variant' => $variant));
}

add_action('admin_init', 'ib2_create_variant');
function ib2_create_variant() {
	global $wpdb;
	if ( isset($_GET['action']) && $_GET['action'] == 'ib2_new_variant' ) {
		$post_id = (int) $_GET['post_id'];
		$mode = $_GET['mode'];
		$template_id = ( isset($_GET['tid']) ) ? (int) $_GET['tid'] : 0;
		$def_variant = ib2_get_default_variant($post_id, false);
		$oldvar = isset($_GET['oldvar']) ? $_GET['oldvar'] : $def_variant;
		$nv = ib2_new_variant($post_id, $mode, $template_id, $oldvar);
			
		$url = admin_url('post.php?post=' . $post_id . '&action=edit&ib2editor=true&variant=' . $nv);
		wp_redirect($url);
		exit;
	}
}

function ib2_page_entry( $post_id, $group_id = -1 ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	if ( !$post = get_post($post_id) )
		return false;
	
	$update = false;
	if ( ib2_page_exists($post_id) )
		$update = true;
	
	$data = array(
		'name' => stripslashes($post->post_title),
		'status' => $post->post_status,
	);
	
	if ( $group_id > -1 )
		$data['group_id'] = $group_id;
	
	if ( $update ) {
		$wpdb->update("{$wpdb->prefix}ib2_pages", $data, array('post_id' => $post_id));
	} else {
		$data['post_id'] = $post_id;
		$data['created'] = date_i18n("Y-m-d H:i:s");
		
		$wpdb->insert("{$wpdb->prefix}ib2_pages", $data);
	}
	
	return true;
}

function ib2_page_exists( $post_id ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_pages` WHERE `post_id` = %d", $post_id));
	
	return  ( ( $chk > 0 ) ? true : false );
}

function ib2_delete_quiz( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	if ( !ib2_quiz_exists($post_id, $variant) )
		return false;
	
	// delete stats first
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_stats", array('post_id' => $post_id, 'variant' => $variant));
	
	// delete answers
	$wpdb->delete("{$wpdb->prefix}ib2_quiz_questions", array('post_id' => $post_id, 'variant' => $variant));
	
	// delete quiz
	$wpdb->delete("{$wpdb->prefix}ib2_quizzes", array('post_id' => $post_id, 'variant' => $variant));
}

function ib2_quiz_exists( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_quizzes` WHERE `post_id` = %d AND `variant` = %s", $post_id, $variant));
	
	return ( ($chk > 0) ? true : false );
}

function ib2_save_quiz( $post_id, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	if ( ib2_quiz_exists($post_id, $variant) )
		return false;
	
	$data = array(
		'post_id' => $post_id,
		'variant' => $variant,
		'created' => date_i18n("Y-m-d H:i:s")
	);
	
	$wpdb->insert("{$wpdb->prefix}ib2_quizzes", $data);
}

function ib2_question_exists( $post_id, $order, $variant ) {
	global $wpdb;
	
	$post_id = (int) $post_id;
	$order = (int) $order;
	$chk = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM `{$wpdb->prefix}ib2_quiz_questions` WHERE `post_id` = %d AND `order` = %d AND `variant` = %s", $post_id, $order, $variant));
	
	return ( ($chk > 0) ? true : false );
}

function ib2_save_question( $args ) {
	global $wpdb;
	$defaults = array(
		'post_id' => 0,
		'order' => 1,
		'question' => '',
		'variant' => 'a',
		'a1' => '',
		'a2' => '',
		'a3' => '',
		'a4' => '',
		'a5' => ''
	);
	$args = wp_parse_args( $args, $defaults );
	extract($args, EXTR_SKIP);
	
	$update = ( ib2_question_exists($post_id, $order, $variant) ) ? true : false;
	
	$data = array(
		'question' => sanitize_text_field($question),
		'a1' => sanitize_text_field($a1),
		'a2' => sanitize_text_field($a2),
		'a3' => sanitize_text_field($a3),
		'a4' => sanitize_text_field($a3),
		'a5' => sanitize_text_field($a5)
	);
		
	if ( $update ) {
		$wpdb->update("{$wpdb->prefix}ib2_quiz_questions", $data, array('post_id' => $post_id, 'order' => $order, 'variant' => $variant));
	} else {
		$data['post_id'] = $post_id;
		$data['order'] = $order;
		$data['variant'] = $variant;
		$wpdb->insert("{$wpdb->prefix}ib2_quiz_questions", $data);
	}
}

require_once 'editor-settings.php';
require_once 'editor-ajax.php';
require_once 'element-settings.php';
