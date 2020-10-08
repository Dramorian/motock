<?php
/*
Plugin Name: Автоматическое обновление с WordPress Lecactus Edition
Plugin URI: http://lecactus.ru/
Description: Для WordPress 2.7+. Изменяет путь к серверу, откуда брать автоматическое обновление. Автор кода Sergey Biryukov <npocmop@gmail.com>. Если вы видите в "обновлении" неправильный адрес(не мой домен) то выключите и включите плагин.
Author: Калинин Иван <lecactusov@gmail.com>
Contributor: Калинин Иван <lecactusov@gmail.com>
Author URI: http://lecactus.ru/
Version: 1.2
*/ 

function change_update_uri($options) {
if (isset($options->updates) && is_array($options->updates))
foreach ( $options->updates as $key => $value ) {
if ($value != '')
{
$options->updates[$key] = (object)
str_replace('http://ru.wordpress.org/',
'http://lecactus.ru/download/', (array) $value); 
}
}
        return $options;
}

add_filter('pre_update_option_update_core', 'change_update_uri');

?>