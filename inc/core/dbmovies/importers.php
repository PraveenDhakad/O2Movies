<?php
/*
* ----------------------------------------------------
*
* DBmovies Importers for DooPlay
*
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* ----------------------------------------------------
*
* @since 2.1.8
*
*/


/* INSERT Movies
========================================================
*/
if( ! function_exists( 'dbm_post_movie' ) ) {
	function dbm_post_movie( $idmovie = null ) {
		global $dbmvsoptions;
		$opt = $dbmvsoptions;
        $tmdb_args = array(
            'append_to_response'     => 'images,trailers',
            'include_image_language' => doo_isset($opt,'lang').',null',
            'language'               => doo_isset($opt,'lang'),
            'api_key'                => doo_isset($opt,'tmdb')
        );
        $tmdb = esc_url_raw( add_query_arg($tmdb_args, DBMOVIES_Api_tmdb.'movie/'.$idmovie) );
        $tmdb = dt_dbmovies_remote($tmdb);
        $data = json_decode($tmdb, TRUE);

		$trailer = isset($data['trailers']['youtube']) ? $data['trailers']['youtube'] : false;
		$imdb    = doo_isset($data,'imdb_id');

		// Trailers
		$youtube = false;
		if($trailer){
			foreach ($trailer as $key) {
				$youtube .= '['. $key['source'].']';
	            break;
			}
		}

		// IMDb Data
    	$jsonn = dt_dbmovies_remote( DBMOVIES_Api_dbmv. $imdb .$opt['dbmv'] );
		$data1 = json_decode($jsonn, TRUE);

		// Compose data from IMDb.com
		$a4 = doo_isset($data1,'imdbRating');
		$a5 = doo_isset($data1,'imdbVotes');
		$a6 = doo_isset($data1,'Rated');
		$a7 = doo_isset($data1,'Country');


		// Compose data from Themoviedb.org
		$b1		= doo_isset($data,'runtime');
		$b2		= doo_isset($data,'tagline');
		$b3		= doo_isset($data,'title');
		$b4		= doo_isset($data,'overview');
		$b9		= doo_isset($data,'vote_count');
		$b10	= doo_isset($data,'vote_average');
		$b11	= doo_isset($data,'release_date');
		$b12	= doo_isset($data,'original_title');
		$a3		= substr($b11, 0, 4);
		$b13	= doo_isset($data,'poster_path');
		$upimg	= isset($b13) ? 'https://image.tmdb.org/t/p/w500' . $b13 : false;
		$b14	= doo_isset($data,'backdrop_path');
		$b15	= isset($data['images']["backdrops"]) ? $data['images']["backdrops"] : false;
		$b16    = doo_isset($data,'genres');

		$i = '0';
		$imgs = false;
		if($b15){
	        foreach($b15 as $img) if ($i < 10) {
				$imgs.= doo_isset($img,'file_path')."\n";
				$i +=1;
			}
		}

		$generos = array();
		if($b16){
			foreach($b16 as $ci) {
				$generos[] = doo_isset($ci,'name');
			}
		}

		// Get CAST from Themoviedb.org
        $tmdb2 = esc_url_raw(add_query_arg($tmdb_args, DBMOVIES_Api_tmdb.'movie/'.$idmovie.'/credits'));
        $tmdb2 = dt_dbmovies_remote($tmdb2);
        $data2 = json_decode($tmdb2, TRUE);

		$w1 = doo_isset($data2,'cast');
		$w2 = doo_isset($data2,'crew');

		$actores = false;
		$d_actores = false;
		if($w1){
			$i = '0';
	        foreach($w1 as $valor) if ($i < 10) {
				$actores.= doo_isset($valor,'name'). ",";
				$i +=1;
			}
			$i = '0';
	        foreach($w1 as $valor) if ($i < 10) {
				if($valor['profile_path'] == NULL) {
					$valor['profile_path'] = "null";
				}
				$d_actores.= "[" . $valor['profile_path'] . ";" . $valor['name'] . "," . $valor['character'] . "]";
				$i +=1;
			}
		}

		$d_dir = false;
		$dir   = false;
		if($w2){
			foreach($w2 as $valorc) {

				$detp = doo_isset($valorc,'department');
				$path = doo_isset($valorc,'profile_path') == NULL ? 'null' : doo_isset($valorc,'profile_path');

				if ($detp == "Directing") {
					$d_dir .= "[".$path.";".doo_isset($valorc,'name')."]";
				}

				if ($detp == "Directing") {
					$dir .= doo_isset($valorc,'name'). ",";
				}
			}
		}

		$optdate = doo_isset($opt,'release') == true ? $b11 : date('Y-m-d H:i:s');
        $title_data = array(
            'name' => $b3,
            'year' => $a3,
        );
        $compose_title = cs_get_option('dbmvstitlemovies','{name}');

		// Compose Post
		$moviepost = array(
			'post_title'	=> dt_dbmovies_text_cleaner( dbmovies_tags_dooplay($compose_title, $title_data) ),
			'post_content'	=> dt_dbmovies_text_cleaner($b4),
			'post_date'     => $optdate,
			'post_date_gmt' => $optdate,
			'post_status'	=> 'publish',
			'post_type'		=> 'movies',
			'post_author'	=> get_current_user_id()
		);

		// Verify parameters required
		if( isset($data['title']) AND dt_dbmovies_very_tmdb($idmovie, 'idtmdb') != true ) {
			$post_id = wp_insert_post($moviepost);

			// Insert taxonomies
			wp_set_post_terms($post_id, $dir, 'dtdirector', false);
			wp_set_post_terms($post_id, $a3, 'dtyear',	false);
			wp_set_post_terms($post_id, $actores, 'dtcast', false);
			wp_set_object_terms($post_id, $generos, 'genres', false);

			// MetaKey and MetaValue
			$add_post_meta = array(
				'ids'				=> $imdb,
				'idtmdb'			=> $idmovie,
				'dt_poster'			=> $b13,
				'dt_backdrop'		=> $b14,
				'imagenes'			=> $imgs,
				'youtube_id'		=> $youtube,
				'imdbRating'		=> $a4,
				'imdbVotes'			=> $a5,
				'Rated'				=> $a6,
				'Country'			=> $a7,
				'original_title'	=> $b12,
				'release_date'		=> $b11,
				'vote_average'		=> $b10,
				'vote_count'		=> $b9,
				'tagline'			=> $b2,
				'runtime'			=> $b1,
				'dt_cast'			=> $d_actores,
				'dt_dir'			=> $d_dir,
			);
			// Add Post meta
			foreach ($add_post_meta as $key => $value) {
				if($key == 'imagenes') {
					if( isset($value) ) add_post_meta($post_id, $key, esc_attr($value), true);
				} else {
					if( isset($value) ) add_post_meta($post_id, $key, sanitize_text_field($value), true);
				}
			}
			// Upload poster
			if( $upimg != false ) dt_dbmovies_upload_image( $upimg, $post_id, true, false );
			// Success import data
			echo '<li class="fadeInDown"><span>'.dt_dbmovies_elapsed_time(time()).'</span> <span>'. __d('Movie') .'</span> <span><a href="'.admin_url("post.php?post={$post_id}&action=edit").'">'.__d('Edit').'</a></span> <a href="'.get_permalink($post_id).'" target="_blank">'.get_the_title($post_id).'</a></li>';
		} else {
			// Error repeated content
			echo '<li class="jump" style="color:orange">'.__d('Unexpected error').' - '.$idmovie.'</li>';
		}
	}
}


