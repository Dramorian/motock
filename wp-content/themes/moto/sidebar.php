
<!-- begin sidebar -->

		<?php 	/* Widgetized sidebar, if you have the plugin installed. */
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : ?>
			
		<div class="title"><h1>Страницы</h1></div>
		<ul>
			<?php wp_list_pages('title_li='); ?>
		</ul>
		<div class="title"><h1>Рубрики</h1></div>
		<ul>
			 <?php wp_list_cats('sort_column=name'); ?>
		</ul>
		<div class="title"><h1>Архивы</h1></div>
		<ul>
			 <?php wp_get_archives('type=monthly'); ?>
		</ul>
		
		<div class="title"><h1>Прочее</h1></div>
		<ul>
			<?php wp_register(); ?>
			<li><?php wp_loginout(); ?></li>
			<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Синдикация через RSS'); ?>"><?php _e('RSS'); ?></a></li>
			<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('Последние отзывы ко всем записям через RSS'); ?>"><?php _e('Комментарии RSS'); ?></a></li>
			<li><a href="http://validator.w3.org/check/referer" title="Эта страница соответствует XHTML 1.0 Transitional">Правильный XHTML</a></li>
			<li><a href="http://wordpress.org/" title="Разработано на WordPress, платформе, которая вдохновляет.">WordPress</a></li>
<li><a href="http://blogstyle.ru/"
title="Плагины, шаблоны, советы по установке и настроке WordPress">Темы WordPress</a></li>
			<?php wp_meta(); ?>
		</ul>
		
		<?php endif; ?>
		
<!-- end sidebar -->
<!--<script type="text/javascript" src="//vk.com/js/api/openapi.js?121"></script> -->
<!--
<!-- VK Widget 
<div id="vk_groups"></div>
<script type="text/javascript">
    VK.Widgets.Group("vk_groups", {mode: 0, width: "224", height: "400", color1: 'FFFFFF', color2: '2B587A', color3: '5B7FA6'}, 109303729);
</script>
-->
