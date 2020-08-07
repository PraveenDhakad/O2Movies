<?php
/*
* ----------------------------------------------------
*
* DBmovies for DooPlay
*
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* ----------------------------------------------------
*
* @since 2.1.4
*
*/


// Define Remote API
define('DBMOVIES_version',  '2.0');
define('DBMOVIES_Api_dbmv', 'http://www.omdbapi.com/?apikey=a777e0dc&i=');
//define('DBMOVIES_Cdn',		'https://www.imdb-scrapper.tk');
define('DBMOVIES_Api_tmdb', 'https://api.themoviedb.org/3/');

/* Dbmovies compose option
========================================================
*/

// The Option
$dbmvsoptions = get_option('dbmovies_options');

// Initial configuration
if( empty( $dbmvsoptions ) ) {

	// Old Parameters
	$d2s4gf2xaq = get_option('_site_register_in_dbmvs');
	$dawerpksjh = get_option('dt_api_key');
	$dgqazsfple = get_option('dt_api_language');

	// New Parameters
	$data = array(
		'dbmv'    => ($d2s4gf2xaq) ? $d2s4gf2xaq['k'] : null,
		'tmdb'    => ($dawerpksjh) ? $dawerpksjh      : '05902896074695709d7763505bb88b4d',
		'lang'    => ($dgqazsfple) ? $dgqazsfple      : 'en-US',
		'active'  => true,
		'upload'  => true,
		'genres'  => true,
		'release' => true
	);
	update_option('dbmovies_options', $data);
}

/* Admin Menu
========================================================
*/
if( ! function_exists( 'dbmovies_page' ) ) {
	function dbmovies_page() {
	    $menu = add_menu_page( __d('Dbmovies'), __d('dbmovies'), 'manage_options', 'dbmovies', 'dbmovies_callback', 'dashicons-admin-plugins');
		add_action('load-'.$menu, 'dbmovies_help_tap');
	}
	add_action('admin_menu', 'dbmovies_page');
}

/* HTML dbmovies.org
========================================================
*/
if( ! function_exists( 'dbmovies_callback' ) ) {
	function dbmovies_callback() {
		get_template_part('inc/core/dbmovies/page');
	}
}