/* INSERT TVShows
========================================================
*/
if(!function_exists('dbm_post_tv')) {
	function dbm_post_tv( $ids = null ) {
		global $dbmvsoptions;
		$opt = $dbmvsoptions;
        $tmdb_args = array(
            'append_to_response'     => 'images,trailers',
            'include_image_language' => doo_isset($opt,'lang').',null',
            'language'               => doo_isset($opt,'lang'),
            'api_key'                => doo_isset($opt,'tmdb')
        );
        $tmdb = esc_url_raw(add_query_arg($tmdb_args, DBMOVIES_Api_tmdb.'tv/'.$ids));
        $tmdb = dt_dbmovies_remote($tmdb);
		$data2 = json_decode($tmdb, TRUE);
		$name			= doo_isset($data2,'name');
		$episodes		= doo_isset($data2,'number_of_episodes');
		$seasons		= doo_isset($data2,'number_of_seasons');
		$year			= substr(doo_isset($data2,'first_air_date'), 0, 4);
		$date1			= doo_isset($data2,'first_air_date');
		$date2			= doo_isset($data2,'last_air_date');
		$overview		= doo_isset($data2,'overview');
		$popularidad	= doo_isset($data2,'popularity');
		$originalname	= doo_isset($data2,'original_name');
		$promedio		= doo_isset($data2,'vote_average');
		$votos			= doo_isset($data2,'vote_count');
		$tipo			= doo_isset($data2,'type');
		$web			= doo_isset($data2,'homepage');
		$status			= doo_isset($data2,'status');
		$poster			= doo_isset($data2,'poster_path');
		$upload_poster	= isset($poster) ? 'https://image.tmdb.org/t/p/w500' . $poster : false;
		$backdrop		= doo_isset($data2,'backdrop_path');

		$images = isset($data2['images']["backdrops"]) ? $data2['images']["backdrops"] : false;
        $imgs = false;
		if($images){
			$i = '0';
			foreach($images as $valor2) if ($i < 10) {
				$imgs .= doo_isset($valor2,'file_path')."\n";
				$i +=1;
			}
		}

		$genres  = doo_isset($data2,'genres');
		$generos = array();
		if($genres){
			foreach($genres as $ci) {
				$generos[] = doo_isset($ci,'name');
			}
		}

		$networks = doo_isset($data2,'networks');
        $redes 	  = false;
		if($networks){
			foreach($networks as $co) {
				$redes.= doo_isset($co,'name').',';
			}
		}

		$studio   = doo_isset($data2,'production_companies');
        $estudios = false;
		if($studio){
			foreach($studio as $ht) {
				$estudios.= doo_isset($ht,'name').',';
			}
		}

		$creator   = doo_isset($data2,'created_by');
        $creador   = false;
		$creador_d = false;
		if($creator){
			foreach($creator as $cg) {
				$creador.= doo_isset($cg,'name').',';
			}
			foreach($creator as $ag) {
				$path = (doo_isset($ag,'profile_path') == NULL) ? 'null' : doo_isset($ag,'profile_path');
				$creador_d.= '['.$path.';'.doo_isset($ag,'name').']';
			}
		}

		$runtime  = doo_isset($data2,'episode_run_time');
        $duracion = false;
		if($runtime){
			foreach($runtime as $tm) {
				$duracion .= $tm;
				break;
			}
		}

        $tmdb2 = esc_url_raw(add_query_arg($tmdb_args, DBMOVIES_Api_tmdb.'tv/'.$ids.'/credits'));
		$tmdb2 = dt_dbmovies_remote($tmdb2);
		$data3 = json_decode($tmdb2, TRUE);

		$cast = doo_isset($data3,'cast');
        $actores   = false;
		$d_actores = false;

		if($cast){
			$i = '0';
			foreach($cast as $valor) if ($i < 10) {
				$actores .= doo_isset($valor,'name').',';
				$i +=1;
			}
			$i = '0';
			foreach($cast as $valor) if ($i < 10) {
				$path = doo_isset($valor,'profile_path') == NULL ? 'null' : doo_isset($valor,'profile_path');
				$d_actores .= '['.$path.';'.doo_isset($valor,'name').','.doo_isset($valor,'character').']';
				$i +=1;
			}
		}

        $tmdb3 = esc_url_raw(add_query_arg($tmdb_args, DBMOVIES_Api_tmdb.'tv/'.$ids.'/videos'));
		$tmdb3 = dt_dbmovies_remote($tmdb3);
		$data4 = json_decode($tmdb3, TRUE);

		$video   = doo_isset($data4,'results');
        $youtube = false;
		if($video){
			foreach($video as $yt) {
				$youtube .= '['.doo_isset($yt,'key').']';
				break;
			}
		}

		// Define date
		$optdate = doo_isset($opt,'release') == true ? $date1 : date('Y-m-d H:i:s');

		// title data
        $title_data = array(
            'name' => $name,
            'year' => $year,
        );

		// Compose title
        $compose_title = cs_get_option('dbmvstitletvshows','{name}');

		// Compose POST
		$tvpost = array(
			'post_title'	=> dt_dbmovies_text_cleaner(dbmovies_tags_dooplay($compose_title,$title_data)),
			'post_content'	=> dt_dbmovies_text_cleaner($overview),
			'post_status'	=> 'publish',
			'post_type'		=> 'tvshows',
			'post_date'     => $optdate,
			'post_date_gmt' => $optdate,
			'post_author'	=> get_current_user_id()
		);

		// Verify parameters required
		if( isset($name) AND dt_dbmovies_very_tmdb($ids,'ids') != true) {

			// Insert POST
			$post_id = wp_insert_post($tvpost);

			// Insert taxonomies
			wp_set_post_terms( $post_id,	$year,		'dtyear',		false );
			wp_set_object_terms( $post_id,	$generos,	'genres',		false );
			wp_set_post_terms( $post_id,	$redes,		'dtnetworks',	false );
			wp_set_post_terms( $post_id,	$estudios,	'dtstudio',		false );
			wp_set_post_terms( $post_id,	$actores,	'dtcast',		false );
			wp_set_post_terms( $post_id,	$creador,	'dtcreator',	false );

			// MetaKey and MetaValue
			$add_post_meta = array(
				'ids'					=> $ids,
				'dt_poster'				=> $poster,
				'dt_backdrop'			=> $backdrop,
				'imagenes'				=> $imgs,
				'youtube_id'			=> $youtube,
				'first_air_date'		=> $date1,
				'last_air_date'			=> $date2,
				'number_of_episodes'	=> $episodes,
				'number_of_seasons'		=> $seasons,
				'original_name'			=> $originalname,
				'status'				=> $status,
				'imdbRating'			=> $promedio,
				'imdbVotes'				=> $votos,
				'episode_run_time'		=> $duracion,
				'dt_cast'				=> $d_actores,
				'dt_creator'			=> $creador_d,
			);

			// Add Post meta
			foreach ($add_post_meta as $key => $value) {
				if($key == 'imagenes') {
					if( isset($value) ) add_post_meta($post_id, $key, esc_attr($value), true);
				} else {
					if( isset($value) ) add_post_meta($post_id, $key, sanitize_text_field($value), true);
				}
			}
			// Upload poster
			if( $upload_poster != false ) dt_dbmovies_upload_image($upload_poster, $post_id, true, false );
			// Success import data
			echo '<li class="fadeInDown"><span>'.dt_dbmovies_elapsed_time(time()).'</span> <span>'. __d('TV') .'</span> <span><a href="'.admin_url("post.php?post={$post_id}&action=edit").'">'.__d('Edit').'</a></span> <a href="'.get_permalink($post_id).'" target="_blank">'.$name.'</a></li>';
		} else {
			// Success Error
			echo '<li class="jump" style="color:orange">'.__d('Unexpected error').' - '.$ids.'</li>';
		}
	}
}

