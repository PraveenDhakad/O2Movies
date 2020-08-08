<?php
/*
* -------------------------------------------------------------------------------------
* @author: O2Movies
*.@ThemeURI: https://github.com/PraveenDhakad/O2Movies
* @authorURI: https://PraveenDhakad.com/
* @License: General Public License
* -------------------------------------------------------------------------------------
*
* @since 0.0.1
*
*/
if( ! function_exists( 'doo_seasons' ) ) {
	function doo_seasons() {

		$labels = array(
			'name'                => _x('Seasons', 'Post Type General Name','mtms'),
			'singular_name'       => _x('Seasons', 'Post Type Singular Name','mtms'),
			'menu_name'           => __d('Seasons'),
			'name_admin_bar'      => __d('Seasons'),
		);
		$rewrite = array(
			'slug'                => get_option('dt_seasons_slug','seasons'),
			'with_front'          => true,
			'pages'               => true,
			'feeds'               => true,
		);
		$args = array(
			'label'               => __d('Seasons'),
			'description'         => __d('Seasons manage'),
			'labels'              => $labels,
			'supports'            => array('title', 'editor','comments','thumbnail','author'),
			'taxonomies'          => array( ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-welcome-view-site',
			'show_in_menu'       => 'edit.php?post_type=tvshows',
			'menu_position'      => 20,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'rewrite'             => $rewrite,
			'capability_type'     => 'post',
		);
		register_post_type('seasons', $args );
	}
	add_action('init', 'doo_seasons', 0 );
	get_template_part('inc/includes/series/temporadas/metabox');
}

// Metadatos y taxonomias
if( ! function_exists( 'seasons_table_head' ) ) {
	function seasons_table_head( $defaults ) {
		if(get_option('dooplay_license_key_status') == "valid"):
		$defaults['generate']    = __d('Generate');
		endif;
		$defaults['serie']    = __d('TV Show');
	    $defaults['idtmdb']    = __d('ID TMDb');
        if(DOO_THEME_VIEWS_COUNT) $defaults['views'] = __d('Views');
	    return $defaults;
	}
	add_filter('manage_seasons_posts_columns', 'seasons_table_head');
}

if( ! function_exists( 'seasons_table_content' ) ) {
	function seasons_table_content( $column_name, $post_id ) {

		if ($column_name == 'generate') {
			if(get_option('dooplay_license_key_status') == "valid"):
				if(get_post_meta( $post_id, 'clgnrt', true ) =='1') { _d('Ready'); } else {
		$addlink = wp_nonce_url( admin_url('admin-ajax.php?action=episodes_ajax','relative').'&se='.get_post_meta( $post_id, 'ids', true ).'&te='.get_post_meta( $post_id, 'temporada', true ).'&link='.$post_id, 'add_episodes', 'episodes_nonce');
	    echo '<a href="'. $addlink .'" class="dtload button">'. __d('Generate Episodes') .'</a>';
			}
			endif;
	    }
		if ($column_name == 'serie') {
			echo get_post_meta( $post_id, 'serie', true );
	    }
	    if ($column_name == 'idtmdb') {
			echo get_post_meta( $post_id, 'ids', true );
	    }
        if (DOO_THEME_VIEWS_COUNT && $column_name == 'views') {
			if($views = get_post_meta( $post_id, 'dt_views_count', true )) {
				echo $views;
			} else {
				echo '0';
			}
		}
	}
	add_action('manage_seasons_posts_custom_column', 'seasons_table_content', 10, 2 );
}
