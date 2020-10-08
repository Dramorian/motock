<?php
/*
Plugin Name: yaCAPTCHA
Plugin URI: http://www.remyroy.com/yacaptcha
Description: Yet Another CAPTCHA plugin for WordPress based on <a href="http://www.captcha.ru/en/">KCAPTCHA</a>.
Version: 0.9
Author: Rémy Roy
Author URI: http://www.remyroy.com
*/
/*  Copyright 2008  Rémy Roy  (email : remyroy@remyroy.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

    function yaCaptchaInit() {
        add_action('comment_form', 'yaCaptchaCommentForm');
        add_action('comment_post', 'yaCaptchaCommentPost');
	
		add_filter('comment_post_redirect', 'yaCaptchaCommentPostRedirect', 10, 2);
    }
    
    function yaCaptchaCommentForm($id) {
	
		global $userdata;
		get_currentuserinfo();
	
		if ('' == $userdata->ID) {
	        ?>
	        <p>
		    
		    <?php if (isset($_GET['yac']) && $_GET['yac'] == 'e') {
		
		    ?>
		    
		    <small class="yacerror">You must enter the characters as seen in this image correctly.</small> <br />
		    
		    <?php
		    } ?>
		    
		    <img src="<?php bloginfo('url'); ?>/wp-content/plugins/yacaptcha/captcha-image.php" width="120" height="60" alt="CAPTCHA image" />
			   
		    <br />
		    
		    <input id="captcha" name="captcha" type="text" value="" />
		    
		    <label for="captcha"><small>Characters in the image above (required)</small></label>
		    
			</p>
	        <?php
		}
    }
    
    function yaCaptchaCommentPost($id) {
	
		global $userdata;
		get_currentuserinfo();
		
		session_start();
	
        if ('' == $userdata->ID && !(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['captcha'])) {
	    
	    wp_set_comment_status($id, 'delete');
	    
		}
    }
    
    function yaCaptchaCommentPostRedirect($location, $comment) {
	
		global $userdata;
		get_currentuserinfo();
	
		session_start();
	
        if ('' == $userdata->ID && !(isset($_SESSION['captcha_keystring']) && $_SESSION['captcha_keystring'] ==  $_POST['captcha'])) {
	    
		    $parsedLocation = parse_url($location);
		    
		    $parsedLocation['fragment'] = 'commentform';
		    
		    if (isset($parsedLocation['query']) && $parsedLocation['query'] != '') {
				$parsedLocation['query'] .= '&yac=e';
		    }
		    else {
			$parsedLocation['query'] = 'yac=e';
		    }
	    
			return glue_url($parsedLocation);
		
		}
		else {
		    return $location;
		}
	
    }
    
    function glue_url($parsed)
    {
	if (!is_array($parsed)) return false;
	$uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '' : '//') : '';
	$uri .= isset($parsed['user']) ? $parsed['user'].(isset($parsed['pass']) ? ':'.$parsed['pass'] : '').'@' : '';
	$uri .= isset($parsed['host']) ? $parsed['host'] : '';
	$uri .= isset($parsed['port']) ? ':'.$parsed['port'] : '';
	if(isset($parsed['path']))
	{
	    $uri .= (substr($parsed['path'], 0, 1) == '/') ? $parsed['path'] : ('/'.$parsed['path']);
	}
	$uri .= isset($parsed['query']) ? '?'.$parsed['query'] : '';
	$uri .= isset($parsed['fragment']) ? '#'.$parsed['fragment'] : '';
	return $uri;
    }
    
    yaCaptchaInit();

?>