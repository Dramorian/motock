<?php
/*
Plugin Name: rss2email.ru
Plugin URI: http://maxsite.org/
Description: Плагин позволяет выбрать и добавить кнопку и форму подписки на RSS-ленту блога через сервис <strong>rss2email.ru</strong>. Поддерживает виджеты
Author: Максима (maxsite.org)
Version: 1.1
Author URI: http://maxsite.org/
*/ 

include_once( 'rss2email-plugin.class.php' );
$r2e_plugin = new maxsite_rss2email_plugin();

$path_to_php_file_plugin = 'rss2email/rss2email-plugin.php';

$r2e_plugin->page_title = 'Настройки RSS<small>2</small>Email.RU';
$r2e_plugin->menu_title = 'RSS2Email.RU';
$r2e_plugin->access_level = 9;
$r2e_plugin->add_page_to = 2;
$r2e_plugin->short_description = '';

add_action('admin_menu', array($r2e_plugin, 'add_admin_menu'));
add_action('deactivate_' . $path_to_php_file_plugin, array($r2e_plugin, 'deactivate')); 
add_action('activate_' . $path_to_php_file_plugin, array($r2e_plugin, 'activate')); 


function rss2email_button($arg = array()) {
	global $r2e_plugin;
	return $r2e_plugin->button($arg);
}

function rss2email_button_options() {
	global $r2e_plugin;
	return $r2e_plugin->button_options();
}

function rss2email_form($arg = array()) {
	global $r2e_plugin;
	return $r2e_plugin->widget($arg);
}

function rss2email_form_options() {
	global $r2e_plugin;
	return $r2e_plugin->widget_options();
}

function rss2email_widget_init($arg) {
	if ( function_exists('register_sidebar_widget') ) {
		register_sidebar_widget('Кнопка rss2email', 'rss2email_button');
		register_widget_control('Кнопка rss2email', 'rss2email_button_options', 200, 270);	
		register_sidebar_widget('Форма rss2email', 'rss2email_form');
		register_widget_control('Форма rss2email', 'rss2email_form_options', 200, 110);
	}
}

add_action('init', 'rss2email_widget_init', 1);

?>