/* FILTER Content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_app_filter_content' ) ) {
	function dt_dbmovies_app_filter_content() {
		set_time_limit(3000);
		global $dbmvsoptions;
		$opt 	= $dbmvsoptions;
		$nonce 	= $_REQUEST['dbmovies-app-filter-nonce'];
		// Only for the administrator
		if( isset( $_REQUEST['action'] ) AND current_user_can('manage_options') AND wp_verify_nonce( $nonce, 'dbmovies-app-filter') ) {
			// Parameters
			$year	= $_REQUEST['year'];
			$order	= $_REQUEST['order'];
			$type	= $_REQUEST['type'];
			$page	= ( $_REQUEST['page'] ) ? $_REQUEST['page'] : '1';
			$yearp	= ( $type == 'tv' ) ? 'first_air_date_year' : 'primary_release_year';
			$genre	= ( $type == 'tv' ) ? $_REQUEST['genre_tv'] : $_REQUEST['genre_movie'];
			$dateid = ( $type == 'tv' ) ? 'first_air_date' : 'release_date';

			// GET data
			$remote = dt_dbmovies_remote( DBMOVIES_Api_tmdb.'discover/'.$type.'?api_key='.$opt['tmdb'].'&language='.$opt['lang'].'&sort_by='.$order.'&page='.$page.'&'.$yearp.'='.$year .'&with_genres='. $genre );
			$data 	= json_decode( $remote, TRUE );

			// Total
			$total_results = $data['total_results'];
			$total_pages   = $data['total_pages'];

			// Pages
			$prevpage = ( $page > 1 ) ? $page-1 : false;
			$nextpage = ( $page < $total_pages) ? $page+1 : false;
			echo '<div class="box">';
			// Pagination
			if($total_results > 1 ) {
				echo '<div class="paginate">';
				if($total_results > 20 ) {
					echo '<ul>';
					echo ( $prevpage ) ? '<li><a id="prevty" class="pagex" data-page="'. $prevpage. '">'. __d('prev') .'</a></li>' : '';
					echo '<li><a class="pagex active" data-page="'.$page.'">'.$page.'</a></li>';
					echo ( $nextpage ) ? '<li><a id="nexty" class="pagex" data-page="'.$nextpage.'">'. __d('next') .'</a></li>' : '';
					echo '</ul>';
				}
				$result_time = sprintf( __d("%s results in %s seconds"), "<strong>{$total_results}</strong>", dt_dbmovies_elapsed_time(time()) );
				echo '<span class="info">'. $result_time .'</span>';
				echo '</div>';
			}
			echo '<div class="items">';
			if( $total_results == 0 ) {
				echo '<div class="no-results"><p>'. __d('No results') .'</p></div>';
			}
			$ctd = array();
			$results = $data['results'];

			// Item
			foreach($results as $ci) {
				$ctd_id		= $ctd[] = $ci['id'];
				$ctd_title	= $ctd[] = ( $type == 'tv' ) ? $ci['name'] : $ci['title'];
				$ctd_poster	= $ctd[] = $ci['poster_path'];
				$ctd_date	= $ctd[] = ( $ci[$dateid] ) ? $ci[$dateid] : '--';
				$img		= ( $ctd_poster ) ? '<img src="https://image.tmdb.org/t/p/w154'.$ctd_poster.'">' : '<img src="'.DOO_URI.'/assets/img/no/poster.png" />';
				$check		= ($type == 'tv') ? dt_dbmovies_very_tmdb($ctd_id, 'ids') : dt_dbmovies_very_tmdb($ctd_id, 'idtmdb');
				$exclude	= ($check == 1) ? ' dbimported' : ' dbimport';
				$import		= ($check != 1) ? '<div id="c'.$ctd_id.'" data-nonce="'. wp_create_nonce( 'import-tmdb-'.$ctd_id ) .'" data-id="'.$ctd_id.'" data-type="'.$type.'" class="cimport"></div>' : null;

				echo '<div id="'.$ctd_id.'" class="item'.$exclude.'"><article>';
				echo '<div class="imagen">'. $img . $import.'</div>';
				echo '<div class="data"><h3>'.$ctd_title.'</H3><span>'. substr($ctd_date, 0, 4) .'</span></div>';
				echo '</article></div>';
			}
			echo '</div>';

			// Pagination
			if($total_results > 1 ) {
				echo '<div class="paginate">';
				if($total_results > 20 ) {
					echo '<ul>';
					echo ( $prevpage ) ? '<li><a class="pagex" data-page="'. $prevpage. '">'. __d('prev') .'</a></li>' : '';
					echo '<li><a class="pagex active" data-page="'.$page.'">'.$page.'</a></li>';
					echo ( $nextpage ) ? '<li><a class="pagex" data-page="'.$nextpage.'">'. __d('next') .'</a></li>' : '';
					echo '</ul>';
				}
				echo '</div>';
			}
			// end Pagination
			echo '</div>';
			// end HTML
		}
		die();
	}
	add_action('wp_ajax_dt_dbmovies_app_filter_content', 'dt_dbmovies_app_filter_content');
	add_action('wp_ajax_nopriv_dt_dbmovies_app_filter_content', 'dt_dbmovies_app_filter_content');
}

/* SEARCH Content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_app_search_content' ) ) {
	function dt_dbmovies_app_search_content() {
		set_time_limit(3000);
		global $dbmvsoptions;
		$opt = $dbmvsoptions;
		$nonce = isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : null;
		// Only for the administrator
		if( isset( $_REQUEST['action'] ) AND current_user_can('manage_options') AND wp_verify_nonce( $nonce, 'dbmovies-app-search') ) {
			// Parameters
			$page	= $_REQUEST['page'];
			$term	= $_REQUEST['term'];
			$type	= $_REQUEST['type'];
			$dateid = ( $type == 'tv' ) ? 'first_air_date' : 'release_date';
			$api	= dt_dbmovies_remote( DBMOVIES_Api_tmdb.'search/'.$type.'?api_key='.$opt['tmdb'].'&language='.$opt['lang'].'&query='.$term.'&page='.$page );
			$data	= json_decode($api, TRUE);

			// Total
			$total_results = $data['total_results'];
			$total_pages   = $data['total_pages'];

			// Pages
			$prevpage = ( $page > 1 ) ? $page-1 : false;
			$nextpage = ( $page < $total_pages) ? $page+1 : false;

			echo '<div class="box">';

			// Pagination
			if($total_results > 1 ) {
				echo '<div class="paginate">';
				if($total_results > 20 ) {
					echo '<ul>';
					echo ( $prevpage ) ? '<li><a id="prevty" class="pagex" data-page="'. $prevpage. '">'. __d('prev') .'</a></li>' : null;
					echo '<li><a class="pagex active" data-page="'.$page.'">'.$page.'</a></li>';
					echo ( $nextpage ) ? '<li><a id="nexty" class="pagex" data-page="'.$nextpage.'">'. __d('next') .'</a></li>' : null;
					echo '</ul>';
				}

				$result_time = sprintf( __d("%s results in %s seconds"), "<strong>{$total_results}</strong>", dt_dbmovies_elapsed_time(time()) );
				echo '<span class="info">'. $result_time .'</span>';
				echo '</div>';
			}

			echo '<div class="items">';
			if( $total_results == 0 ) {
				echo '<div class="no-results"><p>'. __d('No results') .'</p></div>';
			}
			$ctd = array();
			$results = $data['results'];

			// Item
			foreach($results as $ci) {
				$ctd_id		= $ctd[] = $ci['id'];
				$ctd_title	= $ctd[] = ( $type == 'tv' ) ? $ci['name'] : $ci['title'];
				$ctd_poster	= $ctd[] = $ci['poster_path'];
				$ctd_date	= $ctd[] = ( $ci[$dateid] ) ? $ci[$dateid] : '--';
				$img		= ( $ctd_poster ) ? '<img src="https://image.tmdb.org/t/p/w154'.$ctd_poster.'">' : '<img src="'.DOO_URI.'/assets/img/no/poster.png" />';
				$check		= ($type == 'tv') ? dt_dbmovies_very_tmdb($ctd_id, 'ids') : dt_dbmovies_very_tmdb($ctd_id, 'idtmdb');
				$exclude	= ($check == 1) ? ' dbimported' : ' dbimport';
				$import		= ($check != 1) ? '<div id="c'.$ctd_id.'" data-nonce="'. wp_create_nonce( 'import-tmdb-'.$ctd_id ) .'" data-id="'.$ctd_id.'" data-type="'.$type.'" class="cimport"></div>' : null;

				echo '<div id="'.$ctd_id.'" class="item'.$exclude.'"><article>';
				echo '<div class="imagen">'. $img . $import .'</div>';
				echo '<div class="data"><h3>'.$ctd_title.'</H3><span>'. substr($ctd_date, 0, 4) .'</span></div>';
				echo '</article></div>';
			}
			echo '</div>';
			// Pagination
			if($total_results > 1 ) {
				echo '<div class="paginate">';
				if($total_results > 20 ) {
					echo '<ul>';
					echo ( $prevpage ) ? '<li><a class="pagex" data-page="'. $prevpage. '">'. __d('prev') .'</a></li>' : null;
					echo '<li><a class="pagex active" data-page="'.$page.'">'.$page.'</a></li>';
					echo ( $nextpage ) ? '<li><a class="pagex" data-page="'.$nextpage.'">'. __d('next') .'</a></li>' : null;
					echo '</ul>';
				}
				echo '</div>';
			}
			// end Pagination
			echo '</div>';
			// end HTML
		}
		die();
	}
	add_action('wp_ajax_dt_dbmovies_app_search_content', 'dt_dbmovies_app_search_content');
	add_action('wp_ajax_nopriv_dt_dbmovies_app_search_content', 'dt_dbmovies_app_search_content');
}

/* POST Content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_app_post' ) ) {
	function dt_dbmovies_app_post() {
		$time_limit = cs_get_option('dbmvsphptime','300');
		set_time_limit($time_limit);
		$tmdbid = $_REQUEST['id'];
		$nonce	= $_REQUEST['nonce'];
		$type	= $_REQUEST['type'];
		if( isset( $_REQUEST['action'] ) AND current_user_can('manage_options') AND wp_verify_nonce( $nonce, 'import-tmdb-'. $tmdbid ) ) {

			// Post movies
			if($type == 'movie') {
				dbm_post_movie( $tmdbid );
			}

			// Post TVShows
			if($type == 'tv') {
				dbm_post_tv( $tmdbid );
			}

		}
		die();
	}
	add_action('wp_ajax_dt_dbmovies_app_post', 'dt_dbmovies_app_post');
	add_action('wp_ajax_nopriv_dt_dbmovies_app_post', 'dt_dbmovies_app_post');
}

/* Save Dbmovies Options
========================================================
*/
if( ! function_exists( 'dt_dbmovies_save_options' ) ) {
	function dt_dbmovies_save_options() {

		// GET Nonce
		$nonce = isset( $_REQUEST['dbmovies-save-options-nonce'] ) ? $_REQUEST['dbmovies-save-options-nonce'] : false;

		// Verify of security
		if( current_user_can('manage_options') AND isset( $_REQUEST['action'] ) AND wp_verify_nonce( $nonce, 'dbmovies-save-options') ) {
			// Post data
			$a1 = isset( $_POST['dbmv'] )	 ? $_POST['dbmv'] : false;
			$a2 = isset( $_POST['tmdb'] )	 ? $_POST['tmdb'] : false;
			$a3 = isset( $_POST['lang'] )	 ? $_POST['lang'] : false;
			$a4 = isset( $_POST['active'] )  ? true : false;
			$a5 = isset( $_POST['upload'] )  ? true : false;
			$a6 = isset( $_POST['genres'] )  ? true : false;
			$a7 = isset( $_POST['release'] ) ? true : false;

			// Searize data
			$data = array(
				'dbmv'		=> sanitize_text_field($a1),
				'tmdb'		=> sanitize_text_field($a2),
				'lang'		=> sanitize_text_field($a3),
				'active'	=> $a4,
				'upload'	=> $a5,
				'genres'	=> $a6,
				'release'	=> $a7
			);

			// Update option
			update_option('dbmovies_options', $data );
		}
		die();
	}
	add_action('wp_ajax_dt_dbmovies_save_options', 'dt_dbmovies_save_options');
	add_action('wp_ajax_nopriv_dt_dbmovies_save_options', 'dt_dbmovies_save_options');
}