/* INSERT Episodes in Seasons
========================================================
*/
if(!function_exists('dbm_insert_episodes')) {
	function dbm_insert_episodes($seas = null, $tvsh = null, $idps = null) {

		// Get Dbmovies Option
		global $dbmvsoptions;
		$opt = $dbmvsoptions;

        $tmdb_args1 = array(
            'include_image_language' => doo_isset($opt,'lang').',null',
            'language'               => doo_isset($opt,'lang'),
            'api_key'                => doo_isset($opt,'tmdb')
        );

        $tmdb_args2 = array(
            'append_to_response'     => 'images,trailers',
            'include_image_language' => doo_isset($opt,'lang').',null',
            'language'               => doo_isset($opt,'lang'),
            'api_key'                => doo_isset($opt,'tmdb')
        );

		// API Request (TVShow)
        $tmdb1 = esc_url_raw(add_query_arg($tmdb_args1, DBMOVIES_Api_tmdb.'tv/'.$tvsh));
		$json2 = dt_dbmovies_remote($tmdb1);
		$data2 = json_decode($json2, TRUE);

		// TV Show data
		$tituloserie = doo_isset($data2,'name');

		// API Request (Season)
        $tmdb2 = esc_url_raw(add_query_arg($tmdb_args2, DBMOVIES_Api_tmdb.'tv/'.$tvsh.'/season/'.$seas));
		$json1 = dt_dbmovies_remote($tmdb2);
		$data1 = json_decode($json1, TRUE);

		// Season data
		$sdasd	      = count(doo_isset($data1,'episodes'));
		$poster_serie = doo_isset($data1,'poster_path');
		for($cont = 1; $cont <= $sdasd; $cont++) {

			// API Request (Episode)
            $tmdb3 = esc_url_raw(add_query_arg($tmdb_args2, DBMOVIES_Api_tmdb.'tv/'.$tvsh.'/season/'.$seas.'/episode/'.$cont));
			$json = dt_dbmovies_remote($tmdb3);
			$data = json_decode($json, TRUE);

			// Episode data
			$season		 = doo_isset($data,'season_number');
			$episode	 = doo_isset($data,'episode_number');
			$name		 = doo_isset($data,'name');
			$overview	 = doo_isset($data,'overview');
			$air_date	 = isset($data['air_date']) ? $data['air_date'] : date('Y-m-d');
			$still_path  = doo_isset($data,'still_path');
			$upload_img  = isset($still_path) ? 'https://image.tmdb.org/t/p/w500'. $still_path : false;
			$images		 = isset($data['images']["stills"]) ? $data['images']["stills"] : false;
//			print_r($json);die();
			// Compose Images
            $img = false;
			if($images){
				$i = '0';
				foreach($images as $valor2) if ($i < 10) {
					$img .= doo_isset($valor2,'file_path')."\n";
					$i +=1;
				}
			}

            $title_data = array(
                'name'    => $tituloserie,
                'season'  => $season,
                'episode' => $episode
            );

            $compose_title = cs_get_option('dbmvstitleepisodes','{name}: {season}x{episode}');

			// Compose POST
			$postepisodes = array(
				'post_title'	=> doo_clear_text(dbmovies_tags_dooplay($compose_title,$title_data)),
				'post_content'	=> doo_clear_text($overview),
				'post_status'	=> 'publish',
				'post_type'		=> 'episodes',
				'post_author'	=> get_current_user_id()
			);

			// Insert Post
			$post_id = wp_insert_post($postepisodes);

			// MetaKey And MetaValue
			$add_post_meta = array(
				'ids'			=> $tvsh,
				'temporada'		=> $season,
				'episodio'		=> $episode,
				'serie'			=> $tituloserie,
				'episode_name'	=> $name,
				'air_date'		=> $air_date,
				'imagenes'		=> $img,
				'dt_backdrop'	=> $still_path,
				'dt_poster'		=> $poster_serie,
			);

			// Add Post meta
			foreach ($add_post_meta as $key => $value) {
				if($key == 'imagenes') {
					if( isset($value) ) add_post_meta($post_id, $key, $value, true);
				} else {
					if( isset($value) ) add_post_meta($post_id, $key, sanitize_text_field($value), true);
				}
			}
			// Upload Image
			if($upload_img != false) dt_dbmovies_upload_image($upload_img, $post_id, true, false);
		}
		// Update status button
		update_post_meta($idps,'clgnrt','1');
	}
}

