<?php
define('ADMIN_PATH', get_bloginfo('template_url') .'/admin/');

//////////////////////////////////////////////////////////////
// Lindenvalley Theme Design - Admin Head
////////////////////////////////////////////////////////////

if(isset($_REQUEST['page']) && $_REQUEST['page']=='theme-options') :

	add_action( 'admin_init', 'theme_admin_head' );
	
	function theme_admin_head() {
		wp_enqueue_script('media-upload');
		wp_register_script('theme-cookie', ADMIN_PATH .'admin-cookie.js', array('jquery'));
		wp_enqueue_script('theme-cookie');			
		wp_register_script('theme-ajax-upload', ADMIN_PATH .'scripts/ajaxupload.js', array('jquery'));
		wp_enqueue_script('theme-ajax-upload');			
		wp_register_script('theme-jquery-ui', ADMIN_PATH .'scripts/jquery-ui/js/jquery-ui-1.8.1.custom.min.js', array('jquery'));
		wp_enqueue_script('theme-jquery-ui');
		wp_register_script('theme-admin-jquery', ADMIN_PATH .'admin-jquery.js', array('jquery'));
		wp_enqueue_script('theme-admin-jquery');		
		wp_enqueue_style('theme-ui-lightness', ADMIN_PATH .'scripts/jquery-ui/css/ui-lightness/jquery-ui-1.8.1.custom.css', false, "1.0", "all");
		wp_enqueue_style('theme-options', ADMIN_PATH .'style.css', false, "1.0", "all");	
	}

endif;



//////////////////////////////////////////////////////////////
// Lindenvalley Theme Design - Admin Main Menu 
////////////////////////////////////////////////////////////

add_action('admin_menu', 'theme_create_menu');

function theme_create_menu() {
	global $theme_name;
	add_menu_page($theme_name.' Theme Settings', $theme_name, 'administrator', 'theme-options', 'theme_options_page', ADMIN_PATH .'images/theme_menu_icon.png', 61);		
}

?>