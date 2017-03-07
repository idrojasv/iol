<?php

$theme_options_data = get_theme_mods();

if( is_active_sidebar( 'menu_top' ) ) {
	dynamic_sidebar( 'menu_top' );
}