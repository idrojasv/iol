<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class SQ_Social_Share_widget extends WP_Widget {

	/**
	 * Widget setup
	 */
	function __construct() {

		$widget_ops = array(
			'description' => esc_html__( 'Social share icons.', 'buddyapp' )
		);
		parent::__construct( 'kleo_social_share', esc_html__('(BuddyApp) Social Share','buddyapp'), $widget_ops );

		add_action('load-widgets.php', array($this, 'scripts_load') );
		add_action('customize_controls_enqueue_scripts', array($this, 'scripts_load') );

	}

	function scripts_load() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_media();

	}

	/**
	 * Display widget
	 */
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', $instance['title'] );
		$platforms = $instance['platforms'];
		$background_color = $instance['background_color'];
		$background_image = $instance['background_image'];
		$style = $instance['style'];

		global $post;
		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . wp_kses_data( $title ) . $after_title;
		}
		if (! empty( $platforms ) ) { ?>
			
			<?php if ($style == 'style-01' && ( $background_color || $background_image ) ) { ?>
					
				<style>
	
					#sidemenu-wrapper .menu-section.widget.widget_kleo_social_share .style-01.widget-inner{
						background-image: none;
						background-color: transparent;
					}
	
					.sidemenu-is-open #sidemenu-wrapper .menu-section.widget.widget_kleo_social_share .style-01.widget-inner {
						<?php if ( $background_image ) : ?>
						background-image: url('<?php echo $background_image;?>');
						<?php endif; ?>
						<?php if ( $background_color ) : ?>
						background-color: <?php echo $background_color;?>;
						<?php endif; ?>
					}
	
					@media only screen and (min-width: 992px) {
						#sidemenu-wrapper .menu-section.widget.widget_kleo_social_share .style-01.widget-inner{
							<?php if ( $background_image ) : ?>
								background-image: url('<?php echo $background_image;?>');
							<?php endif; ?>
							<?php if ( $background_color ) : ?>
								background-color: <?php echo $background_color;?>;
							<?php endif; ?>
						}
	
						.sidemenu-is-open #sidemenu-wrapper .menu-section.widget.widget_kleo_social_share .style-01.widget-inner{
							background-image: none;
							background-color: transparent;
						}
					}
	
				</style>
				
			<?php } ?>
			
			<div class="widget-inner <?php echo $style;?>">
				<ul class="social-list">
					
					<?php if ( $platforms ) : ?>

					<?php foreach ( $platforms as $platform ) : ?>
						<?php
						$link = '#';
						if (isset($platform['href']) && $platform['href'] != '') {
							$link = esc_attr($platform['href']);
						}
						$icon = '';
						if (isset($platform['icon'])) {
							$icon = esc_attr($platform['icon']);
						}
						?>
						<li>
							<a href="<?php echo $link;?>"><i class="icon-<?php echo esc_attr($icon); ?>"></i></a>
						</li>
					<?php endforeach; ?>

					<?php endif; ?>

				</ul>
			</div>
			
			<?php
		}

		echo $after_widget;

	}

	/**
	 * Update widget
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = esc_attr( $new_instance['title'] );
		$instance['platforms'] = $new_instance['platforms'];
		$instance['background_color'] = $new_instance['background_color'];
		$instance['background_image'] = $new_instance['background_image'];
		$instance['style'] = $new_instance['style'];


		return $instance;

	}

	/**
	 * Widget setting
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array(
			'title' => '',
			'platforms' => null,
			'style' => '',
			'background_color' => '',
			'background_image' => ''
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$title = esc_attr( $instance['title'] );
		$platforms = $instance['platforms'];
		$background_color = $instance['background_color'];
		$background_image = $instance['background_image'];
		$style = $instance['style'];

		$bg_class = $style == '' ? ' hidden' : '';
		?>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'buddyapp' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style', 'buddyapp' ); ?></label>
			<br>
			<select id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
				<option value="">Default</option>
				<option <?php if ($style == 'style-01') { echo 'selected="selected"'; } ?>value="style-01">Fancy</option>
			</select>
		</p>

		<p class="bg-section<?php echo $bg_class; ?>">
			<label for="<?php echo $this->get_field_id( 'background_color' ); ?>"><?php _e( 'Background Color', 'buddyapp' ); ?></label>
			<br>
			<input class="color-picker" type="text" id="<?php echo $this->get_field_id( 'background_color' ); ?>" name="<?php echo $this->get_field_name( 'background_color' ); ?>" value="<?php echo esc_attr( $background_color ); ?>" />
		</p>

		<p class="bg-section<?php echo $bg_class; ?>">
			<label for="<?php echo $this->get_field_id( 'background_image' ); ?>"><?php _e( 'Background Image', 'buddyapp' ); ?></label>
			<br>
			<input type="text" class="widefat image-url" id="<?php echo $this->get_field_id( 'background_image' ); ?>" name="<?php echo $this->get_field_name( 'background_image' ); ?>" value="<?php echo esc_attr( $background_image ); ?>" />
			<span class="img-preview" id="<?php echo $this->get_field_id( 'preview_image' ); ?>">
				<?php if ($background_image) : ?>
					<img style="width:100%" src="<?php echo esc_attr( $background_image ); ?>">
				<?php endif; ?>
			</span>
			<input class="upload_image_button button button-primary" id="<?php echo $this->get_field_id( 'upload_button' ); ?>" type="button" value="Choose Image" />
			<input class="button<?php if (! $background_image) { echo ' hidden'; } ?>" id="<?php echo $this->get_field_id( 'remove_button' ); ?>" type="button" value="Remove Image" />
		</p>

		<div>

			<label for="<?php echo esc_attr( $this->get_field_id( 'platforms' ) ); ?>"><?php esc_html_e( 'Platforms:', 'buddyapp' ); ?></label>
			<br>
			<?php if($platforms) :?>
			<?php $i = 0; foreach ( $platforms as $platform ) : ?>
				<p>
				<input placeholder="Icon" size="7" class="socialset" name="<?php echo $this->get_field_name( 'platforms' ); ?>[<?php echo $i; ?>][icon]" value="<?php echo $platform['icon']; ?>">
				<input placeholder="Link" class="socialset socialset-link" name="<?php echo $this->get_field_name( 'platforms' ); ?>[<?php echo $i; ?>][href]" value="<?php echo $platform['href']; ?>">
				<a href="#" class="<?php echo $this->get_field_id( 'remove_button' ); ?>">X</a>
				</p>
			<?php $i++; endforeach;  ?>
			<?php endif;?>
		</div>

		<p>
			<?php
			$select_icons = kleo_icons_array( '', array( '', '-none-' ));
			$icon_opts = '';
			foreach ( $select_icons as $icon ) {
				$icon_opts .= '<option value="' . $icon . '">' . $icon . '</option> ';
			}
			?>
			<select style="max-width: 66%;" id="<?php echo esc_attr( $this->get_field_id( 'icons' ) ); ?>">
				<?php echo $icon_opts; ?>
			</select>
			<a href="#" class="sq-clone-me <?php echo esc_attr( $this->get_field_id( 'addnew' ) ); ?>"><?php _e( "Add platform", 'buddyapp' );?></a>
		</p>

		<script>
			(function ($) {
				var fName = '<?php echo $this->get_field_name( "platforms" ); ?>';
				var triggerAdd = '.' + '<?php echo esc_attr( $this->get_field_id( "addnew" ) ); ?>';
				var triggerColor = '#' + '<?php echo esc_attr( $this->get_field_id( "background_color" ) ); ?>';
				var triggerUpload = '#' + '<?php echo esc_attr( $this->get_field_id( "upload_button" ) ); ?>';
				var removeUpload = '#' + '<?php echo esc_attr( $this->get_field_id( "remove_button" ) ); ?>';
				var imageInput = '#' + '<?php echo esc_attr( $this->get_field_id( "background_image" ) ); ?>';
				var imagePreview = '#' + '<?php echo esc_attr( $this->get_field_id( "preview_image" ) ); ?>';
				var platformRemove = '.' + '<?php echo esc_attr( $this->get_field_id( "remove_button" ) ); ?>';
				var iconsSelector = '#' + '<?php echo esc_attr( $this->get_field_id( "icons" ) ); ?>';
				var styleSelect = '#' + '<?php echo esc_attr( $this->get_field_id( "style" ) ); ?>';

				$(document).ready(function() {
					$(triggerAdd).on('click', function() {
						var fCount = $(this).closest('p').prev().find('input.socialset').length;
						$( this ).closest('p').prev().append( '<p><input placeholder="Icon" class="socialset" size="7" name="'+ fName +'['+ (fCount + 1)  +'][icon]" value="'+ $(iconsSelector + " option:selected").val() +'" >'
							+ ' <input placeholder="Link" class="socialset" name="'+ fName +'['+ (fCount + 1)  +'][href]" value >'
							+ ' <a href="#" class="<?php echo $this->get_field_id( 'remove_button' ); ?>">X</a></p>'
						);
						return false;
					});

					$(styleSelect).on('change', function() {
						if ( $(this).find('option:selected').val() == '' ) {
							$(this).parent().siblings('.bg-section').addClass('hidden');
						} else {
							$(this).parent().siblings('.bg-section').removeClass('hidden');
						}
					});

					$(triggerColor).wpColorPicker();

					$('body').on('click', platformRemove, function() {
						$(this).closest('p').remove();

						return false;
					});

					var mediaControl = {
						// Initializes a new media manager or returns an existing frame.
						// @see wp.media.featuredImage.frame()
						frame: function() {
							if ( this._frame )
								return this._frame;

							this._frame = wp.media({
								library: {
									type: 'image'
								},
								multiple: false
							});

							this._frame.on( 'open', this.updateFrame ).state('library').on( 'select', this.select );

							return this._frame;
						},

						select: function() {
							var attachment = mediaControl.frame().state().get('selection').first().toJSON();
							$(imageInput).val(attachment.url);
							$(imagePreview).find('img').remove();
							$(imagePreview).append( '<img src="'+attachment.url+'" alt="" style="max-width:100%;"/>' );
							// Unhide the remove image link
							$(removeUpload).removeClass( 'hidden' );
						},

						updateFrame: function() {
							// Do something when the media frame is opened.
						},

						init: function() {
							$('#wpbody').on('click', triggerUpload, function(e) {
								e.preventDefault();

								mediaControl.frame().open();
							});
						}
					};

					mediaControl.init();

					// DELETE IMAGE LINK
					$(removeUpload).on( 'click', function( event ){

						event.preventDefault();

						// Clear out the preview image
						$(removeUpload).siblings('.img-preview').html( '' );

						// Hide the delete image link
						$(removeUpload).addClass( 'hidden' );

						// Delete the image id from the hidden input
						$(imageInput).val( '' );

					});

				});

			})(jQuery);
		</script>
		

		<?php
	}

}

/**
 * Register widget.
 *
 * @since 1.0
 */
add_action( 'widgets_init', create_function( '', 'register_widget( "SQ_Social_Share_widget" );' ) );
