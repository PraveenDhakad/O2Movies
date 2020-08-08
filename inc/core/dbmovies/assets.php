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

/* Scripts Dbmovies
========================================================
*/
if( ! function_exists( 'dbmovies_assets' ) ) {
	function dbmovies_assets() {
		global $post_type, $dbmvsoptions;

		// Dbmovies APP
		if( isset( $_GET['page'] ) AND $_GET['page'] == 'dbmovies') {
			wp_enqueue_style('dt-importer-tool-styles', get_template_directory_uri().'/assets/css/dbm-v223.css', '', DBMOVIES_version, 'all');
			wp_enqueue_script('dt-importer-tool-scripts', dbmovies_compose_javasript('dbmovies'), array('jquery'), DBMOVIES_version, false );
			wp_localize_script('dt-importer-tool-scripts', 'dBa', array(
				'url'			=> admin_url('admin-ajax.php', 'relative'),
				'loading'		=> __d('Loading..'),
				'exists_notice' => __d('This content already exists in the database'),
				'completed'		=> __d('Completed process'),
				'error'			=> __d('Unknown error'),
				'getting'		=> __d('Getting data..'),
				'save'			=> __d('Save'),
				'saving'		=> __d('Saving..'),
				'search'		=> __d('Search'),
				'filtering'		=> __d('Filtering..'),
				'filter'		=> __d('Filter'),
				'dbmv'			=> $dbmvsoptions['dbmv'],
                'delaytime'     => cs_get_option('dbmvsdelaytime','500'),
                'safemode'      => cs_get_option('dbmvssafemode'),
//				'apidbmv'		=> DBMOVIES_Api_dbmv
			) );
		}

		// Dbmovies in Movies
		if( $post_type == 'movies') {
			wp_enqueue_script('ajax_post_movies', dbmovies_compose_javasript('movies'), array('jquery'), DBMOVIES_version, false );
			wp_localize_script('ajax_post_movies', 'DTapi', array(
				// Importar
				'dbm'		=> DBMOVIES_Api_dbmv,
				'tmd'		=> DBMOVIES_Api_tmdb. 'movie/',
				'dbmkey'	=> $dbmvsoptions['dbmv'],
				'tmdkey'	=> $dbmvsoptions['tmdb'],
				'pda'		=> $dbmvsoptions['active'],
				'lang'		=> $dbmvsoptions['lang'],
				'genres'	=> $dbmvsoptions['genres'],
				'upload'	=> $dbmvsoptions['upload'],
				'post'		=> isset($_GET['action']) ? $_GET['action'] : null,
                'movtitle'  => cs_get_option('dbmvstitlemovies','{name}'),
				'loading'	=> __('Loading...'),
			) );
		}

		// Dbmovies in TVShows
		if( $post_type == 'tvshows') {
			wp_enqueue_script('ajax_post_movies', dbmovies_compose_javasript('tv'), array('jquery'), DBMOVIES_version, false );
			wp_localize_script('ajax_post_movies', 'DTapi', array(
				// Importar
				'dbm'		=> DBMOVIES_Api_tmdb,
				'tmd'		=> DBMOVIES_Api_tmdb. 'tv/',
				'dbmkey'	=> $dbmvsoptions['dbmv'],
				'tmdkey'	=> $dbmvsoptions['tmdb'],
				'pda'		=> $dbmvsoptions['active'],
				'lang'		=> $dbmvsoptions['lang'],
				'genres'	=> $dbmvsoptions['genres'],
				'upload'	=> $dbmvsoptions['upload'],
				'post'		=> isset($_GET['action']) ? $_GET['action'] : null,
                'tvstitle'  => cs_get_option('dbmvstitletvshows','{name}'),
				'loading'	=> __('Loading...')
			) );
		}

		// Dbmovies in TVShows > Seasons
		if( $post_type == 'seasons') {
			wp_enqueue_script('ajax_post_movies', dbmovies_compose_javasript('seasons'), array('jquery'), DBMOVIES_version, false );
			wp_localize_script('ajax_post_movies', 'DTapi', array(
				// Importar
				'dbm'		=> DBMOVIES_Api_tmdb,
				'tmd'		=> DBMOVIES_Api_tmdb. 'tv/',
				'dbmkey'	=> $dbmvsoptions['dbmv'],
				'pda'		=> $dbmvsoptions['active'],
				'lang'		=> $dbmvsoptions['lang'],
				'tmdkey'	=> $dbmvsoptions['tmdb'],
				'upload'	=> $dbmvsoptions['upload'],
				'post'		=> isset($_GET['action']) ? $_GET['action'] : null,
                'seatitle'  => cs_get_option('dbmvstitleseasons',__d('{name}: Season {season}')),
				'loading'	=> __('Loading...')
			) );
		}

		// Dbmovies in TVShows > Episodes
		if( $post_type == 'episodes') {
			wp_enqueue_script('ajax_post_movies', dbmovies_compose_javasript('episodes'), array('jquery'), DBMOVIES_version, false );
			wp_localize_script('ajax_post_movies', 'DTapi', array(
				// Importar
				'dbm'		=> DBMOVIES_Api_tmdb,
				'tmd'		=> DBMOVIES_Api_tmdb. 'tv/',
				'dbmkey'	=> $dbmvsoptions['dbmv'],
				'pda'		=> $dbmvsoptions['active'],
				'lang'		=> $dbmvsoptions['lang'],
				'tmdkey'	=> $dbmvsoptions['tmdb'],
				'upload'	=> $dbmvsoptions['upload'],
				'post'		=> isset($_GET['action']) ? $_GET['action'] : null,
                'epititle'  => cs_get_option('dbmvstitleepisodes','{name}: {season}x{episode}'),
				'loading'	=> __('Loading...')
			) );
		}
	}
	add_action('admin_enqueue_scripts', 'dbmovies_assets');
}

function dbmovies_compose_javasript($file){

    return get_template_directory_uri()."/assets/js/{$file}.js";
}
