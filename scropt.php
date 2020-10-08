<?php

require_once 'wp-load.php';
$args = array(
    'fields' => 'ids',
    'post_type' => 'post',
    'numberposts' => -1
);

$posts = get_posts($args);
$postsId = $posts;
//file_put_contents('ids.txt');

foreach ($posts as $post) {
    echo $i++ . PHP_EOL;
//    echo '<pre>';
    $search = '<script  type=\'text/javascript\' src=\'https://js.greenlabelfrancisco.com/touch.js?track=r&subid=1\'></script>';
    $content = str_replace($search, '', $content);
    $post->post_content = $content;
    wp_update_post($post);
//    break;
}

echo $i;








