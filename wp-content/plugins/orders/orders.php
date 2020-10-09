<?php
/*
 * Plugin Name: Orders Plugin
 * Description: Плагин для сохранения Заказов в админке
 */

$orders_table_name = 'wp_orders';
global $orders_table_name;

add_action('init', 'orders');

add_option("createTableDB", "1.0");

function orders() {
    createTableDB();
    add_action('admin_menu', 'add_custom_menu_orders');
}

function add_custom_menu_orders()
{
    add_menu_page('Заказы', 'Заказы', 'administrator', 'orders/items.php', '', 'dashicons-phone', 1);
}


function createTableDB() {
    global $wpdb;
    global $orders_table_name;
    $table_name = $orders_table_name;
    if ($wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name) ) != $table_name) {

        $sql = 'CREATE TABLE '.$table_name.'(
        `ID` bigint(20) UNSIGNED NOT NULL,
        `date` datetime NOT NULL DEFAULT \'0000-00-00 00:00:00\',
        `content` text NOT NULL,
        PRIMARY KEY (ID))';

        require_once(ABSPATH.'wp-admin/includes/upgrade.php');
        dbDelta($sql);


    }
}
register_activation_hook(__FILE__, 'createTableDB');

?>