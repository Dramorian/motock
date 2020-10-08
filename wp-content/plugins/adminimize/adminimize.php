<?php

/*
Plugin Name: Adminimize
Plugin URI: http://bueltge.de/wordpress-admin-theme-adminimize/674/
Description: Visually compresses the administratrive header so that more admin page content can be initially seen.  Also moves 'Dashboard' onto the main administrative menu because having it sit in the tip-top black bar was ticking me off and many other changes in the edit-area. The plugin that lets you hide 'unnecessary' items from the WordPress administration menu, with or without admins. You can also hide post meta controls on the edit-area to simplify the interface.
Author: Frank B&uuml;ltge
Author URI: http://bueltge.de/
Version: 1.6
Last Update: 07.12.2008 12:15:06
*/ 

/**
 * The stylesheet and the initial idea is from Eric A. Meyer, http://meyerweb.com/
 * and i have written a plugin with many options on the basis
 * of differently user-right and a user-friendly range in admin-area.
 *
 * The javascript for de/activate checkboxes ist by Oliver Schlöbe, http://www.schloebe.de
 * - many thanks
 */


// Pre-2.6 compatibility
if ( !defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( !defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( !defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( !defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

function _mw_adminimize_textdomain() {

	if (function_exists('load_plugin_textdomain')) {
		if ( !defined('WP_PLUGIN_DIR') ) {
			load_plugin_textdomain('adminimize', str_replace( ABSPATH, '', dirname(__FILE__) ) . '/languages');
		} else {
			load_plugin_textdomain('adminimize', false, dirname(plugin_basename(__FILE__)) . '/languages');
		}
	}
}


function recursive_in_array($needle, $haystack) {
	if ($haystack != '') {
		foreach ($haystack as $stalk) {
			if ( $needle == $stalk || ( is_array($stalk) && recursive_in_array($needle, $stalk) ) ) {
				return true;
			}
		}
		return false;
	}
}


/**
 * some basics for message
 */
class _mw_adminimize_message_class {
	function _mw_adminimize_message_class() {
		$this->localizionName = 'adminimize';
		$this->errors = new WP_Error();
		$this->initialize_errors();
	}
	
	/**
	get_error - Returns an error message based on the passed code
	Parameters - $code (the error code as a string)
	Returns an error message
	*/
	function get_error($code = '') {
		$errorMessage = $this->errors->get_error_message($code);
		if ($errorMessage == null) {
			return __("Unknown error.", $this->localizionName);
		}
		return $errorMessage;
	}
	
	// Initializes all the error messages
	function initialize_errors() {
		$this->errors->add('_mw_adminimize_update', __('The updates was saved.', $this->localizionName));
		$this->errors->add('_mw_adminimize_access_denied', __('You have not enough rights for edit entries in the database.', $this->localizionName));
		$this->errors->add('_mw_adminimize_deinstall', __('All entries in the database was delleted.', $this->localizionName));
		$this->errors->add('_mw_adminimize_deinstall_yes', __('Set the checkbox on deinstall-button.', $this->localizionName));
		$this->errors->add('_mw_adminimize_get_option', __('Can\'t load menu and submenu.', $this->localizionName));
		$this->errors->add('_mw_adminimize_set_theme', __('Backend-Theme was activated!', $this->localizionName));
	}
}


/**
 * check user-option and add new style
 * @uses $pagenow
 */
function _mw_adminimize_init() {
	global $pagenow, $menu, $submenu, $adminimizeoptions, $top_menu, $wp_version;

	$adminimizeoptions = get_option('mw_adminimize');

	$disabled_metaboxes_post_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_subscriber_items');
	$disabled_metaboxes_page_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_subscriber_items');
	$disabled_metaboxes_post_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_contributor_items');
	$disabled_metaboxes_page_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_contributor_items');
	$disabled_metaboxes_post_author      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_author_items');
	$disabled_metaboxes_page_author      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_author_items');
	$disabled_metaboxes_post             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_items');
	$disabled_metaboxes_page             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_items');
	$disabled_metaboxes_post_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_adm_items');
	$disabled_metaboxes_page_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_adm_items');

	$disabled_metaboxes_post_all = array();
	array_push($disabled_metaboxes_post_all, $disabled_metaboxes_post_subscriber);
	array_push($disabled_metaboxes_post_all, $disabled_metaboxes_post_contributor);
	array_push($disabled_metaboxes_post_all, $disabled_metaboxes_post_author);
	array_push($disabled_metaboxes_post_all, $disabled_metaboxes_post);
	array_push($disabled_metaboxes_post_all, $disabled_metaboxes_post_adm);

	$disabled_metaboxes_page_all = array();
	array_push($disabled_metaboxes_page_all, $disabled_metaboxes_page_subscriber);
	array_push($disabled_metaboxes_page_all, $disabled_metaboxes_page_contributor);
	array_push($disabled_metaboxes_page_all, $disabled_metaboxes_page_author);
	array_push($disabled_metaboxes_page_all, $disabled_metaboxes_page);
	array_push($disabled_metaboxes_page_all, $disabled_metaboxes_page_adm);
	
	$_mw_admin_color = get_user_option('admin_color');

	if ( ('post-new.php' == $pagenow) || ('post.php' == $pagenow) || ('page-new.php' == $pagenow) || ('page.php' == $pagenow) ) {
	
		$_mw_adminimize_writescroll = _mw_adminimize_getOptionValue('_mw_adminimize_writescroll');
		switch ($_mw_adminimize_writescroll) {
		case 1:
			wp_enqueue_script('_mw_adminimize_writescroll', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/js/writescroll.js', array('jquery'));
			break;
		}
		$_mw_adminimize_tb_window = _mw_adminimize_getOptionValue('_mw_adminimize_tb_window');
		switch ($_mw_adminimize_tb_window) {
		case 1:
			wp_deregister_script('media-upload');
			wp_enqueue_script('media-upload', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/js/tb_window.js', array('thickbox'));
			break;
		}
	
		//add_filter('image_downsize', '_mw_adminimize_image_downsize', 1, 3);
	}

	$_mw_adminimize_menu_order = _mw_adminimize_getOptionValue('_mw_adminimize_menu_order');
	switch ($_mw_adminimize_menu_order) {
	case 1:
		add_action('admin_head', '_mw_adminimize_adminmenu', 1);
	}
	
	if ( ($_mw_admin_color == 'mw_fresh') ||
				($_mw_admin_color == 'mw_classic') ||
				($_mw_admin_color == 'mw_colorblind') ||
				($_mw_admin_color == 'mw_grey') ||
				($_mw_admin_color == 'mw_fresh_ozh_am') ||
				($_mw_admin_color == 'mw_classic_ozh_am') ||
				($_mw_admin_color == 'mw_fresh_lm') ||
				($_mw_admin_color == 'mw_classic_lm') ||
				($_mw_admin_color == 'mw_wp23')
		 ) {
		
		if ( ('post-new.php' == $pagenow) || ('post.php' == $pagenow) ) {
			if ( version_compare( substr($wp_version, 0, 3), '2.7', '<' ) )
				add_action('admin_head', '_mw_adminimize_remove_box', 99);
			
			// check for array empty
			if ( !isset($disabled_metaboxes_post['0']) )
				$disabled_metaboxes_post['0'] = '';
			if ( isset($disabled_metaboxes_post_adm['0']) )
			 $disabled_metaboxes_post_adm['0'] = '';
			if ( version_compare(substr($wp_version, 0, 3), '2.7', '<') ) {
				if ( !recursive_in_array('#categorydivsb', $disabled_metaboxes_post_all) )
					add_action('submitpost_box', '_mw_adminimize_sidecat_list_category_box');
				if ( !recursive_in_array('#tagsdivsb', $disabled_metaboxes_post_all) )
					add_action('submitpost_box', '_mw_adminimize_sidecat_list_tag_box');
			}
			if ( recursive_in_array('media_buttons', $disabled_metaboxes_post_all) )
				remove_action('media_buttons', 'media_buttons');
		}

		if ( ('page-new.php' == $pagenow) || ('page.php' == $pagenow) ) {
			
			// check for array empty
			if ( !isset($disabled_metaboxes_page['0']) )
				$disabled_metaboxes_page['0'] = '';
			if ( isset($disabled_metaboxes_page_adm['0']) )
			 $disabled_metaboxes_page_adm['0'] = '';
			if ( recursive_in_array('media_buttons', $disabled_metaboxes_page_all) )
				remove_action('media_buttons', 'media_buttons');
		}

	}

	if ( ('post-new.php' == $pagenow) || ('page-new.php' == $pagenow) || ('page.php' == $pagenow) || ('post.php' == $pagenow) ) {
		
		// set user option in edit-area
		add_action('admin_head', '_mw_adminimize_set_user_option_edit');
	}	
	
	if ( basename($_SERVER['REQUEST_URI']) == 'adminimize.php') {
		wp_enqueue_script('_mw_adminimize', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/js/adminimize.js', array('jquery'));
	}

	// set menu option
	add_action('admin_head', '_mw_adminimize_set_menu_option', 1);

	// set metabox option
	add_action('admin_head', '_mw_adminimize_set_metabox_option', 1);

	add_action('in_admin_footer', '_mw_adminimize_admin_footer');
	
	$adminimizeoptions['mw_adminimize_default_menu'] = $menu;
	$adminimizeoptions['mw_adminimize_default_submenu'] = $submenu;
	if ( isset($top_menu) )
		$adminimizeoptions['mw_adminimize_default_top_menu'] = $top_menu;
}

add_action('init', '_mw_adminimize_textdomain');
if ( is_admin() ) {
	add_action('admin_menu', '_mw_adminimize_add_settings_page');
	add_action('admin_menu', '_mw_adminimize_remove_dashboard');
	add_action('admin_init', '_mw_adminimize_init', 1);
	add_action('admin_init', '_mw_adminimize_admin_styles', 1);
}

if ( function_exists('register_activation_hook') )
	register_activation_hook(__FILE__, '_mw_adminimize_install');
if ( function_exists('register_uninstall_hook') )
	register_uninstall_hook(__FILE__, '_mw_adminimize_deinstall');
//register_deactivation_hook(__FILE__, '_mw_adminimize_deinstall');


/**
 * Uses WordPress filter for image_downsize, kill wp-image-dimension
 * code by Andrew Rickmann
 * http://www.wp-fun.co.uk/
 * @param $value, $id, $size
 */
function _mw_adminimize_image_downsize($value = false,$id = 0, $size = "medium") {

	if ( !wp_attachment_is_image($id) )
		return false;
		
	$img_url = wp_get_attachment_url($id);
	// Mimic functionality in image_downsize function in wp-includes/media.php
	if ( $intermediate = image_get_intermediate_size($id, $size) ) {
		$img_url = str_replace(basename($img_url), $intermediate['file'], $img_url);
	}
	elseif ( $size == 'thumbnail' ) {
		// fall back to the old thumbnail
		if ( $thumb_file = wp_get_attachment_thumb_file() && $info = getimagesize($thumb_file) ) {
			$img_url = str_replace(basename($img_url), basename($thumb_file), $img_url);
		}
	}
	if ( $img_url)
		return array($img_url, 0, 0);
	return false;
}


/**
 * list category-box in sidebar
 * @uses $post_ID
 */
function _mw_adminimize_sidecat_list_category_box() {
	global $post_ID;
	?>

	<div class="inside" id="categorydivsb">
		<p><strong><?php _e("Categories"); ?></strong></p>
		<ul id="categorychecklist" class="list:category categorychecklist form-no-clear">
		<?php wp_category_checklist($post_ID); ?>
		</ul>
	<?php if ( !defined('WP_PLUGIN_DIR') ) { // for wp <2.6 ?>
		<div id="category-adder" class="wp-hidden-children">
			<h4><a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a></h4>
			<p id="category-add" class="wp-hidden-child">
				<input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" tabindex="3" />
				<?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
				<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php _e( 'Add' ); ?>" tabindex="3" />
				<?php wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
				<span id="category-ajax-response"></span>
			</p>
		</div>
	<?php } else { ?>
		<div id="category-adder" class="wp-hidden-children">
			<h4><a id="category-add-toggle" href="#category-add" class="hide-if-no-js" tabindex="3"><?php _e( '+ Add New Category' ); ?></a></h4>
			<p id="category-add" class="wp-hidden-child">
				<label class="hidden" for="newcat"><?php _e( 'Add New Category' ); ?></label><input type="text" name="newcat" id="newcat" class="form-required form-input-tip" value="<?php _e( 'New category name' ); ?>" tabindex="3" aria-required="true"/>
				<br />
				<label class="hidden" for="newcat_parent"><?php _e('Parent category'); ?>:</label><?php wp_dropdown_categories( array( 'hide_empty' => 0, 'name' => 'newcat_parent', 'orderby' => 'name', 'hierarchical' => 1, 'show_option_none' => __('Parent category'), 'tab_index' => 3 ) ); ?>
				<input type="button" id="category-add-sumbit" class="add:categorychecklist:category-add button" value="<?php _e( 'Add' ); ?>" tabindex="3" />
				<?php wp_nonce_field( 'add-category', '_ajax_nonce', false ); ?>
				<span id="category-ajax-response"></span>
			</p>
		</div>
	<?php } ?>
	</div>
<?php
}


/**
 * list tag-box in sidebar
 * @uses $post_ID
 */
function _mw_adminimize_sidecat_list_tag_box() {
	global $post_ID;

	if ( !class_exists('SimpleTagsAdmin') ) {
	?>
	<div class="inside" id="tagsdivsb">
		<p><strong><?php _e('Tags'); ?></strong></p>
		<p id="jaxtag"><label class="hidden" for="newtag"><?php _e('Tags'); ?></label><input type="text" name="tags_input" class="tags-input" id="tags-input" size="40" tabindex="3" value="<?php echo get_tags_to_edit($post_ID); ?>" /></p>
		<div id="tagchecklist"></div>
	</div>
	<?php
	}
}


/**
 * remove default categorydiv
 * @echo script
 */
function _mw_adminimize_remove_box() {
	
	if ( function_exists('remove_meta_box') ) {
		if ( !class_exists('SimpleTagsAdmin') )
			remove_meta_box('tagsdiv', 'post', 'normal');
		remove_meta_box('categorydiv', 'post', 'normal');
	} else {
		$_mw_adminimize_sidecat_admin_head  = "\n" . '<script type="text/javascript">' . "\n";
		$_mw_adminimize_sidecat_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#categorydiv\').remove(); });' . "\n";
		$_mw_adminimize_sidecat_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#tagsdiv\').remove(); });' . "\n";
		$_mw_adminimize_sidecat_admin_head .= '</script>' . "\n";

		print($_mw_adminimize_sidecat_admin_head);
	}
}


/**
 * reorder admin-menu
 * @uses $menu
 * @param $file
 */
function _mw_adminimize_adminmenu($file) {
	global $menu;
	
	$menu[7]  = $menu[5];
	$menu[5]  = $menu[0];
	$menu[32] = $menu[40];
	$menu[40] = $menu[35];
	$menu[35] = $menu[30];
	$menu[30] = $menu[15];
	unset($menu[0]);
	unset($menu[15]);
}


/**
 * add new adminstyle to usersettings
 * @param $file
 */
function _mw_adminimize_admin_styles($file) {
	global $wp_version;
	
	$_mw_adminimize_path = WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/';

	if ( version_compare( $wp_version, '2.6.999', '>' ) ) {
		// MW Adminimize Classic
		$styleName = 'Adminimize:' . ' ' . __('Blue');
		wp_admin_css_color (
			'mw_classic', $styleName, $_mw_adminimize_path . 'mw_classic27.css',
			array('#073447', '#21759b', '#eaf3fa', '#bbd8e7')
		);
	
		// MW Adminimize Fresh
		$styleName = 'Adminimize:' . ' ' . __('Gray');
		wp_admin_css_color (
			'mw_fresh', $styleName, $_mw_adminimize_path . 'mw_fresh27.css',
			array('#464646', '#6d6d6d', '#f1f1f1', '#dfdfdf')
		);
		
	} else {
		// MW Adminimize Classic
		$styleName = 'MW Adminimize:' . ' ' . __('Classic');
		wp_admin_css_color (
			'mw_classic', $styleName, $_mw_adminimize_path . 'mw_classic.css',
			array('#07273E', '#14568A', '#D54E21', '#2683AE')
		);
	
		// MW Adminimize Fresh
		$styleName = 'MW Adminimize:' . ' ' . __('Fresh');
		wp_admin_css_color (
			'mw_fresh', $styleName, $_mw_adminimize_path . 'mw_fresh.css',
			array('#464646', '#CEE1EF', '#D54E21', '#2683AE')
		);

		// MW Adminimize WordPress 2.3
		$styleName = 'MW Adminimize:' . ' ' . __('WordPress 2.3');
		wp_admin_css_color (
			'mw_wp23', $styleName, $_mw_adminimize_path . 'mw_wp23.css',
			array('#000000', '#14568A', '#448ABD', '#83B4D8')
		);
	
		// MW Adminimize Colorblind
		$styleName = 'MW Adminimize:' . ' ' . __('Maybe i\'m colorblind');
		wp_admin_css_color (
			'mw_colorblind', $styleName, $_mw_adminimize_path . 'mw_colorblind.css',
			array('#FF9419', '#F0720C', '#710001', '#550007', '#CF4529')
		);
	
		// MW Adminimize Grey
		$styleName = 'MW Adminimize:' . ' ' . __('Grey');
		wp_admin_css_color (
			'mw_grey', $styleName, $_mw_adminimize_path . 'mw_grey.css',
			array('#000000', '#787878', '#F0F0F0', '#D8D8D8', '#909090')
		);
	}
	/**
	 * style and changes for plugin Admin Drop Down Menu
	 * by Ozh
	 * http://planetozh.com/blog/my-projects/wordpress-admin-menu-drop-down-css/
	 */
	if ( function_exists('wp_ozh_adminmenu') ) {
		
		// MW Adminimize Classic include ozh adminmenu
		$styleName = 'MW Adminimize inc. Admin Drop Down Menu' . ' ' . __('Classic');
		wp_admin_css_color (
			'mw_classic_ozh_am', $styleName, $_mw_adminimize_path . 'mw_classic_ozh_am.css',
			array('#07273E', '#14568A', '#D54E21', '#2683AE')
		);
	
		// MW Adminimize Fresh include ozh adminmenu
		$styleName = 'MW Adminimize inc. Admin Drop Down Menu' . ' ' . __('Fresh');
		wp_admin_css_color (
			'mw_fresh_ozh_am', $styleName, $_mw_adminimize_path . 'mw_fresh_ozh_am.css',
			array('#464646', '#CEE1EF', '#D54E21', '#2683AE')
		);
	
	}
	
	/**
	 * style and changes for plugin Lighter Menus
	 * by corpodibacco
	 * http://www.italyisfalling.com/lighter-menus
	 */
	if ( function_exists('lm_build') ) {
		
		// MW Adminimize Classic include Lighter Menus
		$styleName = 'MW Adminimize inc. Lighter Menus' . ' ' . __('Classic');
		wp_admin_css_color (
			'mw_classic_lm', $styleName, $_mw_adminimize_path . 'mw_classic_lm.css',
			array('#07273E', '#14568A', '#D54E21', '#2683AE')
		);
	
		// MW Adminimize Fresh include Lighter Menus
		$styleName = 'MW Adminimize inc. Lighter Menus' . ' ' . __('Fresh');
		wp_admin_css_color (
			'mw_fresh_lm', $styleName, $_mw_adminimize_path . 'mw_fresh_lm.css',
			array('#464646', '#CEE1EF', '#D54E21', '#2683AE')
		);
	
	}
}


/**
 * remove the dashbord
 * @author of basic Austin Matzko
 * http://www.ilfilosofo.com/blog/2006/05/24/plugin-remove-the-wordpress-dashboard/
 */
function _mw_adminimize_remove_dashboard() {
	global $menu, $submenu, $user_ID, $top_menu;

	$disabled_menu_subscriber      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_subscriber_items');
	$disabled_submenu_subscriber   = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_subscriber_items');
	$disabled_top_menu_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_subscriber_items');
	$disabled_menu_contributor     = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_contributor_items');
	$disabled_submenu_contributor  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_contributor_items');
	$disabled_top_menu_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_contributor_items');
	$disabled_menu_author          = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_author_items');
	$disabled_submenu_author       = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_author_items');
	$disabled_top_menu_author      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_author_items');
	$disabled_menu                 = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_items');
	$disabled_submenu              = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_items');
	$disabled_top_menu             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_items');
	$disabled_menu_adm             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_adm_items');
	$disabled_submenu_adm          = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_adm_items');
	$disabled_top_menu_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_adm_items');

	$disabled_menu_all = array();
	array_push($disabled_menu_all, $disabled_menu_subscriber);
	array_push($disabled_menu_all, $disabled_menu_contributor);
	array_push($disabled_menu_all, $disabled_menu_author);
	array_push($disabled_menu_all, $disabled_menu);
	array_push($disabled_menu_all, $disabled_menu_adm);
	
	$disabled_submenu_all = array();
	array_push($disabled_submenu_all, $disabled_submenu_subscriber);
	array_push($disabled_submenu_all, $disabled_submenu_contributor);
	array_push($disabled_submenu_all, $disabled_submenu_author);
	array_push($disabled_submenu_all, $disabled_submenu);
	array_push($disabled_submenu_all, $disabled_submenu_adm);
	
	$disabled_top_menu_all = array();
	array_push($disabled_top_menu_all, $disabled_top_menu_subscriber);
	array_push($disabled_top_menu_all, $disabled_top_menu_contributor);
	array_push($disabled_top_menu_all, $disabled_top_menu_author);
	array_push($disabled_top_menu_all, $disabled_top_menu);
	array_push($disabled_top_menu_all, $disabled_top_menu_adm);
	
	// remove dashboard
	if ($disabled_menu_all != '' || $disabled_submenu_all  != '' || $disabled_top_menu_all != '') {

		if ( current_user_can('subscriber') ) {
			if (
					recursive_in_array('index.php', $disabled_menu_subscriber) ||
					recursive_in_array('index.php', $disabled_submenu_subscriber) ||
					recursive_in_array('index.php', $disabled_top_menu_subscriber) 
				 )
				 $redirect = 'true';
		} elseif ( current_user_can('contributor') ) {  
			if (
					recursive_in_array('index.php', $disabled_menu_contributor) ||
					recursive_in_array('index.php', $disabled_submenu_contributor) ||
					recursive_in_array('index.php', $disabled_top_menu_contributor) 
				 )
				 $redirect = 'true';
		} elseif ( current_user_can('author') ) {  
			if (
					recursive_in_array('index.php', $disabled_menu_author) ||
					recursive_in_array('index.php', $disabled_submenu_author) ||
					recursive_in_array('index.php', $disabled_top_menu_author) 
				 )
				 $redirect = 'true';
		} elseif ( current_user_can('editor') ) {  
			if (
					recursive_in_array('index.php', $disabled_menu) ||
					recursive_in_array('index.php', $disabled_submenu) ||
					recursive_in_array('index.php', $disabled_top_menu) 
				 )
				 $redirect = 'true';
		} elseif ( current_user_can('administrator') ) {  
			if (
					recursive_in_array('index.php', $disabled_menu_adm) ||
					recursive_in_array('index.php', $disabled_submenu_adm) ||
					recursive_in_array('index.php', $disabled_top_menu_adm) 
				 )
				 $redirect = 'true';
		}
		
		if ( $redirect == 'true' ) {
			$_mw_adminimize_db_redirect = _mw_adminimize_getOptionValue('_mw_adminimize_db_redirect');
			switch ($_mw_adminimize_db_redirect) {
			case 0:
				$_mw_adminimize_db_redirect = 'profile.php';
				break;
			case 1:
				$_mw_adminimize_db_redirect = 'edit.php';
				break;
			case 2:
				$_mw_adminimize_db_redirect = 'edit-pages.php';
				break;
			case 3:
				$_mw_adminimize_db_redirect = 'post-new.php';
				break;
			case 4:
				$_mw_adminimize_db_redirect = 'page-new.php';
				break;
			case 5:
				$_mw_adminimize_db_redirect = 'edit-comments.php';
				break;
			case 6:
				$_mw_adminimize_db_redirect = htmlspecialchars( stripslashes( _mw_adminimize_getOptionValue('_mw_adminimize_db_redirect_txt') ) );
				break;
			}

			$the_user = new WP_User($user_ID);
			reset($menu); $page = key($menu);
			
			while ( (__('Dashboard') != $menu[$page][0]) && next($menu) || (__('Dashboard') != $menu[$page][1]) && next($menu) )
				$page = key($menu);
				
			if (__('Dashboard') == $menu[$page][0] || __('Dashboard') == $menu[$page][1])
				unset($menu[$page]);
			reset($menu); $page = key($menu);
			
			while ( !$the_user->has_cap($menu[$page][1]) && next($menu) )
				$page = key($menu);
				
			if ( preg_match('#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI']) ) {
				if ( function_exists('admin_url') ) {
					wp_redirect( admin_url($_mw_adminimize_db_redirect) );
				} else {
					wp_redirect( get_option('siteurl') . '/wp-admin/' . $_mw_adminimize_db_redirect );
				}
			}
		}
	}
}


/**
 * remove the flash_uploader
 */
function _mw_adminimize_disable_flash_uploader() {
	return false;
}


/**
 * set user options from database in edit-area
 */
function _mw_adminimize_set_user_option_edit() {
	
	$_mw_adminimize_path = WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/';
	
	$_mw_adminimize_sidecat_admin_head = '';
	$_mw_adminimize_sidebar_wight = _mw_adminimize_getOptionValue('_mw_adminimize_sidebar_wight');
	switch ($_mw_adminimize_sidebar_wight) {
	case 300:
		$_mw_adminimize_sidecat_admin_head .= '<link rel="stylesheet" href="' . "\n";
		$_mw_adminimize_sidecat_admin_head .= $_mw_adminimize_path . 'mw_300_sidebar.css';
		$_mw_adminimize_sidecat_admin_head .= '" type="text/css" media="all" />' . "\n";
		break;
	case 400:
		$_mw_adminimize_sidecat_admin_head .= '<link rel="stylesheet" href="' . "\n";
		$_mw_adminimize_sidecat_admin_head .= $_mw_adminimize_path . 'mw_400_sidebar.css';
		$_mw_adminimize_sidecat_admin_head .= '" type="text/css" media="all" />' . "\n";
		break;
	case 20:
		$_mw_adminimize_sidecat_admin_head .= '<link rel="stylesheet" href="' . "\n";
		$_mw_adminimize_sidecat_admin_head .= $_mw_adminimize_path . 'mw_20p_sidebar.css';
		$_mw_adminimize_sidecat_admin_head .= '" type="text/css" media="all" />' . "\n";
		break;
	case 30:
		$_mw_adminimize_sidecat_admin_head .= '<link rel="stylesheet" href="' . "\n";
		$_mw_adminimize_sidecat_admin_head .= $_mw_adminimize_path . 'mw_30p_sidebar.css';
		$_mw_adminimize_sidecat_admin_head .= '" type="text/css" media="all" />' . "\n";
		break;
	}

	print($_mw_adminimize_sidecat_admin_head);
}


/**
 * set menu options from database
 */
function _mw_adminimize_set_menu_option() {
	global $pagenow, $menu, $submenu, $user_identity, $top_menu, $wp_version;
	
	$disabled_menu_subscriber      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_subscriber_items');
	$disabled_submenu_subscriber   = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_subscriber_items');
	$disabled_top_menu_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_subscriber_items');
	$disabled_menu_contributor     = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_contributor_items');
	$disabled_submenu_contributor  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_contributor_items');
	$disabled_top_menu_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_contributor_items');
	$disabled_menu_author          = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_author_items');
	$disabled_submenu_author       = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_author_items');
	$disabled_top_menu_author      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_author_items');
	$disabled_menu                 = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_items');
	$disabled_submenu              = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_items');
	$disabled_top_menu             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_items');
	$disabled_menu_adm             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_menu_adm_items');
	$disabled_submenu_adm          = _mw_adminimize_getOptionValue('mw_adminimize_disabled_submenu_adm_items');
	$disabled_top_menu_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_top_menu_adm_items');
	
	$_mw_adminimize_admin_head       = "\n";
	$_mw_adminimize_favorite_actions = _mw_adminimize_getOptionValue('_mw_adminimize_favorite_actions');
	$_mw_adminimize_screen_options   = _mw_adminimize_getOptionValue('_mw_adminimize_screen_options');
	$_mw_adminimize_user_info        = _mw_adminimize_getOptionValue('_mw_adminimize_user_info');
	$_mw_adminimize_ui_redirect      = _mw_adminimize_getOptionValue('_mw_adminimize_ui_redirect');

	switch ($_mw_adminimize_favorite_actions) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#favorite-actions\').remove(); });' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}
	
	switch ($_mw_adminimize_screen_options) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#screen-options\').remove(); });' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}	
	
	switch ($_mw_adminimize_user_info) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#user_info\').remove(); });' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	case 2:
		if ( version_compare(substr($wp_version, 0, 3), '2.7', '>=') ) {
			$_mw_adminimize_admin_head .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/mw_small_user_info27.css" type="text/css" />' . "\n";
		} else {
			$_mw_adminimize_admin_head .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/mw_small_user_info.css" type="text/css" />' . "\n";
		}
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#user_info\').remove();';
		if ($_mw_adminimize_ui_redirect == '1') {
			$_mw_adminimize_admin_head .= 'jQuery(\'div#wpcontent\').after(\'<div id="small_user_info"><p><a href="' . get_option('siteurl') . wp_nonce_url( ('/wp-login.php?action=logout&amp;redirect_to=') . get_option('siteurl') , 'log-out' ) . '" title="' . __('Log Out') . '">' . __('Log Out') . '</a></p></div>\') });' . "\n";
		} else {
			$_mw_adminimize_admin_head .= 'jQuery(\'div#wpcontent\').after(\'<div id="small_user_info"><p><a href="' . get_option('siteurl') . wp_nonce_url( ('/wp-login.php?action=logout') , 'log-out' ) . '" title="' . __('Log Out') . '">' . __('Log Out') . '</a></p></div>\') });' . "\n";
		}
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	case 3:
		if ( version_compare(substr($wp_version, 0, 3), '2.7', '>=') ) {
			$_mw_adminimize_admin_head .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/mw_small_user_info27.css" type="text/css" />' . "\n";
		} else {
			$_mw_adminimize_admin_head .= '<link rel="stylesheet" href="' . WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/css/mw_small_user_info.css" type="text/css" />' . "\n";
		}
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#user_info\').remove();';
		if ($_mw_adminimize_ui_redirect == '1') {
			$_mw_adminimize_admin_head .= 'jQuery(\'div#wpcontent\').after(\'<div id="small_user_info"><p><a href="' . get_option('siteurl') . ('/wp-admin/profile.php') . '">' . $user_identity . '</a> | <a href="' . get_option('siteurl') . wp_nonce_url( ('/wp-login.php?action=logout&amp;redirect_to=') . get_option('siteurl'), 'log-out' ) . '" title="' . __('Log Out') . '">' . __('Log Out') . '</a></p></div>\') });' . "\n";
		} else {
			$_mw_adminimize_admin_head .= 'jQuery(\'div#wpcontent\').after(\'<div id="small_user_info"><p><a href="' . get_option('siteurl') . ('/wp-admin/profile.php') . '">' . $user_identity . '</a> | <a href="' . get_option('siteurl') . wp_nonce_url( ('/wp-login.php?action=logout'), 'log-out' ) . '" title="' . __('Log Out') . '">' . __('Log Out') . '</a></p></div>\') });' . "\n";
		}
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}

	$_mw_adminimize_dashmenu      = _mw_adminimize_getOptionValue('_mw_adminimize_dashmenu');
	switch ($_mw_adminimize_dashmenu) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#dashmenu\').remove(); });' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}
	$_mw_adminimize_footer = _mw_adminimize_getOptionValue('_mw_adminimize_footer');
	switch ($_mw_adminimize_footer) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'jQuery(document).ready(function() { jQuery(\'#footer\').remove(); });' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}

	// timestamp open
	$_mw_adminimize_timestamp = _mw_adminimize_getOptionValue('_mw_adminimize_timestamp');
	switch ($_mw_adminimize_timestamp) {
	case 1:
		$_mw_adminimize_admin_head .= '<script type="text/javascript">' . "\n";
		$_mw_adminimize_admin_head .= "\t" . 'addLoadEvent(function(){jQuery(\'.edit-timestamp\').click();});' . "\n";
		$_mw_adminimize_admin_head .= '</script>' . "\n";
		break;
	}

	$_mw_adminimize_admin_head .= '<script type="text/javascript">
	/* <![CDATA[ */
	adminimizeL10n = {
		all: "' . __('All', 'adminimize') . '", none: "' . __('None', 'adminimize') . '"
	}
	/* ]]> */
	</script>';

	// set menu
	if ($disabled_menu != '') {
	
		// set admin-menu
		if ( current_user_can('administrator') ) {
			$mw_adminimize_menu     = $disabled_menu_adm;
			$mw_adminimize_submenu  = $disabled_submenu_adm;
			$mw_adminimize_top_menu = $disabled_top_menu_adm;
		} elseif ( current_user_can('editor') ) {  
			$mw_adminimize_menu     = $disabled_menu;
			$mw_adminimize_submenu  = $disabled_submenu;
			$mw_adminimize_top_menu = $disabled_top_menu;
		} elseif ( current_user_can('author') ) {  
			$mw_adminimize_menu     = $disabled_menu_author;
			$mw_adminimize_submenu  = $disabled_submenu_author;
			$mw_adminimize_top_menu = $disabled_top_menu_author;
		} elseif ( current_user_can('contributor') ) {  
			$mw_adminimize_menu     = $disabled_menu_contributor;
			$mw_adminimize_submenu  = $disabled_submenu_contributor;
			$mw_adminimize_top_menu = $disabled_top_menu_contributor;
		} elseif ( current_user_can('subscriber') ) {  
			$mw_adminimize_menu     = $disabled_menu_subscriber;
			$mw_adminimize_submenu  = $disabled_submenu_subscriber;
			$mw_adminimize_top_menu = $disabled_top_menu_subscriber;
		}
		
		foreach ($menu as $index => $item) {
			if ($item == 'index.php')
				continue;
		
			if ( isset($mw_adminimize_menu) && in_array($item[2], $mw_adminimize_menu) )
				unset($menu[$index]);
		
			if ( isset($submenu) && !empty($submenu[$item[2]]) ) {
				foreach ($submenu[$item[2]] as $subindex => $subitem) {
					if ( isset($mw_adminimize_submenu) && in_array($subitem[2], $mw_adminimize_submenu))
						unset($submenu[$item[2]][$subindex]);
				}
			}
			
			//top_menu, new in 2.7
			if ( isset($top_menu) && !empty($top_menu[$item[2]]) ) {
				foreach ($top_menu[$item[2]] as $subindex => $subitem) {
					if ( isset($mw_adminimize_top_menu) && in_array($subitem[2], $mw_adminimize_top_menu))
						unset($top_menu[$item[2]][$subindex]);
				}
			}
		}
				
	}
	
	print($_mw_adminimize_admin_head);
}


/**
 * set metabox options from database
 */
function _mw_adminimize_set_metabox_option() {
	global $pagenow;
	
	$_mw_adminimize_admin_head = "\n";
	
	// post
	if ( ('post-new.php' == $pagenow) || ('post.php' == $pagenow) ) {
		remove_action('admin_head', 'index_js');

		$disabled_metaboxes_post_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_subscriber_items');
		$disabled_metaboxes_post_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_contributor_items');
		$disabled_metaboxes_post_author      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_author_items');
		$disabled_metaboxes_post             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_items');
		$disabled_metaboxes_post_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_post_adm_items');
		
		if ( !isset($disabled_metaboxes_post_adm['0']) )
			$disabled_metaboxes_post_adm['0'] = '';
		if ( !isset($disabled_metaboxes_post['0']) )
			$disabled_metaboxes_post['0'] = '';
		if ( !isset($disabled_metaboxes_post_author['0']) )
			$disabled_metaboxes_post_author['0'] = '';
		if ( !isset($disabled_metaboxes_post_contributor['0']) )
			$disabled_metaboxes_post_contributor['0'] = '';
		if ( !isset($disabled_metaboxes_post_subscriber['0']) )
			$disabled_metaboxes_post_subscriber['0'] = '';
		if ( current_user_can('administrator') ) {
			$metaboxes = implode(',', $disabled_metaboxes_post_adm); // for admins
		} elseif ( current_user_can('editor') ) {
			$metaboxes = implode(',', $disabled_metaboxes_post); // editor
		} elseif ( current_user_can('author') ) {
			$metaboxes = implode(',', $disabled_metaboxes_post_author); // author
		} elseif ( current_user_can('contributor') ) {
			$metaboxes = implode(',', $disabled_metaboxes_post_contributor); // contributor
		} elseif ( current_user_can('subscriber') ) {
			$metaboxes = implode(',', $disabled_metaboxes_post_subscriber); // subscriber
		}
		
		$_mw_adminimize_admin_head .= '<style type="text/css">' . $metaboxes . ' {display: none !important}</style>' . "\n";
	}
	
	// page
	if ( ('page-new.php' == $pagenow) || ('page.php' == $pagenow) ) {
		remove_action('admin_head', 'index_js');
		
		$disabled_metaboxes_page_subscriber  = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_subscriber_items');
		$disabled_metaboxes_page_contributor = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_contributor_items');
		$disabled_metaboxes_page_editor      = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_editor_items');
		$disabled_metaboxes_page             = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_items');
		$disabled_metaboxes_page_adm         = _mw_adminimize_getOptionValue('mw_adminimize_disabled_metaboxes_page_adm_items');
		
		if ( !isset($disabled_metaboxes_page_subscriber['0']) )
			$disabled_metaboxes_page_subscriber['0'] = '';
		if ( !isset($disabled_metaboxes_page_contributor['0']) )
			$disabled_metaboxes_page_contributor['0'] = '';
		if ( !isset($disabled_metaboxes_page_author['0']) )
			$disabled_metaboxes_page_author['0'] = '';
		if ( !isset($disabled_metaboxes_page['0']) )
			$disabled_metaboxes_page['0'] = '';
		if ( !isset($disabled_metaboxes_page_adm['0']) )
			$disabled_metaboxes_page_adm['0'] = '';

		if ( current_user_can('administrator') ) {
			$metaboxes = implode(',', $disabled_metaboxes_page_adm); // admin
		} elseif ( current_user_can('editor') ) {
			$metaboxes = implode(',', $disabled_metaboxes_page); // editor
		} elseif ( current_user_can('author') ) {
			$metaboxes = implode(',', $disabled_metaboxes_page_author); // author
		} elseif ( current_user_can('contributor') ) {
			$metaboxes = implode(',', $disabled_metaboxes_page_contributor); // contributor
		} elseif ( current_user_can('subscriber') ) {
			$metaboxes = implode(',', $disabled_metaboxes_page_subscriber); // subscriber
		}
	
		$_mw_adminimize_admin_head .= '<style type="text/css">' . $metaboxes . ' {display: none !important}</style>' . "\n";
	}
	
	// hide colorscheme
	if ( ('profile.php' == $pagenow) && (_mw_adminimize_getOptionValue('mw_adminimize_disabled_colorscheme') == '1') ) {
		$_mw_adminimize_admin_head .= '<style type="text/css">#your-profile .form-table fieldset {display: none !important}</style>' . "\n";
	}
	
	print($_mw_adminimize_admin_head);
}


/**
 * small user-info
 * @uses $post_ID
 */
function _mw_adminimize_small_user_info() {
?>
	<div id="small_user_info">
		<p><a href="<?php echo wp_nonce_url( site_url('wp-login.php?action=logout'), 'log-out' ) ?>" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a></p>
	</div>
<?php
}


/**
 * include options-page in wp-admin
 */
require_once('adminimize_page.php');


/**
 * credit in wp-footer
 */
function _mw_adminimize_admin_footer() {
	$plugin_data = get_plugin_data( __FILE__ );
	$plugin_data['Title'] = $plugin_data['Name'];
	if ( !empty($plugin_data['PluginURI']) && !empty($plugin_data['Name']) )
		$plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '" title="'.__( 'Visit plugin homepage' ).'">' . $plugin_data['Name'] . '</a>';
	
	if ( basename($_SERVER['REQUEST_URI']) == 'adminimize.php') {
		printf('%1$s ' . __('plugin') . ' | ' . __('Version') . ' <a href="http://bueltge.de/wordpress-admin-theme-adminimize/674/#historie" title="' . __('History', 'adminimize') . '">%2$s</a> | ' . __('Author') . ' %3$s<br />', $plugin_data['Title'], $plugin_data['Version'], $plugin_data['Author']);
	}
	if ( _mw_adminimize_getOptionValue('_mw_adminimize_advice') == 1 && basename($_SERVER['REQUEST_URI']) != 'adminimize.php' ) {
		printf('%1$s ' . __('plugin activate', 'adminimize') . ' | ' . stripslashes( _mw_adminimize_getOptionValue('_mw_adminimize_advice_txt') ) . '<br />', $plugin_data['Title']);
	}
}


/**
 * Add action link(s) to plugins page
 * Thanks Dion Hulse -- http://dd32.id.au/wordpress-plugins/?configure-link
 */
function _mw_adminimize_filter_plugin_actions($links, $file){
	static $this_plugin;

	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);

	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=adminimize/adminimize.php">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}


/**
 * Images/ Icons in base64-encoding
 * @use function _mw_adminimize_get_resource_url() for display
 */
if( isset($_GET['resource']) && !empty($_GET['resource'])) {
	# base64 encoding performed by base64img.php from http://php.holtsmark.no
	$resources = array(
		'adminimize.gif' =>
		'R0lGODlhCwALAKIEAPb29tTU1Kurq5SUlP///wAAAAAAAAAAAC'.
		'H5BAEAAAQALAAAAAALAAsAAAMlSErTuw1Ix4a8s4mYgxZbE4wf'.
		'OIzkAJqop64nWm7tULHu0+xLAgA7'.
		'');
	
	if(array_key_exists($_GET['resource'], $resources)) {

		$content = base64_decode($resources[ $_GET['resource'] ]);

		$lastMod = filemtime(__FILE__);
		$client = ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false );
		// Checking if the client is validating his cache and if it is current.
		if (isset($client) && (strtotime($client) == $lastMod)) {
			// Client's cache IS current, so we just respond '304 Not Modified'.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 304);
			exit;
		} else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $lastMod).' GMT', true, 200);
			header('Content-Length: '.strlen($content));
			header('Content-Type: image/' . substr(strrchr($_GET['resource'], '.'), 1) );
			echo $content;
			exit;
		}
	}
}


