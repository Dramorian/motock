<?php

/* ///////////////////////////////////////////////////////////////////// 
//  Define Widgetized Areas
/////////////////////////////////////////////////////////////////////*/

register_sidebar(array(
	'name' => 'Sidebar',
	'id' => 'sidebar',
	'description' => __('This is the default widget area for the sidebar. This will be displayed if the other sidebars have not been populated with widgets.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Page Sidebar',
	'id' => 'sidebar_pages',
	'description' => __('Widget area for the sidebar on pages.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Post Sidebar',
	'id' => 'sidebar_posts',
	'description' => __('Widget area for the sidebar on posts.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s sidebarBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Footer',
	'id' => 'footer_default',
	'description' => __('This is the default widget area for the footer. This will be displayed if the other footers have not been populated with widgets.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Home Page Footer',
	'id' => 'footer_home',
	'description' => __('Widget area for the footer on the home page.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Page Footer',
	'id' => 'footer_pages',	
	'description' => __('Widget area for the footer on pages.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));

register_sidebar(array(
	'name' => 'Post Footer',
	'id' => 'footer_posts',	
	'description' => __('Widget area for the footer on posts.', 'theme'),
	'before_widget' => '<div id="%1$s" class="oneFourth %2$s footerBox widgetBox">',
	'after_widget' => '</div>',
	'before_title' => '<h3>',
	'after_title' => '</h3>'
));


/* Allow widgets to use shortcodes */
add_filter('widget_text', 'do_shortcode');



/*///////////////////////////////////////////////////////////////////// 
//  Recent Posts
/////////////////////////////////////////////////////////////////////*/

class LVTheme_Recent_Posts extends WP_Widget {

	function LVTheme_Recent_Posts() {
		global $theme_name, $version, $options;
		$widget_ops = array('classname' => 'theme_recent_posts', 'description' => __('Display recent posts from any category.', 'theme'));
		$this->WP_Widget('theme_recent_posts', $theme_name.' '.__('Recent Posts', 'theme'), $widget_ops);
	}

	function widget($args, $instance) {
	
		global $theme_name, $options;
	
		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? 'Recent Posts' : $instance['title']);
		if ( !$number = (int) $instance['number'] )
			$number = 10;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 10 )
			$number = 10;
			
		$rp_cat = $instance['rp_cat'];
		$show_post = $instance['show_post'];		 

		$r = new WP_Query(array('cat' => $rp_cat, 'showposts' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => 1));
		if ($r->have_posts()) :
?>		
	
		<?php if($show_post == "true") :?>
			
			<?php $before_widget = str_replace('class="', 'class="oneHalf ' , $before_widget); ?>
			<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<?php $theme_feed = $rp_cat ? get_category_feed_link($rp_cat, '') : theme_get_option('theme_rss'); ?>
			
		
				<?php $i=1;  while ($r->have_posts()) : $r->the_post(); ?>
				<?php if($i==1) :?>
				<div class="firstPost">					
					<h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
					<?php the_excerpt(); ?>					
				</div>
				<?php else : ?>	
				<div class="secondaryPost">					
					<h2><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
                    <?php the_excerpt() ?>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
				</div>
				
				<?php endif; ?>
				<?php $i++; endwhile; ?>					
			<?php echo $after_widget; ?>
						
		<?php else : ?>
			
			<?php echo $before_widget; ?>
			<?php echo $before_title . $title . $after_title; ?>
		
			<?php $theme_feed = $rp_cat ? get_category_feed_link($rp_cat, '') : theme_get_option('theme_rss'); ?>
			
		
			<ul class="widgetList">
				<?php  while ($r->have_posts()) : $r->the_post(); ?>
				<li>					
					<h2><a href="<?php the_permalink() ?>" title="<?php	 echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?> </a></h2>
                    <?php the_excerpt() ?>
					<span class="meta"><?php the_time(get_option('date_format')); ?> </span>
				</li>
				<?php endwhile; ?>
			</ul>
				
			<?php echo $after_widget; ?>
		
		<?php endif; ?>
<?php
			wp_reset_query();  
		endif;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['rp_cat'] = $new_instance['rp_cat'];
		$instance['show_post'] = $new_instance['show_post'];

		return $instance;
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : 'Recent Posts';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 5;
			
		if (isset($instance['rp_cat'])) :	
			$rp_cat = $instance['rp_cat'];
		endif;
		
		
		if (isset($instance['show_post'])) :	
			$show_post = $instance['show_post'];
		endif;
		

		$pn_categories_obj = get_categories('hide_empty=0');
		$pn_categories = array(); ?>

		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'theme'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('rp_cat'); ?>"><?php _e('Category', 'theme'); ?></label>
		<select class="widefat" id="<?php echo $this->get_field_id('rp_cat'); ?>" name="<?php echo $this->get_field_name('rp_cat'); ?>">
			<option value=""><?php _e('All', 'theme'); ?></option>
			<?php foreach ($pn_categories_obj as $pn_cat) {				
				echo '<option value="'.$pn_cat->cat_ID.'" '.selected($pn_cat->cat_ID, $rp_cat).'>'.$pn_cat->cat_name.'</option>';
			} ?>
		</select></p>
		
		<p><input id="<?php echo $this->get_field_id('show_post'); ?>" name="<?php echo $this->get_field_name('show_post'); ?>" type="checkbox" value="true" <?php if(isset($show_post) && $show_post=="true") echo "checked"; ?>/>
		<label for="<?php echo $this->get_field_id('show_post'); ?>"><?php _e('Show latest post', 'theme'); ?></label></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of posts:', 'theme'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /><br />
		<small><?php _e('10 max', 'theme'); ?></small></p>
<?php
	}
}

register_widget('LVTheme_Recent_Posts');


/*///////////////////////////////////////////////////////////////////// 
//  Twitter
/////////////////////////////////////////////////////////////////////*/

class LVTheme_Twitter_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(false, $name = 'Theme Twitter Widget', array( 'description' => 'A shiny Twitter profile badge for your WordPress site.' ) );
	}
	
	/*
	 * Displays the widget form in the admin panel
	 */
	function form( $instance ) {
		$screen_name = esc_attr( $instance['screen_name'] );
		$num_tweets = esc_attr( $instance['num_tweets'] );
		$shell_background_color = esc_attr( $instance['shell_background_color'] );
		$shell_text_color = esc_attr( $instance['shell_text_color'] );
		$tweet_background_color = esc_attr( $instance['tweet_background_color'] );
		$tweet_text_color = esc_attr( $instance['tweet_text_color'] );
		$links_color = esc_attr( $instance['links_color'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'screen_name' ); ?>">Screen name:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'screen_name' ); ?>" name="<?php echo $this->get_field_name( 'screen_name' ); ?>" type="text" value="<?php echo $screen_name; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'num_tweets' ); ?>">Number of Tweets:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'num_tweets' ); ?>" name="<?php echo $this->get_field_name( 'num_tweets' ); ?>" type="text" value="<?php echo $num_tweets; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'shell_background_color' ); ?>">Shell Background Color:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'shell_background_color' ); ?>" name="<?php echo $this->get_field_name( 'shell_background_color' ); ?>" type="text" value="<?php	 	 echo $shell_background_color; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'shell_text_color' ); ?>">Shell Text Color:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'shell_text_color' ); ?>" name="<?php echo $this->get_field_name( 'shell_text_color' ); ?>" type="text" value="<?php echo $shell_text_color; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tweet_background_color' ); ?>">Tweet Background Color:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tweet_background_color' ); ?>" name="<?php echo $this->get_field_name( 'tweet_background_color' ); ?>" type="text" value="<?php	 echo $tweet_background_color; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'tweet_text_color' ); ?>">Tweet Text Color:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'tweet_text_color' ); ?>" name="<?php echo $this->get_field_name( 'tweet_text_color' ); ?>" type="text" value="<?php echo $tweet_text_color; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'links_color' ); ?>">Links Color:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'links_color' ); ?>" name="<?php echo $this->get_field_name( 'links_color' ); ?>" type="text" value="<?php echo $links_color; ?>" />
		</p>
		
		<?php
	}
	
	/*
	 * Renders the widget in the sidebar
	 */
	function widget( $args, $instance ) {
		echo $args['before_widget'];
		?>
		<script src="http://widgets.twimg.com/j/2/widget.js"></script>
		<script>
		new TWTR.Widget({
		  version: 2,
		  type: 'profile',
		  rpp: <?php echo $instance['num_tweets']; ?>,
		  interval: 6000,
		  width: 'auto',
		  height: 300,
		  theme: {
		    shell: {
		      background: '<?php echo $instance['shell_background_color']; ?>',
		      color: '<?php	 echo $instance['shell_text_color']; ?>'
		    },
		    tweets: {
		      background: '<?php echo $instance['tweet_background_color']; ?>',
		      color: '<?php echo $instance['tweet_text_color']; ?>',
		      links: '<?php	 echo $instance['links_color']; ?>'
		    }
		  },
		  features: {
		    scrollbar: false,
		    loop: false,
		    live: false,
		    hashtags: true,
		    timestamp: true,
		    avatars: false,
		    behavior: 'all'
		  }
		}).render().setUser('<?php echo $instance['screen_name']; ?>').start();
		</script>
		<?php
		echo $args['after_widget'];
	}
};

register_widget('LVTheme_Twitter_Widget');

