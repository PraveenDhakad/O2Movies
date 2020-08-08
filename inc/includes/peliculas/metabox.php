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

/* Register Metabox movies
========================================================
*/
if( ! function_exists( 'dt_metabox_movies' ) ) {
	function dt_metabox_movies() {
		add_meta_box('dt_metabox', __d('Movie Info'), 'dt_metabox_movies_display', 'movies', 'normal', 'high');
	}
	add_action('add_meta_boxes', 'dt_metabox_movies');
}

/* Display Metabox movies
========================================================
*/

if( ! function_exists( 'dt_metabox_movies_display' ) ) {
	function dt_metabox_movies_display( $post) {

	    // Nonce security
	    wp_nonce_field('_movie_nonce', 'movie_nonce');

		// Metabox options
		$options = array(

	        array(
	            'id'          => 'ids',
				'id2'		  => null,
				'id3'		  => null,
	            'type'        => 'generator',
	            'style'       => 'style="background: #f7f7f7"',
	            'class'       => 'regular-text',
	            'placeholder' => 'tt2911666',
	            'label'       => __d('Generate data'),
	            'desc'        => __d('Generate data from <strong>imdb.com</strong>'),
	            'fdesc'       => __d('E.g. http://www.imdb.com/title/<strong>tt2911666</strong>/')
	        ),

	        // Field ( dt_featured_post )
	        array(
	            'id'     => 'dt_featured_post',
	            'type'   => 'checkbox',
	            'label'  => __d('Featured Title'),
	            'clabel' => __d('Do you want to mark this title as a featured item?')
	        ),

	        // Heading ==============================
	        array(
	            'type'    => 'heading',
	            'colspan' => 2,
	            'text'    => __d('Images and trailer')
	        ),

	        // Field ( dt_poster )
	        array(
	            'id'    => 'dt_poster',
	            'type'  => 'upload',
	            'label' => __d('Poster'),
	            'desc'  => __d('Add url image'),
	            'aid'   => 'up_images_poster',
	            'ajax'  => false
	        ),

	        // Field ( dt_backdrop )
	        array(
	            'id'      => 'dt_backdrop',
	            'type'    => 'upload',
	            'label'   => __d('Main Backdrop'),
	            'desc'    => __d('Add url image'),
	            'aid'     => 'up_images_backdrop',
	            'prelink' => 'https://image.tmdb.org/t/p/w500',
	            'ajax'    => true
	        ),

	        // Field ( imagenes )
	        array(
	            'id'     => 'imagenes',
	            'type'   => 'textarea',
	            'rows'   => 5,
	            'upload' => true,
	            'aid'    => 'up_images_images',
	            'label'  => __d('Backdrops'),
	            'desc'   => __d('Place each image url below another')
	        ),

	        // Field ( youtube_id )
	        array(
	            'id'    => 'youtube_id',
	            'type'  => 'text',
	            'class' => 'small-text',
	            'label' => __d('Video trailer'),
	            'desc'  => __d('Add id Youtube video'),
	            'fdesc' => '[id_video_youtube]',
				'double' => null,
	        ),

	        // Heading ==============================
	        array(
	            'type'    => 'heading',
	            'colspan' => 2,
	            'text'    => __d('IMDb.com data')
	        ),

	        // Field ( imdbRating / imdbVotes )
	        array(
	            'double' => true,
	            'id'     => 'imdbRating',
	            'id2'    => 'imdbVotes',
	            'type'   => 'text',
	            'label'  => __d('Rating IMDb'),
	            'desc'   => __d('Average / votes')
	        ),

	        // Field ( Rated )
	        array(
	            'id'    => 'Rated',
	            'type'  => 'text',
	            'class' => 'small-text',
				'double' => null,
				'fdesc'	=> null,
	            'label' => __d('Rated')
	        ),

	        // Field ( Country )
	        array(
	            'id'    => 'Country',
	            'type'  => 'text',
	            'class' => 'small-text',
				'fdesc'	=> null,
				'desc'	=> null,
				'double' => null,
	            'label' => __d('Country')
	        ),

	        // Heading ==============================
	        array(
	            'type'    => 'heading',
	            'colspan' => 2,
	            'text' => __d('Themoviedb.org data')
	        ),

	        // Field ( idtmdb )
	        array(
	            'id'    => 'idtmdb',
	            'type'  => 'text',
	            'class' => 'small-text',
				'fdesc'	=> null,
				'desc'	=> null,
				'double' => null,
				'class' => null,
	            'label' => __d('ID TMDb')
	        ),

	        // Field ( original_title )
	        array(
	            'id'    => 'original_title',
	            'type'  => 'text',
	            'class' => 'small-text',
				'fdesc'	=> null,
				'double' => null,
				'class' => null,
				'desc' => null,
	            'label' => __d('Original title')
	        ),

	        // Field ( tagline )
	        array(
	            'id'    => 'tagline',
	            'type'  => 'text',
	            'class' => 'small-text',
				'fdesc'	=> null,
				'double' => null,
				'desc' => null,
	            'label' => __d('Tag line')
	        ),

	        // Field ( release_date )
	        array(
	            'id'    => 'release_date',
	            'type'  => 'date',
	            'label' => __d('Release Date')
	        ),

	        // Field ( vote_average / vote_count )
	        array(
	            'double' => true,
	            'id'     => 'vote_average',
	            'id2'    => 'vote_count',
	            'type'   => 'text',
	            'label'  => __d('Rating TMDb'),
	            'desc'   => __d('Average / votes')
	        ),

	        // Field ( runtime )
	        array(
	            'id'    => 'runtime',
	            'type'  => 'text',
	            'class' => 'small-text',
	            'label' => __d('Runtime')
	        ),

	        // Field ( dt_cast )
	        array(
	            'id' => 'dt_cast',
	            'type' => 'textarea',
	            'rows' => 5,
				'upload' => false,
	            'label' => __d('Cast')
	        ),

	        // Field ( dt_dir )
	        array(
	            'id'    => 'dt_dir',
	            'type'  => 'text',
	            'label' => __d('Director')
	        )
	    );

		// Start HTML
	    echo '<div id="loading_api"></div>';
	    echo '<div id="api_table"><table class="options-table-responsive dt-options-table"><tbody>';

			new Doofields($options);

		echo '</tbody></table></div>';
	    // End HTML
	}
}