/* GET TVShow > Seasons
========================================================
*/
if(!function_exists('dbm_post_tvshow_seasons')){
	function dbm_post_tvshow_seasons() {

        // Define time limit
        $time_limit = cs_get_option('dbmvsphptime','300');
		set_time_limit($time_limit);

		global $dbmvsoptions;
		$opt = $dbmvsoptions;

		// Conditional (1)
		if( isset($_GET['seasons_nonce'] ) AND wp_verify_nonce($_GET['seasons_nonce'], 'add_seasons') ) {

			// Conditional (2)
			if(is_user_logged_in() AND isset($_GET["se"]) AND isset($_GET["link"]) ) {

				// Main data
				$ids  = doo_isset($_GET,'se');
				$link = doo_isset($_GET,'link');


                $tmdb_args1 = array(
                    'include_image_language' => doo_isset($opt,'lang').',null',
                    'language'               => doo_isset($opt,'lang'),
                    'api_key'                => doo_isset($opt,'tmdb')
                );

                $tmdb_args2 = array(
                    'append_to_response'     => 'images',
                    'include_image_language' => doo_isset($opt,'lang').',null',
                    'language'               => doo_isset($opt,'lang'),
                    'api_key'                => doo_isset($opt,'tmdb')
                );

				// API Request (TVShow)
                $tmdb1 = esc_url_raw(add_query_arg($tmdb_args1, DBMOVIES_Api_tmdb.'tv/'.$ids));
				$json2 = dt_dbmovies_remote($tmdb1);
				$data2 = json_decode($json2, TRUE);

				// Data
				$tituloserie = doo_isset($data2,'name');
				$sdasd		 = doo_isset($data2,'number_of_seasons');

				// Get Seasons
				for ($cont = 1; $cont <= $sdasd; $cont++) {

					// API Request (Season)
                    $tmdb2 = esc_url_raw(add_query_arg($tmdb_args2, DBMOVIES_Api_tmdb.'tv/'.$ids.'/season/'.$cont));
					$json = dt_dbmovies_remote($tmdb2);
					$data = json_decode($json, TRUE);

					// Get Data
					$name			= doo_isset($data,'name');
					$poster_serie	= doo_isset($data,'poster_path');
					$upload_poster	= isset($poster_serie) ? 'https://image.tmdb.org/t/p/w500' . $poster_serie : false;
					$overview		= doo_isset($data,'overview');
					$fecha			= isset($data['air_date']) ? $data['air_date'] : date('Y-m-d');
					$season_number	= doo_isset($data,'season_number');


                    $title_data = array(
                        'name'   => $tituloserie,
                        'season' => $cont,
                    );

                    $compose_title = cs_get_option('dbmvstitleseasons',__d('{name}: Season {season}'));

					// Compose POST
					$seasonpost = array(
						'post_title'	=> dt_dbmovies_text_cleaner(dbmovies_tags_dooplay($compose_title, $title_data)),
						'post_content'	=> dt_dbmovies_text_cleaner($overview),
						'post_status'	=> 'publish',
						'post_type'		=> 'seasons',
						'post_author'	=> get_current_user_id()
					);

					// Insert POST
					$post_id = wp_insert_post($seasonpost);

					// KeyMeta and KeyValue
					$add_post_meta = array(
						'ids'		=> $ids,
						'temporada' => $season_number,
						'serie'		=> $tituloserie,
						'air_date'	=> $fecha,
						'dt_poster' => $poster_serie
					);

					// Add Post meta
					foreach ($add_post_meta as $key => $value) {
						if( isset($value) ) add_post_meta( $post_id, $key, sanitize_text_field($value), true );
					}

					// Upload Image
					if( $upload_poster != false ) dt_dbmovies_upload_image($upload_poster, $post_id, true, false);
				}

				update_post_meta($link, 'clgnrt', '1');
				// Cache
				$cache = new DooPlayCache;
				// Delete cache
				$cache->delete($link.'_postmeta');
				wp_redirect(admin_url('edit.php?post_type=seasons')); exit;
			}
		}
		die();
	}
	add_action('wp_ajax_seasons_ajax', 'dbm_post_tvshow_seasons');
	add_action('wp_ajax_nopriv_seasons_ajax', 'dbm_post_tvshow_seasons');
}



