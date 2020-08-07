<?php
/*
* -------------------------------------------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.4
*
*/
if( ! function_exists( 'doo_peliculas' ) ) {
	function doo_peliculas() {
		$labels = array(
			'name'                => __d('Movies'),
			'singular_name'       => __d('Movies'),
			'menu_name'           => __d('Movies'),
			'name_admin_bar'      => __d('Movies'),
			'all_items'           => __d('Movies'),
		);
		$rewrite = array(
			'slug'                => get_option('dt_movies_slug','movies'),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __d('Movies'),
			'description'         => __d('Movies manage'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor','comments','thumbnail','author'),
			'taxonomies'          => array('genres','dtquality'),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
            'show_in_rest'        => false,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-editor-video',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type('movies', $args );
	}
	add_action('init', 'doo_peliculas', 0 );
	get_template_part('inc/includes/peliculas/metabox');
}

if(!function_exists('movie_table_head')){
	function movie_table_head( $defaults ) {
	    $defaults['imdbrating'] = __d('Rating user');
		$defaults['report']		= __d('Report');
        if(DOO_THEME_VIEWS_COUNT) $defaults['views'] = __d('Views');
		$defaults['featured']	= __d('Featured Title');
	    return $defaults;
	}
	add_filter('manage_movies_posts_columns', 'movie_table_head');
}

if(!function_exists('movie_table_content')){
	function movie_table_content( $column_name, $post_id ) {
	    if ($column_name == 'imdbrating') {
			$urating	= get_post_meta( $post_id, DOO_MAIN_RATING, true );
			$uratotal	= ( $urating ) ? $urating : '0.0';
			echo '<span class="dt_rating">'. $uratotal .'</span>';
	    }
		if ($column_name == 'report') {
			if($minutes = get_post_meta( $post_id, 'numreport', true )) {
				echo '<span class="dt_report_video">'. $minutes .'</span> <a href="'.get_admin_url(get_current_blog_id(),'admin-ajax.php?action=delete_notice_report&id='.$post_id).'">'.__d('Solved').'</a>';
			} else {
				echo "0";
			}
	    }
        if (DOO_THEME_VIEWS_COUNT && $column_name == 'views') {
			if($views = get_post_meta( $post_id, 'dt_views_count', true )) {
				echo $views;
			} else {
				echo '0';
			}
		}
		if ($column_name == 'featured') {
			$featured = get_post_meta( $post_id, 'dt_featured_post', true );
			$hideA = ( 1 == $featured ) ? 'style="display:none"' : '';
			$hideB = ( 1 != $featured ) ? 'style="display:none"' : '';
			echo '<a id="feature-add-'.$post_id.'" class="button add-to-featured button-primary" data-postid="'.$post_id.'" data-nonce="'.wp_create_nonce('dt-featured-'.$post_id).'"  '.$hideA.'>'. __d('Add to featured'). '</a>';
			echo '<a id="feature-del-'.$post_id.'" class="button del-of-featured" data-postid="'.$post_id.'" data-nonce="'.wp_create_nonce('dt-featured-'.$post_id).'" '.$hideB.'>'. __d('Remove featured'). '</a>';
		}
	}
	add_action('manage_movies_posts_custom_column', 'movie_table_content', 10, 2 );
}
