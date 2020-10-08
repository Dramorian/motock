<?php get_header(); ?>

<div id="content"><div class="cont_top">
	<div class="left"><div class="lpadding">
		<?php get_sidebar(); ?>
	</div></div>
	<div class="right"><div class="rpadding">
		<?php if (have_posts()) : ?>
			
		<div style="margin: 10px;"><h3>Результаты поиска</h3></div>

		<?php next_posts_link('&laquo; Предыдущая') ?>

		<?php previous_posts_link('Следующая &raquo;') ?>

		<?php while (have_posts()) : the_post(); ?>
		


		<div class="title">
			
			<h1><a href="<?php the_permalink() ?>" target="_self"><?php the_title(); ?></a></h1>

		</div>
		
		<?php the_content(__('(далее...)')); ?>
		<div class="clear"></div>
		
		
		<div class="permalink"><?php _e("Рубрика:"); ?> <?php the_category(',') ?> | <?php edit_post_link(__('Править')); ?> | <?php comments_popup_link(__('Отзывов (0)'), __('Отзывов (1)'), __('Отзывов (%)')); ?></div>
		
		<div class="tags"><?php the_tags('Метки: ', ', ', ''); ?></div>
		
		<?php endwhile; ?>


			<?php next_posts_link('&laquo; Предыдущая') ?>

			<?php previous_posts_link('Следующая &raquo;') ?>
			

		<?php else : ?>
	<br/>
			<div style="margin: 10px;"><h4>Ничего не найдено. Попробуете по другому запросу?</h4></div>
			
			<?php/* include (TEMPLATEPATH . '/searchform.php'); */?>

		<?php endif; ?>

	</div></div>
	<div class="clear"></div>
</div></div>

<?php get_footer(); ?>