/**
 * Display Images/ Icons in base64-encoding
 * @return $resourceID
 */
function _mw_adminimize_get_resource_url($resourceID) {
	
	return trailingslashit( get_bloginfo('url') ) . '?resource=' . $resourceID;
}


/**
 * settings in plugin-admin-page
 */
function _mw_adminimize_add_settings_page() {
	global $wp_version;

	if( current_user_can('switch_themes') && function_exists('add_submenu_page') ) {
		
		$menutitle = '';
		if ( version_compare( $wp_version, '2.6.999', '>' ) ) {
			$menutitle = '<img src="' . _mw_adminimize_get_resource_url('adminimize.gif') . '" alt="" />';
		}
		$menutitle .= ' ' . __('Adminimize', 'adminimize');

		add_submenu_page('options-general.php', __('Adminimize Options', 'adminimize'), $menutitle, 8, __FILE__, '_mw_adminimize_options');
		add_filter('plugin_action_links', '_mw_adminimize_filter_plugin_actions', 10, 2);
	}
}


/**
 * Set theme for users
 */
function _mw_adminimize_set_theme() {

	if ( !current_user_can('edit_users') )
		wp_die(__('Cheatin&#8217; uh?'));

	$user_ids    = $_POST[mw_adminimize_theme_items];
	$admin_color = htmlspecialchars( stripslashes( $_POST[_mw_adminimize_set_theme] ) );

	if ( !$user_ids )
		return false;

	foreach( $user_ids as $user_id) {
		update_usermeta($user_id, 'admin_color', $admin_color);
	}
}


