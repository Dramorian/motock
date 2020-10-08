<?php get_header();?>

<div id="content"><div class="cont_top">
	<div class="left"><div class="lpadding">
		<?php get_sidebar(); ?>
	</div></div>
	<div class="right"><div class="rpadding">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<div class="title">
			
			<h1><a href="<?php the_permalink() ?>" target="_self"><?php the_title(); ?></a></h1>
			<?php //the_date('d M Y','<h4>','</h4>'); ?>
		</div>

		<?php the_content(__('(далее...)')); ?>

            <?php if (is_single()) { ?>
            <div class="share42init" style="    margin: 15px;"></div>
            <script type="text/javascript" src="http://moto.ck.ua/share42/share42.js"></script>
            <?php } ?>

		<div class="clear"></div>


		
		<div class="permalink"><?php _e("Рубрика:"); ?> <?php the_category(',') ?> | <?php edit_post_link(__('Править')); ?> | <?php comments_popup_link(__('Отзывов (0)'), __('Отзывов (1)'), __('Отзывов (%)')); ?></div>
		
		<div class="tags"><?php the_tags('Метки: ', ', ', ''); ?></div>

		<?php comments_template(); // Get wp-comments.php template ?>

	<?php endwhile; else: ?>
	<p><?php _e('К сожалению, по вашему запросу ничего не найдено.'); ?></p>
	<?php endif; ?>

<?php if(function_exists('wp_page_numbers')) : wp_page_numbers(); endif; ?>

	</div></div>
	<div class="clear"></div>
</div></div>

<?php get_footer(); ?>
