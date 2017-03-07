<?php
/**
 * Include the TGM_Plugin_Activation class.
 */
add_action( 'tgmpa_register', 'thim_tp_register_required_plugins' );

/**
 * Register the required plugins for this theme.
 *
 * In this example, we register two plugins - one included with the TGMPA library
 * and one from the .org repo.
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */


if ( ! function_exists( 'thim_tp_register_required_plugins' ) ) {
	function thim_tp_register_required_plugins() {
		$plugins = array(
			
			array(
				'name'               => 'Thim Framework',
				// The plugin name
				'slug'               => 'thim-framework',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/thim-framework.zip',
				// The plugin source
				'required'           => true,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '1.9.7.5',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'     => 'SiteOrigin Page Builder',
				'slug'     => 'siteorigin-panels',
				'required' => false,
			),

			array(
				'name'     => 'Black Studio TinyMCE Widget',
				'slug'     => 'black-studio-tinymce-widget',
				'required' => false,
			),
			array(
				'name'     => 'Contact Form 7',
				'slug'     => 'contact-form-7',
				'required' => false
			),
			array(
				'name'     => 'MailChimp for WordPress',
				'slug'     => 'mailchimp-for-wp',
				'required' => false
			),
			array(
				'name'     => 'WooCommerce',
				'slug'     => 'woocommerce',
				'required' => false,
			),
			array(
				'name'     => 'bbPress',
				'slug'     => 'bbpress',
				'required' => false,
			),
			array(
				'name'     => 'Social Login',
				'slug'     => 'miniorange-login-openid',
				'required' => false,
			),

			array(
				'name'               => 'Slider Revolution',
				// The plugin name
				'slug'               => 'revslider',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/revslider.zip',
				// The plugin source
				'required'           => true,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '5.3.1.5',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'               => 'WPBakery Visual Composer',
				// The plugin name
				'slug'               => 'js_composer',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/js_composer.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '5.0.1',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'               => 'Thim Events',
				// The plugin name
				'slug'               => 'tp-event',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/tp-event.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '1.4.1.3',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'               => 'Thim Portfolio',
				// The plugin name
				'slug'               => 'tp-portfolio',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/tp-portfolio.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '1.3',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'               => 'Thim Our Team',
				// The plugin name
				'slug'               => 'thim-our-team',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/thim-our-team.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '1.3.1',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),
			array(
				'name'               => 'Thim Testimonials',
				// The plugin name
				'slug'               => 'thim-testimonials',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/thim-testimonials.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '1.3.1',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'               => 'LearnPress Certificates',
				// The plugin name
				'slug'               => 'learnpress-certificates',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/learnpress-certificates.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '2.2.5',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

//			array(
//				'name'               => 'LearnPress Co-Instructors',
//				// The plugin name
//				'slug'               => 'learnpress-co-instructor',
//				// The plugin slug (typically the folder name)
//				'source'             => THIM_DIR . 'inc/plugins/learnpress-co-instructor.zip',
//				// The plugin source
//				'required'           => false,
//				// If false, the plugin is only 'recommended' instead of required
//				'version'            => '2.0',
//				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
//				'force_activation'   => false,
//				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
//				'force_deactivation' => false,
//				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
//				'external_url'       => '',
//				// If set, overrides default API URL and points to an external URL
//			),

			array(
				'name'               => 'LearnPress Collections',
				// The plugin name
				'slug'               => 'learnpress-collections',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/learnpress-collections.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '2.1.2',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

//			array(
//				'name'               => 'LearnPress Content Drip',
//				// The plugin name
//				'slug'               => 'learnpress-content-drip',
//				// The plugin slug (typically the folder name)
//				'source'             => THIM_DIR . 'inc/plugins/learnpress-content-drip.zip',
//				// The plugin source
//				'required'           => false,
//				// If false, the plugin is only 'recommended' instead of required
//				'version'            => '2.0',
//				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
//				'force_activation'   => false,
//				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
//				'force_deactivation' => false,
//				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
//				'external_url'       => '',
//				// If set, overrides default API URL and points to an external URL
//			),

			array(
				'name'               => 'LearnPress - Paid Memberships Pro',
				// The plugin name
				'slug'               => 'learnpress-paid-membership-pro',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/learnpress-paid-membership-pro.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '2.2.2',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

//			array(
//				'name'               => 'LearnPress Sorting Choice',
//				// The plugin name
//				'slug'               => 'learnpress-sorting-choice',
//				// The plugin slug (typically the folder name)
//				'source'             => THIM_DIR . 'inc/plugins/learnpress-sorting-choice.zip',
//				// The plugin source
//				'required'           => false,
//				// If false, the plugin is only 'recommended' instead of required
//				'version'            => '2.0',
//				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
//				'force_activation'   => false,
//				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
//				'force_deactivation' => false,
//				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
//				'external_url'       => '',
//				// If set, overrides default API URL and points to an external URL
//			),

//			array(
//				'name'               => 'LearnPress Stripe',
//				// The plugin name
//				'slug'               => 'learnpress-stripe',
//				// The plugin slug (typically the folder name)
//				'source'             => THIM_DIR . 'inc/plugins/learnpress-stripe.zip',
//				// The plugin source
//				'required'           => false,
//				// If false, the plugin is only 'recommended' instead of required
//				'version'            => '2.0',
//				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
//				'force_activation'   => false,
//				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
//				'force_deactivation' => false,
//				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
//				'external_url'       => '',
//				// If set, overrides default API URL and points to an external URL
//			),

			array(
				'name'               => 'LearnPress WooCommerce Payment Methods',
				// The plugin name
				'slug'               => 'learnpress-woo-payment',
				// The plugin slug (typically the folder name)
				'source'             => THIM_DIR . 'inc/plugins/learnpress-woo-payment.zip',
				// The plugin source
				'required'           => false,
				// If false, the plugin is only 'recommended' instead of required
				'version'            => '2.2.3.1',
				// E.g. 1.0.0. If set, the active plugin must be this version or higher, otherwise a notice is presented
				'force_activation'   => false,
				// If true, plugin is activated upon theme activation and cannot be deactivated until theme switch
				'force_deactivation' => false,
				// If true, plugin is deactivated upon theme switch, useful for theme-specific plugins
				'external_url'       => '',
				// If set, overrides default API URL and points to an external URL
			),

			array(
				'name'     => 'LearnPress',
				'slug'     => 'learnpress',
				'required' => true,
			),

			array(
				'name'     => 'LearnPress Course Review',
				'slug'     => 'learnpress-course-review',
				'required' => false,
			),
			array(
				'name'     => 'LearnPress Wishlist',
				'slug'     => 'learnpress-wishlist',
				'required' => false,
			),
			array(
				'name'     => 'LearnPress bbPress',
				'slug'     => 'learnpress-bbpress',
				'required' => false,
			),

		);

		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       => 'eduma', // Text domain - likely want to be the same as your theme.
			'default_path' => '', // Default absolute path to pre-packaged plugins
			'parent_slug'  => 'themes.php', // Default parent menu slug
			'menu'         => 'install-required-plugins', // Menu slug
			'has_notices'  => true, // Show admin notices or not
			'is_automatic' => true, // Automatically activate plugins after installation or not
			'message'      => '', // Message to output right before the plugins table
			'strings'      => array(
				'page_title'                      => esc_html__( 'Install Required Plugins', 'eduma' ),
				'menu_title'                      => esc_html__( 'Install Plugins', 'eduma' ),
				'installing'                      => esc_html__( 'Installing Plugin: %s', 'eduma' ),
				// %1$s = plugin name
				'oops'                            => esc_html__( 'Something went wrong with the plugin API.', 'eduma' ),
				'notice_can_install_required'     => _n_noop( 'This theme requires the following plugin: %1$s.', 'This theme requires the following plugins: %1$s.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin: %1$s.', 'This theme recommends the following plugins: %1$s.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.', 'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.', 'eduma' ),
				// %1$s = plugin name(s)
				'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'eduma' ),
				// %1$s = plugin name(s)
				'install_link'                    => _n_noop( 'Begin installing plugin', 'Begin installing plugins', 'eduma' ),
				'activate_link'                   => _n_noop( 'Activate installed plugin', 'Activate installed plugins', 'eduma' ),
				'return'                          => esc_html__( 'Return to Required Plugins Installer', 'eduma' ),
				'plugin_activated'                => esc_html__( 'Plugin activated successfully.', 'eduma' ),
				'complete'                        => esc_html__( 'All plugins installed and activated successfully. %s', 'eduma' ),
				// %1$s = dashboard link
				'nag_type'                        => 'updated'
				// Determines admin notice type - can only be 'updated' or 'error'
			)
		);
		tgmpa( $plugins, $config );
	}
}