/* Save Dbmovies Options
========================================================
*/
if( ! function_exists( 'dt_dbmovies_register_key' ) ) {
	function dt_dbmovies_register_key() {
		set_time_limit(30);
		global $dbmvsoptions;
		if( current_user_can('administrator') AND isset( $_REQUEST['action'] ) ) {

			# Compose data
			$apidata = array(
				'email'		=> get_option('admin_email'),
				'domain'	=> dt_domain( get_option('siteurl') ),
				'license'	=> get_option('dooplay_license_key'),
				'item'		=> DOO_ITEM_ID
			);

		//$api	= esc_url_raw( add_query_arg( $apidata, DBMOVIES_Server. '/add/') );
		//$remote	= dt_dbmovies_remote( $api );
		//$data	= json_decode($remote, TRUE);

		# Get data
		$success = 'dom_exists';
		$error	 = 'invalid_license';
		echo $data['key']= 'streamal.me';
		# Verify License and data
		if($success == 'registered' OR $success == 'dom_exists') {

			// Searize data
			$data = array(
				'dbmv'		=> 'streamal.me',
				'tmdb'		=> $dbmvsoptions['tmdb'],
				'lang'		=> $dbmvsoptions['lang'],
				'active'	=> $dbmvsoptions['active'],
				'upload'	=> $dbmvsoptions['upload'],
				'genres'	=> $dbmvsoptions['genres'],
				'release'	=> $dbmvsoptions['release']
			);
				// Update option
				update_option('dbmovies_options', $data );

			}elseif($error == 'invalid_license'){

				echo __d('Invalid License');

			}else{

				echo __d('Unknown error');
			}
		}
		die();
	}
	add_action('wp_ajax_register_dbmv', 'dt_dbmovies_register_key');
	add_action('wp_ajax_nopriv_register_dbmv', 'dt_dbmovies_register_key');
}

