<?php
$thim_animation = $sub_heading = $sub_heading_css = $html = $css = $line = $line_css = '';
$thim_animation .= thim_getCSSAnimation( $instance['css_animation'] );
if ( $instance['textcolor'] ) {
	$css .= 'color:' . $instance['textcolor'] . ';';
}

//foreach ( $instance['custom_font_heading'] as $i => $feature ) :
if ( $instance['font_heading'] == 'custom' ) {
	if ( $instance['custom_font_heading']['custom_font_size'] <> '' ) {
		$css .= 'font-size:' . $instance['custom_font_heading']['custom_font_size'] . 'px;';
	}
	if ( $instance['custom_font_heading']['custom_font_weight'] <> '' ) {
		$css .= 'font-weight:' . $instance['custom_font_heading']['custom_font_weight'] . ';';
	}
	if ( $instance['custom_font_heading']['custom_font_style'] <> '' ) {
		$css .= 'font-style:' . $instance['custom_font_heading']['custom_font_style'] . ';';
	}
}

//endforeach;

if ( $css ) {
	$css = ' style="' . $css . '"';
}

if($instance['sub_heading'] && $instance['sub_heading'] <> ''){
	if($instance['sub_heading_color']) {
		$sub_heading_css = 'color:' . $instance['sub_heading_color'] . ';';
	}	
		
	$sub_heading = '<p class="sub-heading" style="'.$sub_heading_css.'">'.$instance['sub_heading'].'</p>';
}

if($instance['line'] && $instance['line'] <> ''){
	if ( $instance['bg_line'] ) {
		$line_css = ' style="background-color:' . $instance['bg_line'] . '"';
	}
	$line = '<span' . $line_css . ' class="line"></span>';
}



/*
 *
 */
 
$text_align = '';
if($instance['text_align'] && $instance['text_align'] <> ''){
	$text_align = $instance['text_align'];
}

$html .= '<div class="sc_heading' . $thim_animation . ' '.$text_align.'">';
$html .= '<' . $instance['size'] . $css . ' class="title">' . $instance['title'] . '</' . $instance['size'] . '>';
$html .= $sub_heading;
$html .= $line;
$html .= '</div>';

echo ent2ncr( $html );