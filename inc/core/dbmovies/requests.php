<?php
/*
* -------------------------------------------------------------------------------------
*
* DBmovies for DooPlay
*
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* -------------------------------------------------------------------------------------
*
* @since 2.1.3
*
*/

/* Filter content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_request_search_content' ) ) {
	function dt_dbmovies_request_search_content() {
		set_time_limit(3000);
		global $dbmvsoptions;
		$opt	= $dbmvsoptions;
		$page	= isset( $_REQUEST['page'] ) ? $_REQUEST['page'] : 1;
		$term	= isset( $_REQUEST['term'] ) ? $_REQUEST['term'] : null;
		$type	= isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : null;
		$nonce	= isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : null;
		$dateid	= ( $type == 'tv' ) ? 'first_air_date' : 'release_date';
        $unkuser = cs_get_option('requestunknown');

		if(is_user_logged_in() || $unkuser == true){

            if(wp_verify_nonce($nonce, 'dbmovies_requests_users')){
                $api	= dt_dbmovies_remote( DBMOVIES_Api_tmdb.'search/'.$type.'?api_key='.$opt['tmdb'].'&language='.$opt['lang'].'&query='.$term.'&page='.$page );
    			$data	= json_decode($api, TRUE);

    			// Total
    			$total_results = $data['total_results'];
    			$total_pages   = $data['total_pages'];

    			// Pages
    			$prevpage = ( $page > 1 ) ? $page-1 : false;
    			$nextpage = ( $page < $total_pages) ? $page+1 : false;

    			if( $total_results == 0 ) {
    				echo '<div class="metainfo">'. __d('No results') .'</div>';
    			}

    			if($total_results > 1 ) {
    				echo '<div class="resultinfo"><strong>'.$total_results.'</strong> '. __d('results') .' '. __d('in') .' '. dt_dbmovies_elapsed_time(time()) .' '. __d('seconds') .'</div>';
    			}

    			// Results
    			$ctd = array();
    			$results = $data['results'];
    			echo '<div class="items">';
    			foreach($results as $ci) {

    				$ctd_id		= $ctd[] = $ci['id'];
    				$ctd_title	= $ctd[] = ( $type == 'tv' ) ? $ci['name'] : $ci['title'];
    				$ctd_poster	= $ctd[] = $ci['poster_path'];
    				$ctd_date	= $ctd[] = ( $ci[$dateid] ) ? $ci[$dateid] : '--';
    				$img		= ( $ctd_poster ) ? 'https://image.tmdb.org/t/p/w185'.$ctd_poster : DOO_URI.'/assets/img/no/poster.png';
    				// Verificar contenido repetido
    				if($type == 'tv') {

    					$check = dt_dbmovies_very_tmdb($ctd_id, 'ids');

    				}elseif($type == 'movie') {

    					$check = ( dt_dbmovies_very_tmdb($ctd_id, 'idtmdb') == 1 ) ? dt_dbmovies_very_tmdb($ctd_id, 'idtmdb') : dt_dbmovies_very_tmdb($ctd_id, 'ids');

    				}

    				$exclude	= ($check == 1) ? ' existing' : 'get_data';
    				$import		= ($check != 1) ? '<a class="get_content_dbmovies" data-id="'.$ctd_id.'" data-type="'.$type.'" data-nonce="'.wp_create_nonce( $ctd_id.'_post_request').'">'. __d('Request') .'</a>' : '<div class="itm-exists">'. __d('already exists').'</div>';

    				echo '<article id="'.$ctd_id.'" class="item animation-1 '.$exclude.'">';
    				echo '<div class="box">';
    				echo '<div class="poster"><img src="'. $img .'" /></div>';
    				echo '<h3>'. $ctd_title .'</h3>';
    				echo '<div class="data"><span id="tmdb-'.$ctd_id.'">'.$import.'</span></div>';
    				echo '</div>';
    				echo '</article>';
    			}
    			echo '</div>';
            } else {
                echo '<div class="metainfo">'. __d('Error verification nonce') .'</div>';
            }

		} else {
			echo '<div class="metainfo">'. __d('Please <a class="clicklogin">sign in</a> to continue') .'</div>';
		}
		die();
	}

	add_action('wp_ajax_dbmovies_requests_search', 'dt_dbmovies_request_search_content');
	add_action('wp_ajax_nopriv_dbmovies_requests_search', 'dt_dbmovies_request_search_content');
}


/* Post content
========================================================
*/
if( ! function_exists( 'dt_dbmovies_request_post_content' ) ) {
	function dt_dbmovies_request_post_content() {
		global $dbmvsoptions;
		$opt = $dbmvsoptions;
		// All data
		$id 	= isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : false;
		$type 	= isset( $_REQUEST['type'] ) ? $_REQUEST['type'] : false;
		$nonce 	= isset( $_REQUEST['nonce'] ) ? $_REQUEST['nonce'] : false;
        $unkuser = cs_get_option('requestunknown');
		// Conditional
        if(is_user_logged_in() || $unkuser == true){
            if(wp_verify_nonce($nonce, $id. '_post_request') ) {
    			// Get Info from API
    			$json = dt_dbmovies_remote( DBMOVIES_Api_tmdb.$type."/".$id."?language=".$opt['lang']."&include_image_language=".$opt['lang'].",null&api_key=".$opt['tmdb'] );
    			$data = json_decode($json, TRUE);
    			// GET Data
    			$overview	= $data['overview'];
    			$title		= ($type == 'tv') ? $data['name'] : $data['title'];
    			$backdrop	= $data['backdrop_path'];
    			$poster		= $data['poster_path'];
    			$post = array(
    				'post_title'	=> dt_dbmovies_text_cleaner($title),
    				'post_status'	=> dt_dbmovies_requests_poststatus(),
    				'post_type'		=> 'requests',
    				'post_date'     => date('Y-m-d H:i:s'),
    				'post_date_gmt' => date('Y-m-d H:i:s'),
    				'post_author'	=> is_user_logged_in() ? get_current_user_id() : '1'
    			);
    			if( isset( $title ) AND dt_dbmovies_very_tmdb($id, 'ids') != 1 ) {
    				// Insert post
    				$post_id = wp_insert_post($post);
    				// Post Meta
    				$data = array(
    					'type'		=> $type,
    					'overview'	=> $overview,
    					'backdrop'	=> $backdrop,
    					'poster'	=> $poster,
    				);
    				add_post_meta($post_id, 'ids', esc_attr($id));
    				add_post_meta($post_id, '_dbmv_requests_post', $data);
    			}
    		}
        }

		die();
	}
	add_action('wp_ajax_dbmovies_post_requests','dt_dbmovies_request_post_content');
	add_action('wp_ajax_nopriv_dbmovies_post_requests','dt_dbmovies_request_post_content');
}

