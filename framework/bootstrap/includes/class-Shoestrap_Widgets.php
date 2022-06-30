<?php

/**
 * Live Search widget
 */
class PA_Live_Search_Widget extends WP_Widget {
	private $fields = array(
		'title'          => 'Title ( optional )',
	 );

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_pa_live_search', 'description' => __( 'Use this widget to add a live search', 'knowledgepress' ) );

		parent::__construct( 'widget_pa_live_search', __( 'KP Live Search', 'knowledgepress' ), $widget_ops );
		$this->alt_option_name = 'widget_pa_live_search';

		add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
	}

	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'widget_pa_live_search', 'widget' );

		if ( !is_array( $cache ) ) {
			$cache = array();
		}

		if ( !isset( $args['widget_id'] ) ) {
			$args['widget_id'] = null;
		}

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Live Search', 'knowledgepress' ) : $instance['title'], $instance, $this->id_base );

		foreach( $this->fields as $name => $label ) {
			if ( !isset( $instance[$name] ) ) { $instance[$name] = ''; }
		}

		echo $before_widget;

		if ( $title ) {
			echo $before_title, $title, $after_title;
		}
		
		global $ss_settings;
		?>

		<?php if( $ss_settings['disable_widget_live_search'] ) { ?>
			<div class="live-search">
				<form role="search" method="get" id="searchform" class="form-search" action="<?php echo home_url('/'); ?>">
				  <div class="input-group">
				    <input type="text" name="s" id="s" class="search-query form-control" autocomplete="off" placeholder="<?php _e('Find help! Enter search term here.','knowledgepress'); ?>">
				    <span class="input-group-btn">
				      <input type="submit" id="searchsubmit" value="<?php _e('Search', 'knowledgepress'); ?>" class="btn btn-primary">
				    </span>
				  </div>
				</form>
			</div>
		<?php } else { ?>
			<div class="live-search">
				<form role="search" method="get" id="searchform" class="form-search" action="<?php echo home_url('/'); ?>">
				  <div class="input-group">
				    <input type="text" id="autocomplete-ajax" name="s" id="s" class="searchajax search-query form-control" autocomplete="off" placeholder="<?php _e('Find help! Enter search term here.','knowledgepress'); ?>">
				    <span class="input-group-btn">
				      <input type="submit" id="searchsubmit" value="<?php _e('Search', 'knowledgepress'); ?>" class="btn btn-primary">
				    </span>
				  </div>
				</form>
				<script> _url = '<?php echo admin_url("admin-ajax.php"); ?>';</script>		
			</div>
		<?php } ?>

		<?php
		$search_analytics     = $ss_settings['search_analytics'];
		$top_searches         = $ss_settings['top_searches'];
		$top_searches_title   = $ss_settings['top_searches_title'];
		$top_searches_period  = $ss_settings['top_searches_period'];
		$top_searches_terms   = $ss_settings['top_searches_terms'];
		?>

		<?php if ($top_searches && $search_analytics) {
		echo '<p class="top-searches">' . $top_searches_title . ' ';
		pa_popular_searches( $top_searches_period, $top_searches_terms );
		echo '</p>';
		} ?>

	<?php
		echo $after_widget;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_pa_live_search', $cache, 'widget' );
	}

	function update( $new_instance, $old_instance ) {
		$instance = array_map( 'strip_tags', $new_instance );

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );

		if ( isset( $alloptions['widget_pa_live_search'] ) ) {
			delete_option( 'widget_pa_live_search' );
		}

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_pa_live_search', 'widget' );
	}

	function form( $instance ) {
		$title = '';
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'knowledgepress'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 		

	}
}

function pa_live_search_widget_init() {
	register_widget( 'PA_Live_Search_Widget' );
}
add_action( 'widgets_init', 'pa_live_search_widget_init' );