/* Import and Upload IMAGE POST Edit
========================================================
*/
if( ! function_exists( 'dt_upload_ajax_image' ) ) {

	function dt_upload_ajax_image() {

		$urlimage = isset( $_REQUEST['url'] )    ? $_REQUEST['url']    : false;
		$nonce	  = isset( $_REQUEST['nonce'] )  ? $_REQUEST['nonce']  : false;
		$postid	  = isset( $_REQUEST['postid'] ) ? $_REQUEST['postid'] : false;
		$field	  = isset( $_REQUEST['field'] )  ? $_REQUEST['field']  : false;

		if( is_user_logged_in() AND $urlimage != null AND wp_verify_nonce( $nonce, 'dt-ajax-upload-image')) {
			$get_url_image = dt_dbmovies_upload_image( $urlimage, null, false, true );
			$url = ($get_url_image) ? $get_url_image : false;
			update_post_meta( $postid, $field, $url );
			echo $url;
		} else {
			echo 'error';
		}
		die();
	}
	add_action('wp_ajax_dt_upload_ajax_image', 'dt_upload_ajax_image');
	add_action('wp_ajax_nopriv_dt_upload_ajax_image', 'dt_upload_ajax_image');
}

/* Verify content in Database
========================================================
*/
if( ! function_exists( 'dt_dbmovies_very_tmdb' ) ) {
	function dt_dbmovies_very_tmdb($term = null, $meta= null) {
		global $wpdb;
		$id		= $term;
		$query	= "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = '$meta' AND meta_value = '$id' ";
		$very	= $wpdb->get_results( $query, OBJECT );
		if ($very) {
			return true;
		} else {
			return false;
		}
	}
}