/* Save Metabox movies @since 2.1.3
========================================================
*/
if( ! function_exists( 'dt_metabox_movies_save' ) ) {
	function dt_metabox_movies_save( $post_id ) {

        // Cache class
        $cache = new DooPlayCache;

	    // Conditionals
	    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( ! isset( $_POST['movie_nonce'] ) || ! wp_verify_nonce( $_POST['movie_nonce'], '_movie_nonce') ) return;
		if ( ! current_user_can('edit_post', $post_id ) ) return;

		// Array Post-meta
		$update_post_meta = array(
			'ids'				=> 'text',
			'dt_poster'			=> 'text',
			'dt_backdrop'		=> 'text',
			'imagenes'			=> 'textarea',
			'youtube_id'		=> 'text',
			'imdbRating'		=> 'text',
			'imdbVotes'			=> 'text',
			'original_title'	=> 'text',
			'Rated'				=> 'text',
			'release_date'		=> 'date',
			'runtime'			=> 'text',
			'Country'			=> 'text',
			'vote_average'		=> 'text',
			'vote_count'		=> 'text',
			'tagline'			=> 'text',
			'dt_cast'			=> 'text',
			'dt_dir'			=> 'textarea',
			'idtmdb'			=> 'text',
			'dt_featured_post'	=> 'checkbox',
		);

		// Generate POST @since 2.1.3
		foreach ($update_post_meta as $key => $type) {
			if ( isset( $_POST[$key] ) ) update_post_meta( $post_id, $key, esc_attr( $_POST[$key] ) ); else delete_post_meta( $post_id, $key );
		}

		// Insert Poster @since 2.1.3
		$posterurl = isset( $_POST['dt_poster'] ) ? 'https://image.tmdb.org/t/p/w500'. $_POST['dt_poster'] : null;
		if ( $posterurl != null AND has_post_thumbnail() == false ) dt_dbmovies_upload_image( $posterurl, $post_id, true, false );
		// Insert genres @since 2.1.3
		dt_dbmovies_insert_genres($post_id, 'movie');
        // Delete cache @since 2.1.8
        $cache->delete($post_id.'_postmeta');
	}
	add_action('save_post', 'dt_metabox_movies_save');
}
