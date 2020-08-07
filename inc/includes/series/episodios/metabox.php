<?php
/*
* ----------------------------------------------------
* @author: Doothemes
* @author URI: https://doothemes.com/
* @copyright: (c) 2018 Doothemes. All rights reserved
* ----------------------------------------------------
*
* @since 2.1.8
*
*/

/* Register Metabox Episodes
========================================================
*/
if( ! function_exists( 'dt_metabox_episodes' ) ) {
	function dt_metabox_episodes() {
		add_meta_box('dt_metabox', __d('Episodes'),'dt_metabox_episodes_display','episodes','normal','high');
	}
	add_action('add_meta_boxes', 'dt_metabox_episodes');
}

/* Display Metabox Episodes
========================================================
*/
if( ! function_exists( 'dt_metabox_episodes_display' ) ) {
	function dt_metabox_episodes_display( $post) {

	    // Nonce security
	    wp_nonce_field('_episodios_nonce', 'episodios_nonce');

	    // Metabox options
	    $options = array(

	        array(
	            'id'           => 'ids',
	            'id2'          => 'temporada',
	            'id3'          => 'episodio',
	            'type'         => 'generator',
	            'style'        => 'style="background: #f7f7f7"',
	            'class'        => 'extra-small-text',
	            'placeholder'  => '1402',
	            'placeholder2' => '1',
	            'placeholder3' => '2',
	            'label'        => __d('Generate data'),
	            'desc'         => __d('Generate data from <strong>themoviedb.org</strong>'),
	            'fdesc'        => __d('E.g. https://www.themoviedb.org/tv/<strong>1402</strong>-the-walking-dead/season/<strong>1</strong>/episode/<strong>2</strong>')
	        ),

	        // Field ( episode_name )
	        array(
	            'id'    => 'episode_name',
	            'type'  => 'text',
	            'class' => 'regular-text',
	            'label' => __d('Episode title')
	        ),

	        // Field ( serie )
	        array(
	            'id'    => 'serie',
	            'type'  => 'text',
	            'class' => 'regular-text',
	            'label' => __d('Serie name')
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
	            'ajax'    => false
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

	        // Field ( air_date )
	        array(
	            'id'    => 'air_date',
	            'type'  => 'date',
	            'label' => __d('Air date')
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
/* Save Metabox TVShows > Seasons > Episodes @since 2.1.3
========================================================
*/
if(!function_exists( 'dt_metabox_episodes_save')){
	function dt_metabox_episodes_save($post_id){

        // Cache class
        $cache = new DooPlayCache;

		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if(!isset($_POST['episodios_nonce']) || !wp_verify_nonce($_POST['episodios_nonce'], '_episodios_nonce')) return;
		if(!current_user_can('edit_post', $post_id)) return;

		// Post-meta
		$update_post_meta = array(
			'ids'			=> 'text',
			'temporada'		=> 'text',
			'episodio'		=> 'text',
			'air_date'		=> 'text',
			'episode_name'	=> 'text',
			'dt_poster'		=> 'text',
			'dt_backdrop'	=> 'text',
			'imagenes'		=> 'text',
			'serie'			=> 'text'
		);

		// Generate POST @since 2.1.3
		foreach ($update_post_meta as $key => $type) {
			if ( isset( $_POST[$key] ) ) update_post_meta( $post_id, $key, esc_attr( $_POST[$key] ) ); else delete_post_meta( $post_id, $key );
		}

		// Insert Poster @since 2.1.3
		$backdropurl = isset($_POST['dt_backdrop']) ? 'https://image.tmdb.org/t/p/w500'. $_POST['dt_backdrop'] : null;
		if ( $backdropurl != null AND has_post_thumbnail() == false ) dt_dbmovies_upload_image( $backdropurl, $post_id, true, false );
        // Delete cache @since 2.1.8
        $cache->delete($post_id.'_postmeta');
	}
	add_action('save_post', 'dt_metabox_episodes_save');
}