/**
 * read otpions
 */
function _mw_adminimize_getOptionValue($key) {

	$adminimizeoptions = get_option('mw_adminimize');
	return ($adminimizeoptions[$key]);
}


/**
 * Update options in database
 */
function _mw_adminimize_update() {
	global $menu, $submenu, $adminimizeoptions;

	if (isset($_POST['_mw_adminimize_favorite_actions'])) {
		$adminimizeoptions['_mw_adminimize_favorite_actions'] = strip_tags(stripslashes($_POST['_mw_adminimize_favorite_actions']));
	} else {
		$adminimizeoptions['_mw_adminimize_favorite_actions'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_screen_options'])) {
		$adminimizeoptions['_mw_adminimize_screen_options'] = strip_tags(stripslashes($_POST['_mw_adminimize_screen_options']));
	} else {
		$adminimizeoptions['_mw_adminimize_screen_options'] = 0;
	}

	if (isset($_POST['_mw_adminimize_menu_order'])) {
		$adminimizeoptions['_mw_adminimize_menu_order'] = strip_tags(stripslashes($_POST['_mw_adminimize_menu_order']));
	} else {
		$adminimizeoptions['_mw_adminimize_menu_order'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_user_info'])) {
		$adminimizeoptions['_mw_adminimize_user_info'] = strip_tags(stripslashes($_POST['_mw_adminimize_user_info']));
	} else {
		$adminimizeoptions['_mw_adminimize_user_info'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_dashmenu'])) {
		$adminimizeoptions['_mw_adminimize_dashmenu'] = strip_tags(stripslashes($_POST['_mw_adminimize_dashmenu']));
	} else {
		$adminimizeoptions['_mw_adminimize_dashmenu'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_sidebar_wight'])) {
		$adminimizeoptions['_mw_adminimize_sidebar_wight'] = strip_tags(stripslashes($_POST['_mw_adminimize_sidebar_wight']));
	} else {
		$adminimizeoptions['_mw_adminimize_sidebar_wight'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_footer'])) {
		$adminimizeoptions['_mw_adminimize_footer'] = strip_tags(stripslashes($_POST['_mw_adminimize_footer']));
	} else {
		$adminimizeoptions['_mw_adminimize_footer'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_writescroll'])) {
		$adminimizeoptions['_mw_adminimize_writescroll'] = strip_tags(stripslashes($_POST['_mw_adminimize_writescroll']));
	} else {
		$adminimizeoptions['_mw_adminimize_writescroll'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_tb_window'])) {
		$adminimizeoptions['_mw_adminimize_tb_window'] = strip_tags(stripslashes($_POST['_mw_adminimize_tb_window']));
	} else {
		$adminimizeoptions['_mw_adminimize_tb_window'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_db_redirect'])) {
		$adminimizeoptions['_mw_adminimize_db_redirect'] = strip_tags(stripslashes($_POST['_mw_adminimize_db_redirect']));
	} else {
		$adminimizeoptions['_mw_adminimize_db_redirect'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_ui_redirect'])) {
		$adminimizeoptions['_mw_adminimize_ui_redirect'] = strip_tags(stripslashes($_POST['_mw_adminimize_ui_redirect']));
	} else {
		$adminimizeoptions['_mw_adminimize_ui_redirect'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_advice'])) {
		$adminimizeoptions['_mw_adminimize_advice'] = strip_tags(stripslashes($_POST['_mw_adminimize_advice']));
	} else {
		$adminimizeoptions['_mw_adminimize_advice'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_advice_txt'])) {
		$adminimizeoptions['_mw_adminimize_advice_txt'] = stripslashes($_POST['_mw_adminimize_advice_txt']);
	} else {
		$adminimizeoptions['_mw_adminimize_advice_txt'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_timestamp'])) {
		$adminimizeoptions['_mw_adminimize_timestamp'] = strip_tags(stripslashes($_POST['_mw_adminimize_timestamp']));
	} else {
		$adminimizeoptions['_mw_adminimize_timestamp'] = 0;
	}
	
	if (isset($_POST['_mw_adminimize_db_redirect_txt'])) {
		$adminimizeoptions['_mw_adminimize_db_redirect_txt'] = stripslashes($_POST['_mw_adminimize_db_redirect_txt']);
	} else {
		$adminimizeoptions['_mw_adminimize_db_redirect_txt'] = 0;
	}
	
	// menu update
	if (isset($_POST['mw_adminimize_disabled_menu_subscriber_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_menu_subscriber_items'] = $_POST['mw_adminimize_disabled_menu_subscriber_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_menu_subscriber_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_submenu_subscriber_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_submenu_subscriber_items'] = $_POST['mw_adminimize_disabled_submenu_subscriber_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_submenu_subscriber_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_top_menu_subscriber_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_subscriber_items'] = $_POST['mw_adminimize_disabled_top_menu_subscriber_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_subscriber_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_menu_contributor_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_menu_contributor_items'] = $_POST['mw_adminimize_disabled_menu_contributor_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_menu_contributor_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_submenu_contributor_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_submenu_contributor_items'] = $_POST['mw_adminimize_disabled_submenu_contributor_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_submenu_contributor_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_top_menu_contributor_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_contributor_items'] = $_POST['mw_adminimize_disabled_top_menu_contributor_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_contributor_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_menu_author_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_menu_author_items'] = $_POST['mw_adminimize_disabled_menu_author_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_menu_author_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_submenu_author_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_submenu_author_items'] = $_POST['mw_adminimize_disabled_submenu_author_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_submenu_author_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_top_menu_author_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_author_items'] = $_POST['mw_adminimize_disabled_top_menu_author_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_author_items'] = array();
	}

	if (isset($_POST['mw_adminimize_disabled_menu_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_menu_items'] = $_POST['mw_adminimize_disabled_menu_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_menu_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_submenu_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_submenu_items'] = $_POST['mw_adminimize_disabled_submenu_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_submenu_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_top_menu_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_items'] = $_POST['mw_adminimize_disabled_top_menu_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_items'] = array();
	}

	if (isset($_POST['mw_adminimize_disabled_menu_adm_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_menu_adm_items'] = $_POST['mw_adminimize_disabled_menu_adm_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_menu_adm_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_submenu_adm_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_submenu_adm_items'] = $_POST['mw_adminimize_disabled_submenu_adm_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_submenu_adm_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_top_menu_adm_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_adm_items'] = $_POST['mw_adminimize_disabled_top_menu_adm_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_top_menu_adm_items'] = array();
	}
	
	// metaboxes update
	if (isset($_POST['mw_adminimize_disabled_metaboxes_post_adm_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_adm_items'] = $_POST['mw_adminimize_disabled_metaboxes_post_adm_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_adm_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_page_adm_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_adm_items'] = $_POST['mw_adminimize_disabled_metaboxes_page_adm_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_adm_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_post_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_items'] = $_POST['mw_adminimize_disabled_metaboxes_post_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_page_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_items'] = $_POST['mw_adminimize_disabled_metaboxes_page_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_post_author_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_author_items'] = $_POST['mw_adminimize_disabled_metaboxes_post_author_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_author_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_page_author_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_author_items'] = $_POST['mw_adminimize_disabled_metaboxes_page_author_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_author_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_post_contributor_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_contributor_items'] = $_POST['mw_adminimize_disabled_metaboxes_post_contributor_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_contributor_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_page_contributor_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_contributor_items'] = $_POST['mw_adminimize_disabled_metaboxes_page_contributor_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_contributor_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_post_subscriber_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_subscriber_items'] = $_POST['mw_adminimize_disabled_metaboxes_post_subscriber_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_subscriber_items'] = array();
	}
	
	if (isset($_POST['mw_adminimize_disabled_metaboxes_page_subscriber_items'])) {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_subscriber_items'] = $_POST['mw_adminimize_disabled_metaboxes_page_subscriber_items'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_subscriber_items'] = array();
	}
	
	// color scheme
	if (isset($_POST['mw_adminimize_disabled_colorscheme'])) {
		$adminimizeoptions['mw_adminimize_disabled_colorscheme'] = $_POST['mw_adminimize_disabled_colorscheme'];
	} else {
		$adminimizeoptions['mw_adminimize_disabled_colorscheme'] = array();
	}
	
	update_option('mw_adminimize', $adminimizeoptions);
	$adminimizeoptions = get_option('mw_adminimize');
	
	$myErrors = new _mw_adminimize_message_class();
	$myErrors = '<div id="message" class="updated fade"><p>' . $myErrors->get_error('_mw_adminimize_update') . '</p></div>';
	echo $myErrors;
}


/**
 * Delete options in database
 */
function _mw_adminimize_deinstall() {

	delete_option('mw_adminimize');
}


/**
 * Install options in database
 */
function _mw_adminimize_install() {
	global $menu, $submenu, $top_menu;

	$adminimizeoptions = array();
	
	$adminimizeoptions['mw_adminimize_disabled_menu_subscriber_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_submenu_subscriber_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_top_menu_subscriber_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_menu_contributor_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_submenu_contributor_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_top_menu_contributor_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_menu_author_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_submenu_author_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_top_menu_author_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_menu_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_submenu_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_top_menu_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_menu_adm_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_submenu_adm_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_top_menu_adm_items'] = array();
	
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_subscriber_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_subscriber_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_contributor_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_contributor_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_author_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_author_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_post_adm_items'] = array();
	$adminimizeoptions['mw_adminimize_disabled_metaboxes_page_adm_items'] = array();
	
	$adminimizeoptions['mw_adminimize_default_menu'] = $menu;
	$adminimizeoptions['mw_adminimize_default_submenu'] = $submenu;
	if ( isset($top_menu) )
		$adminimizeoptions['mw_adminimize_default_top_menu'] = $top_menu;
	
	add_option('mw_adminimize', $adminimizeoptions);
}
?>