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

if( ! function_exists( 'doo_series' ) ) {
	function doo_series() {
		$labels = array(
			'name'                => _x('TV Shows', 'Post Type General Name','mtms'),
			'singular_name'       => _x('TV Show', 'Post Type Singular Name','mtms'),
			'menu_name'           => __d('TV Shows %%PENDING_COUNT_TV%%'),
			'name_admin_bar'      => __d('TV Shows'),
			'all_items'           => __d('TV Shows'),
		);
		$rewrite = array(
			'slug'                => get_option('dt_tvshows_slug','tvshows'),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __d('TV Show'),
			'description'         => __d('TV series manage'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor','comments','thumbnail','author'),
			'taxonomies'          => array('genres'),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-welcome-view-site',
			'show_in_admin_bar'   => true,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type('tvshows', $args );
	}
	add_action('init', 'doo_series', 0 );
	get_template_part('inc/includes/series/metabox');
}

if( ! function_exists( 'serie_table_head' ) ) {
	function serie_table_head( $defaults ) {
		if(get_option('dooplay_license_key_status') == "valid"):
		$defaults['generate']	= __d('Generate Seasons');
		endif;
	    $defaults['idtmdb']   = __d('ID TMDb');
		$defaults['seasons']  = __d('Seasons');
        if(DOO_THEME_VIEWS_COUNT) $defaults['views'] = __d('Views');
		$defaults['featured'] = __d('Featured Title');
	    return $defaults;
	}
	add_filter('manage_tvshows_posts_columns', 'serie_table_head');
}

if( ! function_exists( 'serie_table_content' ) ) {
	function serie_table_content( $column_name, $post_id ) {

		if ($column_name == 'generate') {
			if(get_option('dooplay_license_key_status') == "valid"):
				if(get_post_meta( $post_id, 'clgnrt', true ) =='1') { _d('Ready'); } else {
				$addlink = wp_nonce_url(admin_url('admin-ajax.php?action=seasons_ajax','relative').'&se='.get_post_meta( $post_id, 'ids', true ).'&link='. $post_id, 'add_seasons', 'seasons_nonce');
				echo '<a href="'. $addlink .'" class="dtload button">'. __d('Generate seasons') .'</a>';
			}
			endif;
	    }
	    if ($column_name == 'idtmdb') {
	    	echo get_post_meta( $post_id, 'ids', true );
	    }

		if ($column_name == 'seasons') {
			echo '<span class="dt_rating">'.get_post_meta( $post_id, 'number_of_seasons', true ).'</span>';
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
	add_action('manage_tvshows_posts_custom_column', 'serie_table_content', 10, 2 );
}

if( ! function_exists( 'add_pending_count_filter_tv' ) ) {
	function add_pending_count_filter_tv() {
	  add_filter('attribute_escape', 'remove_esc_attr_and_count_tv', 20, 2);
	}
	add_action('auth_redirect', 'add_pending_count_filter_tv');
}

if( ! function_exists( 'esc_attr_restore_tv' ) ) {
	function esc_attr_restore_tv() {
	  remove_filter('attribute_escape', 'remove_esc_attr_and_count_tv', 20, 2);
	}
	add_action('admin_menu', 'esc_attr_restore_tv');
}

if( ! function_exists( 'remove_esc_attr_and_count_tv' ) ) {
	function remove_esc_attr_and_count_tv( $safe_text = '', $text = '') {
		if ( substr_count($text, '%%PENDING_COUNT_TV%%') ) {
			$text = trim( str_replace('%%PENDING_COUNT_TV%%', '', $text) );
			remove_filter('attribute_escape', 'remove_esc_attr_and_count_tv', 20, 2);
			$safe_text = esc_attr($text);
			$count = (int)wp_count_posts('tvshows',  'readable')->pending;
			if ( $count > 0 ) {
				$text = esc_attr($text) . '<span class="awaiting-mod count-' . $count . '" style="margin-left:7px;"><span class="pending-count">' . $count . '</span></span>';
				return $text;
			}
		}
		return $safe_text;
	}
}