/* TIME Load process
========================================================
*/
if( ! function_exists( 'dt_dbmovies_elapsed_time' ) ) {
	function dt_dbmovies_elapsed_time( $time ) {
		$micro	= microtime(TRUE);
		return number_format($micro - $time, 2);
	}
}

/* TEXT Cleaner
========================================================
*/
if( ! function_exists( 'dt_dbmovies_text_cleaner' ) ) {
	function dt_dbmovies_text_cleaner( $text ) {
		return wp_strip_all_tags(html_entity_decode($text));
	}
}

/* Upload IMAGE POST
========================================================
*/
if( ! function_exists( 'dt_dbmovies_upload_image' ) ) {
	function dt_dbmovies_upload_image( $url = null, $post = null, $thumbnail = false, $showurl = false ) {

		// Global variables
		global $wp_filesystem, $dbmvsoptions;

		// If and only if
		if( is_user_logged_in() and $dbmvsoptions['upload'] == true AND $url != null ) {

			// WordPress Lib
			WP_Filesystem();
			require_once( ABSPATH . 'wp-admin/includes/image.php');

			// Get Image
			$upload_dir		= wp_upload_dir();
			$image_remote	= wp_remote_get($url);
			$image_data		= wp_remote_retrieve_body($image_remote);
			$filename		= wp_basename($url);

			// Path folder
			if(wp_mkdir_p($upload_dir['path'])) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			$wp_filesystem->put_contents( $file, $image_data );
			$wp_filetype = wp_check_filetype($filename, null );

			// Compose attachment Post
			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title' => sanitize_file_name($filename),
				'post_content' => false,
				'post_status' => 'inherit'
			);

			// Insert Attachment
			$attach_id	 = wp_insert_attachment($attachment, $file, $post);
			$attach_data = wp_generate_attachment_metadata($attach_id, $file);
			wp_update_attachment_metadata($attach_id, $attach_data );

			// Featured Image
			if( $thumbnail == true ) set_post_thumbnail($post, $attach_id);
			if( $showurl == true ) return wp_get_attachment_url($attach_id);
		}
	}
}