// Post Archive
if( ! function_exists( 'dt_dbmovies_post_archive' ) ) {
	function dt_dbmovies_post_archive() {
		global $post;

		// Info in database
		$id    = isset( $_REQUEST['id'] ) ? $_REQUEST['id'] : null;
		$meta  = get_post_meta($id, '_dbmv_requests_post', true);
		$title = get_the_title( $id );
		if( $id != null ) {
			$type     = isset( $meta['type'] ) ? $meta['type'] : null;
			$overview = isset( $meta['overview'] ) ? $meta['overview'] : null;
			$backdrop = isset( $meta['backdrop'] ) ? $meta['backdrop'] : null;
			$poster   = isset( $meta['poster'] ) ? $meta['poster'] : null;

			if( $type == 'movie') $maintype = __d('Movie');
			if( $type == 'tv') $maintype = __d('TVShow');

			echo ( $backdrop != null ) ? "<div class='backdrop'><img src='https:\/\/image.tmdb.org/t/p/w500{$backdrop}'> <span>{$maintype}</span></div>" : null;
			echo "<div class='data'>";
			echo "<h3>{$title}</h3>";
			echo ( $overview != null) ? "<p>{$overview}</p>" : null;
			echo "</div>";

		}
		die();
	}
	add_action('wp_ajax_dbmovies_post_archive','dt_dbmovies_post_archive');
	add_action('wp_ajax_nopriv_dbmovies_post_archive','dt_dbmovies_post_archive');
}


function dt_dbmovies_requests_poststatus(){
    // Post status
    $a = 'publish';
    $b = 'pending';
    // Comparate User Role
    if(!is_user_logged_in()){
        return doo_is_true('requestpublisherole','unk') ? $a : $b;
    }
    elseif(current_user_can('administrator')) {
        return doo_is_true('requestpublisherole','adm') ? $a : $b;
    }
    elseif(current_user_can('editor')) {
        return doo_is_true('requestpublisherole','edt') ? $a : $b;
    }
    elseif(current_user_can('author')) {
        return doo_is_true('requestpublisherole','atr') ? $a : $b;
    }
    elseif(current_user_can('contributor')) {
        return doo_is_true('requestpublisherole','ctr') ? $a : $b;
    }
    elseif(current_user_can('subscriber')) {
        return doo_is_true('requestpublisherole','sbr') ? $a : $b;
    }
    else {
        return $b;
    }
}
