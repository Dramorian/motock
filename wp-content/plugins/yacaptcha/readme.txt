=== yaCAPTCHA ===
Contributors: remyroy
Donate link: http://www.remyroy.com
Tags: comments, spam, captcha
Requires at least: 2.0.11
Tested up to: 2.6
Stable tag: 1.0

yaCAPTCHA is a CAPTCHA plugin for WordPress that adds an image in the comment
form of your WordPress application.

== Description ==

yaCAPTCHA is a CAPTCHA plugin for WordPress that adds an image in the comment
form of your WordPress application. In order to post comments, users will have
to write down the characters that are part of the image. This can help prevent
spam from automated bots.

= Requirements =

* WordPress 2.0.11 or above.
* PHP 4.0.6 or above with GD2 library support.
* Theme must support the 'comment_form' action.

== Installation ==

1. You must first copy the directory yacaptcha in your WordPress plugin directory which must be /wp-content/plugins/.
2. After, you need to go in your site admin, in the Plugins section and activate the yaCAPTCHA plugin.
3. The default theme places the additionnal comment form items after the submit button. I suggest you change it so that it appears before the submit button. This might also apply to other themes. Have a look at the "How can I customize it?" section in the FAQ.

== Frequently Asked Questions ==

= How can I customize it? =

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

= It does not work! =

First, make sure that you meet all the requirements. Make sure that your
theme support the 'comment_form' action. There should be a call similar to
do_action('comment_form', $post->ID) in your theme's comments.php file where
the comment form section is located.
    
If you are still having problems, you can contact me.

== Screenshots ==

1. The comment form with the default theme.
2. A sample CAPTCHA image (1).
3. A sample CAPTCHA image (2).
4. A sample CAPTCHA image (3).
5. A sample CAPTCHA image (4).

== Version history ==

* 1.1: Tested to work with Wordpress 2.6 and the default theme.
* 1.0: Updated the CAPTCHA generator, KCAPTCHA, to the latest version. Tested to work with Wordpress 2.5.1 and the default theme.
* 0.9: Make all file cases lower to prevent potential problems. Remove CAPTCHA for logged users.
* 0.8: Initial version. Should work flawlessly with WordPress 2.3.2 and the default theme.

== Thanks ==

Thanks to Kruglov Sergei for creating KCAPTCHA, his pretty good CAPTCHA. You
can visit KCAPTCHA website [here](http://www.captcha.ru/en/ "KCAPTCHA website").