/* Insert Genres
========================================================
*/
if( ! function_exists( 'dt_dbmovies_insert_genres' ) ) {
	function dt_dbmovies_insert_genres( $post_id = null, $type = null ) {
		// Insert genres @since 2.1.3
		global $dbmvsoptions;
		$title = isset( $_POST['ids'] ) ? $_POST['ids'] : null;
		$term  = get_the_term_list( $post_id, 'genres' );
		if( is_user_logged_in() and $dbmvsoptions['genres'] == true and $post_id != null AND $type != null AND $title != null ) {
			if( $term == false ) {
				$json 	 = dt_dbmovies_remote( DBMOVIES_Api_tmdb. $type. "/" . $title . "?language=" . $dbmvsoptions['lang'] . "&api_key=" . $dbmvsoptions['tmdb'] );
				$data    = json_decode($json, TRUE);
				$genres  = $data['genres'];
				$generos = array();
				foreach($genres as $dat) {
					$generos[] = $dat['name'];
				}
				wp_set_object_terms( $post_id, $generos, 'genres', false);
			}
		}
	}
}


/* Update Rating from IMDB.com
========================================================
*/
if( ! function_exists( 'dt_update_imdb_rating' ) ) {
	function dt_update_imdb_rating() {
		set_time_limit(30000);

		// Global variables
		global $dbmvsoptions;

		$apikey = $dbmvsoptions['dbmv'];
		$postid = isset( $_POST['id'] )	  ? $_POST['id']	: false;
		$imdbid = isset( $_POST['imdb'] ) ? $_POST['imdb']	: false;
		if( (is_user_logged_in()) && ($apikey)){
            $cache  = new DooPlayCache;
			$api	= dt_dbmovies_remote( DBMOVIES_Api_dbmv. $imdbid . '&key=' . $apikey );	
			$data	= json_decode($api, TRUE);
			$rating = $data['imdbRating'];
			$votes	= $data['imdbVotes'];
				update_post_meta( $postid, 'imdbRating', $rating );
				update_post_meta( $postid, 'imdbVotes', $votes );
				echo '<strong>'. $rating. '</strong> '. $votes .' '. __d('votes');

            // Delete Cache
            $cache->delete($postid.'_postmeta');
		}
		die();
	}
	add_action('wp_ajax_update_imdb_rating', 'dt_update_imdb_rating');
	add_action('wp_ajax_nopriv_update_imdb_rating', 'dt_update_imdb_rating');
}

/* GET Remote content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_remote' ) ) {
	function dt_dbmovies_remote( $api = null ) {
		return wp_remote_retrieve_body( wp_remote_get( $api ) );
	}
}


if(!function_exists('dbmovies_tags_dooplay')){
    function dbmovies_tags_dooplay($option, $data){
        $option = str_replace('{name}',doo_isset($data,'name'),$option);
        $option = str_replace('{year}',doo_isset($data,'year'),$option);
        $option = str_replace('{season}',doo_isset($data,'season'),$option);
        $option = str_replace('{episode}',doo_isset($data,'episode'),$option);
        $option = apply_filters('dbmovies_tags_dooplay',$option);
        return $option;
    }
}

/* Class Elements for API
========================================================
*/

