<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-tp-import-update
 *
 * @author Tuannv
 */
class Thim_ThemeOption_And_Metabox {
	function __construct() {
		add_action( 'tf_create_options', array( $this, 'post_settings' ) );
	}

	function post_settings() {
		$titan = TitanFramework::getInstance( 'thim' );
		// Post format settings
		include( 'meta-box/post-format.php' );
		// Display settngs
		include( 'meta-box/setting.php' );
	}
}

new Thim_ThemeOption_And_Metabox();

