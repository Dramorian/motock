<?php
/* 
Plugin Name: Login LockDown
Plugin URI: http://www.bad-neighborhood.com/
Version: v1.2
Author: Michael VanDeMar
Description: Adds some extra security to WordPress by restricting the rate at which failed logins can be re-attempted from a given IP range. Distributed through <a href="http://www.bad-neighborhood.com/" target="_blank">Bad Neighborhood</a>.
*/

/*
* Change Log
*
* ver. 1.0 29-Aug-2007
* - released
*
* ver. 1.1 01-Sep-2007
* - revised time query to MySQL 4.0 compatability
*
* ver. 1.2 15-Jun-2008
* - now compatible with WordPress 2.5 and up only
*/

/*
== Installation ==

1. Extract loginlockdown-1.2.zip into your wp-content/plugins directory into its own folder.
2. Activate the plugin in the Plugin options.
3. Customize the settings from the Options panel, if desired.

*/

/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| Login LockDown - added security measures to WordPress intended to  |
| inhibit or reduce brute force password discovery.                  |
| Copyright (C) 2007, Michael VanDeMar,                              |
| http://www.bad-neighborhood.com                                    |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/

$loginlockdown_db_version = "1.0";
$loginlockdownOptions = get_loginlockdownOptions();

function loginLockdown_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . "login_fails";

	if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
		$sql = "CREATE TABLE " . $table_name . " (
			`login_attempt_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`login_attempt_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`login_attempt_IP` varchar(100) NOT NULL default '',
			PRIMARY KEY  (`login_attempt_ID`)
			);";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
		add_option("loginlockdown_db1_version", $loginlockdown_db_version);
	}

	$table_name = $wpdb->prefix . "lockdowns";

	if( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
		$sql = "CREATE TABLE " . $table_name . " (
			`lockdown_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`lockdown_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`release_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`lockdown_IP` varchar(100) NOT NULL default '',
			PRIMARY KEY  (`lockdown_ID`)
			);";

		require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
		dbDelta($sql);
		add_option("loginlockdown_db2_version", $loginlockdown_db_version);
	}
}

function countFails($username = "") {
	global $wpdb;
	global $loginlockdownOptions;
	$table_name = $wpdb->prefix . "login_fails";
	$ip = $_SERVER['REMOTE_ADDR'];
	$class_c = substr ($ip, 0 , strrpos ( $ip, "." ));

	$numFails = $wpdb->get_var("SELECT COUNT(login_attempt_ID) FROM $table_name " . 
					"WHERE login_attempt_date + INTERVAL " .
					$loginlockdownOptions['retries_within'] . " MINUTE > now() AND " . 
					"login_attempt_IP LIKE '$class_c%'");
	return $numFails;
}

function incrementFails($username = "") {
	global $wpdb;
	$table_name = $wpdb->prefix . "login_fails";
	$ip = $_SERVER['REMOTE_ADDR'];

	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	if ( $user ) {
		$insert = "INSERT INTO " . $table_name . " (user_id, login_attempt_date, login_attempt_IP) " .
				"VALUES ('" . $user->ID . "', now(), '" . $ip . "')";
		$results = $wpdb->query($insert);
	}
}

function lockDown($username = "") {
	global $wpdb;
	global $loginlockdownOptions;
	$table_name = $wpdb->prefix . "lockdowns";
	$ip = $_SERVER['REMOTE_ADDR'];

	$username = sanitize_user($username);
	$user = get_userdatabylogin($username);
	if ( $user ) {
		$insert = "INSERT INTO " . $table_name . " (user_id, lockdown_date, release_date, lockdown_IP) " .
				"VALUES ('" . $user->ID . "', now(), date_add(now(), INTERVAL " .
				$loginlockdownOptions['lockout_length'] . " MINUTE), '" . $ip . "')";
		$results = $wpdb->query($insert);
	}
}

function isLockedDown() {
	global $wpdb;
	$table_name = $wpdb->prefix . "lockdowns";
	$ip = $_SERVER['REMOTE_ADDR'];
	$class_c = substr ($ip, 0 , strrpos ( $ip, "." ));

	$stillLocked = $wpdb->get_var("SELECT user_id FROM $table_name " . 
					"WHERE release_date > now() AND " . 
					"lockdown_IP LIKE '$class_c%'");

	return $stillLocked;
}

function listLockedDown() {
	global $wpdb;
	$table_name = $wpdb->prefix . "lockdowns";

	$listLocked = $wpdb->get_results("SELECT lockdown_ID, floor((UNIX_TIMESTAMP(release_date)-UNIX_TIMESTAMP(now()))/60) AS minutes_left, ".
					"lockdown_IP FROM $table_name WHERE release_date > now()", ARRAY_A);

	return $listLocked;
}

function get_loginlockdownOptions() {
	$loginlockdownAdminOptions = array(
		'max_login_retries' => 3,
		'retries_within' => 5,
		'lockout_length' => 60);
	$loginlockdownOptions = get_option("loginlockdownAdminOptions");
	if ( !empty($loginlockdownOptions) ) {
		foreach ( $loginlockdownOptions as $key => $option ) {
			$loginlockdownAdminOptions[$key] = $option;
		}
	}
	update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
	return $loginlockdownAdminOptions;
}

function print_loginlockdownAdminPage() {
	global $wpdb;
	$table_name = $wpdb->prefix . "lockdowns";
	$loginlockdownAdminOptions = get_loginlockdownOptions();

	if (isset($_POST['update_loginlockdownSettings'])) {
		if (isset($_POST['ll_max_login_retries'])) {
			$loginlockdownAdminOptions['max_login_retries'] = $_POST['ll_max_login_retries'];
		}
		if (isset($_POST['ll_retries_within'])) {
			$loginlockdownAdminOptions['retries_within'] = $_POST['ll_retries_within'];
		}
		if (isset($_POST['ll_lockout_length'])) {
			$loginlockdownAdminOptions['lockout_length'] = $_POST['ll_lockout_length'];
		}
		update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
		?>
<div class="updated"><p><strong><?php _e("Settings Updated.", "loginlockdown");?></strong></p></div>
		<?php
	}
	if (isset($_POST['release_lockdowns'])) {
		if (isset($_POST['releaseme'])) {
			$released = $_POST['releaseme'];
			foreach ( $released as $release_id ) {
				$results = $wpdb->query("UPDATE $table_name SET release_date = now() " .
							"WHERE lockdown_ID = $release_id");
			}
		}
		update_option("loginlockdownAdminOptions", $loginlockdownAdminOptions);
		?>
<div class="updated"><p><strong><?php _e("Lockdowns Released.", "loginlockdown");?></strong></p></div>
		<?php
	}
	$dalist = listLockedDown();
?>
<div class=wrap>
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h2><?php _e('Login LockDown Options', 'loginlockdown') ?></h2>
<h3><?php _e('Max Login Retries', 'loginlockdown') ?></h3>
<input name="ll_max_login_retries" size="8" value="<?php echo $loginlockdownAdminOptions['max_login_retries']; ?>">
<h3><?php _e('Retry Time Period Restriction (minutes)', 'loginlockdown') ?></h3>
<input name="ll_retries_within" size="8" value="<?php echo $loginlockdownAdminOptions['retries_within']; ?>">
<h3><?php _e('Lockout Length (minutes)', 'loginlockdown') ?></h3>
<input name="ll_lockout_length" size="8" value="<?php echo $loginlockdownAdminOptions['lockout_length']; ?>">
<div class="submit">
<input type="submit" name="update_loginlockdownSettings" value="<?php _e('Update Settings', 'loginlockdown') ?>" /></div>
</form>
<br />
<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<h3><?php _e('Currently Locked Out', 'loginlockdown') ?></h3>
<?php
	$num_lockedout = count($dalist);
	if( 0 == $num_lockedout ) {
		echo "<p>No current IP blocks locked out.</p>";
	} else {
		foreach ( $dalist as $key => $option ) {
			?>
<li><input type="checkbox" name="releaseme[]" value="<?php echo $option['lockdown_ID']; ?>"> <?php echo $option['lockdown_IP']; ?> (<?php echo $option['minutes_left']; ?> minutes left)</li>
			<?php
		}
	}
?>
<div class="submit">
<input type="submit" name="release_lockdowns" value="<?php _e('Release Selected', 'loginlockdown') ?>" /></div>
</form>
</div>
<?php
}//End function print_loginlockdownAdminPage()

function loginlockdown_ap() {
	if ( function_exists('add_options_page') ) {
		add_options_page('Login LockDown', 'Login LockDown', 9, basename(__FILE__), 'print_loginlockdownAdminPage');
	}
}

function ll_credit_link(){
	echo "<p>Login form protected by <a href='http://www.bad-neighborhood.com/login-lockdown.html'>Login LockDown</a>.</p>";
}

//Actions and Filters   
if ( isset($loginlockdown_db_version) ) {
	//Actions
	add_action('admin_menu', 'loginlockdown_ap');
	add_action('activate_loginlockdown/loginlockdown.php', 'loginLockdown_install');
	add_action('login_form', 'll_credit_link');
	//Filters
	//Functions
	if ( !function_exists('wp_authenticate') ) :
	function wp_authenticate($username, $password) {
		global $wpdb, $error;
		global $loginlockdownOptions;

		if ( 0 < isLockedDown() ) {
			return new WP_Error('incorrect_password', "<strong>ERROR</strong>: We're sorry, but this IP range has been blocked due to too many recent " .
					"failed login attempts.<br /><br />Please try again later.");
		}

		if ( '' == $username )
			return new WP_Error('empty_username', __('<strong>ERROR</strong>: The username field is empty.'));

		if ( '' == $password ) {
			return new WP_Error('empty_password', __('<strong>ERROR</strong>: The password field is empty.'));
		}

		$user = get_userdatabylogin($username);

		if ( !$user || ($user->user_login != $username) ) {
			do_action( 'wp_login_failed', $username );
			return new WP_Error('invalid_username', __('<strong>ERROR</strong>: Invalid username.'));
		}

		$user = apply_filters('wp_authenticate_user', $user, $password);
		if ( is_wp_error($user) ) {
			incrementFails($username);
			if ( $loginlockdownOptions['max_login_retries'] <= countFails($username) ) {
				lockDown($username);
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry, but this IP range has been blocked due to too many recent " .
						"failed login attempts.<br /><br />Please try again later."));
			}
			do_action( 'wp_login_failed', $username );
			return $user;
		}

		if ( !wp_check_password($password, $user->user_pass, $user->ID) ) {
			incrementFails($username);
			if ( $loginlockdownOptions['max_login_retries'] <= countFails($username) ) {
				lockDown($username);
				return new WP_Error('incorrect_password', __("<strong>ERROR</strong>: We're sorry, but this IP range has been blocked due to too many recent " .
						"failed login attempts.<br /><br />Please try again later."));
			}
			do_action( 'wp_login_failed', $username );
			return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Incorrect password.'));
		}

		return new WP_User($user->ID);

	}
	endif;
}


?>