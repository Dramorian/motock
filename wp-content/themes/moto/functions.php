<?php
// Load utility functions
require_once (TEMPLATEPATH . '/admin/utilities.php');

// Load main options panel file
require_once (TEMPLATEPATH . '/admin/options.php');

//////////////////////////////////////////////////////////////
// Get Options
/////////////////////////////////////////////////////////////

function theme_get_option($key) {
    global $theme_options;
    $theme_options = get_option('theme_options');

    $theme_defaults = array(
        'theme_canonical_url' => true

    );

    foreach($theme_defaults as $k=>$v) {
        if (!isset($theme_options[$k])  || $theme_options[$k] == NULL)
            $theme_options[$k] = $theme_defaults[$k];
    }

    if($key == 'all'){
        return $theme_options;
    }else{
        if(isset($theme_options[$key])){
            return $theme_options[$key];
        }else{
            return NULL;
        }
    }
}



/** ������� ������ ��*/
remove_action('wp_head', 'wp_generator');
/** ������� ������ ��*/
if ( function_exists('register_sidebar') )
	register_sidebar(array(
        'before_widget' => '',
        'after_widget' => '',
        'before_title' => '<div class="title"><h1>',
        'after_title' => '</h1></div>',
    ));

//for metatags http://ogp.me/
function catch_that_image() {
    global $post, $posts;
    $first_img = '';
    ob_start();
    ob_end_clean();
    $output = preg_match_all('/<a.href=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = $matches [1] [0];

    if(empty($first_img)){ //Defines a default image
        $first_img = "/wp-content/themes/moto/images/logo.jpg";
    }
    return $first_img;
}

function _set_meta_tag()
{
    global $descr;
    global $file;
    global $path_meta;
    global $post;

    $post_content = $post->post_content;
    $post_content = preg_replace('/\\(.*?\\)|\\[.*?\\]/s', '', $post_content);

    if(!is_front_page()){
        if ($post) {
            $output = "";
            $output .= '<meta property="og:title" content="' . $post->post_title . '" />';
            $output .= '<meta property="fb:app_id" content="1566626886702275" />';
            $output .= '<meta property="og:type" content="article" />';
            $output .= '<meta property="og:image" content="';
            $output .= catch_that_image().'" />';
            $output .= '<meta property="og:image:width" content="400" />';
            $output .= '<meta property="og:image:height" content="300" />';
            $output .= '<meta property="og:url" content="' . get_permalink($post) . '" />';
            $output .= '<meta property="og:description" content="'.trim(stripcslashes(substr(strip_tags($post_content),0,200))).'" />';
            $output .= '<meta property="og:site_name" content="';
            $output .= get_bloginfo('name');
            $output .= '" />';
            echo $output;
        } else {
            return;
        }
    } else{
        add_action( 'init', 'All_in_One_SEO_Pack' );
        $allInOneSeoPackClass = new All_in_One_SEO_Pack();
        if(!empty($allInOneSeoPackClass)){
            $description = trim(stripcslashes($allInOneSeoPackClass->internationalize(get_option('aiosp_home_description'))));
        }else{
            $description = trim(stripcslashes(get_bloginfo('description')));
        }
        if ($post) {
            $output = "";
            $output .= '<meta property="og:title" content="' . $post->post_title . '" />';
            $output .= '<meta property="fb:app_id" content="1566626886702275" />';
            $output .= '<meta property="og:type" content="article" />';
            $output .= '<meta property="og:image" content="'.get_permalink($post).'/wp-content/themes/moto/images/logo.jpg" />';
            $output .= '<meta property="og:url" content="' . get_permalink($post) . '" />';
            $output .= '<meta property="og:description" content="'.$description.'" />';
            $output .= '<meta property="og:site_name" content="';
            $output .= get_bloginfo('name');
            $output .= '" />';
            echo $output;
        } else {
            return;
        }
    }
}

add_action('wp_head', '_set_meta_tag');
?>