class Dbmovies_for_dooplay_class {

	// Construct
	function __construct() {
		// none
	}

	// get genres Movies ID
	function genres_movie() {
		$genres = array(
			null	=> __d('All genres'),
			'28'	=> __d('Action'),
			'12'	=> __d('Adventure'),
			'16'	=> __d('Animation'),
			'35'	=> __d('Comedy'),
			'80'	=> __d('Crime'),
			'99'	=> __d('Documentary'),
			'18'	=> __d('Drama'),
			'10751' => __d('Family'),
			'14'	=> __d('Fantasy'),
			'36'	=> __d('History'),
			'27'	=> __d('Horror'),
			'10402' => __d('Music'),
			'9648'	=> __d('Mystery'),
			'10749' => __d('Romance'),
			'878'	=> __d('Science Fiction'),
			'10770' => __d('TV Movie'),
			'53'	=> __d('Thriller'),
			'10752' => __d('War'),
			'37'	=> __d('Western')
		);
		return $genres;
	}

	// get genres TV ID
	function genres_tv() {
		$genres = array(
			null	=> __d('All genres'),
			'10759'	=> __d('Action & Adventure'),
			'16'	=> __d('Animation'),
			'35'	=> __d('Comedy'),
			'80'	=> __d('Crime'),
			'99'	=> __d('Documentary'),
			'18'	=> __d('Drama'),
			'10751'	=> __d('Family'),
			'10762'	=> __d('Kids'),
			'9648'	=> __d('Mystery'),
			'10763'	=> __d('News'),
			'10764'	=> __d('Reality'),
			'10765'	=> __d('Sci-Fi & Fantasy'),
			'10766'	=> __d('Soap'),
			'10767'	=> __d('Talk'),
			'10768'	=> __d('War & Politics'),
			'37'	=> __d('Western'),
		);
		return $genres;
	}

	// get Languages
	function languages() {
		$languages = array(
			"ar-AR" => __d('Arabic'),
			"bs-BS" => __d('Bosnian'),
			"bg-BG" => __d('Bulgarian'),
			"hr-HR" => __d('Croatian'),
			"cs-CZ" => __d('Czech'),
			"da-DK" => __d('Danish'),
			"nl-NL" => __d('Dutch'),
			"en-US" => __d('English'),
			"fi-FI" => __d('Finnish'),
			"fr-FR" => __d('French'),
			"de-DE" => __d('German'),
			"el-GR" => __d('Greek'),
			"he-IL" => __d('Hebrew'),
			"hu-HU" => __d('Hungarian'),
			"is-IS" => __d('Icelandic'),
			"id-ID" => __d('Indonesian'),
			"it-IT" => __d('Italian'),
			"ko-KR" => __d('Korean'),
			"lb-LB" => __d('Letzeburgesch'),
			"lt-LT" => __d('Lithuanian'),
			"zh-CN" => __d('Mandarin'),
			"fa-IR" => __d('Persian'),
			"pl-PL" => __d('Polish'),
			"pt-PT" => __d('Portuguese'),
			"pt-BR" => __d('Portuguese'),
			"ro-RO" => __d('Romanian'),
			"ru-RU" => __d('Russian'),
			"sk-SK" => __d('Slovak'),
			"es-ES" => __d('Spanish'),
			"es-MX" => __d('Spanish LA'),
			"sv-SE" => __d('Swedish'),
			"th-TH" => __d('Thai'),
			"tr-TR" => __d('Turkish'),
			"tw-TW" => __d('Twi'),
			"uk-UA" => __d('Ukrainian'),
			"vi-VN" => __d('Vietnamese')
		);
		return $languages;
	}
}


/* All requires
========================================================
*/
get_template_part('inc/core/dbmovies/importers');
get_template_part('inc/core/dbmovies/helptabs');
get_template_part('inc/core/dbmovies/assets');
get_template_part('inc/core/dbmovies/restapi');
get_template_part('inc/core/dbmovies/requests');
