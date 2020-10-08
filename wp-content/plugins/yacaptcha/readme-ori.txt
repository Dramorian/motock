--------------------------------------------------------------------------------

yaCAPTCHA: Yet Another CAPTCHA plugin for WordPress based on KCAPTCHA

--------------------------------------------------------------------------------

Version 1.0
by Rémy Roy (http://www.remyroy.com)

Plugin home page: http://www.remyroy.com/yacaptcha

--------------------------------------------------------------------------------

yaCAPTCHA is a CAPTCHA plugin for WordPress that adds an image in the comment
form of your WordPress application. In order to post comments, users will have
to write down the characters that are part of the image. This can help prevent
spam from automated bots.

--------------------------------------------------------------------------------

* Requirements

    - WordPress 2.0.11 or above.
    - PHP 4.0.6 or above with GD2 library support.
    - Theme must support the 'comment_form' action.

* How to install?

    You must first copy the directory yacaptcha in your WordPress plugin
    directory which must be /wp-content/plugins/ . After, you need to go in your
    site admin, in the Plugins section and activate the yaCAPTCHA plugin.
    
    It should works flawlessly with the default theme.
	
	The default theme places the additionnal comment form items after the submit
	button. I suggest you change it so that it appears before the submit button.
	This might also apply to other themes. Have a look at the "How can I customize
	it?" section.

* How can I customize it?

    You can customize the image properties like how many characters are shown or
    which characters are used by changing values in the kcaptcha_config.php
    file.
    
    You can change the location of the CAPTCHA field within the comment form by
    changing the location of the 'comment_form' call in your theme's
    comments.php file. The CAPTCHA field appears at the same location as the
    'comment_form' call.
    
    You can customize what appears in the CAPTCHA field, the messages used and
    how it is displayed be changing the code in the yaCaptchaCommentForm
    function located in the yacaptcha.php file.

* It does not work!

    First, make sure that you meet all the requirements. Make sure that your
    theme support the 'comment_form' action. There should be a call similar to
    do_action('comment_form', $post->ID) in your theme's comments.php file where
    the comment form section is located.
    
    If you are still having problems, you can contact me.

--------------------------------------------------------------------------------

Version history

* 1.0

	Updated the CAPTCHA generator, KCAPTCHA, to the latest version. Tested to
	work with Wordpress 2.5.1 and the default theme.

* 0.9

	Make all file cases lower to prevent potential problems.  Remove CAPTCHA for
	logged users.

* 0.8

    Initial version. Should work flawlessly with WordPress 2.3.2 and the default
    theme.

--------------------------------------------------------------------------------

Thanks

    Thanks to Kruglov Sergei for creating KCAPTCHA, his pretty good CAPTCHA. You
    can visit KCAPTCHA website at http://www.captcha.ru/en/ .

--------------------------------------------------------------------------------

    Copyright 2008  Rémy Roy  (email : remyroy@remyroy.com)

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