/* GET TVShow > Season > Episodes ( wp-admin )
========================================================
*/
if( ! function_exists( 'dbm_post_episodes_ajax' ) ) {
	function dbm_post_episodes_ajax() {
		// Define time limit
		$time_limit = cs_get_option('dbmvsphptime','300');
		set_time_limit($time_limit);
		if( isset($_GET['episodes_nonce'] ) and wp_verify_nonce($_GET['episodes_nonce'], 'add_episodes') ) {
			if ( is_user_logged_in() AND isset($_GET["te"]) AND isset($_GET["se"]) AND isset($_GET["link"]) ) {
				// Cache
				$cache = new DooPlayCache;
				// Main data
				$seas = doo_isset($_GET,'te');
				$tvsh = doo_isset($_GET,'se');
				$idps = doo_isset($_GET,'link');
				dbm_insert_episodes( $seas, $tvsh, $idps );
				$cache->delete($idps.'_postmeta');
				wp_redirect(admin_url('edit.php?post_type=seasons')); exit;
			}
		}
		die();
	}
	add_action('wp_ajax_episodes_ajax', 'dbm_post_episodes_ajax');
	add_action('wp_ajax_nopriv_episodes_ajax', 'dbm_post_episodes_ajax');
}



/* GET TVShow > Season > Episodes ( front-end )
========================================================
*/
if(!function_exists( 'dbm_post_episodes_front_ajax')) {
	function dbm_post_episodes_front_ajax() {
		// Define time limit
		$time_limit = cs_get_option('dbmvsphptime','300');
		set_time_limit($time_limit);
		if( isset($_GET['episodes_nonce'] ) and wp_verify_nonce($_GET['episodes_nonce'], 'add_episodes') ) {
			if ( is_user_logged_in() AND isset($_GET["te"]) AND isset($_GET["se"]) AND isset($_GET["link"]) ) {
				// Cache
				$cache = new DooPlayCache;
				// Main data
				$seas = doo_isset($_GET,'te');
				$tvsh = doo_isset($_GET,'se');
				$idps = doo_isset($_GET,'link');
				dbm_insert_episodes($seas, $tvsh, $idps);
				$cache->delete($idps.'_postmeta');
				wp_redirect(get_permalink($idps)); exit;
			}
		}
		die();
	}
	add_action('wp_ajax_seasonsf_ajax', 'dbm_post_episodes_front_ajax');
	add_action('wp_ajax_nopriv_seasonsf_ajax', 'dbm_post_episodes_front_ajax');
}
