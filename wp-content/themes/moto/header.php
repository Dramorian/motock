<!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
    <link rel="shortcut icon" href="https://moto.ck.ua/moto.ico" type="image/x-icon" />
	<title><?php bloginfo('name'); ?><?php wp_title(); ?></title>
    <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="alternate" type="text/xml" title="RSS .92" href="<?php bloginfo('rss_url'); ?>" />
	<link rel="alternate" type="application/atom+xml" title="Atom 1.0" href="<?php bloginfo('atom_url'); ?>" />


	<?php wp_get_archives('type=monthly&format=link'); ?>
	<?php //comments_popup_script(); // off by default ?>
    <script type='text/javascript' src='https://code.jquery.com/jquery-2.2.3.min.js'></script>
	<?php wp_head(); ?>
    <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/hide.js"></script>
</head>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-10025611-1");
pageTracker._trackPageview();
} catch(err) {}</script>
<body>

<div id="header">
<table width="880" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="205" height="225">
    <object type="application/x-shockwave-flash" data="<?php bloginfo('stylesheet_directory'); ?>/images/Sound.swf" height="200" width="200" hspace="5" vspace="25">
    <param name="movie" value="<?php bloginfo('stylesheet_directory'); ?>/images/Sound.swf">
    <img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.jpg" alt="Logo" height="200" width="200" hspace="5" vspace="25"/>
    </object>
    </td>
    <td>
    <h1><a href="<?php bloginfo('url'); ?>/" target="_self"><?php bloginfo('name'); ?></a></h1>
	<h4><?php bloginfo('description'); ?></h4>
        <div class="moto-phone">
            <p>(0472) 38-20-47</p>
            <p>(093) 754-45-49</p>
            <p>(068) 150-33-33</p>
        </div>
	<div class="search">
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>
        </div>
    </td>
  </tr>
</table>
    
    
	</div>
	<div class="clear"></div>


<!-- end header -->
