<?php
if ( empty($_GET['mp4']) && empty($_GET['ogg']) && empty($_GET['webm']) ) wp_die('ERROR: Video not found.');
$mp4 = '';
$ogg = '';
$webm = '';
$splash = '';
$controls = true;
$autoplay = '';
$ratio = 0.5625;

if ( !empty($_GET['mp4']) ) $mp4 = urldecode($_GET['mp4']);
if ( !empty($_GET['ogg']) ) $ogg = urldecode($_GET['ogg']);
if ( !empty($_GET['webm']) ) $webm = urldecode($_GET['webm']);
if ( !empty($_GET['splash']) ) $splash = urldecode($_GET['splash']);
if ( !empty($_GET['ratio']) ) $ratio = urldecode($_GET['ratio']);
if ( isset($_GET['autoplay']) && $_GET['autoplay'] == 1 ) $autoplay = ' autoplay';
if ( isset($_GET['controls']) && $_GET['controls'] == 0 ) $controls = false;

$is_splash = '';
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
<title>Untitled Document</title>
<?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions. ?>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js" type="text/javascript"></script>
<![endif]-->
<style type='text/css'>
html, body, div, applet, object, iframe, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	vertical-align: baseline;
}
</style>
<link rel='stylesheet' id="flowplayer-css" href="//releases.flowplayer.org/6.0.5/skin/functional.css" type='text/css' media='all'>	
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type='text/javascript' src='//releases.flowplayer.org/6.0.5/flowplayer.min.js'></script>
<?php if ( !$controls ) : ?>
<style>
	.fp-controls, .fp-time {
		display: none;
	}
</style>
<?php endif; ?>
<?php if ( !empty($splash) ) : 
$is_splash = ' is-splash';
?>
<style>
.flowplayer {
  background: #000 url("<?php echo $splash; ?>");
}
</style>
<?php endif; ?>
<body>
<div class="flowplayer<?php echo $is_splash; ?>" data-fullscreen="true" data-ratio="<?php echo $ratio; ?>" style="background-color:#000">
	<video<?php echo $autoplay; ?>>
		<?php if ( !empty($mp4) ) : ?><source type="video/mp4" src="<?php echo $mp4; ?>"><?php endif; ?>
		<?php if ( !empty($ogg) ) : ?><source type="video/ogv" src="<?php echo $ogg; ?>"><?php endif; ?>
		<?php if ( !empty($webm) ) : ?><source type="video/webm" src="<?php echo $webm; ?>"><?php endif; ?>
	</video>
</div>
   
</body>
</html>
